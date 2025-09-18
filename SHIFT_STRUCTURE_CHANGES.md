# Perubahan Struktur Shift Database

## 📋 Ringkasan Perubahan

Struktur tabel `shifts` telah diubah untuk memisahkan **nama shift** dan **kategori waktu** agar lebih fleksibel dan tidak membingungkan admin.

## 🔄 Perubahan Database

### Struktur Lama:
```sql
CREATE TABLE shifts (
    id BIGINT PRIMARY KEY,
    name ENUM('Pagi', 'Siang', 'Malam'),
    start_time TIME,
    end_time TIME,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Struktur Baru:
```sql
CREATE TABLE shifts (
    id BIGINT PRIMARY KEY,
    shift_name VARCHAR(255),           -- Nama shift spesifik
    category ENUM('Pagi', 'Siang', 'Malam'), -- Kategori waktu
    start_time TIME,
    end_time TIME,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 📁 File yang Diubah

### 1. Database & Models
- ✅ `database/migrations/2025_08_06_013937_create_shifts_table.php`
- ✅ `app/Models/Shift.php`
- ✅ `database/seeders/ShiftSeeder.php`

### 2. Controllers
- ✅ `app/Http/Controllers/Admin/ShiftController.php`
- ✅ `app/Http/Controllers/Admin/ScheduleController.php`
- ✅ `app/Http/Controllers/Users/DashboardController.php`
- ✅ `app/Http/Controllers/Users/CalendarController.php`
- ✅ `app/Exports/ScheduleReportExport.php`

### 3. Views - Shifts
- ✅ `resources/views/admin/shifts/create.blade.php`
- ✅ `resources/views/admin/shifts/edit.blade.php`
- ✅ `resources/views/admin/shifts/index.blade.php`

### 4. Views - Schedules
- ✅ `resources/views/admin/schedules/create.blade.php`
- ✅ `resources/views/admin/schedules/edit.blade.php`
- ✅ `resources/views/admin/schedules/users_schedules.blade.php`
- ✅ `resources/views/admin/schedules/history.blade.php`

### 5. Views - Attendances
- ✅ `resources/views/admin/attendances/index.blade.php`
- ✅ `resources/views/admin/attendances/history.blade.php`
- ✅ `resources/views/users/attendances/index.blade.php`
- ✅ `resources/views/users/attendances/history.blade.php`

## 🎯 Contoh Data Baru

```php
// Contoh data shift dengan struktur baru
[
    'shift_name' => 'Shift A',
    'category' => 'Pagi',
    'start_time' => '08:00:00',
    'end_time' => '16:00:00'
],
[
    'shift_name' => 'Shift Security',
    'category' => 'Malam',
    'start_time' => '22:00:00',
    'end_time' => '06:00:00'
],
[
    'shift_name' => 'Shift Cleaning',
    'category' => 'Pagi',
    'start_time' => '06:00:00',
    'end_time' => '14:00:00'
]
```

## 🚀 Cara Menjalankan Perubahan

1. **Backup Database** (PENTING!)
   ```bash
   mysqldump -u username -p database_name > backup_before_shift_changes.sql
   ```

2. **Jalankan Migration**
   ```bash
   php artisan migrate:fresh --seed
   ```
   
   Atau jika ingin mempertahankan data lain:
   ```bash
   php artisan migrate
   ```

3. **Verifikasi Perubahan**
   - Cek tabel shifts memiliki kolom `shift_name` dan `category`
   - Test create, edit, delete shift
   - Test create schedule dengan shift baru
   - Test export report

## 💡 Keuntungan Struktur Baru

1. **Fleksibilitas**: Bisa membuat multiple shift dengan kategori sama
   - "Shift A Pagi", "Shift Security Pagi", "Shift Cleaning Pagi"

2. **Clarity**: Pemisahan jelas antara nama dan kategori

3. **Scalability**: Mudah menambah shift baru tanpa konflik

4. **User Experience**: Admin tidak bingung saat buat shift serupa

## ⚠️ Catatan Penting

- Semua referensi `$shift->name` telah diubah menjadi `$shift->shift_name`
- Filter berdasarkan kategori menggunakan `$shift->category`
- Seeder sudah diupdate dengan data contoh baru
- Migration otomatis akan mengkonversi data lama ke format baru

## 🧪 Testing Checklist

- [ ] Create shift baru dengan nama dan kategori
- [ ] Edit shift yang sudah ada  
- [ ] Delete shift
- [ ] Create schedule dengan shift baru
- [ ] Filter schedule berdasarkan kategori shift
- [ ] Export report schedule
- [ ] View calendar dengan shift baru
- [ ] Swap schedule antar user

## 🔧 Troubleshooting

Jika ada error "Column 'name' not found":
1. Pastikan migration sudah dijalankan
2. Clear cache: `php artisan cache:clear`
3. Clear view cache: `php artisan view:clear`
4. Restart web server

## 📞 Support

Jika ada masalah dengan perubahan ini, silakan hubungi tim development.
