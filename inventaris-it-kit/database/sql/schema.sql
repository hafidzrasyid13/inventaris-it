-- ============================================================
-- Inventaris Aset IT & Tiket Perbaikan  -  skema MySQL 8 (referensi)
-- Sumber kebenaran tetap migration Laravel (database/migrations).
-- File ini untuk belajar/mempresentasikan desain database.
-- ============================================================

CREATE TABLE users (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name              VARCHAR(255) NOT NULL,
  email             VARCHAR(255) NOT NULL,
  email_verified_at TIMESTAMP NULL,
  password          VARCHAR(255) NOT NULL,
  role              ENUM('admin','teknisi','staff') NOT NULL DEFAULT 'staff',
  remember_token    VARCHAR(100) NULL,
  created_at        TIMESTAMP NULL,
  updated_at        TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  kode       VARCHAR(10)  NOT NULL,
  nama       VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY categories_kode_unique (kode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE locations (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama       VARCHAR(100) NOT NULL,
  lantai     VARCHAR(20)  NULL,
  keterangan VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY locations_nama_unique (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE assets (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  kode_aset     VARCHAR(30)  NOT NULL,
  nama          VARCHAR(150) NOT NULL,
  category_id   BIGINT UNSIGNED NOT NULL,
  location_id   BIGINT UNSIGNED NOT NULL,
  merk          VARCHAR(100) NULL,
  serial_number VARCHAR(100) NULL,
  ip_address    VARCHAR(45)  NULL,
  tanggal_beli  DATE NULL,
  status        ENUM('aktif','maintenance','rusak','nonaktif') NOT NULL DEFAULT 'aktif',
  catatan       TEXT NULL,
  created_at    TIMESTAMP NULL,
  updated_at    TIMESTAMP NULL,
  deleted_at    TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY assets_kode_aset_unique (kode_aset),
  UNIQUE KEY assets_serial_number_unique (serial_number),
  KEY assets_status_category_id_index (status, category_id),
  CONSTRAINT assets_category_id_foreign FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE RESTRICT,
  CONSTRAINT assets_location_id_foreign FOREIGN KEY (location_id) REFERENCES locations (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tickets (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nomor_tiket  VARCHAR(20) NOT NULL,
  asset_id     BIGINT UNSIGNED NOT NULL,
  pelapor_id   BIGINT UNSIGNED NOT NULL,
  teknisi_id   BIGINT UNSIGNED NULL,
  judul        VARCHAR(150) NOT NULL,
  deskripsi    TEXT NOT NULL,
  prioritas    ENUM('rendah','sedang','tinggi') NOT NULL DEFAULT 'sedang',
  status       ENUM('baru','diproses','selesai','ditutup') NOT NULL DEFAULT 'baru',
  selesai_pada TIMESTAMP NULL,
  created_at   TIMESTAMP NULL,
  updated_at   TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY tickets_nomor_tiket_unique (nomor_tiket),
  KEY tickets_status_prioritas_index (status, prioritas),
  CONSTRAINT tickets_asset_id_foreign   FOREIGN KEY (asset_id)   REFERENCES assets (id) ON DELETE RESTRICT,
  CONSTRAINT tickets_pelapor_id_foreign FOREIGN KEY (pelapor_id) REFERENCES users (id)  ON DELETE RESTRICT,
  CONSTRAINT tickets_teknisi_id_foreign FOREIGN KEY (teknisi_id) REFERENCES users (id)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ticket_logs (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  ticket_id  BIGINT UNSIGNED NOT NULL,
  user_id    BIGINT UNSIGNED NOT NULL,
  status     VARCHAR(20) NOT NULL,
  catatan    TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  CONSTRAINT ticket_logs_ticket_id_foreign FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE,
  CONSTRAINT ticket_logs_user_id_foreign   FOREIGN KEY (user_id)   REFERENCES users (id)   ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
