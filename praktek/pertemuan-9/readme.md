# Praktikum Pertemuan 9 - Form Handling, Validation, dan Request Handling

## Soal 1: Form Registrasi dengan Validasi Dasar

**Tujuan:** Membuat form registrasi user dengan validasi dasar

**Instruksi:**

1. Buat controller untuk registrasi:

   ```bash
   php artisan make:controller RegistrationController
   ```

2. Implementasi method di `RegistrationController.php`:

   ```php
   <?php

   namespace App\Http\Controllers;

   use Illuminate\Http\Request;
   use App\Models\User;
   use Illuminate\Support\Facades\Hash;

   class RegistrationController extends Controller
   {
       public function create()
       {
           return view('registration.create');
       }

       public function store(Request $request)
       {
           $validated = $request->validate([
               'name' => 'required|min:3|max:255',
               'email' => 'required|email|unique:users,email',
               'password' => 'required|min:8|confirmed',
               'phone' => 'required|regex:/^08[0-9]{9,11}$/',
               'birth_date' => 'required|date|before:today',
           ], [
               'name.required' => 'Nama wajib diisi',
               'name.min' => 'Nama minimal 3 karakter',
               'email.required' => 'Email wajib diisi',
               'email.email' => 'Format email tidak valid',
               'email.unique' => 'Email sudah terdaftar',
               'password.required' => 'Password wajib diisi',
               'password.min' => 'Password minimal 8 karakter',
               'password.confirmed' => 'Konfirmasi password tidak cocok',
               'phone.regex' => 'Format nomor HP tidak valid (08xxxxxxxxx)',
               'birth_date.before' => 'Tanggal lahir harus sebelum hari ini',
           ]);

           $user = User::create([
               'name' => $validated['name'],
               'email' => $validated['email'],
               'password' => Hash::make($validated['password']),
           ]);

           return redirect()->route('registration.success')
               ->with('success', 'Registrasi berhasil! Silakan login.');
       }
   }
   ```

3. Buat view `resources/views/registration/create.blade.php`:

   ```html
   <!DOCTYPE html>
   <html lang="id">
     <head>
       <meta charset="UTF-8" />
       <meta name="viewport" content="width=device-width, initial-scale=1.0" />
       <title>Registrasi User</title>
       <style>
         body {
           font-family: Arial, sans-serif;
           max-width: 500px;
           margin: 50px auto;
           padding: 20px;
         }
         .form-group {
           margin-bottom: 15px;
         }
         label {
           display: block;
           margin-bottom: 5px;
           font-weight: bold;
         }
         input {
           width: 100%;
           padding: 8px;
           border: 1px solid #ddd;
           border-radius: 4px;
         }
         .error {
           color: red;
           font-size: 14px;
           margin-top: 5px;
         }
         .btn {
           background-color: #4caf50;
           color: white;
           padding: 10px 20px;
           border: none;
           border-radius: 4px;
           cursor: pointer;
         }
         .alert {
           padding: 15px;
           margin-bottom: 20px;
           border-radius: 4px;
         }
         .alert-danger {
           background-color: #f8d7da;
           color: #721c24;
           border: 1px solid #f5c6cb;
         }
       </style>
     </head>
     <body>
       <h1>Form Registrasi</h1>

       @if ($errors->any())
       <div class="alert alert-danger">
         <ul>
           @foreach ($errors->all() as $error)
           <li>{{ $error }}</li>
           @endforeach
         </ul>
       </div>
       @endif

       <form action="{{ route('registration.store') }}" method="POST">
         @csrf

         <div class="form-group">
           <label>Nama Lengkap:</label>
           <input
             type="text"
             name="name"
             value="{{ old('name') }}"
             placeholder="Masukkan nama lengkap"
           />
           @error('name')
           <div class="error">{{ $message }}</div>
           @enderror
         </div>

         <div class="form-group">
           <label>Email:</label>
           <input
             type="email"
             name="email"
             value="{{ old('email') }}"
             placeholder="email@example.com"
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
             placeholder="Minimal 8 karakter"
           />
           @error('password')
           <div class="error">{{ $message }}</div>
           @enderror
         </div>

         <div class="form-group">
           <label>Konfirmasi Password:</label>
           <input
             type="password"
             name="password_confirmation"
             placeholder="Ulangi password"
           />
         </div>

         <div class="form-group">
           <label>Nomor HP:</label>
           <input
             type="text"
             name="phone"
             value="{{ old('phone') }}"
             placeholder="08xxxxxxxxxx"
           />
           @error('phone')
           <div class="error">{{ $message }}</div>
           @enderror
         </div>

         <div class="form-group">
           <label>Tanggal Lahir:</label>
           <input
             type="date"
             name="birth_date"
             value="{{ old('birth_date') }}"
           />
           @error('birth_date')
           <div class="error">{{ $message }}</div>
           @enderror
         </div>

         <button type="submit" class="btn">Daftar</button>
       </form>
     </body>
   </html>
   ```

4. Tambahkan route di `routes/web.php`:

   ```php
   use App\Http\Controllers\RegistrationController;

   Route::get('/register', [RegistrationController::class, 'create'])->name('registration.create');
   Route::post('/register', [RegistrationController::class, 'store'])->name('registration.store');
   ```

5. Test form dengan berbagai skenario:
   - Submit form kosong
   - Email format salah
   - Password kurang dari 8 karakter
   - Password dan konfirmasi tidak cocok
   - Nomor HP format salah
   - Email yang sudah terdaftar

**Deliverable:**

- Screenshot controller
- Screenshot view dengan form
- Screenshot error validation (minimal 3 skenario berbeda)
- Screenshot form berhasil submit
- Video demo testing form

---

## Soal 2: Form Kontak dengan Custom Validation Messages

**Tujuan:** Membuat form kontak dengan pesan validasi kustom dalam Bahasa Indonesia

**Instruksi:**

1. Buat controller:

   ```bash
   php artisan make:controller ContactController
   ```

2. Buat model dan migration untuk Contact:

   ```bash
   php artisan make:model Contact -m
   ```

3. Edit migration `create_contacts_table.php`:

   ```php
   public function up(): void
   {
       Schema::create('contacts', function (Blueprint $table) {
           $table->id();
           $table->string('name', 100);
           $table->string('email', 100);
           $table->string('phone', 20)->nullable();
           $table->string('subject', 200);
           $table->text('message');
           $table->enum('status', ['new', 'read', 'replied'])->default('new');
           $table->timestamps();
       });
   }
   ```

4. Jalankan migration:

   ```bash
   php artisan migrate
   ```

5. Edit model `Contact.php`:

   ```php
   protected $fillable = [
       'name',
       'email',
       'phone',
       'subject',
       'message',
       'status'
   ];
   ```

6. Implementasi controller dengan custom messages:

   ```php
   public function create()
   {
       return view('contact.create');
   }

   public function store(Request $request)
   {
       $validated = $request->validate([
           'name' => 'required|min:3|max:100',
           'email' => 'required|email',
           'phone' => 'nullable|regex:/^08[0-9]{9,11}$/',
           'subject' => 'required|min:5|max:200',
           'message' => 'required|min:20|max:1000',
       ], [
           'name.required' => 'Kolom nama harus diisi.',
           'name.min' => 'Nama harus memiliki minimal :min karakter.',
           'name.max' => 'Nama tidak boleh lebih dari :max karakter.',
           'email.required' => 'Kolom email harus diisi.',
           'email.email' => 'Alamat email tidak valid.',
           'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format 08xxxxxxxxxx.',
           'subject.required' => 'Kolom subjek harus diisi.',
           'subject.min' => 'Subjek harus memiliki minimal :min karakter.',
           'message.required' => 'Kolom pesan harus diisi.',
           'message.min' => 'Pesan harus memiliki minimal :min karakter.',
           'message.max' => 'Pesan tidak boleh lebih dari :max karakter.',
       ]);

       Contact::create($validated);

       return redirect()->back()
           ->with('success', 'Terima kasih! Pesan Anda telah berhasil dikirim.');
   }

   public function index()
   {
       $contacts = Contact::latest()->paginate(10);
       return view('contact.index', compact('contacts'));
   }
   ```

7. Buat view dan test semua validasi

**Deliverable:**

- Screenshot migration dan model
- Screenshot controller dengan custom messages
- Screenshot form kontak
- Screenshot berbagai pesan error dalam Bahasa Indonesia
- Screenshot data tersimpan di database

---

## Soal 3: Form Request Class untuk Validation

**Tujuan:** Memisahkan logic validasi menggunakan Form Request class

**Instruksi:**

1. Buat Form Request untuk Product:

   ```bash
   php artisan make:request StoreProductRequest
   php artisan make:request UpdateProductRequest
   ```

2. Edit `StoreProductRequest.php`:

   ```php
   <?php

   namespace App\Http\Requests;

   use Illuminate\Foundation\Http\FormRequest;

   class StoreProductRequest extends FormRequest
   {
       public function authorize(): bool
       {
           return true;
       }

       public function rules(): array
       {
           return [
               'name' => 'required|min:3|max:200|unique:products,name',
               'slug' => 'required|alpha_dash|unique:products,slug',
               'category_id' => 'required|exists:categories,id',
               'description' => 'nullable|max:1000',
               'price' => 'required|numeric|min:0|max:999999999',
               'stock' => 'required|integer|min:0',
               'sku' => 'required|unique:products,sku',
               'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
               'is_featured' => 'boolean',
               'is_available' => 'boolean',
           ];
       }

       public function messages(): array
       {
           return [
               'name.required' => 'Nama produk wajib diisi',
               'name.unique' => 'Nama produk sudah ada',
               'slug.alpha_dash' => 'Slug hanya boleh huruf, angka, dash dan underscore',
               'category_id.exists' => 'Kategori tidak valid',
               'price.numeric' => 'Harga harus berupa angka',
               'price.min' => 'Harga tidak boleh negatif',
               'stock.integer' => 'Stok harus berupa bilangan bulat',
               'sku.unique' => 'SKU sudah digunakan',
               'image.image' => 'File harus berupa gambar',
               'image.mimes' => 'Gambar harus format: jpeg, png, jpg',
               'image.max' => 'Ukuran gambar maksimal 2MB',
           ];
       }

       public function attributes(): array
       {
           return [
               'name' => 'nama produk',
               'category_id' => 'kategori',
               'price' => 'harga',
               'stock' => 'stok',
               'sku' => 'SKU',
           ];
       }
   }
   ```

3. Edit `UpdateProductRequest.php`:

   ```php
   public function rules(): array
   {
       $productId = $this->route('product');

       return [
           'name' => 'required|min:3|max:200|unique:products,name,' . $productId,
           'slug' => 'required|alpha_dash|unique:products,slug,' . $productId,
           'category_id' => 'required|exists:categories,id',
           'description' => 'nullable|max:1000',
           'price' => 'required|numeric|min:0',
           'stock' => 'required|integer|min:0',
           'sku' => 'required|unique:products,sku,' . $productId,
           'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
           'is_featured' => 'boolean',
           'is_available' => 'boolean',
       ];
   }
   ```

4. Gunakan di ProductController:

   ```php
   use App\Http\Requests\StoreProductRequest;
   use App\Http\Requests\UpdateProductRequest;

   public function store(StoreProductRequest $request)
   {
       $validated = $request->validated();

       if ($request->hasFile('image')) {
           $validated['image'] = $request->file('image')->store('products', 'public');
       }

       Product::create($validated);

       return redirect()->route('products.index')
           ->with('success', 'Produk berhasil ditambahkan');
   }

   public function update(UpdateProductRequest $request, $id)
   {
       $product = Product::findOrFail($id);
       $validated = $request->validated();

       if ($request->hasFile('image')) {
           // Hapus gambar lama jika ada
           if ($product->image) {
               Storage::disk('public')->delete($product->image);
           }
           $validated['image'] = $request->file('image')->store('products', 'public');
       }

       $product->update($validated);

       return redirect()->route('products.index')
           ->with('success', 'Produk berhasil diperbarui');
   }
   ```

5. Buat form create dan edit product
6. Test semua validasi

**Deliverable:**

- Screenshot StoreProductRequest dan UpdateProductRequest
- Screenshot controller yang menggunakan Form Request
- Screenshot form create product
- Screenshot form edit product
- Screenshot berbagai skenario validasi error
- Screenshot produk berhasil disimpan

---

## Soal 4: File Upload dengan Validasi

**Tujuan:** Implementasi upload file dengan validasi lengkap

**Instruksi:**

1. Buat controller untuk profile:

   ```bash
   php artisan make:controller ProfileController
   ```

2. Tambahkan kolom di tabel users (jika belum ada):

   ```bash
   php artisan make:migration add_profile_fields_to_users_table
   ```

   Edit migration:

   ```php
   public function up(): void
   {
       Schema::table('users', function (Blueprint $table) {
           $table->string('avatar')->nullable()->after('email');
           $table->string('phone', 20)->nullable()->after('avatar');
           $table->text('bio')->nullable()->after('phone');
           $table->date('birth_date')->nullable()->after('bio');
       });
   }
   ```

3. Jalankan migration:

   ```bash
   php artisan migrate
   ```

4. Update Model User:

   ```php
   protected $fillable = [
       'name',
       'email',
       'password',
       'avatar',
       'phone',
       'bio',
       'birth_date'
   ];
   ```

5. Implementasi ProfileController:

   ```php
   use Illuminate\Support\Facades\Storage;
   use Illuminate\Support\Facades\Auth;

   public function edit()
   {
       $user = Auth::user();
       return view('profile.edit', compact('user'));
   }

   public function update(Request $request)
   {
       $user = Auth::user();

       $validated = $request->validate([
           'name' => 'required|min:3|max:255',
           'email' => 'required|email|unique:users,email,' . $user->id,
           'phone' => 'nullable|regex:/^08[0-9]{9,11}$/',
           'bio' => 'nullable|max:500',
           'birth_date' => 'nullable|date|before:today',
           'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
       ], [
           'avatar.image' => 'File harus berupa gambar',
           'avatar.mimes' => 'Format gambar harus: jpeg, png, atau jpg',
           'avatar.max' => 'Ukuran gambar maksimal 2MB',
           'avatar.dimensions' => 'Dimensi gambar minimal 100x100px dan maksimal 2000x2000px',
       ]);

       if ($request->hasFile('avatar')) {
           // Hapus avatar lama jika ada
           if ($user->avatar) {
               Storage::disk('public')->delete($user->avatar);
           }

           // Upload avatar baru
           $avatarPath = $request->file('avatar')->store('avatars', 'public');
           $validated['avatar'] = $avatarPath;
       }

       $user->update($validated);

       return redirect()->back()
           ->with('success', 'Profile berhasil diperbarui!');
   }

   public function deleteAvatar()
   {
       $user = Auth::user();

       if ($user->avatar) {
           Storage::disk('public')->delete($user->avatar);
           $user->update(['avatar' => null]);
       }

       return redirect()->back()
           ->with('success', 'Avatar berhasil dihapus!');
   }
   ```

6. Buat view `resources/views/profile/edit.blade.php`:

   ```html
   <!DOCTYPE html>
   <html>
     <head>
       <title>Edit Profile</title>
       <style>
         /* Add styling */
       </style>
     </head>
     <body>
       <h1>Edit Profile</h1>

       @if (session('success'))
       <div class="alert alert-success">{{ session('success') }}</div>
       @endif

       <form
         action="{{ route('profile.update') }}"
         method="POST"
         enctype="multipart/form-data"
       >
         @csrf @method('PUT') @if($user->avatar)
         <div>
           <img
             src="{{ asset('storage/' . $user->avatar) }}"
             width="150"
             height="150"
           />
           <a
             href="{{ route('profile.delete-avatar') }}"
             onclick="return confirm('Yakin hapus avatar?')"
             >Hapus Avatar</a
           >
         </div>
         @endif

         <div>
           <label>Avatar:</label>
           <input type="file" name="avatar" accept="image/*" />
           @error('avatar')
           <div class="error">{{ $message }}</div>
           @enderror
           <small
             >Format: JPEG, PNG, JPG | Max: 2MB | Dimensi: 100x100 - 2000x2000
             px</small
           >
         </div>

         <div>
           <label>Nama:</label>
           <input
             type="text"
             name="name"
             value="{{ old('name', $user->name) }}"
           />
           @error('name')
           <div class="error">{{ $message }}</div>
           @enderror
         </div>

         <div>
           <label>Email:</label>
           <input
             type="email"
             name="email"
             value="{{ old('email', $user->email) }}"
           />
           @error('email')
           <div class="error">{{ $message }}</div>
           @enderror
         </div>

         <div>
           <label>Phone:</label>
           <input
             type="text"
             name="phone"
             value="{{ old('phone', $user->phone) }}"
           />
           @error('phone')
           <div class="error">{{ $message }}</div>
           @enderror
         </div>

         <div>
           <label>Bio:</label>
           <textarea name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
           @error('bio')
           <div class="error">{{ $message }}</div>
           @enderror
         </div>

         <div>
           <label>Tanggal Lahir:</label>
           <input
             type="date"
             name="birth_date"
             value="{{ old('birth_date', $user->birth_date) }}"
           />
           @error('birth_date')
           <div class="error">{{ $message }}</div>
           @enderror
         </div>

         <button type="submit">Update Profile</button>
       </form>
     </body>
   </html>
   ```

7. Tambahkan route:

   ```php
   Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
   Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
   Route::get('/profile/delete-avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');
   ```

8. Jangan lupa link storage:

   ```bash
   php artisan storage:link
   ```

9. Test upload dengan berbagai skenario

**Deliverable:**

- Screenshot migration dan model
- Screenshot controller
- Screenshot form edit profile
- Screenshot upload gambar berhasil
- Screenshot validasi error (ukuran, format, dimensi)
- Screenshot gambar tersimpan di folder storage
- Screenshot hapus avatar

---

## Soal 5: Dynamic Form dengan Multiple Input

**Tujuan:** Membuat form dengan input dinamis (tambah/hapus field)

**Instruksi:**

1. Buat model dan migration untuk Invoice dan InvoiceItem:

   ```bash
   php artisan make:model Invoice -m
   php artisan make:model InvoiceItem -m
   ```

2. Edit migration `create_invoices_table.php`:

   ```php
   public function up(): void
   {
       Schema::create('invoices', function (Blueprint $table) {
           $table->id();
           $table->string('invoice_number')->unique();
           $table->string('customer_name');
           $table->string('customer_email');
           $table->text('customer_address')->nullable();
           $table->decimal('subtotal', 12, 2);
           $table->decimal('tax', 12, 2)->default(0);
           $table->decimal('total', 12, 2);
           $table->date('invoice_date');
           $table->date('due_date');
           $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft');
           $table->timestamps();
       });
   }
   ```

3. Edit migration `create_invoice_items_table.php`:

   ```php
   public function up(): void
   {
       Schema::create('invoice_items', function (Blueprint $table) {
           $table->id();
           $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
           $table->string('description');
           $table->integer('quantity');
           $table->decimal('unit_price', 12, 2);
           $table->decimal('total', 12, 2);
           $table->timestamps();
       });
   }
   ```

4. Jalankan migration

5. Buat controller:

   ```bash
   php artisan make:controller InvoiceController
   ```

6. Implementasi controller:

   ```php
   public function create()
   {
       return view('invoices.create');
   }

   public function store(Request $request)
   {
       $validated = $request->validate([
           'customer_name' => 'required|min:3|max:255',
           'customer_email' => 'required|email',
           'customer_address' => 'nullable|max:500',
           'invoice_date' => 'required|date',
           'due_date' => 'required|date|after_or_equal:invoice_date',
           'items' => 'required|array|min:1',
           'items.*.description' => 'required|max:255',
           'items.*.quantity' => 'required|integer|min:1',
           'items.*.unit_price' => 'required|numeric|min:0',
       ], [
           'items.required' => 'Minimal harus ada 1 item',
           'items.*.description.required' => 'Deskripsi item wajib diisi',
           'items.*.quantity.required' => 'Jumlah item wajib diisi',
           'items.*.quantity.min' => 'Jumlah minimal 1',
           'items.*.unit_price.required' => 'Harga satuan wajib diisi',
           'due_date.after_or_equal' => 'Tanggal jatuh tempo harus setelah atau sama dengan tanggal invoice',
       ]);

       // Generate invoice number
       $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);

       // Calculate totals
       $subtotal = 0;
       foreach ($request->items as $item) {
           $subtotal += $item['quantity'] * $item['unit_price'];
       }
       $tax = $subtotal * 0.11; // PPN 11%
       $total = $subtotal + $tax;

       // Create invoice
       $invoice = Invoice::create([
           'invoice_number' => $invoiceNumber,
           'customer_name' => $validated['customer_name'],
           'customer_email' => $validated['customer_email'],
           'customer_address' => $validated['customer_address'],
           'subtotal' => $subtotal,
           'tax' => $tax,
           'total' => $total,
           'invoice_date' => $validated['invoice_date'],
           'due_date' => $validated['due_date'],
           'status' => 'draft',
       ]);

       // Create invoice items
       foreach ($request->items as $item) {
           InvoiceItem::create([
               'invoice_id' => $invoice->id,
               'description' => $item['description'],
               'quantity' => $item['quantity'],
               'unit_price' => $item['unit_price'],
               'total' => $item['quantity'] * $item['unit_price'],
           ]);
       }

       return redirect()->route('invoices.show', $invoice->id)
           ->with('success', 'Invoice berhasil dibuat!');
   }
   ```

7. Buat view dengan JavaScript untuk dynamic fields:

   ```html
   <!DOCTYPE html>
   <html>
     <head>
       <title>Buat Invoice</title>
       <style>
         /* Add styling */
         .item-row {
           display: flex;
           gap: 10px;
           margin-bottom: 10px;
           align-items: start;
         }
         .remove-btn {
           background: red;
           color: white;
           border: none;
           padding: 8px 12px;
           cursor: pointer;
         }
       </style>
     </head>
     <body>
       <h1>Buat Invoice Baru</h1>

       @if ($errors->any())
       <div class="alert alert-danger">
         <ul>
           @foreach ($errors->all() as $error)
           <li>{{ $error }}</li>
           @endforeach
         </ul>
       </div>
       @endif

       <form
         action="{{ route('invoices.store') }}"
         method="POST"
         id="invoiceForm"
       >
         @csrf

         <div>
           <label>Nama Customer:</label>
           <input
             type="text"
             name="customer_name"
             value="{{ old('customer_name') }}"
             required
           />
         </div>

         <div>
           <label>Email Customer:</label>
           <input
             type="email"
             name="customer_email"
             value="{{ old('customer_email') }}"
             required
           />
         </div>

         <div>
           <label>Alamat Customer:</label>
           <textarea name="customer_address">
   {{ old('customer_address') }}</textarea
           >
         </div>

         <div>
           <label>Tanggal Invoice:</label>
           <input
             type="date"
             name="invoice_date"
             value="{{ old('invoice_date', date('Y-m-d')) }}"
             required
           />
         </div>

         <div>
           <label>Tanggal Jatuh Tempo:</label>
           <input
             type="date"
             name="due_date"
             value="{{ old('due_date') }}"
             required
           />
         </div>

         <h3>Items</h3>
         <div id="items-container">
           <div class="item-row">
             <input
               type="text"
               name="items[0][description]"
               placeholder="Deskripsi"
               required
             />
             <input
               type="number"
               name="items[0][quantity]"
               placeholder="Qty"
               min="1"
               value="1"
               required
             />
             <input
               type="number"
               name="items[0][unit_price]"
               placeholder="Harga"
               min="0"
               step="0.01"
               required
             />
             <button
               type="button"
               class="remove-btn"
               onclick="removeItem(this)"
             >
               Hapus
             </button>
           </div>
         </div>

         <button type="button" onclick="addItem()">+ Tambah Item</button>
         <br /><br />
         <button type="submit">Buat Invoice</button>
       </form>

       <script>
         let itemIndex = 1;

         function addItem() {
           const container = document.getElementById("items-container");
           const itemRow = document.createElement("div");
           itemRow.className = "item-row";
           itemRow.innerHTML = `
                   <input type="text" name="items[${itemIndex}][description]" placeholder="Deskripsi" required>
                   <input type="number" name="items[${itemIndex}][quantity]" placeholder="Qty" min="1" value="1" required>
                   <input type="number" name="items[${itemIndex}][unit_price]" placeholder="Harga" min="0" step="0.01" required>
                   <button type="button" class="remove-btn" onclick="removeItem(this)">Hapus</button>
               `;
           container.appendChild(itemRow);
           itemIndex++;
         }

         function removeItem(button) {
           const container = document.getElementById("items-container");
           if (container.children.length > 1) {
             button.parentElement.remove();
           } else {
             alert("Minimal harus ada 1 item");
           }
         }
       </script>
     </body>
   </html>
   ```

8. Test form dengan multiple items

**Deliverable:**

- Screenshot migration dan model
- Screenshot controller
- Screenshot form invoice dengan dynamic items
- Screenshot tambah dan hapus item
- Screenshot validasi error
- Screenshot invoice berhasil dibuat
- Screenshot data di database (invoices dan invoice_items)

---

## Soal 6: Conditional Validation

**Tujuan:** Implementasi validasi kondisional berdasarkan input tertentu

**Instruksi:**

1. Buat form pendaftaran event dengan validasi kondisional:

   - Jika kategori "Mahasiswa", wajib input NIM
   - Jika kategori "Umum", wajib input Pekerjaan
   - Jika pilih "Butuh Sertifikat", wajib bayar biaya tambahan

2. Buat model dan migration:

   ```bash
   php artisan make:model EventRegistration -m
   ```

3. Edit migration:

   ```php
   public function up(): void
   {
       Schema::create('event_registrations', function (Blueprint $table) {
           $table->id();
           $table->string('name');
           $table->string('email')->unique();
           $table->string('phone', 20);
           $table->enum('category', ['mahasiswa', 'umum']);
           $table->string('nim', 20)->nullable();
           $table->string('occupation')->nullable();
           $table->boolean('need_certificate')->default(false);
           $table->decimal('registration_fee', 10, 2)->default(0);
           $table->text('notes')->nullable();
           $table->timestamps();
       });
   }
   ```

4. Buat Form Request dengan conditional validation:

   ```bash
   php artisan make:request StoreEventRegistrationRequest
   ```

5. Implementasi conditional validation:

   ```php
   public function rules(): array
   {
       $rules = [
           'name' => 'required|min:3|max:255',
           'email' => 'required|email|unique:event_registrations,email',
           'phone' => 'required|regex:/^08[0-9]{9,11}$/',
           'category' => 'required|in:mahasiswa,umum',
           'need_certificate' => 'boolean',
           'notes' => 'nullable|max:500',
       ];

       // Conditional validation based on category
       if ($this->category === 'mahasiswa') {
           $rules['nim'] = 'required|regex:/^[0-9]{8,15}$/';
           $rules['occupation'] = 'nullable';
       } elseif ($this->category === 'umum') {
           $rules['nim'] = 'nullable';
           $rules['occupation'] = 'required|min:3|max:100';
       }

       // Conditional validation for certificate
       if ($this->need_certificate) {
           $rules['registration_fee'] = 'required|numeric|min:50000';
       } else {
           $rules['registration_fee'] = 'nullable|numeric|min:0';
       }

       return $rules;
   }

   public function messages(): array
   {
       return [
           'nim.required' => 'NIM wajib diisi untuk kategori Mahasiswa',
           'nim.regex' => 'Format NIM tidak valid',
           'occupation.required' => 'Pekerjaan wajib diisi untuk kategori Umum',
           'registration_fee.min' => 'Biaya sertifikat minimal Rp 50.000',
       ];
   }
   ```

6. Buat view dengan JavaScript untuk show/hide conditional fields

7. Test semua kondisi validasi

**Deliverable:**

- Screenshot Form Request dengan conditional validation
- Screenshot view dengan dynamic show/hide fields
- Screenshot validasi untuk kategori Mahasiswa
- Screenshot validasi untuk kategori Umum
- Screenshot validasi dengan/tanpa sertifikat
- Screenshot data tersimpan di database

---

## Soal 7: AJAX Form Validation

**Tujuan:** Implementasi validasi real-time dengan AJAX

**Instruksi:**

1. Buat form check availability (username/email) dengan AJAX

2. Buat route untuk check availability:

   ```php
   Route::post('/check-email', [RegistrationController::class, 'checkEmail'])->name('check.email');
   Route::post('/check-username', [RegistrationController::class, 'checkUsername'])->name('check.username');
   ```

3. Implementasi controller method:

   ```php
   public function checkEmail(Request $request)
   {
       $exists = User::where('email', $request->email)->exists();

       return response()->json([
           'available' => !$exists,
           'message' => $exists ? 'Email sudah terdaftar' : 'Email tersedia'
       ]);
   }

   public function checkUsername(Request $request)
   {
       $exists = User::where('username', $request->username)->exists();

       return response()->json([
           'available' => !$exists,
           'message' => $exists ? 'Username sudah digunakan' : 'Username tersedia'
       ]);
   }
   ```

4. Buat form dengan AJAX validation:

   ```html
   <!DOCTYPE html>
   <html>
     <head>
       <title>Registrasi dengan AJAX Validation</title>
       <meta name="csrf-token" content="{{ csrf_token() }}" />
       <style>
         .available {
           color: green;
         }
         .unavailable {
           color: red;
         }
         .checking {
           color: orange;
         }
       </style>
     </head>
     <body>
       <h1>Form Registrasi</h1>

       <form action="{{ route('registration.store') }}" method="POST">
         @csrf

         <div>
           <label>Username:</label>
           <input type="text" name="username" id="username" />
           <span id="username-status"></span>
         </div>

         <div>
           <label>Email:</label>
           <input type="email" name="email" id="email" />
           <span id="email-status"></span>
         </div>

         <div>
           <label>Password:</label>
           <input type="password" name="password" />
         </div>

         <button type="submit" id="submit-btn">Daftar</button>
       </form>

       <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
       <script>
         $.ajaxSetup({
           headers: {
             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
           },
         });

         let emailTimeout;
         let usernameTimeout;
         let emailAvailable = false;
         let usernameAvailable = false;

         $("#email").on("keyup", function () {
           clearTimeout(emailTimeout);
           const email = $(this).val();
           const status = $("#email-status");

           if (email.length < 3) {
             status.html("").removeClass();
             return;
           }

           status.html("Mengecek...").removeClass().addClass("checking");

           emailTimeout = setTimeout(function () {
             $.ajax({
               url: '{{ route("check.email") }}',
               method: "POST",
               data: { email: email },
               success: function (response) {
                 emailAvailable = response.available;
                 if (response.available) {
                   status
                     .html("✓ " + response.message)
                     .removeClass()
                     .addClass("available");
                 } else {
                   status
                     .html("✗ " + response.message)
                     .removeClass()
                     .addClass("unavailable");
                 }
                 updateSubmitButton();
               },
             });
           }, 500);
         });

         $("#username").on("keyup", function () {
           clearTimeout(usernameTimeout);
           const username = $(this).val();
           const status = $("#username-status");

           if (username.length < 3) {
             status.html("").removeClass();
             return;
           }

           status.html("Mengecek...").removeClass().addClass("checking");

           usernameTimeout = setTimeout(function () {
             $.ajax({
               url: '{{ route("check.username") }}',
               method: "POST",
               data: { username: username },
               success: function (response) {
                 usernameAvailable = response.available;
                 if (response.available) {
                   status
                     .html("✓ " + response.message)
                     .removeClass()
                     .addClass("available");
                 } else {
                   status
                     .html("✗ " + response.message)
                     .removeClass()
                     .addClass("unavailable");
                 }
                 updateSubmitButton();
               },
             });
           }, 500);
         });

         function updateSubmitButton() {
           const submitBtn = $("#submit-btn");
           if (emailAvailable && usernameAvailable) {
             submitBtn.prop("disabled", false);
           } else {
             submitBtn.prop("disabled", true);
           }
         }
       </script>
     </body>
   </html>
   ```

5. Test real-time validation

**Deliverable:**

- Screenshot controller method untuk AJAX
- Screenshot form dengan real-time validation
- Video demo check availability real-time
- Screenshot console network tab menunjukkan AJAX request
- Screenshot validasi berhasil dan gagal

---

## Soal 8: Multi-Step Form dengan Session

**Tujuan:** Membuat form multi-step dengan menyimpan data di session

**Instruksi:**

1. Buat wizard registration 3 step:

   - Step 1: Personal Information
   - Step 2: Contact Information
   - Step 3: Account Information

2. Buat controller:

   ```bash
   php artisan make:controller RegistrationWizardController
   ```

3. Implementasi controller:

   ```php
   public function step1()
   {
       return view('registration.step1');
   }

   public function postStep1(Request $request)
   {
       $validated = $request->validate([
           'first_name' => 'required|min:2|max:100',
           'last_name' => 'required|min:2|max:100',
           'gender' => 'required|in:male,female',
           'birth_date' => 'required|date|before:today',
       ]);

       $request->session()->put('registration.step1', $validated);

       return redirect()->route('registration.step2');
   }

   public function step2()
   {
       if (!session()->has('registration.step1')) {
           return redirect()->route('registration.step1');
       }

       return view('registration.step2');
   }

   public function postStep2(Request $request)
   {
       $validated = $request->validate([
           'email' => 'required|email|unique:users,email',
           'phone' => 'required|regex:/^08[0-9]{9,11}$/',
           'address' => 'required|min:10|max:500',
           'city' => 'required|max:100',
           'postal_code' => 'required|regex:/^[0-9]{5}$/',
       ]);

       $request->session()->put('registration.step2', $validated);

       return redirect()->route('registration.step3');
   }

   public function step3()
   {
       if (!session()->has('registration.step1') || !session()->has('registration.step2')) {
           return redirect()->route('registration.step1');
       }

       $step1 = session('registration.step1');
       $step2 = session('registration.step2');

       return view('registration.step3', compact('step1', 'step2'));
   }

   public function postStep3(Request $request)
   {
       $validated = $request->validate([
           'username' => 'required|min:4|max:50|unique:users,username',
           'password' => 'required|min:8|confirmed',
           'agree_terms' => 'accepted',
       ]);

       $step1 = session('registration.step1');
       $step2 = session('registration.step2');

       $user = User::create([
           'name' => $step1['first_name'] . ' ' . $step1['last_name'],
           'email' => $step2['email'],
           'username' => $validated['username'],
           'password' => Hash::make($validated['password']),
           // ... other fields
       ]);

       // Clear session
       $request->session()->forget(['registration.step1', 'registration.step2']);

       return redirect()->route('registration.complete')
           ->with('success', 'Registrasi berhasil!');
   }
   ```

4. Buat view untuk setiap step dengan progress indicator

5. Implementasi back button untuk kembali ke step sebelumnya

**Deliverable:**

- Screenshot controller lengkap
- Screenshot setiap step form
- Screenshot progress indicator
- Screenshot data di session
- Screenshot validasi setiap step
- Video demo navigasi antar step
- Screenshot registrasi berhasil

---

## Soal 9: Custom Validation Rules

**Tujuan:** Membuat custom validation rule sendiri

**Instruksi:**

1. Buat custom validation rule untuk validasi nama Indonesia:

   ```bash
   php artisan make:rule IndonesianName
   ```

2. Implementasi rule:

   ```php
   <?php

   namespace App\Rules;

   use Illuminate\Contracts\Validation\Rule;

   class IndonesianName implements Rule
   {
       public function passes($attribute, $value)
       {
           // Nama Indonesia hanya boleh huruf dan spasi
           // Minimal 2 kata (ada spasi)
           // Tidak boleh angka atau karakter special

           if (!preg_match('/^[a-zA-Z\s]+$/', $value)) {
               return false;
           }

           $words = explode(' ', trim($value));

           if (count($words) < 2) {
               return false;
           }

           return true;
       }

       public function message()
       {
           return 'Nama harus terdiri dari minimal 2 kata (nama depan dan belakang) dan hanya boleh mengandung huruf.';
       }
   }
   ```

3. Buat rule untuk validasi NIK (Nomor Induk Kependudukan):

   ```bash
   php artisan make:rule ValidNIK
   ```

4. Implementasi:

   ```php
   class ValidNIK implements Rule
   {
       public function passes($attribute, $value)
       {
           // NIK harus 16 digit
           if (!preg_match('/^[0-9]{16}$/', $value)) {
               return false;
           }

           // 2 digit pertama: kode provinsi (11-94)
           $provinceCode = substr($value, 0, 2);
           if ($provinceCode < 11 || $provinceCode > 94) {
               return false;
           }

           // 4 digit berikutnya: kode kabupaten/kota
           // 6 digit berikutnya: tanggal lahir (DDMMYY)
           $day = substr($value, 6, 2);
           $month = substr($value, 8, 2);
           $year = substr($value, 10, 2);

           // Untuk perempuan, hari ditambah 40
           $actualDay = $day > 40 ? $day - 40 : $day;

           if ($actualDay < 1 || $actualDay > 31 || $month < 1 || $month > 12) {
               return false;
           }

           return true;
       }

       public function message()
       {
           return 'Format NIK tidak valid. NIK harus 16 digit dengan format yang benar.';
       }
   }
   ```

5. Buat rule untuk validasi strong password:

   ```bash
   php artisan make:rule StrongPassword
   ```

6. Implementasi:

   ```php
   class StrongPassword implements Rule
   {
       public function passes($attribute, $value)
       {
           // Minimal 8 karakter
           if (strlen($value) < 8) {
               return false;
           }

           // Harus ada huruf besar
           if (!preg_match('/[A-Z]/', $value)) {
               return false;
           }

           // Harus ada huruf kecil
           if (!preg_match('/[a-z]/', $value)) {
               return false;
           }

           // Harus ada angka
           if (!preg_match('/[0-9]/', $value)) {
               return false;
           }

           // Harus ada karakter special
           if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
               return false;
           }

           return true;
       }

       public function message()
       {
           return 'Password harus minimal 8 karakter dan mengandung: huruf besar, huruf kecil, angka, dan karakter special (!@#$%^&*).';
       }
   }
   ```

7. Gunakan custom rules di controller:

   ```php
   use App\Rules\IndonesianName;
   use App\Rules\ValidNIK;
   use App\Rules\StrongPassword;

   public function store(Request $request)
   {
       $validated = $request->validate([
           'name' => ['required', new IndonesianName],
           'nik' => ['required', new ValidNIK],
           'password' => ['required', new StrongPassword, 'confirmed'],
       ]);

       // Process...
   }
   ```

8. Buat form dan test semua custom rules

**Deliverable:**

- Screenshot semua custom rule classes
- Screenshot controller yang menggunakan custom rules
- Screenshot form
- Screenshot berbagai skenario validasi error untuk setiap custom rule
- Screenshot validasi berhasil
- Dokumentasi logic setiap custom rule

---

## Soal 10: Complete Form System dengan Best Practices

**Tujuan:** Implementasi sistem form lengkap dengan semua best practices

**Instruksi:**

1. Buat sistem aplikasi lamaran kerja (Job Application) lengkap dengan:

   - Personal information
   - Educational background (multiple)
   - Work experience (multiple)
   - Skills
   - CV upload
   - Cover letter

2. Struktur database:

   ```bash
   php artisan make:model JobApplication -m
   php artisan make:model Education -m
   php artisan make:model WorkExperience -m
   php artisan make:model Skill -m
   ```

3. Implementasi lengkap dengan:

   - Form Request class untuk validasi
   - Custom validation rules
   - File upload dengan validasi
   - Dynamic form (multiple education & experience)
   - AJAX validation untuk check duplicate
   - Flash messages
   - Error handling yang baik
   - Repopulate form on error
   - Progress indicator
   - Confirmation sebelum submit

4. Fitur tambahan:

   - Auto-save draft ke session setiap 30 detik
   - Preview sebelum submit
   - Email notification setelah submit
   - Admin panel untuk review aplikasi

5. Best practices yang harus diterapkan:

   - Validation di Form Request class
   - CSRF protection
   - XSS prevention
   - SQL injection prevention dengan Eloquent
   - Mass assignment protection dengan $fillable
   - File upload security
   - Rate limiting
   - Clean code dan commenting
   - Responsive design

6. Buat dokumentasi lengkap:
   - ERD
   - Flowchart form submission
   - User manual
   - API documentation (jika ada AJAX endpoint)
   - Screenshots semua fitur

**Deliverable:**

- Complete source code (controllers, models, views, migrations, Form Requests)
- ERD dan flowchart
- Screenshots lengkap semua fitur
- Video demo aplikasi (5-10 menit)
- Dokumentasi PDF lengkap (minimal 20 halaman)
- Database export dengan sample data

**Bonus Points:**

- Unit testing untuk validation
- Export aplikasi ke PDF
- Dashboard statistik
- Email template yang menarik
- Mobile responsive

---

## Catatan Pengerjaan:

1. **Setup:**

   - Gunakan Laravel versi terbaru
   - Konfigurasi database dengan benar
   - Test setiap fitur sebelum lanjut

2. **Validation:**

   - Selalu validasi di server side
   - Client side validation hanya untuk UX
   - Gunakan Form Request untuk logic kompleks
   - Custom messages untuk user-friendly

3. **Security:**

   - Selalu gunakan @csrf
   - Escape output dengan {{ }}
   - Gunakan $fillable di Model
   - Validasi file upload dengan ketat

4. **Testing:**

   - Test happy path (input valid)
   - Test sad path (input invalid)
   - Test edge cases
   - Test dengan berbagai browser

5. **Documentation:**
   - Screenshot setiap step
   - Catat error dan solusinya
   - Buat user manual
   - Buat technical documentation

## Kriteria Penilaian:

- **Functionality (30%):** Semua fitur berjalan dengan baik
- **Validation (25%):** Validasi lengkap dan error handling yang baik
- **Security (20%):** Implementasi security best practices
- **Code Quality (15%):** Clean code, reusable, maintainable
- **Documentation (10%):** Screenshot dan penjelasan lengkap

## Command Penting:

```bash
# Form Request
php artisan make:request StoreUserRequest

# Custom Rule
php artisan make:rule CustomRuleName

# Storage Link
php artisan storage:link

# Clear Cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

**Selamat Mengerjakan!** 🚀

**Estimasi Waktu:** 6-8 jam untuk semua soal

**Tips:**

- Kerjakan soal berurutan (1-10)
- Soal 1-5: Basic (2-3 jam)
- Soal 6-9: Intermediate (2-3 jam)
- Soal 10: Advanced (2-3 jam)
- Commit Git setiap soal selesai
- Test di browser berbeda
- Dokumentasi sambil mengerjakan
