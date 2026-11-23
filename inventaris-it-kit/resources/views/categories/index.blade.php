@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<h1 class="h3 mb-3">Kategori Aset</h1>

<div class="row g-4">
    <div class="col-md-4">
        <form method="POST" action="{{ route('categories.store') }}" class="card card-body">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="kode">Kode *</label>
                <input type="text" id="kode" name="kode" value="{{ old('kode') }}"
                       class="form-control @error('kode') is-invalid @enderror" placeholder="PC" required>
                @error('kode') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="nama">Nama *</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                       class="form-control @error('nama') is-invalid @enderror" required>
                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <button class="btn btn-primary">Tambah</button>
        </form>
    </div>

    <div class="col-md-8">
        <div class="card">
            <table class="table align-middle mb-0">
                <thead class="table-light"><tr><th>Kode</th><th>Nama</th><th>Jumlah aset</th><th></th></tr></thead>
                <tbody>
                @forelse ($categories as $c)
                    <tr>
                        <td>{{ $c->kode }}</td>
                        <td>{{ $c->nama }}</td>
                        <td>{{ $c->assets_count }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('categories.destroy', $c) }}"
                                  onsubmit="return confirm('Hapus kategori {{ $c->nama }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Belum ada kategori.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
