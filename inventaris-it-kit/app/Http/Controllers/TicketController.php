<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $tickets = Ticket::visibleTo($request->user())
            ->with(['asset', 'pelapor', 'teknisi'])
            ->when(in_array($status, Ticket::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('tickets.index', [
            'tickets' => $tickets,
            'statuses' => Ticket::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('tickets.create', [
            'assets' => Asset::with('location')
                ->where('status', '!=', 'nonaktif')
                ->orderBy('kode_aset')
                ->get(),
            'prioritas' => Ticket::PRIORITAS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'asset_id' => ['required', Rule::exists('assets', 'id')->whereNull('deleted_at')],
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:2000'],
            'prioritas' => ['required', Rule::in(Ticket::PRIORITAS)],
        ]);

        $user = $request->user();

        // Transaksi: tiket, log, dan status aset harus tersimpan bersama atau tidak sama sekali
        $ticket = DB::transaction(function () use ($data, $user) {
            $ticket = Ticket::create($data + [
                'nomor_tiket' => Ticket::generateNumber(),
                'pelapor_id' => $user->id,
                'status' => 'baru',
            ]);

            $ticket->logs()->create([
                'user_id' => $user->id,
                'status' => 'baru',
                'catatan' => 'Tiket dibuat.',
            ]);

            $this->syncAssetStatus($ticket);

            return $ticket;
        });

        return redirect()->route('tickets.show', $ticket)->with('success', "Tiket {$ticket->nomor_tiket} berhasil dibuat.");
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $user = $request->user();

        // Cegah IDOR: staff hanya boleh membuka tiketnya sendiri
        if ($user->role === 'staff' && $ticket->pelapor_id !== $user->id) {
            abort(403);
        }

        $ticket->load(['asset.location', 'pelapor', 'teknisi', 'logs.user']);

        return view('tickets.show', [
            'ticket' => $ticket,
            'teknisiList' => $user->role === 'admin'
                ? User::where('role', 'teknisi')->orderBy('name')->get()
                : collect(),
            'bisaUpdate' => $this->bisaUpdate($user, $ticket),
        ]);
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = $request->user();

        if (! $this->bisaUpdate($user, $ticket)) {
            abort(403, 'Tiket ini tidak dapat Anda ubah.');
        }

        $data = $request->validate([
            'status' => ['required', Rule::in(Ticket::STATUSES)],
            'teknisi_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'teknisi')],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $newStatus = $data['status'];

        if ($newStatus !== $ticket->status && ! in_array($newStatus, Ticket::TRANSITIONS[$ticket->status], true)) {
            return back()->with('error', "Status tidak bisa diubah dari {$ticket->status} ke {$newStatus}.");
        }

        // Teknisi selalu mengerjakan tiket atas namanya sendiri; admin boleh menunjuk teknisi
        $teknisiId = $user->role === 'teknisi'
            ? $user->id
            : ($data['teknisi_id'] ?? $ticket->teknisi_id);

        if ($newStatus === 'diproses' && ! $teknisiId) {
            return back()
                ->withErrors(['teknisi_id' => 'Pilih teknisi yang menangani tiket.'])
                ->withInput();
        }

        DB::transaction(function () use ($ticket, $newStatus, $teknisiId, $data, $user) {
            $ticket->update([
                'status' => $newStatus,
                'teknisi_id' => $teknisiId,
                'selesai_pada' => in_array($newStatus, ['selesai', 'ditutup'], true)
                    ? ($ticket->selesai_pada ?? now())
                    : null,
            ]);

            $ticket->logs()->create([
                'user_id' => $user->id,
                'status' => $newStatus,
                'catatan' => $data['catatan'] ?? null,
            ]);

            $this->syncAssetStatus($ticket);
        });

        return back()->with('success', 'Tiket diperbarui.');
    }

    /** Admin/teknisi boleh mengubah; tiket ditutup terkunci; teknisi hanya tiket miliknya atau yang belum ditugaskan. */
    private function bisaUpdate(User $user, Ticket $ticket): bool
    {
        if (! in_array($user->role, ['admin', 'teknisi'], true) || $ticket->status === 'ditutup') {
            return false;
        }

        if ($user->role === 'teknisi' && $ticket->teknisi_id !== null && $ticket->teknisi_id !== $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Hitung ulang status aset dari tiket yang masih terbuka:
     * ada tiket 'diproses' -> maintenance, ada tiket 'baru' -> rusak, tidak ada -> aktif.
     * Aset 'nonaktif' tidak pernah diubah otomatis.
     */
    private function syncAssetStatus(Ticket $ticket): void
    {
        $asset = $ticket->asset;

        if (! $asset || $asset->status === 'nonaktif') {
            return;
        }

        $open = $asset->tickets()->whereIn('status', ['baru', 'diproses']);

        if ((clone $open)->where('status', 'diproses')->exists()) {
            $new = 'maintenance';
        } elseif ($open->exists()) {
            $new = 'rusak';
        } else {
            $new = 'aktif';
        }

        $asset->update(['status' => $new]);
    }
}
