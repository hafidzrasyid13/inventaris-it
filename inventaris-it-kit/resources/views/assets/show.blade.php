@extends('layouts.app')

@section('title', $asset->kode_aset)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">
        {{ $asset->kode_aset }}
        <span class="badge text-bg-{{ $asset->badgeClass() }} fs-6 align-middle">{{ $asset->status }}</span>
    </h1>
    <div class="d-flex gap-2">
        <a href="{{ route('tickets.create', ['asset_id' => $asset->id]) }}" class="btn btn-warning">Lapor kerusakan</a>
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline-primary">Ubah</a>
        @endif
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Nama</dt><dd class="col-sm-9">{{ $asset->nama }}</dd>
            <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9">{{ $asset->category->nama }}</dd>
            <dt class="col-sm-3">Lokasi</dt><dd class="col-sm-9">{{ $asset->location->nama }}</dd>
            <dt class="col-sm-3">Merk</dt><dd class="col-sm-9">{{ $asset->merk ?? '-' }}</dd>
            <dt class="col-sm-3">Serial number</dt><dd class="col-sm-9">{{ $asset->serial_number ?? '-' }}</dd>
            <dt class="col-sm-3">IP address</dt><dd class="col-sm-9">{{ $asset->ip_address ?? '-' }}</dd>
            <dt class="col-sm-3">Tanggal beli</dt><dd class="col-sm-9">{{ $asset->tanggal_beli?->translatedFormat('d F Y') ?? '-' }}</dd>
            <dt class="col-sm-3">Catatan</dt><dd class="col-sm-9">{{ $asset->catatan ?? '-' }}</dd>
        </dl>
    </div>
</div>

<div class="card">
    <div class="card-header">Riwayat tiket perbaikan</div>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Nomor</th><th>Judul</th><th>Prioritas</th><th>Status</th><th>Teknisi</th></tr></thead>
            <tbody>
            @forelse ($asset->tickets as $t)
                <tr>
                    <td>
                        @if (auth()->user()->role !== 'staff' || $t->pelapor_id === auth()->id())
                            <a href="{{ route('tickets.show', $t) }}">{{ $t->nomor_tiket }}</a>
                        @else
                            {{ $t->nomor_tiket }}
                        @endif
                    </td>
                    <td>{{ $t->judul }}</td>
                    <td><span class="badge text-bg-{{ $t->prioritasBadge() }}">{{ $t->prioritas }}</span></td>
                    <td><span class="badge text-bg-{{ $t->statusBadge() }}">{{ $t->status }}</span></td>
                    <td>{{ $t->teknisi?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Belum pernah ada tiket.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
