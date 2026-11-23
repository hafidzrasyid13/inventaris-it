@extends('layouts.app')

@section('title', 'Aset')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Daftar Aset</h1>
    @if (auth()->user()->role === 'admin')
        <div class="d-flex gap-2">
            <a href="{{ route('assets.export') }}" class="btn btn-outline-secondary">Ekspor CSV</a>
            <a href="{{ route('assets.create') }}" class="btn btn-primary">+ Tambah Aset</a>
        </div>
    @endif
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control"
               placeholder="Cari kode, nama, serial number, atau IP">
    </div>
    <div class="col-md-3">
        <select name="category_id" class="form-select">
            <option value="">Semua kategori</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected((int) request('category_id') === $c->id)>{{ $c->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">Semua status</option>
            @foreach ($statuses as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button class="btn btn-dark">Filter</button>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr><th>Kode</th><th>Nama</th><th>Kategori</th><th>Lokasi</th><th>IP</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
            @forelse ($assets as $a)
                <tr>
                    <td><a href="{{ route('assets.show', $a) }}">{{ $a->kode_aset }}</a></td>
                    <td>{{ $a->nama }}</td>
                    <td>{{ $a->category->nama }}</td>
                    <td>{{ $a->location->nama }}</td>
                    <td>{{ $a->ip_address ?? '-' }}</td>
                    <td><span class="badge text-bg-{{ $a->badgeClass() }}">{{ $a->status }}</span></td>
                    <td class="text-end">
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('assets.edit', $a) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                            <form method="POST" action="{{ route('assets.destroy', $a) }}" class="d-inline"
                                  onsubmit="return confirm('Hapus aset {{ $a->kode_aset }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada aset yang cocok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $assets->links('pagination::bootstrap-5') }}</div>
@endsection
