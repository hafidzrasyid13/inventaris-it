<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $users = $this->seedUsers();
            $categories = $this->seedCategories();
            $locations = $this->seedLocations();
            $assets = $this->seedAssets($categories, $locations);
            $this->seedTickets($assets, $users);
        });
    }

    /** @return array<string, User> */
    private function seedUsers(): array
    {
        $rows = [
            'admin' => ['Admin IT', 'admin@demo.test', 'admin'],
            'budi' => ['Budi Santoso', 'budi@demo.test', 'teknisi'],
            'sari' => ['Sari Lestari', 'sari@demo.test', 'teknisi'],
            'rina' => ['Rina Marlina', 'rina@demo.test', 'staff'],
            'andi' => ['Andi Pratama', 'andi@demo.test', 'staff'],
        ];

        $users = [];
        foreach ($rows as $key => [$name, $email, $role]) {
            $users[$key] = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => $role,
            ]);
        }

        return $users;
    }

    /** @return array<string, Category> */
    private function seedCategories(): array
    {
        $rows = [
            'PC' => 'Komputer & Laptop',
            'NET' => 'Perangkat Jaringan',
            'CCTV' => 'CCTV & Kamera',
            'PRN' => 'Printer',
            'SRV' => 'Server',
            'UPS' => 'UPS & Kelistrikan',
        ];

        $categories = [];
        foreach ($rows as $kode => $nama) {
            $categories[$kode] = Category::create(['kode' => $kode, 'nama' => $nama]);
        }

        return $categories;
    }

    /** @return array<string, Location> */
    private function seedLocations(): array
    {
        $rows = [
            ['Front Office', 'Lt. 1', 'Resepsionis dan check-in'],
            ['Server Room', 'Lt. 1', 'Ruang server dan rak jaringan'],
            ['Lobby', 'Lt. 1', null],
            ['Ruang Admin', 'Lt. 2', 'Administrasi dan keuangan'],
            ['Area Parkir', 'Lt. Dasar', null],
            ['Ruang Produksi', 'Lt. 1', 'Gedung B'],
        ];

        $locations = [];
        foreach ($rows as [$nama, $lantai, $ket]) {
            $locations[$nama] = Location::create(['nama' => $nama, 'lantai' => $lantai, 'keterangan' => $ket]);
        }

        return $locations;
    }

    /** @return array<string, Asset> */
    private function seedAssets(array $categories, array $locations): array
    {
        // [kode, nama, kategori, lokasi, merk, serial, ip, tgl beli, status]
        $rows = [
            ['PC-001', 'PC Front Office 1', 'PC', 'Front Office', 'Dell', 'DL-1001', '192.168.1.11', '2024-02-10', 'aktif'],
            ['PC-002', 'PC Front Office 2', 'PC', 'Front Office', 'Dell', 'DL-1002', '192.168.1.12', '2024-02-10', 'rusak'],
            ['PC-003', 'Laptop Admin', 'PC', 'Ruang Admin', 'Lenovo', 'LN-2001', '192.168.1.21', '2023-08-15', 'aktif'],
            ['PC-004', 'PC Produksi 1', 'PC', 'Ruang Produksi', 'HP', 'HP-3001', '192.168.2.11', '2022-11-01', 'aktif'],
            ['NET-001', 'Router Utama', 'NET', 'Server Room', 'MikroTik', 'MT-4001', '192.168.1.1', '2023-01-20', 'aktif'],
            ['NET-002', 'Switch Lantai 1', 'NET', 'Server Room', 'TP-Link', 'TP-4002', '192.168.1.2', '2023-01-20', 'aktif'],
            ['NET-003', 'Access Point Lobby', 'NET', 'Lobby', 'Ubiquiti', 'UB-4003', '192.168.1.31', '2024-05-05', 'maintenance'],
            ['CCTV-001', 'Kamera Lobby', 'CCTV', 'Lobby', 'Hikvision', 'HK-5001', '192.168.1.101', '2023-06-12', 'rusak'],
            ['CCTV-002', 'Kamera Parkir', 'CCTV', 'Area Parkir', 'Hikvision', 'HK-5002', '192.168.1.102', '2023-06-12', 'aktif'],
            ['CCTV-003', 'NVR 16 Channel', 'CCTV', 'Server Room', 'Hikvision', 'HK-5003', '192.168.1.100', '2023-06-12', 'aktif'],
            ['PRN-001', 'Printer Front Office', 'PRN', 'Front Office', 'Epson', 'EP-6001', '192.168.1.51', '2022-03-03', 'aktif'],
            ['PRN-002', 'Printer Admin', 'PRN', 'Ruang Admin', 'Canon', 'CN-6002', null, '2021-09-09', 'nonaktif'],
            ['SRV-001', 'Server Aplikasi', 'SRV', 'Server Room', 'Dell', 'DL-7001', '192.168.1.5', '2024-01-15', 'aktif'],
            ['UPS-001', 'UPS Server Room', 'UPS', 'Server Room', 'APC', 'AP-8001', null, '2023-04-04', 'aktif'],
        ];

        $assets = [];
        foreach ($rows as [$kode, $nama, $kat, $lok, $merk, $serial, $ip, $tgl, $status]) {
            $assets[$kode] = Asset::create([
                'kode_aset' => $kode,
                'nama' => $nama,
                'category_id' => $categories[$kat]->id,
                'location_id' => $locations[$lok]->id,
                'merk' => $merk,
                'serial_number' => $serial,
                'ip_address' => $ip,
                'tanggal_beli' => $tgl,
                'status' => $status,
            ]);
        }

        return $assets;
    }

    /** Status aset di atas sudah dibuat konsisten dengan tiket terbuka di bawah. */
    private function seedTickets(array $assets, array $users): void
    {
        $rows = [
            // [aset, pelapor, teknisi, judul, deskripsi, prioritas, status]
            ['PC-002', 'rina', null, 'PC tidak bisa menyala', 'Komputer resepsionis mati total saat dinyalakan, lampu indikator tidak hidup.', 'tinggi', 'baru'],
            ['CCTV-001', 'rina', null, 'Kamera lobby tidak ada gambar', 'Layar monitor CCTV untuk kamera lobby hitam sejak pagi.', 'tinggi', 'baru'],
            ['NET-003', 'andi', 'budi', 'Wi-Fi lobby putus-putus', 'Tamu mengeluh koneksi Wi-Fi di lobby sering terputus.', 'sedang', 'diproses'],
            ['PRN-001', 'rina', 'sari', 'Printer macet kertas', 'Kertas sering macet di roller.', 'rendah', 'selesai'],
            ['PC-004', 'andi', 'budi', 'Aplikasi produksi lambat', 'Komputer produksi lambat setelah update. Sudah dibersihkan dan ditambah RAM.', 'sedang', 'ditutup'],
        ];

        $prefix = 'TKT-' . now()->format('Ym') . '-';

        foreach ($rows as $i => [$kodeAset, $pelapor, $teknisi, $judul, $deskripsi, $prioritas, $status]) {
            $ticket = Ticket::create([
                'nomor_tiket' => $prefix . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'asset_id' => $assets[$kodeAset]->id,
                'pelapor_id' => $users[$pelapor]->id,
                'teknisi_id' => $teknisi ? $users[$teknisi]->id : null,
                'judul' => $judul,
                'deskripsi' => $deskripsi,
                'prioritas' => $prioritas,
                'status' => $status,
                'selesai_pada' => in_array($status, ['selesai', 'ditutup'], true) ? now()->subDay() : null,
            ]);

            $ticket->logs()->create([
                'user_id' => $users[$pelapor]->id,
                'status' => 'baru',
                'catatan' => 'Tiket dibuat.',
            ]);

            if ($teknisi) {
                $ticket->logs()->create([
                    'user_id' => $users[$teknisi]->id,
                    'status' => $status === 'baru' ? 'diproses' : $status,
                    'catatan' => 'Ditangani oleh teknisi.',
                ]);
            }
        }
    }
}
