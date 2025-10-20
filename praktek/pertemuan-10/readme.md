# Praktikum Pertemuan 10 - Middleware dan Authentication Dasar

## Soal 1: Membuat Custom Middleware untuk Log Activity

**Tujuan:** Membuat middleware yang mencatat setiap aktivitas user

**Instruksi:**

1. Buat middleware LogActivity:

   ```bash
   php artisan make:middleware LogActivity
   ```

2. Edit file `app/Http/Middleware/LogActivity.php`:

   ```php
   <?php

   namespace App\Http\Middleware;

   use Closure;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Log;
   use Illuminate\Support\Facades\Auth;

   class LogActivity
   {
       public function handle(Request $request, Closure $next)
       {
           // Log sebelum request diproses
           $user = Auth::check() ? Auth::user()->name : 'Guest';
           $url = $request->fullUrl();
           $method = $request->method();
           $ip = $request->ip();

           Log::info("Activity Log", [
               'user' => $user,
               'method' => $method,
               'url' => $url,
               'ip' => $ip,
               'time' => now()->toDateTimeString()
           ]);

           // Lanjutkan request
           $response = $next($request);

           // Log setelah request diproses (opsional)
           Log::info("Response Status: " . $response->status());

           return $response;
       }
   }
   ```

3. Registrasi middleware di `app/Http/Kernel.php`:

   ```php
   protected $middlewareAliases = [
       // ... middleware lainnya
       'log.activity' => \App\Http\Middleware\LogActivity::class,
   ];
   ```

4. Buat route untuk testing:

   ```php
   Route::get('/test-log', function () {
       return 'Activity logged!';
   })->middleware('log.activity');

   Route::middleware('log.activity')->group(function () {
       Route::get('/dashboard', function () {
           return 'Dashboard';
       });
       Route::get('/profile', function () {
           return 'Profile';
       });
   });
   ```

5. Test middleware dengan mengakses route
6. Cek log di `storage/logs/laravel.log`

**Deliverable:**

- Screenshot middleware LogActivity
- Screenshot registrasi di Kernel.php
- Screenshot routes
- Screenshot file log menunjukkan aktivitas tercatat
- Screenshot testing di browser

---

## Soal 2: Middleware Check Age dengan Parameter

**Tujuan:** Membuat middleware yang menerima parameter untuk validasi umur

**Instruksi:**

1. Buat middleware CheckAge:

   ```bash
   php artisan make:middleware CheckAge
   ```

2. Implementasi middleware dengan parameter:

   ```php
   <?php

   namespace App\Http\Middleware;

   use Closure;
   use Illuminate\Http\Request;

   class CheckAge
   {
       public function handle(Request $request, Closure $next, $minAge = 18)
       {
           // Untuk demo, ambil age dari query parameter
           // Di real app, ambil dari user profile
           $userAge = $request->query('age');

           if (!$userAge) {
               return response()->json([
                   'error' => 'Age parameter is required'
               ], 400);
           }

           if ($userAge < $minAge) {
               return redirect('/')->with('error', "Anda harus berusia minimal {$minAge} tahun untuk mengakses halaman ini.");
           }

           return $next($request);
       }
   }
   ```

3. Registrasi middleware:

   ```php
   protected $middlewareAliases = [
       'check.age' => \App\Http\Middleware\CheckAge::class,
   ];
   ```

4. Buat routes dengan berbagai parameter:

   ```php
   // Minimal 18 tahun (default)
   Route::get('/adult-content', function () {
       return view('adult-content');
   })->middleware('check.age');

   // Minimal 21 tahun
   Route::get('/casino', function () {
       return view('casino');
   })->middleware('check.age:21');

   // Minimal 17 tahun
   Route::get('/movie-r', function () {
       return view('movie-r');
   })->middleware('check.age:17');
   ```

5. Buat view sederhana untuk testing
6. Test dengan URL: `/adult-content?age=20`, `/casino?age=20`, dll

**Deliverable:**

- Screenshot middleware CheckAge
- Screenshot routes dengan berbagai parameter
- Screenshot testing dengan age valid
- Screenshot testing dengan age invalid (redirect)
- Screenshot flash message error

---

## Soal 3: Implementasi Sistem Authentication Manual

**Tujuan:** Membuat sistem login-logout dari scratch tanpa package

**Instruksi:**

1. Pastikan tabel users sudah ada, jika belum jalankan:

   ```bash
   php artisan migrate
   ```

2. Buat AuthController:

   ```bash
   php artisan make:controller AuthController
   ```

3. Implementasi AuthController lengkap:

   ```php
   <?php

   namespace App\Http\Controllers;

   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;
   use Illuminate\Support\Facades\Hash;
   use App\Models\User;

   class AuthController extends Controller
   {
       // Tampilkan form login
       public function showLogin()
       {
           return view('auth.login');
       }

       // Proses login
       public function login(Request $request)
       {
           $credentials = $request->validate([
               'email' => 'required|email',
               'password' => 'required|min:6',
           ], [
               'email.required' => 'Email wajib diisi',
               'email.email' => 'Format email tidak valid',
               'password.required' => 'Password wajib diisi',
               'password.min' => 'Password minimal 6 karakter',
           ]);

           $remember = $request->boolean('remember');

           if (Auth::attempt($credentials, $remember)) {
               $request->session()->regenerate();

               return redirect()->intended('dashboard')
                   ->with('success', 'Login berhasil! Selamat datang ' . Auth::user()->name);
           }

           return back()->withErrors([
               'email' => 'Email atau password salah.',
           ])->onlyInput('email');
       }

       // Tampilkan form register
       public function showRegister()
       {
           return view('auth.register');
       }

       // Proses register
       public function register(Request $request)
       {
           $validated = $request->validate([
               'name' => 'required|min:3|max:255',
               'email' => 'required|email|unique:users,email',
               'password' => 'required|min:8|confirmed',
           ], [
               'name.required' => 'Nama wajib diisi',
               'email.unique' => 'Email sudah terdaftar',
               'password.confirmed' => 'Konfirmasi password tidak cocok',
           ]);

           $user = User::create([
               'name' => $validated['name'],
               'email' => $validated['email'],
               'password' => Hash::make($validated['password']),
           ]);

           // Auto login setelah register
           Auth::login($user);

           return redirect()->route('dashboard')
               ->with('success', 'Registrasi berhasil! Selamat datang ' . $user->name);
       }

       // Proses logout
       public function logout(Request $request)
       {
           Auth::logout();
           $request->session()->invalidate();
           $request->session()->regenerateToken();

           return redirect()->route('login')
               ->with('success', 'Logout berhasil!');
       }
   }
   ```

4. Buat view `resources/views/auth/login.blade.php`:

   ```html
   <!DOCTYPE html>
   <html lang="id">
     <head>
       <meta charset="UTF-8" />
       <meta name="viewport" content="width=device-width, initial-scale=1.0" />
       <title>Login</title>
       <style>
         * {
           margin: 0;
           padding: 0;
           box-sizing: border-box;
         }
         body {
           font-family: Arial, sans-serif;
           background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
           min-height: 100vh;
           display: flex;
           justify-content: center;
           align-items: center;
         }
         .container {
           background: white;
           padding: 40px;
           border-radius: 10px;
           box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
           width: 100%;
           max-width: 400px;
         }
         h2 {
           margin-bottom: 30px;
           color: #333;
           text-align: center;
         }
         .form-group {
           margin-bottom: 20px;
         }
         label {
           display: block;
           margin-bottom: 5px;
           color: #555;
           font-weight: bold;
         }
         input {
           width: 100%;
           padding: 12px;
           border: 1px solid #ddd;
           border-radius: 5px;
           font-size: 14px;
         }
         input:focus {
           outline: none;
           border-color: #667eea;
         }
         .error {
           color: red;
           font-size: 13px;
           margin-top: 5px;
         }
         .alert {
           padding: 12px;
           margin-bottom: 20px;
           border-radius: 5px;
         }
         .alert-danger {
           background: #f8d7da;
           color: #721c24;
         }
         .alert-success {
           background: #d4edda;
           color: #155724;
         }
         .btn {
           width: 100%;
           padding: 12px;
           background: #667eea;
           color: white;
           border: none;
           border-radius: 5px;
           font-size: 16px;
           cursor: pointer;
           transition: background 0.3s;
         }
         .btn:hover {
           background: #5568d3;
         }
         .checkbox-group {
           display: flex;
           align-items: center;
           gap: 8px;
           margin-bottom: 20px;
         }
         .checkbox-group input {
           width: auto;
         }
         .link {
           text-align: center;
           margin-top: 20px;
         }
         .link a {
           color: #667eea;
           text-decoration: none;
         }
         .link a:hover {
           text-decoration: underline;
         }
       </style>
     </head>
     <body>
       <div class="container">
         <h2>Login</h2>

         @if (session('success'))
         <div class="alert alert-success">{{ session('success') }}</div>
         @endif @if ($errors->any())
         <div class="alert alert-danger">
           <ul style="margin: 0; padding-left: 20px;">
             @foreach ($errors->all() as $error)
             <li>{{ $error }}</li>
             @endforeach
           </ul>
         </div>
         @endif

         <form method="POST" action="{{ route('login') }}">
           @csrf

           <div class="form-group">
             <label>Email:</label>
             <input
               type="email"
               name="email"
               value="{{ old('email') }}"
               placeholder="email@example.com"
               required
             />
             @error('email')
             <div class="error">{{ $message }}</div>
             @enderror
           </div>

           <div class="form-group">
             <label>Password:</label>
             <input
               type="password"
               name="password"
               placeholder="Masukkan password"
               required
             />
             @error('password')
             <div class="error">{{ $message }}</div>
             @enderror
           </div>

           <div class="checkbox-group">
             <input type="checkbox" name="remember" id="remember" />
             <label for="remember" style="margin: 0; font-weight: normal;"
               >Remember Me</label
             >
           </div>

           <button type="submit" class="btn">Login</button>
         </form>

         <div class="link">
           Belum punya akun?
           <a href="{{ route('register') }}">Daftar di sini</a>
         </div>
       </div>
     </body>
   </html>
   ```

5. Buat view `resources/views/auth/register.blade.php` dengan style serupa

6. Buat routes di `routes/web.php`:

   ```php
   use App\Http\Controllers\AuthController;

   // Guest routes (hanya untuk yang belum login)
   Route::middleware('guest')->group(function () {
       Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
       Route::post('/login', [AuthController::class, 'login']);
       Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
       Route::post('/register', [AuthController::class, 'register']);
   });

   // Auth routes (hanya untuk yang sudah login)
   Route::middleware('auth')->group(function () {
       Route::get('/dashboard', function () {
           return view('dashboard');
       })->name('dashboard');
       Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
   });

   // Public route
   Route::get('/', function () {
       return view('welcome');
   });
   ```

7. Buat view dashboard
8. Test seluruh flow: register → login → dashboard → logout

**Deliverable:**

- Screenshot AuthController lengkap
- Screenshot view login dan register
- Screenshot routes
- Screenshot halaman login
- Screenshot halaman register
- Screenshot dashboard setelah login
- Screenshot logout berhasil
- Video demo complete flow (1-2 menit)

---

## Soal 4: Middleware untuk Role-Based Access Control

**Tujuan:** Membuat middleware yang membatasi akses berdasarkan role user

**Instruksi:**

1. Tambahkan kolom role di tabel users:

   ```bash
   php artisan make:migration add_role_to_users_table
   ```

   Edit migration:

   ```php
   public function up(): void
   {
       Schema::table('users', function (Blueprint $table) {
           $table->enum('role', ['admin', 'user', 'editor'])->default('user')->after('email');
       });
   }

   public function down(): void
   {
       Schema::table('users', function (Blueprint $table) {
           $table->dropColumn('role');
       });
   }
   ```

2. Jalankan migration:

   ```bash
   php artisan migrate
   ```

3. Update model User untuk fillable:

   ```php
   protected $fillable = [
       'name',
       'email',
       'password',
       'role',
   ];
   ```

4. Buat middleware CheckRole:

   ```bash
   php artisan make:middleware CheckRole
   ```

   Implementasi:

   ```php
   <?php

   namespace App\Http\Middleware;

   use Closure;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;

   class CheckRole
   {
       public function handle(Request $request, Closure $next, ...$roles)
       {
           if (!Auth::check()) {
               return redirect()->route('login');
           }

           $user = Auth::user();

           if (!in_array($user->role, $roles)) {
               abort(403, 'Unauthorized. You need ' . implode(' or ', $roles) . ' role.');
           }

           return $next($request);
       }
   }
   ```

5. Registrasi middleware:

   ```php
   protected $middlewareAliases = [
       'role' => \App\Http\Middleware\CheckRole::class,
   ];
   ```

6. Buat routes dengan role protection:

   ```php
   // Admin only
   Route::middleware(['auth', 'role:admin'])->group(function () {
       Route::get('/admin/dashboard', function () {
           return view('admin.dashboard');
       });
       Route::get('/admin/users', function () {
           return view('admin.users');
       });
   });

   // Admin atau Editor
   Route::middleware(['auth', 'role:admin,editor'])->group(function () {
       Route::get('/posts/create', function () {
           return view('posts.create');
       });
       Route::get('/posts/edit/{id}', function ($id) {
           return view('posts.edit', compact('id'));
       });
   });

   // User biasa
   Route::middleware(['auth', 'role:user'])->group(function () {
       Route::get('/user/profile', function () {
           return view('user.profile');
       });
   });
   ```

7. Update seeder atau manual create user dengan berbagai role
8. Test akses dengan user berbeda role

**Deliverable:**

- Screenshot migration add role
- Screenshot middleware CheckRole
- Screenshot routes dengan role protection
- Screenshot akses berhasil (role sesuai)
- Screenshot akses ditolak (403 Unauthorized)
- Screenshot database menunjukkan user dengan berbagai role

---

## Soal 5: Middleware untuk Rate Limiting

**Tujuan:** Implementasi rate limiting untuk mencegah spam request

**Instruksi:**

1. Buat middleware CustomThrottle:

   ```bash
   php artisan make:middleware CustomThrottle
   ```

2. Implementasi:

   ```php
   <?php

   namespace App\Http\Middleware;

   use Closure;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Cache;
   use Illuminate\Support\Facades\RateLimiter;

   class CustomThrottle
   {
       public function handle(Request $request, Closure $next, $maxAttempts = 5, $decayMinutes = 1)
       {
           $key = $this->resolveRequestSignature($request);

           if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
               $seconds = RateLimiter::availableIn($key);

               return response()->json([
                   'error' => 'Too many requests. Please try again in ' . $seconds . ' seconds.'
               ], 429);
           }

           RateLimiter::hit($key, $decayMinutes * 60);

           $response = $next($request);

           return $response->header(
               'X-RateLimit-Remaining',
               RateLimiter::remaining($key, $maxAttempts)
           );
       }

       protected function resolveRequestSignature(Request $request)
       {
           return sha1(
               $request->method() .
               '|' . $request->server('SERVER_NAME') .
               '|' . $request->path() .
               '|' . $request->ip()
           );
       }
   }
   ```

3. Registrasi middleware

4. Buat route untuk testing:

   ```php
   // Max 5 request per menit
   Route::get('/api/data', function () {
       return response()->json([
           'message' => 'Success',
           'time' => now()->toDateTimeString()
       ]);
   })->middleware('throttle.custom:5,1');

   // Max 10 request per menit untuk login
   Route::post('/login', [AuthController::class, 'login'])
       ->middleware('throttle.custom:10,1');
   ```

5. Buat script testing untuk simulasi spam request:

   ```html
   <!DOCTYPE html>
   <html>
     <head>
       <title>Rate Limit Test</title>
     </head>
     <body>
       <h2>Rate Limit Testing</h2>
       <button onclick="testRateLimit()">
         Test Rate Limit (Click Multiple Times)
       </button>
       <div id="results"></div>

       <script>
         async function testRateLimit() {
           const resultsDiv = document.getElementById("results");

           try {
             const response = await fetch("/api/data");
             const data = await response.json();
             const remaining = response.headers.get("X-RateLimit-Remaining");

             const p = document.createElement("p");
             if (response.status === 429) {
               p.style.color = "red";
               p.textContent = `❌ ${data.error}`;
             } else {
               p.style.color = "green";
               p.textContent = `✓ Success! Remaining: ${remaining}`;
             }
             resultsDiv.appendChild(p);
           } catch (error) {
             console.error("Error:", error);
           }
         }
       </script>
     </body>
   </html>
   ```

6. Test dengan mengakses endpoint berkali-kali

**Deliverable:**

- Screenshot middleware CustomThrottle
- Screenshot routes dengan throttle
- Screenshot testing page
- Screenshot response header showing rate limit
- Screenshot error 429 saat limit tercapai
- Video demo testing rate limit

---

## Soal 6: Dashboard dengan Authentication dan Authorization

**Tujuan:** Membuat dashboard lengkap dengan sistem auth dan berbagai level akses

**Instruksi:**

1. Buat struktur dashboard dengan layout:

   ```bash
   php artisan make:controller DashboardController
   ```

2. Implementasi DashboardController:

   ```php
   <?php

   namespace App\Http\Controllers;

   use Illuminate\Http\Request;
   use App\Models\User;
   use Illuminate\Support\Facades\Auth;

   class DashboardController extends Controller
   {
       public function index()
       {
           $user = Auth::user();

           $stats = [
               'total_users' => User::count(),
               'admin_count' => User::where('role', 'admin')->count(),
               'user_count' => User::where('role', 'user')->count(),
           ];

           return view('dashboard.index', compact('user', 'stats'));
       }

       public function profile()
       {
           $user = Auth::user();
           return view('dashboard.profile', compact('user'));
       }

       public function updateProfile(Request $request)
       {
           $validated = $request->validate([
               'name' => 'required|min:3|max:255',
               'email' => 'required|email|unique:users,email,' . Auth::id(),
           ]);

           Auth::user()->update($validated);

           return redirect()->back()
               ->with('success', 'Profile berhasil diperbarui!');
       }
   }
   ```

3. Buat master layout `resources/views/layouts/dashboard.blade.php`:

   ```html
   <!DOCTYPE html>
   <html lang="id">
     <head>
       <meta charset="UTF-8" />
       <meta name="viewport" content="width=device-width, initial-scale=1.0" />
       <title>@yield('title', 'Dashboard')</title>
       <style>
         * {
           margin: 0;
           padding: 0;
           box-sizing: border-box;
         }
         body {
           font-family: Arial, sans-serif;
         }
         .navbar {
           background: #2c3e50;
           color: white;
           padding: 15px 30px;
           display: flex;
           justify-content: space-between;
           align-items: center;
         }
         .navbar h1 {
           font-size: 24px;
         }
         .navbar .user-info {
           display: flex;
           align-items: center;
           gap: 20px;
         }
         .logout-btn {
           background: #e74c3c;
           color: white;
           border: none;
           padding: 8px 16px;
           border-radius: 4px;
           cursor: pointer;
         }
         .container {
           display: flex;
           min-height: calc(100vh - 60px);
         }
         .sidebar {
           width: 250px;
           background: #34495e;
           padding: 20px;
         }
         .sidebar a {
           display: block;
           color: white;
           text-decoration: none;
           padding: 12px;
           margin-bottom: 8px;
           border-radius: 4px;
           transition: background 0.3s;
         }
         .sidebar a:hover,
         .sidebar a.active {
           background: #2c3e50;
         }
         .content {
           flex: 1;
           padding: 30px;
           background: #ecf0f1;
         }
         .alert {
           padding: 15px;
           margin-bottom: 20px;
           border-radius: 4px;
         }
         .alert-success {
           background: #d4edda;
           color: #155724;
           border: 1px solid #c3e6cb;
         }
         .card {
           background: white;
           padding: 20px;
           border-radius: 8px;
           box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
           margin-bottom: 20px;
         }
       </style>
     </head>
     <body>
       <nav class="navbar">
         <h1>Dashboard</h1>
         <div class="user-info">
           <span>{{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
           <form
             method="POST"
             action="{{ route('logout') }}"
             style="display: inline;"
           >
             @csrf
             <button type="submit" class="logout-btn">Logout</button>
           </form>
         </div>
       </nav>

       <div class="container">
         <aside class="sidebar">
           <a
             href="{{ route('dashboard') }}"
             class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"
           >
             Dashboard
           </a>
           <a
             href="{{ route('dashboard.profile') }}"
             class="{{ request()->routeIs('dashboard.profile') ? 'active' : '' }}"
           >
             Profile
           </a>
           @if(Auth::user()->role === 'admin')
           <a href="{{ route('admin.users') }}">Manage Users</a>
           @endif
         </aside>

         <main class="content">
           @if (session('success'))
           <div class="alert alert-success">{{ session('success') }}</div>
           @endif @yield('content')
         </main>
       </div>
     </body>
   </html>
   ```

4. Buat view dashboard, profile, dan admin
5. Setup routes lengkap
6. Test semua fitur

**Deliverable:**

- Screenshot DashboardController
- Screenshot master layout
- Screenshot dashboard page
- Screenshot profile page
- Screenshot admin page (jika role admin)
- Screenshot berbagai role mengakses dashboard
- Video demo lengkap navigasi dashboard

---

## Soal 7: Remember Me dan Session Management

**Tujuan:** Implementasi fitur "Remember Me" dan pengelolaan session

**Instruksi:**

1. Pastikan tabel users memiliki kolom `remember_token` (sudah ada default)

2. Update AuthController untuk handle remember me (sudah di soal 3)

3. Buat middleware untuk check remember token:

   ```bash
   php artisan make:middleware CheckRememberToken
   ```

   Implementasi:

   ```php
   <?php

   namespace App\Http\Middleware;

   use Closure;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;

   class CheckRememberToken
   {
       public function handle(Request $request, Closure $next)
       {
           if (Auth::viaRemember()) {
               // User login via remember token
               // Bisa tambahkan log atau notifikasi
               \Log::info('User login via remember token: ' . Auth::user()->email);
           }

           return $next($request);
       }
   }
   ```

4. Buat halaman untuk manage sessions:

   ```bash
   php artisan make:controller SessionController
   ```

   Implementasi:

   ```php
   <?php

   namespace App\Http\Controllers;

   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Session;

   class SessionController extends Controller
   {
       public function index()
       {
           $sessions = [
               'session_id' => Session::getId(),
               'session_lifetime' => config('session.lifetime'),
               'session_driver' => config('session.driver'),
               'all_data' => Session::all(),
           ];

           return view('sessions.index', compact('sessions'));
       }

       public function destroy()
       {
           Session::flush();
           return redirect()->route('login')
               ->with('success', 'All sessions cleared!');
       }
   }
   ```

5. Buat view untuk display session info

6. Test remember me functionality:
   - Login dengan remember me checked
   - Tutup browser
   - Buka lagi, pastikan masih login

**Deliverable:**

- Screenshot checkbox remember me di form
- Screenshot database showing remember_token
- Screenshot middleware CheckRememberToken
- Screenshot session info page
- Screenshot testing remember me (before/after close browser)
- Dokumentasi cara kerja remember me

---

## Soal 8: Middleware untuk Maintenance Mode

**Tujuan:** Membuat middleware custom untuk maintenance mode dengan whitelist IP

**Instruksi:**

1. Buat middleware MaintenanceMode:

   ```bash
   php artisan make:middleware MaintenanceMode
   ```

2. Implementasi:

   ```php
   <?php

   namespace App\Http\Middleware;

   use Closure;
   use Illuminate\Http\Request;

   class MaintenanceMode
   {
       protected $allowedIps = [
           '127.0.0.1',
           '::1',
           // Tambahkan IP yang diizinkan
       ];

       public function handle(Request $request, Closure $next)
       {
           $maintenanceMode = config('app.maintenance_mode', false);

           if ($maintenanceMode && !in_array($request->ip(), $this->allowedIps)) {
               return response()->view('maintenance', [], 503);
           }

           return $next($request);
       }
   }
   ```

3. Tambahkan config di `config/app.php`:

   ```php
   'maintenance_mode' => env('MAINTENANCE_MODE', false),
   ```

4. Tambahkan di `.env`:

   ```
   MAINTENANCE_MODE=false
   ```

5. Registrasi sebagai global middleware di Kernel.php

6. Buat view maintenance yang menarik:

   ```html
   <!DOCTYPE html>
   <html lang="id">
     <head>
       <meta charset="UTF-8" />
       <meta name="viewport" content="width=device-width, initial-scale=1.0" />
       <title>Under Maintenance</title>
       <style>
         * {
           margin: 0;
           padding: 0;
           box-sizing: border-box;
         }
         body {
           font-family: Arial, sans-serif;
           background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
           min-height: 100vh;
           display: flex;
           justify-content: center;
           align-items: center;
           color: white;
           text-align: center;
           padding: 20px;
         }
         .container {
           max-width: 600px;
         }
         h1 {
           font-size: 48px;
           margin-bottom: 20px;
         }
         p {
           font-size: 18px;
           margin-bottom: 10px;
         }
         .icon {
           font-size: 100px;
           margin-bottom: 30px;
         }
       </style>
     </head>
     <body>
       <div class="container">
         <div class="icon">🔧</div>
         <h1>Under Maintenance</h1>
         <p>We're currently performing scheduled maintenance.</p>
         <p>We'll be back shortly. Thank you for your patience!</p>
         <p style="margin-top: 30px; font-size: 14px;">
           Expected completion: {{ now()->addHours(2)->format('H:i') }}
         </p>
       </div>
     </body>
   </html>
   ```

7. Test dengan mengubah MAINTENANCE_MODE di .env

**Deliverable:**

- Screenshot middleware MaintenanceMode
- Screenshot config dan .env
- Screenshot halaman maintenance
- Screenshot akses dari allowed IP (berhasil)
- Screenshot akses dari IP lain (maintenance page)
- Screenshot HTTP status code 503

---

## Soal 9: Authentication dengan Multiple Guards

**Tujuan:** Implementasi multiple authentication guards (user dan admin terpisah)

**Instruksi:**

1. Buat tabel admins:

   ```bash
   php artisan make:model Admin -m
   ```

   Migration:

   ```php
   public function up(): void
   {
       Schema::create('admins', function (Blueprint $table) {
           $table->id();
           $table->string('name');
           $table->string('email')->unique();
           $table->string('password');
           $table->rememberToken();
           $table->timestamps();
       });
   }
   ```

2. Update Model Admin:

   ```php
   <?php

   namespace App\Models;

   use Illuminate\Foundation\Auth\User as Authenticatable;

   class Admin extends Authenticatable
   {
       protected $fillable = [
           'name',
           'email',
           'password',
       ];

       protected $hidden = [
           'password',
           'remember_token',
       ];
   }
   ```

3. Konfigurasi guards di `config/auth.php`:

   ```php
   'guards' => [
       'web' => [
           'driver' => 'session',
           'provider' => 'users',
       ],
       'admin' => [
           'driver' => 'session',
           'provider' => 'admins',
       ],
   ],

   'providers' => [
       'users' => [
           'driver' => 'eloquent',
           'model' => App\Models\User::class,
       ],
       'admins' => [
           'driver' => 'eloquent',
           'model' => App\Models\Admin::class,
       ],
   ],
   ```

4. Buat AdminAuthController:

   ```bash
   php artisan make:controller AdminAuthController
   ```

   Implementasi:

   ```php
   public function showLogin()
   {
       return view('admin.auth.login');
   }

   public function login(Request $request)
   {
       $credentials = $request->validate([
           'email' => 'required|email',
           'password' => 'required',
       ]);

       if (Auth::guard('admin')->attempt($credentials)) {
           $request->session()->regenerate();
           return redirect()->route('admin.dashboard');
       }

       return back()->withErrors([
           'email' => 'Invalid credentials.',
       ]);
   }

   public function logout(Request $request)
   {
       Auth::guard('admin')->logout();
       $request->session()->invalidate();
       $request->session()->regenerateToken();
       return redirect()->route('admin.login');
   }
   ```

5. Buat middleware CheckAdmin:

   ```php
   public function handle(Request $request, Closure $next)
   {
       if (!Auth::guard('admin')->check()) {
           return redirect()->route('admin.login');
       }
       return $next($request);
   }
   ```

6. Setup routes terpisah untuk admin dan user

7. Test login sebagai user dan admin secara bersamaan

**Deliverable:**

- Screenshot migration admins table
- Screenshot config/auth.php
- Screenshot AdminAuthController
- Screenshot middleware CheckAdmin
- Screenshot routes admin dan user
- Screenshot login admin dan user di browser berbeda
- Screenshot dashboard admin dan user
- Dokumentasi perbedaan guards

---

## Soal 10: Complete Authentication System dengan Best Practices

**Tujuan:** Membuat sistem authentication lengkap dengan semua best practices

**Instruksi:**

1. **Implementasi fitur lengkap:**

   - Registration dengan email verification
   - Login dengan remember me
   - Logout
   - Forgot password
   - Reset password
   - Profile management
   - Change password
   - 2FA (Two Factor Authentication) optional
   - Activity log
   - Session management
   - Role-based access control

2. **Security features:**

   - CSRF protection
   - Password hashing dengan bcrypt
   - Session regeneration
   - Rate limiting untuk login
   - Account lockout setelah failed attempts
   - XSS prevention
   - SQL injection prevention
   - Secure headers

3. **Middleware yang harus dibuat:**

   - LogActivity
   - CheckRole
   - AccountLockout
   - ForcePasswordChange
   - LastActivity

4. **Database schema lengkap:**

   ```php
   // Users table (sudah ada)
   // + role kolom
   // + last_login_at
   // + login_attempts
   // + locked_until

   // activity_logs table
   Schema::create('activity_logs', function (Blueprint $table) {
       $table->id();
       $table->foreignId('user_id')->constrained()->onDelete('cascade');
       $table->string('action');
       $table->string('ip_address', 45);
       $table->text('user_agent')->nullable();
       $table->text('properties')->nullable();
       $table->timestamps();
   });

   // password_resets table (sudah ada)
   ```

5. **Views yang harus dibuat:**

   - Login
   - Register
   - Dashboard (berbeda untuk setiap role)
   - Profile
   - Change password
   - Forgot password
   - Reset password
   - Activity logs
   - Admin panel (manage users)
   - 403 Forbidden page
   - 503 Maintenance page

6. **Testing yang harus dilakukan:**

   - Register user baru
   - Login dengan berbagai role
   - Remember me functionality
   - Forgot password flow
   - Reset password
   - Update profile
   - Change password
   - Access control (admin vs user)
   - Rate limiting
   - Account lockout
   - Session timeout
   - Activity logging
   - Maintenance mode

7. **Bonus features:**
   - Social login (Google, Facebook)
   - Email notifications
   - SMS verification
   - Password strength meter
   - Login history
   - Export activity logs to CSV
   - Dashboard statistics
   - Real-time notifications

**Deliverable:**

- Complete source code (organized dengan baik)
- Database schema lengkap (ERD)
- All migrations
- All models dengan relationships
- All controllers dengan proper validation
- All middleware
- All views dengan responsive design
- routes/web.php lengkap
- Documentation lengkap (min 30 halaman):
  - System architecture
  - Database design
  - API endpoints (jika ada)
  - Security measures
  - User manual
  - Installation guide
  - Testing scenarios
- Screenshots semua fitur (minimal 20 screenshots)
- Video demo lengkap (5-10 menit)
- Unit tests untuk authentication logic
- Postman collection (jika ada API)

**Kriteria Penilaian:**

- **Functionality (30%):** Semua fitur berjalan dengan baik
- **Security (25%):** Implementasi security best practices
- **Code Quality (20%):** Clean code, organized, reusable
- **UI/UX (15%):** User-friendly, responsive, attractive
- **Documentation (10%):** Lengkap dan jelas

---

## Catatan Pengerjaan:

1. **Setup:**

   - Laravel fresh install atau gunakan yang existing
   - Database configured
   - .env setup correctly

2. **Best Practices:**

   - Always use CSRF protection
   - Hash passwords
   - Regenerate session after login
   - Validate all inputs
   - Use middleware for authorization
   - Log important activities
   - Handle errors gracefully

3. **Testing:**

   - Test happy path
   - Test error cases
   - Test edge cases
   - Test security vulnerabilities
   - Cross-browser testing

4. **Documentation:**
   - Comment your code
   - Document all functions
   - Create user manual
   - Create technical documentation
   - Screenshot everything

## Command Penting:

```bash
# Middleware
php artisan make:middleware MiddlewareName

# Controller
php artisan make:controller ControllerName

# Model with migration
php artisan make:model ModelName -m

# Migration
php artisan make:migration migration_name

# Run migration
php artisan migrate

# Rollback migration
php artisan migrate:rollback

# Tinker (testing)
php artisan tinker

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

**Selamat Mengerjakan!** 🔐

**Estimasi Waktu:**

- Soal 1-3: Basic (2 jam)
- Soal 4-6: Intermediate (2-3 jam)
- Soal 7-9: Advanced (2-3 jam)
- Soal 10: Expert (4-5 jam)

**Tips:**

- Kerjakan berurutan
- Test setiap soal sebelum lanjut
- Commit Git per soal
- Security first!
- Documentation is important
