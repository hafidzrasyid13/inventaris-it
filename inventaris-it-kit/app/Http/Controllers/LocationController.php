<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        return view('locations.index', [
            'locations' => Location::withCount('assets')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:locations,nama'],
            'lantai' => ['nullable', 'string', 'max:20'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        Location::create($data);

        return back()->with('success', 'Lokasi ditambahkan.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->assets()->withTrashed()->exists()) {
            return back()->with('error', 'Lokasi masih dipakai oleh aset, tidak bisa dihapus.');
        }

        $location->delete();

        return back()->with('success', 'Lokasi dihapus.');
    }
}
