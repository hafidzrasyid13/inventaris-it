-- ============================================================
-- Latihan query SQL untuk proyek Inventaris IT
-- Memakai SQL standar (JOIN, GROUP BY, HAVING, subquery, CASE, window function)
-- sehingga bisa dijalankan di MySQL 8 maupun SQL Server.
-- Catatan SQL Server: ganti "LIMIT n" dengan "SELECT TOP (n)" atau "FETCH NEXT n ROWS ONLY".
-- ============================================================

-- 1) JOIN: daftar aset beserta kategori dan lokasinya
SELECT a.kode_aset, a.nama, c.nama AS kategori, l.nama AS lokasi, a.status
FROM assets a
JOIN categories c ON c.id = a.category_id
JOIN locations  l ON l.id = a.location_id
WHERE a.deleted_at IS NULL
ORDER BY a.kode_aset;

-- 2) LEFT JOIN + GROUP BY: jumlah aset per kategori (kategori kosong tetap muncul dengan 0)
SELECT c.nama AS kategori, COUNT(a.id) AS jumlah_aset
FROM categories c
LEFT JOIN assets a ON a.category_id = c.id AND a.deleted_at IS NULL
GROUP BY c.id, c.nama
ORDER BY jumlah_aset DESC, c.nama;

-- 3) EXISTS (subquery berkorelasi): aset yang punya tiket terbuka
SELECT a.kode_aset, a.nama, a.status
FROM assets a
WHERE a.deleted_at IS NULL
  AND EXISTS (
      SELECT 1 FROM tickets t
      WHERE t.asset_id = a.id AND t.status IN ('baru', 'diproses')
  )
ORDER BY a.kode_aset;

-- 4) GROUP BY + HAVING: jumlah tiket per aset (hanya aset yang pernah punya tiket)
SELECT a.kode_aset, a.nama, COUNT(t.id) AS jumlah_tiket
FROM assets a
JOIN tickets t ON t.asset_id = a.id
GROUP BY a.id, a.kode_aset, a.nama
HAVING COUNT(t.id) >= 1
ORDER BY jumlah_tiket DESC, a.kode_aset;

-- 5) CASE WHEN: beban kerja tiap teknisi
SELECT u.name AS teknisi,
       SUM(CASE WHEN t.status = 'diproses' THEN 1 ELSE 0 END)              AS sedang_dikerjakan,
       SUM(CASE WHEN t.status IN ('selesai', 'ditutup') THEN 1 ELSE 0 END) AS sudah_selesai
FROM users u
LEFT JOIN tickets t ON t.teknisi_id = u.id
WHERE u.role = 'teknisi'
GROUP BY u.id, u.name
ORDER BY u.name;

-- 6) NOT EXISTS: aset yang belum pernah punya tiket sama sekali
SELECT a.kode_aset, a.nama
FROM assets a
WHERE a.deleted_at IS NULL
  AND NOT EXISTS (SELECT 1 FROM tickets t WHERE t.asset_id = a.id)
ORDER BY a.kode_aset;

-- 7) Window function: peringkat aset per kategori berdasarkan jumlah tiket
SELECT kategori, kode_aset, jumlah_tiket,
       RANK() OVER (PARTITION BY kategori ORDER BY jumlah_tiket DESC) AS peringkat
FROM (
    SELECT c.nama AS kategori, a.kode_aset, COUNT(t.id) AS jumlah_tiket
    FROM assets a
    JOIN categories c ON c.id = a.category_id
    LEFT JOIN tickets t ON t.asset_id = a.id
    WHERE a.deleted_at IS NULL
    GROUP BY c.nama, a.kode_aset
) x
ORDER BY kategori, peringkat, kode_aset;

-- 8) VIEW: dipakai ulang untuk laporan "aset bermasalah"
CREATE VIEW v_aset_bermasalah AS
SELECT a.id AS asset_id, a.kode_aset, a.nama, COUNT(t.id) AS tiket_terbuka
FROM assets a
JOIN tickets t ON t.asset_id = a.id AND t.status IN ('baru', 'diproses')
WHERE a.deleted_at IS NULL
GROUP BY a.id, a.kode_aset, a.nama;

SELECT * FROM v_aset_bermasalah ORDER BY tiket_terbuka DESC, kode_aset;
