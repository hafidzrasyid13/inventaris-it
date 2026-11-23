{{-- Dipakai oleh create & edit. Variabel: $asset, $categories, $locations, $statuses --}}
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="kode_aset">Kode aset *</label>
        <input type="text" id="kode_aset" name="kode_aset" value="{{ old('kode_aset', $asset->kode_aset) }}"
               class="form-control @error('kode_aset') is-invalid @enderror" placeholder="PC-001" required>
        @error('kode_aset') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-8">
        <label class="form-label" for="nama">Nama aset *</label>
        <input type="text" id="nama" name="nama" value="{{ old('nama', $asset->nama) }}"
               class="form-control @error('nama') is-invalid @enderror" required>
        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="category_id">Kategori *</label>
        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">-- pilih --</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected((int) old('category_id', $asset->category_id) === $c->id)>{{ $c->nama }}</option>
            @endforeach
        </select>
        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="location_id">Lokasi *</label>
        <select id="location_id" name="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
            <option value="">-- pilih --</option>
            @foreach ($locations as $l)
                <option value="{{ $l->id }}" @selected((int) old('location_id', $asset->location_id) === $l->id)>{{ $l->nama }}</option>
            @endforeach
        </select>
        @error('location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="merk">Merk</label>
        <input type="text" id="merk" name="merk" value="{{ old('merk', $asset->merk) }}"
               class="form-control @error('merk') is-invalid @enderror">
        @error('merk') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="serial_number">Serial number</label>
        <input type="text" id="serial_number" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}"
               class="form-control @error('serial_number') is-invalid @enderror">
        @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="ip_address">IP address</label>
        <input type="text" id="ip_address" name="ip_address" value="{{ old('ip_address', $asset->ip_address) }}"
               class="form-control @error('ip_address') is-invalid @enderror" placeholder="192.168.1.10">
        @error('ip_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="tanggal_beli">Tanggal beli</label>
        <input type="date" id="tanggal_beli" name="tanggal_beli"
               value="{{ old('tanggal_beli', $asset->tanggal_beli?->format('Y-m-d')) }}"
               class="form-control @error('tanggal_beli') is-invalid @enderror">
        @error('tanggal_beli') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="status">Status *</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach ($statuses as $s)
                <option value="{{ $s }}" @selected(old('status', $asset->status) === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="catatan">Catatan</label>
        <textarea id="catatan" name="catatan" rows="3"
                  class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan', $asset->catatan) }}</textarea>
        @error('catatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
