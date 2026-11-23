@extends('layouts.app')

@section('title', 'Tiket')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Tiket Perbaikan</h1>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary">+ Buat Tiket</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">Semua status</option>
            @foreach ($statuses as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr><th>Nomor</th><th>Aset</th><th>Judul</th><th>Prioritas</th><th>Status</th><th>Pelapor</th><th>Teknisi</th><th>Dibuat</th></tr>
            </thead>
            <tbody>
            @forelse ($tickets as $t)
                <tr>
                    <td><a href="{{ route('tickets.show', $t) }}">{{ $t->nomor_tiket }}</a></td>
                    <td>{{ $t->asset->kode_aset }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($t->judul, 40) }}</td>
                    <td><span class="badge text-bg-{{ $t->prioritasBadge() }}">{{ $t->prioritas }}</span></td>
                    <td><span class="badge text-bg-{{ $t->statusBadge() }}">{{ $t->status }}</span></td>
                    <td>{{ $t->pelapor->name }}</td>
                    <td>{{ $t->teknisi?->name ?? '-' }}</td>
                    <td class="text-nowrap">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada tiket.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $tickets->links('pagination::bootstrap-5') }}</div>
@endsection
