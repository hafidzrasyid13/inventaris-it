@extends('layouts.app')

@section('title', 'Buat Tiket')

@section('content')
<h1 class="h3 mb-3">Buat Tiket Perbaikan</h1>

<form method="POST" action="{{ route('tickets.store') }}" class="card card-body">
    @csrf
    <div class="mb-3">
        <label class="form-label" for="asset_id">Aset bermasalah *</label>
        <select id="asset_id" name="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
            <option value="">-- pilih aset --</option>
            @foreach ($assets as $a)
                <option value="{{ $a->id }}" @selected((int) old('asset_id', request('asset_id')) === $a->id)>
                    {{ $a->kode_aset }} · {{ $a->nama }} ({{ $a->location->nama }})
                </option>
            @endforeach
        </select>
        @error('asset_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label" for="judul">Judul masalah *</label>
        <input type="text" id="judul" name="judul" value="{{ old('judul') }}"
               class="form-control @error('judul') is-invalid @enderror" required>
        @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label" for="deskripsi">Deskripsi *</label>
        <textarea id="deskripsi" name="deskripsi" rows="4"
                  class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi') }}</textarea>
        @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label" for="prioritas">Prioritas *</label>
        <select id="prioritas" name="prioritas" class="form-select @error('prioritas') is-invalid @enderror" required>
            @foreach ($prioritas as $p)
                <option value="{{ $p }}" @selected(old('prioritas', 'sedang') === $p)>{{ ucfirst($p) }}</option>
            @endforeach
        </select>
        @error('prioritas') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary">Kirim tiket</button>
        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
@endsection
