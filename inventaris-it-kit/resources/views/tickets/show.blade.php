@extends('layouts.app')

@section('title', $ticket->nomor_tiket)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">
        {{ $ticket->nomor_tiket }}
        <span class="badge text-bg-{{ $ticket->statusBadge() }} fs-6 align-middle">{{ $ticket->status }}</span>
    </h1>
    <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">Kembali</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h5">{{ $ticket->judul }}</h2>
                <p class="mb-3" style="white-space: pre-line;">{{ $ticket->deskripsi }}</p>
                <dl class="row mb-0 small">
                    <dt class="col-sm-4">Aset</dt>
                    <dd class="col-sm-8">
                        <a href="{{ route('assets.show', $ticket->asset_id) }}">{{ $ticket->asset->kode_aset }}</a>
                        · {{ $ticket->asset->nama }} ({{ $ticket->asset->location->nama }})
                    </dd>
                    <dt class="col-sm-4">Prioritas</dt>
                    <dd class="col-sm-8"><span class="badge text-bg-{{ $ticket->prioritasBadge() }}">{{ $ticket->prioritas }}</span></dd>
                    <dt class="col-sm-4">Pelapor</dt><dd class="col-sm-8">{{ $ticket->pelapor->name }}</dd>
                    <dt class="col-sm-4">Teknisi</dt><dd class="col-sm-8">{{ $ticket->teknisi?->name ?? 'Belum ditugaskan' }}</dd>
                    <dt class="col-sm-4">Dibuat</dt><dd class="col-sm-8">{{ $ticket->created_at->format('d/m/Y H:i') }}</dd>
                    <dt class="col-sm-4">Selesai</dt><dd class="col-sm-8">{{ $ticket->selesai_pada?->format('d/m/Y H:i') ?? '-' }}</dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Riwayat</div>
            <ul class="list-group list-group-flush">
                @foreach ($ticket->logs as $log)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span><span class="badge text-bg-secondary">{{ $log->status }}</span> oleh {{ $log->user->name }}</span>
                            <span class="small text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($log->catatan)
                            <div class="small mt-1">{{ $log->catatan }}</div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="col-lg-5">
        @if ($bisaUpdate)
            <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="card card-body">
                @csrf
                @method('PUT')
                <h2 class="h6 mb-3">Perbarui tiket</h2>

                <div class="mb-3">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach (array_merge([$ticket->status], \App\Models\Ticket::TRANSITIONS[$ticket->status]) as $s)
                            <option value="{{ $s }}" @selected(old('status', $ticket->status) === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                @if (auth()->user()->role === 'admin')
                    <div class="mb-3">
                        <label class="form-label" for="teknisi_id">Teknisi</label>
                        <select id="teknisi_id" name="teknisi_id" class="form-select @error('teknisi_id') is-invalid @enderror">
                            <option value="">-- belum ditugaskan --</option>
                            @foreach ($teknisiList as $tk)
                                <option value="{{ $tk->id }}" @selected((int) old('teknisi_id', $ticket->teknisi_id) === $tk->id)>{{ $tk->name }}</option>
                            @endforeach
                        </select>
                        @error('teknisi_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @else
                    @error('teknisi_id') <div class="alert alert-danger py-2">{{ $message }}</div> @enderror
                @endif

                <div class="mb-3">
                    <label class="form-label" for="catatan">Catatan</label>
                    <textarea id="catatan" name="catatan" rows="3"
                              class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan') }}</textarea>
                    @error('catatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button class="btn btn-primary">Simpan</button>
            </form>
        @elseif ($ticket->status === 'ditutup')
            <div class="alert alert-secondary mb-0">Tiket ini sudah ditutup.</div>
        @endif
    </div>
</div>
@endsection
