<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $totalAset = Asset::count();

        // SELECT status, COUNT(*) FROM assets WHERE deleted_at IS NULL GROUP BY status
        $asetPerStatus = Asset::select('status', DB::raw('COUNT(*) AS total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // JOIN + GROUP BY: jumlah aset per kategori
        $asetPerKategori = DB::table('assets')
            ->join('categories', 'categories.id', '=', 'assets.category_id')
            ->whereNull('assets.deleted_at')
            ->select('categories.nama', DB::raw('COUNT(assets.id) AS total'))
            ->groupBy('categories.id', 'categories.nama')
            ->orderByDesc('total')
            ->get();

        $tiketPerStatus = Ticket::visibleTo($user)
            ->select('status', DB::raw('COUNT(*) AS total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $tiketTerbaru = Ticket::visibleTo($user)
            ->with(['asset', 'pelapor'])
            ->latest('id')
            ->limit(5)
            ->get();

        // Aset dengan tiket terbuka (baru/diproses) terbanyak
        $asetBermasalah = DB::table('tickets')
            ->join('assets', 'assets.id', '=', 'tickets.asset_id')
            ->whereIn('tickets.status', ['baru', 'diproses'])
            ->select('assets.id', 'assets.kode_aset', 'assets.nama', DB::raw('COUNT(tickets.id) AS jumlah'))
            ->groupBy('assets.id', 'assets.kode_aset', 'assets.nama')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalAset',
            'asetPerStatus',
            'asetPerKategori',
            'tiketPerStatus',
            'tiketTerbaru',
            'asetBermasalah',
        ));
    }
}
