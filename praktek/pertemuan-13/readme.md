# Praktek Pertemuan 13: Mail & Notification di Laravel

## Soal 1: Konfigurasi Mail dengan Mailtrap

Setup email configuration untuk testing:

- Daftar akun di [Mailtrap.io](https://mailtrap.io) (gratis)
- Konfigurasi `.env` dengan kredensial Mailtrap
- Buat route `/test-mail` untuk testing koneksi
- Kirim email sederhana dengan subject "Test Email" dan body "Hello from Laravel!"
- Verify email berhasil diterima di Mailtrap inbox

**Hint:** Gunakan `Mail::raw()` untuk mengirim email sederhana tanpa template

**Config `.env` yang diperlukan:**

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

---

## Soal 2: Membuat Welcome Email

Buat fitur welcome email saat user register:

- Buat Mailable class `WelcomeEmail` menggunakan artisan command
- Buat Blade template `emails/welcome.blade.php` dengan design yang menarik
- Email harus menampilkan nama user dan link ke dashboard
- Kirim email otomatis setelah user berhasil register
- Template harus memiliki styling CSS inline

**Hint:** Gunakan `php artisan make:mail WelcomeEmail`

**Komponen yang harus ada di email:**

- Header dengan judul "Welcome!"
- Greeting dengan nama user
- Paragraf penjelasan singkat
- Button/link "Go to Dashboard"
- Footer dengan informasi contact

---

## Soal 3: Email dengan Attachment

Implementasikan pengiriman email dengan file attachment:

- Buat Mailable class `InvoiceEmail`
- Email harus bisa mengirim file PDF invoice sebagai attachment
- Tambahkan method `attach()` untuk lampiran file
- Buat route untuk trigger email dengan invoice
- Test dengan file PDF dummy

**Hint:** Gunakan method `attach()` di Mailable class

**Example:**

```php
public function build()
{
    return $this->view('emails.invoice')
                ->attach(storage_path('app/invoices/invoice.pdf'));
}
```

---

## Soal 4: Email Template dengan Data Dinamis

Buat email order confirmation dengan data dinamis:

- Buat Mailable class `OrderConfirmation`
- Pass data order (order number, items, total, customer name) ke template
- Template harus menampilkan daftar items dalam table
- Tambahkan informasi total pembayaran
- Format currency dengan baik (Rp. 100.000)

**Hint:** Pass data menggunakan public property atau method `with()`

**Data yang harus ditampilkan:**

- Order number
- Customer name
- Order date
- List of items (nama produk, qty, harga)
- Subtotal
- Total

---

## Soal 5: Multiple Recipients Email

Implementasikan pengiriman email ke multiple recipients:

- Buat fitur broadcast email untuk announcement
- Email harus terkirim ke semua user yang terdaftar
- Tambahkan CC ke admin email
- Tambahkan BCC untuk monitoring email
- Tampilkan jumlah email yang berhasil dikirim

**Hint:** Gunakan `Mail::to()` dengan array atau loop untuk setiap user

**Feature yang harus ada:**

- Form untuk input subject dan message
- Checkbox untuk select recipients (All users, Active users only, etc)
- Preview email sebelum kirim
- Confirmation sebelum send

---

## Soal 6: Membuat Notification Class

Buat notification system untuk update order:

- Buat Notification class `OrderShipped` menggunakan artisan
- Notification harus support 2 channel: `mail` dan `database`
- Untuk channel mail: kirim email dengan tracking number
- Untuk channel database: simpan notifikasi ke table notifications
- User bisa menerima notifikasi via kedua channel

**Hint:** Gunakan `php artisan make:notification OrderShipped`

**Method yang harus diimplementasikan:**

- `via()` - return array channels
- `toMail()` - format untuk email
- `toDatabase()` - data untuk disimpan di database

---

## Soal 7: Database Notification

Implementasikan notification yang tersimpan di database:

- Buat migration untuk table `notifications` (jika belum ada)
- Kirim notification ke user saat ada event tertentu (contoh: new comment)
- Tampilkan daftar notifikasi di halaman user
- Tambahkan badge counter untuk unread notifications
- Buat fitur mark as read

**Hint:** Gunakan `auth()->user()->notify()` untuk kirim notification

**Feature yang harus dibuat:**

- Bell icon dengan counter notifikasi unread
- Dropdown list notifications
- Mark as read saat diklik
- Mark all as read button
- Timestamp "2 hours ago" style

---

## Soal 8: Email Queue

Optimasi pengiriman email menggunakan Queue:

- Setup queue driver (database atau redis)
- Buat migration untuk table `jobs`
- Convert email sending ke queue job
- Email harus dikirim secara asynchronous
- Monitor queue dengan command `queue:work`

**Hint:** Gunakan `Mail::queue()` atau implement `ShouldQueue` di Mailable

**Steps:**

1. Configure `.env` untuk queue (QUEUE_CONNECTION=database)
2. Run `php artisan queue:table` dan migrate
3. Implement `ShouldQueue` di Mailable class
4. Run `php artisan queue:work` untuk process queue

---

## Soal 9: Email Verification

Implementasikan fitur email verification:

- User harus verify email sebelum bisa login
- Kirim email verification link setelah register
- Buat route untuk verify email
- Update status `email_verified_at` di database
- Redirect ke dashboard setelah verifikasi berhasil

**Hint:** Laravel sudah menyediakan trait `MustVerifyEmail` dan middleware `verified`

**Implementation:**

- Add `MustVerifyEmail` interface ke User model
- Use middleware `verified` di routes yang butuh verification
- Customize email template jika perlu
- Handle expired verification link

---

## Soal 10: Notification Preferences

Buat sistem notification preferences untuk user:

- User bisa memilih channel notifikasi yang diinginkan (email, database, atau keduanya)
- Simpan preference di table `user_preferences` atau di table users
- Implement logic di Notification class untuk respect user preference
- Buat halaman settings untuk user mengatur preferences
- Tampilkan preview notifikasi untuk setiap channel

**Bonus:**

- Tambahkan notification categories (order, comment, system, promotion)
- User bisa mengatur preference per category
- Toggle switch untuk enable/disable setiap channel

**Database structure suggestion:**

```php
// Table: notification_preferences
- user_id
- notification_type (order_shipped, new_comment, etc)
- channel_email (boolean)
- channel_database (boolean)
- channel_sms (boolean)
```

**Feature yang harus dibuat:**

- Settings page dengan toggle switches
- Update preferences via AJAX
- Apply preferences saat kirim notification
- Default preferences untuk new users

---

## Checklist Praktek

Pastikan semua soal sudah mencakup:

- [ ] Konfigurasi mail di `.env`
- [ ] Membuat Mailable class
- [ ] Membuat Blade template untuk email
- [ ] Mengirim email dengan `Mail::send()`
- [ ] Passing data ke email template
- [ ] Email dengan attachment
- [ ] Multiple recipients (to, cc, bcc)
- [ ] Membuat Notification class
- [ ] Multi-channel notification
- [ ] Database notifications
- [ ] Queue untuk email
- [ ] Testing dengan Mailtrap

---

## Tips Pengerjaan

### 1. Testing Email

**Mailtrap untuk Development:**

- Semua email tidak akan terkirim ke user asli
- Preview email di browser
- Check spam score
- View HTML & plain text version

**Mail Log Driver:**

```env
MAIL_MAILER=log
```

Email akan tersimpan di `storage/logs/laravel.log`

### 2. Debugging Email

**Common Issues:**

**Email tidak terkirim:**

- Cek konfigurasi `.env`
- Cek log di `storage/logs/laravel.log`
- Pastikan queue worker running (jika pakai queue)

**Template tidak muncul:**

- Clear view cache: `php artisan view:clear`
- Cek path template sudah benar

**Authentication error:**

- Verify MAIL_USERNAME dan MAIL_PASSWORD
- Cek MAIL_ENCRYPTION (tls/ssl)

### 3. Best Practices

**Email Template:**

- Use inline CSS (email clients tidak support external CSS)
- Test di berbagai email clients (Gmail, Outlook, etc)
- Keep design simple dan responsive
- Include plain text version

**Performance:**

- Gunakan Queue untuk bulk email
- Implement throttling untuk prevent spam
- Monitor queue size

**Security:**

- Validate email addresses
- Sanitize data yang ditampilkan di email
- Use rate limiting untuk prevent abuse

### 4. Queue Management

**Start Queue Worker:**

```bash
php artisan queue:work
```

**Monitor Queue:**

```bash
php artisan queue:failed  # Lihat failed jobs
php artisan queue:retry all  # Retry failed jobs
```

**Supervisor (Production):**
Install supervisor untuk auto-restart queue worker jika crash

---

## Advanced Topics (Optional)

### 1. Markdown Mail

Laravel menyediakan markdown mail template:

```bash
php artisan make:mail OrderShipped --markdown=emails.orders.shipped
```

Lebih mudah untuk styling email dengan markdown syntax.

### 2. Custom Mail Driver

Buat custom mail driver untuk service email tertentu (contoh: local SMTP server).

### 3. Notification Events

Listen to notification events untuk logging atau analytics:

```php
Event::listen(NotificationSent::class, function ($event) {
    // Log notification sent
});
```

### 4. Real-time Notification

Implementasi real-time notification menggunakan:

- Laravel Echo
- Pusher atau Socket.io
- WebSocket untuk instant notification

---

## Testing

### Unit Test untuk Mailable

```php
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

public function test_welcome_email_is_sent()
{
    Mail::fake();

    $user = User::factory()->create();

    Mail::to($user)->send(new WelcomeEmail($user));

    Mail::assertSent(WelcomeEmail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
}
```

### Test Notification

```php
use Illuminate\Support\Facades\Notification;

public function test_order_notification_is_sent()
{
    Notification::fake();

    $user = User::factory()->create();

    $user->notify(new OrderShipped($order));

    Notification::assertSentTo($user, OrderShipped::class);
}
```

---

## Referensi

- **Laravel Mail:** https://laravel.com/docs/mail
- **Laravel Notifications:** https://laravel.com/docs/notifications
- **Laravel Queue:** https://laravel.com/docs/queues
- **Mailtrap:** https://mailtrap.io
- **Email Testing Tools:** https://putsmail.com
- **CSS Inliner:** https://htmlemailcheck.com/inline

---

## Submission Guidelines

Untuk setiap soal, submit:

1. **Source Code:**

   - Mailable/Notification class
   - Blade templates
   - Controllers
   - Routes

2. **Screenshot:**

   - Email preview di Mailtrap
   - Notification list
   - Queue dashboard (jika applicable)

3. **Documentation:**
   - Cara setup environment
   - Cara testing
   - Known issues (jika ada)

---

**Selamat Mengerjakan! 📧**
