@extends('layouts.app')

@section('title', 'Tambah Aset')

@section('content')
<h1 class="h3 mb-3">Tambah Aset</h1>

<form method="POST" action="{{ route('assets.store') }}" class="card card-body">
    @csrf
    @include('assets._form')
    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
@endsection
