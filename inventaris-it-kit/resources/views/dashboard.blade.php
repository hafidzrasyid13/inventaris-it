@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1 class="h3 mb-4">Dashboard</h1>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center h-100"><div class="card-body">
            <div class="fs-2 fw-bold">{{ $totalAset }}</div>
            <div class="text-muted">Total aset</div>
        </div></div>
    </div>
    @foreach (['aktif' => 'success', 'maintenance' => 'warning', 'rusak' => 'danger'] as $st => $warna)
        <div class="col-6 col-md-3">
            <div class="card text-center h-100 border-{{ $warna }}"><div class="card-body">
                <div class="fs-2 fw-bold text-{{ $warna }}">{{ $asetPerStatus[$st] ?? 0 }}</div>
                <div class="text-muted text-capitalize">{{ $st }}</div>
            </div></div>
        </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">Aset per kategori</div>
            <div class="card-body">
                @forelse ($asetPerKategori as $row)
                    @php($persen = $totalAset > 0 ? round($row->total / $totalAset * 100) : 0)
                    <div class="d-flex justify-content-between small">
                        <span>{{ $row->nama }}</span><span>{{ $row->total }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar" style="width: {{ $persen }}%"></div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">Tiket per status</div>
            <ul class="list-group list-group-flush">
                @foreach (\App\Models\Ticket::STATUSES as $st)
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-capitalize">{{ $st }}</span>
                        <span class="badge text-bg-secondary">{{ $tiketPerStatus[$st] ?? 0 }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Tiket terbaru</span>
                <a href="{{ route('tickets.index') }}" class="small">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead><tr><th>Nomor</th><th>Aset</th><th>Judul</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse ($tiketTerbaru as $t)
                        <tr>
                            <td><a href="{{ route('tickets.show', $t) }}">{{ $t->nomor_tiket }}</a></td>
                            <td>{{ $t->asset->kode_aset }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($t->judul, 35) }}</td>
                            <td><span class="badge text-bg-{{ $t->statusBadge() }}">{{ $t->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Belum ada tiket.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">Aset dengan tiket terbuka terbanyak</div>
            <ul class="list-group list-group-flush">
                @forelse ($asetBermasalah as $a)
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('assets.show', $a->id) }}">{{ $a->kode_aset }} · {{ $a->nama }}</a>
                        <span class="badge text-bg-danger">{{ $a->jumlah }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Tidak ada tiket terbuka.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
