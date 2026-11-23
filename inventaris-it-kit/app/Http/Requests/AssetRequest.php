<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pembatasan role sudah ditangani middleware di routes/web.php
        return true;
    }

    public function rules(): array
    {
        $asset = $this->route('asset'); // null saat create, model Asset saat update

        return [
            'kode_aset' => ['required', 'string', 'max:30', Rule::unique('assets', 'kode_aset')->ignore($asset)],
            'nama' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'merk' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'max:100', Rule::unique('assets', 'serial_number')->ignore($asset)],
            'ip_address' => ['nullable', 'ip'],
            'tanggal_beli' => ['nullable', 'date', 'before_or_equal:today'],
            'status' => ['required', Rule::in(Asset::STATUSES)],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'kode_aset' => 'kode aset',
            'category_id' => 'kategori',
            'location_id' => 'lokasi',
            'serial_number' => 'serial number',
            'ip_address' => 'IP address',
            'tanggal_beli' => 'tanggal beli',
        ];
    }
}
