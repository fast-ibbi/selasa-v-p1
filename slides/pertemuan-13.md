---
title: Mail & Notification di Laravel

version: 1.0.0
header: Mail & Notification di Laravel

footer: https://github.com/ibbi/selasa-v-p1
paginate: true
marp: true
---

<!--
_class: lead
_paginate: skip
-->

# Mail & Notification di Laravel

---

## Tujuan Pembelajaran

- Mahasiswa memahami sistem notifikasi Laravel dan dapat mengirim email/notifikasi
- Mahasiswa mampu mengimplementasikan fitur pengiriman email dengan Mailable
- Mahasiswa dapat membuat notification multi-channel (email & database)
- Mahasiswa mampu mengelola notification dalam aplikasi

---

## Pentingnya Sistem Notifikasi

**Mengapa Notifikasi Penting?**

- Komunikasi otomatis dengan user (welcome email, reset password)
- Notifikasi real-time untuk aktivitas penting (order, payment)
- Meningkatkan user engagement dan retention
- Memberikan feedback kepada user tentang status sistem

**Contoh Use Case:**

- Email verifikasi akun
- Notifikasi transaksi berhasil
- Reminder untuk deadline
- Alert keamanan akun

---

## Gambaran Umum Mail & Notification

**Fitur Laravel untuk Komunikasi:**

**Mail System:**

- Mengirim email dengan template Blade
- Support multiple mail drivers
- Queue support untuk pengiriman massal

**Notification System:**

- Multi-channel (email, database, SMS, Slack)
- Centralized notification logic
- Markable sebagai read/unread

---

## Apa itu Mail System di Laravel?

**Mail System Laravel:**

- Built-in email sending functionality
- Menggunakan SwiftMailer library
- Template-based dengan Blade
- Support untuk HTML dan plain text email

**Keuntungan:**

- Easy configuration
- Multiple driver support
- Testing-friendly dengan Mailtrap
- Queue integration untuk performa

---

## Arsitektur Mail di Laravel

**Mailable Class:**

- Class yang merepresentasikan satu jenis email
- Extends `Illuminate\Mail\Mailable`
- Berisi logic untuk build email (subject, view, data)

**Flow Pengiriman Email:**

```
Controller → Mailable Class → Mail Facade → Driver → Recipient
```

**Komponen Utama:**

- Mailable class (app/Mail)
- Blade template (resources/views/emails)
- Configuration (.env)
- Mail Facade

---

## Konfigurasi Mail di .env

**Setting Environment Variables:**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Parameter Penting:**

- `MAIL_MAILER`: Driver yang digunakan (smtp, sendmail, mailgun, etc)
- `MAIL_HOST`: SMTP server host
- `MAIL_PORT`: Port untuk koneksi
- `MAIL_FROM_ADDRESS`: Default sender email

---

## Driver Mail yang Tersedia

**Laravel Mail Drivers:**

| Driver   | Deskripsi            | Use Case                     |
| -------- | -------------------- | ---------------------------- |
| smtp     | Standard SMTP server | Production (Gmail, SendGrid) |
| sendmail | PHP sendmail         | Simple hosting               |
| mailgun  | Mailgun API          | Transactional emails         |
| ses      | Amazon SES           | AWS infrastructure           |
| log      | Log file only        | Development/Testing          |
| array    | Store in memory      | Unit testing                 |

**Rekomendasi:**

- Development: `log` atau `mailtrap`
- Production: `smtp`, `mailgun`, atau `ses`

---

## Setup Mailtrap untuk Testing

**Mailtrap.io - Email Testing Tool:**

**Langkah Setup:**

1. Daftar di mailtrap.io (gratis)
2. Buat inbox baru
3. Copy kredensial SMTP
4. Paste ke `.env` Laravel

**Keuntungan Mailtrap:**

- Email tidak terkirim ke user asli
- Preview email di browser
- Testing spam score
- Inspect HTML & plain text version

**Config Example:**

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
```

---

## Struktur Folder Mail

**Lokasi File Mail:**

```
app/
└── Mail/
    ├── WelcomeEmail.php
    ├── OrderConfirmation.php
    └── PasswordReset.php

resources/
└── views/
    └── emails/
        ├── welcome.blade.php
        ├── order-confirmation.blade.php
        └── password-reset.blade.php
```

**Konvensi Penamaan:**

- Class: PascalCase (WelcomeEmail)
- View: kebab-case (welcome.blade.php)

---

## Membuat Mailable Class

**Artisan Command:**

```bash
php artisan make:mail WelcomeEmail
```

**Generated Class:**

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function build()
    {
        return $this->view('emails.welcome');
    }
}
```

---

## Struktur Mailable Class

**Anatomy Mailable Class:**

```php
class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user; // Public property untuk pass data

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->from('noreply@example.com')
                    ->subject('Welcome to Our Application')
                    ->view('emails.welcome')
                    ->with(['name' => $this->user->name]);
    }
}
```

**Method Penting:**

- `from()`: Set sender
- `subject()`: Set subject email
- `view()`: Blade template
- `with()`: Pass data ke view

---

## Membuat Blade Template untuk Email

**File: resources/views/emails/welcome.blade.php**

```blade
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 20px; }
        .content { padding: 20px; background: #f9f9f9; }
        .button { background: #4CAF50; color: white; padding: 10px 20px;
                  text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Our Application!</h1>
        </div>
        <div class="content">
            <p>Hello {{ $user->name }},</p>
            <p>Thank you for registering. We're excited to have you on board!</p>
            <p>
                <a href="{{ url('/dashboard') }}" class="button">
                    Get Started
                </a>
            </p>
        </div>
    </div>
</body>
</html>
```

---

## Passing Data ke Email Template

**Cara Pass Data:**

**Method 1: Public Property**

```php
class WelcomeEmail extends Mailable
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user; // Otomatis available di view
    }
}
```

**Method 2: with() Method**

```php
public function build()
{
    return $this->view('emails.welcome')
                ->with([
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'token' => $this->generateToken()
                ]);
}
```

**Di Blade Template:**

```blade
<p>Hello {{ $name }},</p>
<p>Your email: {{ $email }}</p>
```

---

## Mengirim Email

**Menggunakan Mail Facade:**

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use App\Models\User;

// Di Controller
public function sendWelcome()
{
    $user = User::find(1);

    Mail::to($user->email)->send(new WelcomeEmail($user));

    return 'Email sent successfully!';
}
```

**Multiple Recipients:**

```php
Mail::to($user->email)
    ->cc('manager@example.com')
    ->bcc('admin@example.com')
    ->send(new WelcomeEmail($user));
```

**Send to Multiple Users:**

```php
Mail::to(['user1@example.com', 'user2@example.com'])
    ->send(new WelcomeEmail($user));
```

---

## Demo Praktis - Welcome Email

**Skenario: Kirim email setelah registrasi**

**RegisterController.php:**

```php
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8'
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password'])
    ]);

    // Kirim welcome email
    Mail::to($user->email)->send(new WelcomeEmail($user));

    return redirect('/login')
           ->with('success', 'Registration successful! Check your email.');
}
```

---

## Apa itu Notification di Laravel?

**Laravel Notification:**

- Sistem notifikasi yang lebih fleksibel dari Mail
- Support multiple channels dalam satu class
- Dapat dikirim via email, database, SMS, Slack, dll
- Centralized logic untuk berbagai jenis notifikasi

**Perbedaan dengan Mail:**

- Mail: Fokus pada pengiriman email saja
- Notification: Multi-channel, lebih abstrak

**Kapan Menggunakan Notification:**

- Butuh multi-channel delivery
- Perlu simpan history notifikasi
- Notification yang bisa di-mark sebagai read

---

## Perbedaan Mail vs Notification

**Comparison Table:**

| Aspek      | Mail            | Notification               |
| ---------- | --------------- | -------------------------- |
| Purpose    | Email saja      | Multi-channel              |
| Complexity | Sederhana       | Lebih kompleks             |
| Storage    | Tidak tersimpan | Bisa ke database           |
| Use Case   | Email blast     | User alerts                |
| Channels   | Email only      | Email, DB, SMS, Slack, etc |

**Contoh Penggunaan:**

- Mail: Newsletter, marketing email
- Notification: Order update, friend request, system alert

---

## Channel Notification

**Available Channels:**

1. **mail** - Email notification
2. **database** - Simpan ke database
3. **broadcast** - Real-time via WebSocket
4. **nexmo** - SMS via Nexmo (Vonage)
5. **slack** - Slack channel message

**Custom Channels:**

- FCM (Firebase Cloud Messaging)
- WhatsApp
- Telegram
- Push notification

**Configuration per Channel:**

```php
public function via($notifiable)
{
    return ['mail', 'database']; // Multi-channel delivery
}
```

---

## Soal 1

**Atribut apa yang wajib ada di file `.env` untuk konfigurasi email di Laravel?**

A. `EMAIL_HOST` dan `EMAIL_PORT`  
B. `MAIL_MAILER` dan `MAIL_HOST`  
C. `SMTP_SERVER` dan `SMTP_PORT`  
D. `SEND_MAIL` dan `MAIL_SERVER`

<!-- **Jawaban: B** - `MAIL_MAILER` dan `MAIL_HOST` adalah atribut wajib untuk konfigurasi mail di Laravel -->

---

## Soal 2

**Command artisan apa yang digunakan untuk membuat Mailable class baru?**

A. `php artisan create:mail WelcomeEmail`  
B. `php artisan make:email WelcomeEmail`  
C. `php artisan make:mail WelcomeEmail`  
D. `php artisan generate:mailable WelcomeEmail`

<!-- **Jawaban: C** - Command `php artisan make:mail WelcomeEmail` digunakan untuk membuat Mailable class -->

---

## Soal 3

**Method apa yang digunakan di Mailable class untuk menentukan Blade template email?**

A. `template()`  
B. `render()`  
C. `view()`  
D. `blade()`

<!-- **Jawaban: C** - Method `view()` digunakan untuk menentukan Blade template yang akan digunakan untuk email -->

---

## Soal 4

**Facade apa yang digunakan untuk mengirim email di Laravel?**

A. `Email::send()`  
B. `Mail::send()`  
C. `Mailer::send()`  
D. `Send::mail()`

<!-- **Jawaban: B** - `Mail::send()` adalah facade yang digunakan untuk mengirim email di Laravel -->

---

## Soal 5

**Driver mail apa yang direkomendasikan untuk testing di development environment?**

A. `smtp` atau `sendmail`  
B. `log` atau `mailtrap`  
C. `mailgun` atau `ses`  
D. `array` atau `null`

<!-- **Jawaban: B** - `log` atau `mailtrap` direkomendasikan untuk testing karena tidak mengirim email sesungguhnya -->

---

## Soal 6

**Apa perbedaan utama antara Mail dan Notification di Laravel?**

A. Mail lebih cepat dari Notification  
B. Notification hanya untuk database, Mail untuk email  
C. Mail fokus pada email, Notification support multi-channel  
D. Tidak ada perbedaan, keduanya sama

<!-- **Jawaban: C** - Mail fokus pada pengiriman email saja, sedangkan Notification support multi-channel (email, database, SMS, dll) -->

---

## Soal 7

**Channel apa saja yang tersedia di Laravel Notification secara default?**

A. Email, SMS, WhatsApp  
B. Mail, Database, Broadcast, Slack  
C. Email, Push, Database  
D. SMTP, Database, Queue

<!-- **Jawaban: B** - Laravel Notification menyediakan channel: mail, database, broadcast, nexmo (SMS), dan slack secara default -->

---

## Soal 8

**Bagaimana cara mengirim email ke multiple recipients menggunakan Mail facade?**

A. `Mail::to(['email1', 'email2'])->send()`  
B. `Mail::toMany(['email1', 'email2'])->send()`  
C. `Mail::send(['email1', 'email2'])`  
D. `Mail::multiple(['email1', 'email2'])->send()`

<!-- **Jawaban: A** - `Mail::to()` bisa menerima array email addresses untuk multiple recipients -->

---

## Soal 9

**Method apa yang digunakan untuk passing data ke email template di Mailable class?**

A. `data()` atau `pass()`  
B. `with()` atau public property  
C. `send()` atau `attach()`  
D. `bind()` atau `compact()`

<!-- **Jawaban: B** - Bisa menggunakan method `with()` atau mendefinisikan public property di Mailable class -->

---

## Soal 10

**Apa keuntungan menggunakan Mailtrap untuk testing email?**

A. Email terkirim lebih cepat  
B. Email tidak terkirim ke user asli dan bisa di-preview  
C. Gratis unlimited email  
D. Otomatis mengirim ke semua user

<!-- **Jawaban: B** - Mailtrap mencegah email terkirim ke user asli dan menyediakan preview email di browser untuk testing -->
