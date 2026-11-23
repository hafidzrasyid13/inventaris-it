<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetRequest;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $categoryId = $request->integer('category_id');

        $assets = Asset::with(['category', 'location'])
            ->search($request->string('q')->trim()->toString())
            ->when(in_array($status, Asset::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->when($categoryId > 0, fn ($q) => $q->where('category_id', $categoryId))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('assets.index', [
            'assets' => $assets,
            'categories' => Category::orderBy('nama')->get(),
            'statuses' => Asset::STATUSES,
        ]);
    }

    public function show(Asset $asset): View
    {
        $asset->load(['category', 'location', 'tickets' => fn ($q) => $q->latest('id')->with('teknisi')]);

        return view('assets.show', compact('asset'));
    }

    public function create(): View
    {
        return view('assets.create', [
            'asset' => new Asset(['status' => 'aktif']),
            'categories' => Category::orderBy('nama')->get(),
            'locations' => Location::orderBy('nama')->get(),
            'statuses' => Asset::STATUSES,
        ]);
    }

    public function store(AssetRequest $request): RedirectResponse
    {
        $asset = Asset::create($request->validated());

        return redirect()->route('assets.show', $asset)->with('success', 'Aset berhasil ditambahkan.');
    }

    public function edit(Asset $asset): View
    {
        return view('assets.edit', [
            'asset' => $asset,
            'categories' => Category::orderBy('nama')->get(),
            'locations' => Location::orderBy('nama')->get(),
            'statuses' => Asset::STATUSES,
        ]);
    }

    public function update(AssetRequest $request, Asset $asset): RedirectResponse
    {
        $asset->update($request->validated());

        return redirect()->route('assets.show', $asset)->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $asset->delete(); // soft delete: riwayat tiket tetap utuh

        return redirect()->route('assets.index')->with('success', 'Aset dihapus.');
    }

    /** Ekspor seluruh aset ke CSV (streamed, aman untuk data besar). */
    public function export(): StreamedResponse
    {
        $filename = 'aset-it-' . now()->format('Ymd-His') . '.csv';

        // Cegah CSV/formula injection saat file dibuka di Excel
        $safe = fn ($v) => is_string($v) && preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v;

        return response()->streamDownload(function () use ($safe) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Kode', 'Nama', 'Kategori', 'Lokasi', 'Merk', 'Serial', 'IP', 'Tanggal Beli', 'Status'], ',', '"', '\\');

            Asset::with(['category', 'location'])
                ->orderBy('kode_aset')
                ->chunk(200, function ($rows) use ($out, $safe) {
                    foreach ($rows as $a) {
                        fputcsv($out, [
                            $safe($a->kode_aset),
                            $safe($a->nama),
                            $a->category->nama,
                            $a->location->nama,
                            $safe($a->merk),
                            $safe($a->serial_number),
                            $a->ip_address,
                            $a->tanggal_beli?->format('Y-m-d'),
                            $a->status,
                        ], ',', '"', '\\');
                    }
                });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
