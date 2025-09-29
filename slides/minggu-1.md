---
title: Pengenalan Pemrograman Web, Konsep MVC, dan Framework Laravel
version: 1.0.0
header: Pengenalan Pemrograman Web, Konsep MVC, dan Framework Laravel
footer: https://github.com/ibbi/selasa-v-p1
paginate: true
marp: true
---

<!--
_class: lead
_paginate: skip
-->

# Pengenalan Pemrograman Web, Konsep MVC, dan Framework Laravel

---

## Tujuan Pembelajaran

- Memahami evolusi pengembangan web dari static ke dynamic.
- Memahami konsep client–server dalam web.
- Memahami permasalahan development tanpa framework.
- Memahami konsep MVC pattern.
- Mengenal framework Laravel dan fitur utamanya.
- Melakukan instalasi Laravel dan setup environment.
- Diskusi dan kuis singkat tentang konsep dasar Laravel.

---

## Evolusi Pengembangan Web

- **Generasi 1 (Static Web):** HTML + CSS (hanya menampilkan informasi statis).
- **Generasi 2 (Dynamic Web):** PHP, ASP, JSP (server-side rendering).
- **Generasi 3 (Framework Era):** Laravel, Django, Rails (MVC, reusable code).
- **Generasi 4 (API & SPA):** REST API, React, Vue, Angular.

---

## Konsep Client–Server

- **Client (Browser):** mengirim request.
- **Server (Web Server):** memproses request, mengakses database.
- **Response:** Server mengirim kembali HTML/JSON yang ditampilkan client.

---

## Permasalahan Development Tanpa Framework

- Code sulit diorganisir.
- Banyak redundansi (copy-paste file PHP/HTML).
- Security tidak standar.
- Sulit maintain project besar.

---

## Konsep MVC Pattern

- **Model**: data & aturan bisnis.
- **View**: tampilan untuk user.
- **Controller**: logika yang menghubungkan model ↔ view.

---

## Framework Laravel

- Framework PHP berbasis **MVC**.
- Dikenal sebagai **“PHP Framework for Web Artisans”**.
- Fitur unggulan:
  - Routing sederhana
  - Eloquent ORM (database mudah)
  - Blade template engine
  - Middleware & autentikasi bawaan
  - REST API & integrasi modern

---

## Kenapa Laravel Populer?

- Dokumentasi lengkap.
- Komunitas besar.
- Banyak package siap pakai.
- Sangat sesuai untuk project skala kecil–besar.

---

## Use Case Laravel

- Aplikasi e-commerce (toko online).
- Learning Management System (LMS).
- Sistem kepegawaian/HRIS.
- REST API untuk mobile apps (Android/iOS).

---

## Ekosistem Laravel

- **Composer** (dependency manager).
- **Artisan CLI** (command line Laravel).
- **Eloquent ORM** (akses database).
- **Blade** (template engine).
- **Laravel Forge** (deployment).
- **Laravel Sanctum/Passport** (API Auth).

---

## Demo Instalasi Laravel

**Langkah instalasi dengan Composer:**

```bash
composer create-project laravel/laravel laravel-app
cd laravel-app
php artisan serve
```

- Laravel berjalan di **http://127.0.0.1:8000/**.
- Default page: "Laravel Welcome Page".

---

## Hands-on Setup Environment

- Instalasi prerequisite: PHP ≥8, Composer, MySQL, VS Code.
- Buat project baru `my-first-laravel`.
- Jalankan dengan `php artisan serve`.
- Buka browser untuk memastikan Laravel aktif.

---

## Diskusi + Kuis Setup

- **Diskusi:**

  - Apa perbedaan coding PHP native dengan menggunakan Laravel?
  - Bagaimana MVC membuat development lebih cepat?

- **Kuis singkat:**
  1. Sebutkan 3 komponen utama dalam arsitektur MVC.
  2. Jalankan perintah untuk membuat _controller_ “UserController”.
     ```bash
     php artisan make:controller UserController
     ```

---
