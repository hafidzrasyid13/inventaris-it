@extends('layouts.app')

@section('title', 'Lokasi')

@section('content')
<h1 class="h3 mb-3">Lokasi</h1>

<div class="row g-4">
    <div class="col-md-4">
        <form method="POST" action="{{ route('locations.store') }}" class="card card-body">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="nama">Nama *</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                       class="form-control @error('nama') is-invalid @enderror" required>
                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="lantai">Lantai</label>
                <input type="text" id="lantai" name="lantai" value="{{ old('lantai') }}"
                       class="form-control @error('lantai') is-invalid @enderror">
                @error('lantai') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="keterangan">Keterangan</label>
                <input type="text" id="keterangan" name="keterangan" value="{{ old('keterangan') }}"
                       class="form-control @error('keterangan') is-invalid @enderror">
                @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <button class="btn btn-primary">Tambah</button>
        </form>
    </div>

    <div class="col-md-8">
        <div class="card">
            <table class="table align-middle mb-0">
                <thead class="table-light"><tr><th>Nama</th><th>Lantai</th><th>Jumlah aset</th><th></th></tr></thead>
                <tbody>
                @forelse ($locations as $l)
                    <tr>
                        <td>{{ $l->nama }}</td>
                        <td>{{ $l->lantai ?? '-' }}</td>
                        <td>{{ $l->assets_count }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('locations.destroy', $l) }}"
                                  onsubmit="return confirm('Hapus lokasi {{ $l->nama }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Belum ada lokasi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
