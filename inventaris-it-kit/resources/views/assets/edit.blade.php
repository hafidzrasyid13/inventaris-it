@extends('layouts.app')

@section('title', 'Ubah Aset')

@section('content')
<h1 class="h3 mb-3">Ubah Aset {{ $asset->kode_aset }}</h1>

<form method="POST" action="{{ route('assets.update', $asset) }}" class="card card-body">
    @csrf
    @method('PUT')
    @include('assets._form')
    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary">Simpan perubahan</button>
        <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
@endsection
