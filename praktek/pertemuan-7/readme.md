# Praktikum Pertemuan 7 - Database Connection, Migration, dan Eloquent ORM (CRUD Dasar)

## Soal 1: Konfigurasi Database dan Testing Koneksi

**Tujuan:** Mengkonfigurasi koneksi database dan memastikan koneksi berhasil

**Instruksi:**

1. Buka phpMyAdmin atau MySQL client
2. Buat database baru dengan nama `toko_online_db`
3. Edit file `.env` di root project Laravel:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=toko_online_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. Buat route untuk test koneksi di `routes/web.php`:

   ```php
   use Illuminate\Support\Facades\DB;

   Route::get('/test-db', function () {
       try {
           DB::connection()->getPdo();
           $dbName = DB::connection()->getDatabaseName();
           return "Connected to database: " . $dbName;
       } catch (\Exception $e) {
           return "Could not connect to database. Error: " . $e->getMessage();
       }
   });
   ```

5. Jalankan development server:
   ```bash
   php artisan serve
   ```
6. Akses `http://localhost:8000/test-db` di browser
7. Screenshot hasil koneksi yang berhasil
8. Cek status migration:
   ```bash
   php artisan migrate:status
   ```

**Deliverable:**

- Screenshot konfigurasi `.env`
- Screenshot database di phpMyAdmin
- Screenshot hasil test koneksi di browser
- Screenshot output `migrate:status`

**Troubleshooting:**

- Jika error, pastikan MySQL service running
- Pastikan nama database sudah dibuat
- Cek kredensial username dan password

---

## Soal 2: Membuat Migration untuk Tabel Categories

**Tujuan:** Membuat migration dan memahami struktur tabel dengan berbagai tipe data

**Instruksi:**

1. Generate migration untuk tabel categories:

   ```bash
   php artisan make:migration create_categories_table --create=categories
   ```

2. Edit file migration di `database/migrations/xxxx_create_categories_table.php`:

   ```php
   public function up(): void
   {
       Schema::create('categories', function (Blueprint $table) {
           $table->id();
           $table->string('name', 100);
           $table->string('slug', 100)->unique();
           $table->text('description')->nullable();
           $table->string('icon', 50)->nullable();
           $table->boolean('is_active')->default(true);
           $table->integer('order')->default(0);
           $table->timestamps();
       });
   }

   public function down(): void
   {
       Schema::dropIfExists('categories');
   }
   ```

3. Jalankan migration:

   ```bash
   php artisan migrate
   ```

4. Cek database di phpMyAdmin untuk melihat tabel yang terbuat

5. Dokumentasikan setiap kolom dalam tabel:

| Kolom | Tipe Data | Panjang | Constraint              | Fungsi                  |
| ----- | --------- | ------- | ----------------------- | ----------------------- |
| id    | BIGINT    | -       | PRIMARY, AUTO_INCREMENT | Primary key             |
| name  | VARCHAR   | 100     | NOT NULL                | Nama kategori           |
| slug  | VARCHAR   | 100     | UNIQUE                  | URL-friendly identifier |
| ...   | ...       | ...     | ...                     | ...                     |

6. Screenshot hasil migration dan struktur tabel

**Deliverable:**

- Screenshot file migration
- Screenshot output terminal saat migrate
- Screenshot struktur tabel di phpMyAdmin
- Dokumentasi tabel lengkap

---

## Soal 3: Membuat Model dan Migration untuk Products

**Tujuan:** Membuat model dengan migration sekaligus

**Instruksi:**

1. Generate model Product dengan migration:

   ```bash
   php artisan make:model Product -m
   ```

2. Edit migration `create_products_table.php`:

   ```php
   public function up(): void
   {
       Schema::create('products', function (Blueprint $table) {
           $table->id();
           $table->foreignId('category_id')->constrained()->onDelete('cascade');
           $table->string('name', 200);
           $table->string('slug', 200)->unique();
           $table->text('description')->nullable();
           $table->decimal('price', 12, 2);
           $table->decimal('discount_price', 12, 2)->nullable();
           $table->integer('stock')->default(0);
           $table->string('sku', 50)->unique();
           $table->string('image')->nullable();
           $table->boolean('is_featured')->default(false);
           $table->boolean('is_available')->default(true);
           $table->timestamps();
       });
   }
   ```

3. Edit Model `Product.php`:

   ```php
   <?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;

   class Product extends Model
   {
       use HasFactory;

       protected $fillable = [
           'category_id',
           'name',
           'slug',
           'description',
           'price',
           'discount_price',
           'stock',
           'sku',
           'image',
           'is_featured',
           'is_available'
       ];

       protected $casts = [
           'price' => 'decimal:2',
           'discount_price' => 'decimal:2',
           'stock' => 'integer',
           'is_featured' => 'boolean',
           'is_available' => 'boolean'
       ];
   }
   ```

4. Jalankan migration:

   ```bash
   php artisan migrate
   ```

5. Verifikasi foreign key constraint di database

**Deliverable:**

- Screenshot file migration dan model
- Screenshot hasil migration
- Screenshot struktur tabel dengan foreign key
- Penjelasan tentang `onDelete('cascade')`

---

## Soal 4: CRUD - Create (Insert Data dengan Eloquent)

**Tujuan:** Menyimpan data ke database menggunakan Eloquent

**Instruksi:**

1. Buat seeder untuk categories:

   ```bash
   php artisan make:seeder CategorySeeder
   ```

2. Edit `CategorySeeder.php`:

   ```php
   <?php

   namespace Database\Seeders;

   use Illuminate\Database\Seeder;
   use App\Models\Category;

   class CategorySeeder extends Seeder
   {
       public function run(): void
       {
           $categories = [
               [
                   'name' => 'Elektronik',
                   'slug' => 'elektronik',
                   'description' => 'Produk elektronik dan gadget',
                   'icon' => 'fas fa-laptop',
                   'is_active' => true,
                   'order' => 1
               ],
               [
                   'name' => 'Fashion',
                   'slug' => 'fashion',
                   'description' => 'Pakaian dan aksesoris',
                   'icon' => 'fas fa-tshirt',
                   'is_active' => true,
                   'order' => 2
               ],
               [
                   'name' => 'Buku',
                   'slug' => 'buku',
                   'description' => 'Buku dan alat tulis',
                   'icon' => 'fas fa-book',
                   'is_active' => true,
                   'order' => 3
               ]
           ];

           foreach ($categories as $category) {
               Category::create($category);
           }
       }
   }
   ```

3. Jangan lupa buat Model Category terlebih dahulu:

   ```bash
   php artisan make:model Category
   ```

4. Edit `Category.php`:

   ```php
   protected $fillable = [
       'name', 'slug', 'description', 'icon', 'is_active', 'order'
   ];
   ```

5. Jalankan seeder:

   ```bash
   php artisan db:seed --class=CategorySeeder
   ```

6. Buat route untuk insert product manual:

   ```php
   Route::get('/add-product', function () {
       $product = Product::create([
           'category_id' => 1,
           'name' => 'Laptop ASUS ROG',
           'slug' => 'laptop-asus-rog',
           'description' => 'Laptop gaming dengan spesifikasi tinggi',
           'price' => 15000000,
           'discount_price' => 14500000,
           'stock' => 10,
           'sku' => 'LP-ASUS-ROG-001',
           'is_featured' => true,
           'is_available' => true
       ]);

       return "Product created with ID: " . $product->id;
   });
   ```

7. Cek data di database

**Deliverable:**

- Screenshot seeder file
- Screenshot output seeder
- Screenshot data categories di database
- Screenshot route add-product dan hasilnya
- Screenshot data products di database

---

## Soal 5: CRUD - Read (Mengambil Data dengan Eloquent)

**Tujuan:** Membaca data dari database dengan berbagai method Eloquent

**Instruksi:**

1. Tambahkan lebih banyak data products (minimal 5 products) menggunakan seeder atau route

2. Buat controller untuk products:

   ```bash
   php artisan make:controller ProductController
   ```

3. Implementasi method di `ProductController.php`:

   ```php
   <?php

   namespace App\Http\Controllers;

   use App\Models\Product;
   use Illuminate\Http\Request;

   class ProductController extends Controller
   {
       // Menampilkan semua products
       public function index()
       {
           $products = Product::all();
           return view('products.index', compact('products'));
       }

       // Menampilkan satu product berdasarkan ID
       public function show($id)
       {
           $product = Product::findOrFail($id);
           return view('products.show', compact('product'));
       }

       // Menampilkan products dengan pagination
       public function indexPaginated()
       {
           $products = Product::paginate(5);
           return view('products.index', compact('products'));
       }

       // Search products
       public function search(Request $request)
       {
           $keyword = $request->keyword;
           $products = Product::where('name', 'like', "%{$keyword}%")
                              ->orWhere('description', 'like', "%{$keyword}%")
                              ->get();
           return view('products.search', compact('products', 'keyword'));
       }

       // Filter by category
       public function byCategory($categoryId)
       {
           $products = Product::where('category_id', $categoryId)->get();
           return view('products.index', compact('products'));
       }

       // Featured products
       public function featured()
       {
           $products = Product::where('is_featured', true)
                              ->where('is_available', true)
                              ->get();
           return view('products.featured', compact('products'));
       }
   }
   ```

4. Buat route di `routes/web.php`:

   ```php
   use App\Http\Controllers\ProductController;

   Route::get('/products', [ProductController::class, 'index']);
   Route::get('/products/{id}', [ProductController::class, 'show']);
   Route::get('/products-paginated', [ProductController::class, 'indexPaginated']);
   Route::get('/products/search', [ProductController::class, 'search']);
   Route::get('/products/category/{categoryId}', [ProductController::class, 'byCategory']);
   Route::get('/products/featured', [ProductController::class, 'featured']);
   ```

5. Buat view sederhana untuk testing (minimal `products/index.blade.php`)

6. Test semua method dengan mengakses route yang berbeda

**Deliverable:**

- Screenshot controller lengkap
- Screenshot routes
- Screenshot hasil setiap method (all, find, where, search, dll)
- Dokumentasi perbedaan `all()` vs `get()` vs `paginate()`

---

## Soal 6: CRUD - Update (Mengubah Data dengan Eloquent)

**Tujuan:** Mengupdate data menggunakan berbagai method Eloquent

**Instruksi:**

1. Tambahkan method update di `ProductController`:

   ```php
   // Update dengan find + save
   public function updateMethod1($id)
   {
       $product = Product::find($id);

       if ($product) {
           $product->name = 'Laptop ASUS ROG Updated';
           $product->price = 14000000;
           $product->stock = 15;
           $product->save();

           return "Product updated successfully using method 1";
       }

       return "Product not found";
   }

   // Update dengan where + update
   public function updateMethod2($id)
   {
       $affected = Product::where('id', $id)->update([
           'name' => 'Laptop ASUS ROG Updated v2',
           'price' => 14500000
       ]);

       return "Rows affected: " . $affected;
   }

   // Update dengan findOrFail + update
   public function updateMethod3($id)
   {
       $product = Product::findOrFail($id);

       $product->update([
           'name' => 'Laptop ASUS ROG Final',
           'price' => 14800000,
           'stock' => 20
       ]);

       return "Product updated successfully using method 3";
   }

   // Update stock (increment/decrement)
   public function updateStock($id, $quantity)
   {
       $product = Product::findOrFail($id);

       // Kurangi stock (misal: pembelian)
       $product->decrement('stock', $quantity);

       // Atau tambah stock (misal: restock)
       // $product->increment('stock', $quantity);

       return "Stock updated. Current stock: " . $product->stock;
   }

   // Update multiple products
   public function discountAll()
   {
       Product::where('price', '>', 10000000)
              ->update([
                  'discount_price' => DB::raw('price * 0.9')
              ]);

       return "Discount applied to all products > 10 juta";
   }
   ```

2. Tambahkan route:

   ```php
   Route::get('/products/update-method1/{id}', [ProductController::class, 'updateMethod1']);
   Route::get('/products/update-method2/{id}', [ProductController::class, 'updateMethod2']);
   Route::get('/products/update-method3/{id}', [ProductController::class, 'updateMethod3']);
   Route::get('/products/update-stock/{id}/{quantity}', [ProductController::class, 'updateStock']);
   Route::get('/products/discount-all', [ProductController::class, 'discountAll']);
   ```

3. Test semua method update
4. Verifikasi perubahan di database setelah setiap update
5. Dokumentasikan perbedaan setiap method

**Deliverable:**

- Screenshot method update di controller
- Screenshot hasil setiap method update
- Screenshot perubahan data di database
- Dokumentasi perbedaan `save()` vs `update()`
- Penjelasan `increment()` dan `decrement()`

---

## Soal 7: CRUD - Delete (Menghapus Data dengan Eloquent)

**Tujuan:** Menghapus data dengan berbagai method dan implementasi soft delete

**Instruksi:**

1. Tambahkan method delete di `ProductController`:

   ```php
   use Illuminate\Support\Facades\DB;

   // Delete dengan find + delete
   public function deleteMethod1($id)
   {
       $product = Product::find($id);

       if ($product) {
           $product->delete();
           return "Product deleted successfully using method 1";
       }

       return "Product not found";
   }

   // Delete dengan destroy
   public function deleteMethod2($id)
   {
       Product::destroy($id);
       return "Product deleted successfully using method 2";
   }

   // Delete multiple products
   public function deleteMultiple()
   {
       Product::destroy([2, 3, 4]);
       return "Multiple products deleted";
   }

   // Delete dengan kondisi
   public function deleteOutOfStock()
   {
       $deleted = Product::where('stock', 0)
                         ->where('is_available', false)
                         ->delete();

       return "Deleted {$deleted} out of stock products";
   }
   ```

2. Implementasi Soft Delete:

   a. Buat migration untuk menambah kolom deleted_at:

   ```bash
   php artisan make:migration add_soft_delete_to_products_table
   ```

   b. Edit migration:

   ```php
   public function up(): void
   {
       Schema::table('products', function (Blueprint $table) {
           $table->softDeletes();
       });
   }

   public function down(): void
   {
       Schema::table('products', function (Blueprint $table) {
           $table->dropSoftDeletes();
       });
   }
   ```

   c. Jalankan migration:

   ```bash
   php artisan migrate
   ```

   d. Update Model `Product.php`:

   ```php
   use Illuminate\Database\Eloquent\SoftDeletes;

   class Product extends Model
   {
       use HasFactory, SoftDeletes;

       // ... kode lainnya
   }
   ```

3. Tambahkan method untuk soft delete:

   ```php
   // Soft delete product
   public function softDeleteProduct($id)
   {
       $product = Product::findOrFail($id);
       $product->delete(); // Ini akan soft delete

       return "Product soft deleted";
   }

   // Lihat data yang di-soft delete
   public function trashedProducts()
   {
       $products = Product::onlyTrashed()->get();
       return view('products.trashed', compact('products'));
   }

   // Restore soft deleted product
   public function restoreProduct($id)
   {
       Product::withTrashed()->find($id)->restore();
       return "Product restored";
   }

   // Force delete (hapus permanen)
   public function forceDeleteProduct($id)
   {
       Product::withTrashed()->find($id)->forceDelete();
       return "Product permanently deleted";
   }
   ```

4. Buat route untuk semua method
5. Test semua method delete

**Deliverable:**

- Screenshot method delete di controller
- Screenshot migration soft delete
- Screenshot model dengan SoftDeletes trait
- Screenshot hasil soft delete di database (deleted_at terisi)
- Screenshot restore product
- Dokumentasi perbedaan delete biasa vs soft delete

---

## Soal 8: Query Builder vs Eloquent Comparison

**Tujuan:** Memahami perbedaan Query Builder dan Eloquent ORM

**Instruksi:**

1. Buat controller baru:

   ```bash
   php artisan make:controller ComparisonController
   ```

2. Implementasi method perbandingan:

   ```php
   <?php

   namespace App\Http\Controllers;

   use App\Models\Product;
   use Illuminate\Support\Facades\DB;

   class ComparisonController extends Controller
   {
       public function compareSelect()
       {
           // Query Builder
           $qbProducts = DB::table('products')->get();

           // Eloquent
           $eloquentProducts = Product::all();

           return view('comparison.select', compact('qbProducts', 'eloquentProducts'));
       }

       public function compareWhere()
       {
           // Query Builder
           $qbProducts = DB::table('products')
                           ->where('price', '>', 1000000)
                           ->get();

           // Eloquent
           $eloquentProducts = Product::where('price', '>', 1000000)->get();

           return view('comparison.where', compact('qbProducts', 'eloquentProducts'));
       }

       public function compareInsert()
       {
           // Query Builder
           DB::table('products')->insert([
               'category_id' => 1,
               'name' => 'Test Product QB',
               'slug' => 'test-product-qb',
               'price' => 100000,
               'stock' => 10,
               'sku' => 'TEST-QB-001',
               'created_at' => now(),
               'updated_at' => now()
           ]);

           // Eloquent
           Product::create([
               'category_id' => 1,
               'name' => 'Test Product Eloquent',
               'slug' => 'test-product-eloquent',
               'price' => 100000,
               'stock' => 10,
               'sku' => 'TEST-EL-001'
           ]);

           return "Data inserted using both methods";
       }

       public function compareUpdate()
       {
           // Query Builder
           DB::table('products')
               ->where('id', 1)
               ->update(['price' => 200000]);

           // Eloquent
           $product = Product::find(1);
           $product->price = 200000;
           $product->save();

           return "Data updated using both methods";
       }

       public function compareDelete()
       {
           // Query Builder
           DB::table('products')->where('id', 10)->delete();

           // Eloquent
           Product::destroy(11);

           return "Data deleted using both methods";
       }

       public function compareAggregate()
       {
           // Query Builder
           $qbCount = DB::table('products')->count();
           $qbSum = DB::table('products')->sum('price');
           $qbAvg = DB::table('products')->avg('price');

           // Eloquent
           $eloquentCount = Product::count();
           $eloquentSum = Product::sum('price');
           $eloquentAvg = Product::avg('price');

           return [
               'query_builder' => [
                   'count' => $qbCount,
                   'sum' => $qbSum,
                   'avg' => $qbAvg
               ],
               'eloquent' => [
                   'count' => $eloquentCount,
                   'sum' => $eloquentSum,
                   'avg' => $eloquentAvg
               ]
           ];
       }
   }
   ```

3. Buat tabel perbandingan:

| Aspek           | Query Builder      | Eloquent ORM             |
| --------------- | ------------------ | ------------------------ |
| Return Type     | stdClass/Array     | Model Object             |
| Timestamps      | Manual             | Otomatis                 |
| Mass Assignment | Tidak ada proteksi | Ada proteksi ($fillable) |
| ...             | ...                | ...                      |

**Deliverable:**

- Screenshot controller perbandingan
- Screenshot hasil setiap method
- Tabel perbandingan lengkap
- Rekomendasi kapan menggunakan masing-masing

---

## Soal 9: Membuat API CRUD dengan Eloquent

**Tujuan:** Membuat REST API untuk CRUD products

**Instruksi:**

1. Buat API Controller:

   ```bash
   php artisan make:controller Api/ProductApiController
   ```

2. Implementasi CRUD API di `ProductApiController.php`:

   ```php
   <?php

   namespace App\Http\Controllers\Api;

   use App\Http\Controllers\Controller;
   use App\Models\Product;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Validator;

   class ProductApiController extends Controller
   {
       // GET /api/products
       public function index()
       {
           $products = Product::with('category')->paginate(10);

           return response()->json([
               'success' => true,
               'data' => $products
           ]);
       }

       // GET /api/products/{id}
       public function show($id)
       {
           $product = Product::find($id);

           if (!$product) {
               return response()->json([
                   'success' => false,
                   'message' => 'Product not found'
               ], 404);
           }

           return response()->json([
               'success' => true,
               'data' => $product
           ]);
       }

       // POST /api/products
       public function store(Request $request)
       {
           $validator = Validator::make($request->all(), [
               'category_id' => 'required|exists:categories,id',
               'name' => 'required|string|max:200',
               'slug' => 'required|string|unique:products,slug',
               'price' => 'required|numeric|min:0',
               'stock' => 'required|integer|min:0',
               'sku' => 'required|string|unique:products,sku'
           ]);

           if ($validator->fails()) {
               return response()->json([
                   'success' => false,
                   'errors' => $validator->errors()
               ], 422);
           }

           $product = Product::create($request->all());

           return response()->json([
               'success' => true,
               'message' => 'Product created successfully',
               'data' => $product
           ], 201);
       }

       // PUT /api/products/{id}
       public function update(Request $request, $id)
       {
           $product = Product::find($id);

           if (!$product) {
               return response()->json([
                   'success' => false,
                   'message' => 'Product not found'
               ], 404);
           }

           $validator = Validator::make($request->all(), [
               'name' => 'string|max:200',
               'price' => 'numeric|min:0',
               'stock' => 'integer|min:0'
           ]);

           if ($validator->fails()) {
               return response()->json([
                   'success' => false,
                   'errors' => $validator->errors()
               ], 422);
           }

           $product->update($request->all());

           return response()->json([
               'success' => true,
               'message' => 'Product updated successfully',
               'data' => $product
           ]);
       }

       // DELETE /api/products/{id}
       public function destroy($id)
       {
           $product = Product::find($id);

           if (!$product) {
               return response()->json([
                   'success' => false,
                   'message' => 'Product not found'
               ], 404);
           }

           $product->delete();

           return response()->json([
               'success' => true,
               'message' => 'Product deleted successfully'
           ]);
       }
   }
   ```

3. Buat route API di `routes/api.php`:

   ```php
   use App\Http\Controllers\Api\ProductApiController;

   Route::prefix('products')->group(function () {
       Route::get('/', [ProductApiController::class, 'index']);
       Route::get('/{id}', [ProductApiController::class, 'show']);
       Route::post('/', [ProductApiController::class, 'store']);
       Route::put('/{id}', [ProductApiController::class, 'update']);
       Route::delete('/{id}', [ProductApiController::class, 'destroy']);
   });
   ```

4. Test API menggunakan Postman atau Thunder Client:
   - GET /api/products
   - GET /api/products/1
   - POST /api/products (dengan body JSON)
   - PUT /api/products/1 (dengan body JSON)
   - DELETE /api/products/1

**Deliverable:**

- Screenshot API Controller
- Screenshot routes/api.php
- Screenshot testing dengan Postman/Thunder Client untuk semua endpoint
- Dokumentasi API (method, endpoint, request, response)

---

## Soal 10: Sistem Toko Online Lengkap dengan Dashboard

**Tujuan:** Mengintegrasikan semua yang dipelajari dalam satu aplikasi lengkap

**Instruksi:**

1. **Lengkapi struktur database:**

   - Tabel categories (sudah ada)
   - Tabel products (sudah ada)
   - Tambah tabel customers
   - Tambah tabel orders
   - Tambah tabel order_items

2. **Buat Model dan Migration untuk Customers:**

   ```bash
   php artisan make:model Customer -m
   ```

   Migration:

   ```php
   Schema::create('customers', function (Blueprint $table) {
       $table->id();
       $table->string('name');
       $table->string('email')->unique();
       $table->string('phone', 20);
       $table->text('address')->nullable();
       $table->date('birth_date')->nullable();
       $table->enum('gender', ['male', 'female'])->nullable();
       $table->timestamps();
   });
   ```

3. **Buat Model dan Migration untuk Orders:**

   ```bash
   php artisan make:model Order -m
   ```

   Migration:

   ```php
   Schema::create('orders', function (Blueprint $table) {
       $table->id();
       $table->string('order_number')->unique();
       $table->foreignId('customer_id')->constrained()->onDelete('cascade');
       $table->decimal('total_amount', 12, 2);
       $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
       $table->text('notes')->nullable();
       $table->timestamps();
   });
   ```

4. **Buat Model dan Migration untuk Order Items:**

   ```bash
   php artisan make:model OrderItem -m
   ```

   Migration:

   ```php
   Schema::create('order_items', function (Blueprint $table) {
       $table->id();
       $table->foreignId('order_id')->constrained()->onDelete('cascade');
       $table->foreignId('product_id')->constrained()->onDelete('cascade');
       $table->integer('quantity');
       $table->decimal('price', 12, 2);
       $table->decimal('subtotal', 12, 2);
       $table->timestamps();
   });
   ```

5. **Buat Controller untuk Dashboard:**

   ```bash
   php artisan make:controller DashboardController
   ```

   Implementasi:

   ```php
   public function index()
   {
       $data = [
           'total_products' => Product::count(),
           'total_categories' => Category::count(),
           'total_customers' => Customer::count(),
           'total_orders' => Order::count(),
           'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
           'low_stock_products' => Product::where('stock', '<', 10)->count(),
           'featured_products' => Product::where('is_featured', true)->count(),
           'latest_orders' => Order::with('customer')->latest()->take(5)->get(),
           'top_products' => Product::orderBy('stock', 'desc')->take(5)->get()
       ];

       return view('dashboard.index', compact('data'));
   }
   ```

6. **Buat Resource Controller untuk semua modul:**

   ```bash
   php artisan make:controller CategoryController --resource
   php artisan make:controller CustomerController --resource
   php artisan make:controller OrderController --resource
   ```

7. **Implementasi view dashboard dengan statistik:**

   - Total produk, kategori, customer, order
   - Total revenue
   - Latest orders
   - Top selling products
   - Low stock alert

8. **Buat seeder untuk data dummy:**

   - 5 categories
   - 20 products
   - 10 customers
   - 15 orders dengan order items

9. **Implementasi fitur lengkap:**

   - CRUD Categories
   - CRUD Products
   - CRUD Customers
   - View Orders
   - Dashboard dengan statistik

10. **Buat route lengkap:**
    ```php
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('orders', OrderController::class);
    ```

**Deliverable:**

- ERD lengkap sistem toko online
- Screenshot semua migration files
- Screenshot semua model files
- Screenshot struktur database lengkap
- Screenshot seeder files
- Screenshot semua controller
- Screenshot routes lengkap
- Screenshot dashboard dengan statistik
- Screenshot minimal 3 halaman CRUD
- Video demo aplikasi berjalan (opsional)
- Dokumentasi lengkap dalam PDF

**Bonus Points:**

- Implementasi search dan filter
- Export data ke Excel/PDF
- Chart untuk statistik penjualan
- Relasi antar model (category->products, customer->orders, dll)

---

## Catatan Pengerjaan:

1. **Persiapan:**

   - Laravel sudah terinstall
   - Database MySQL running
   - Composer dan PHP terinstall

2. **Workflow:**

   - Kerjakan soal berurutan
   - Commit setiap soal selesai
   - Test sebelum lanjut

3. **Testing:**

   - Test manual via browser
   - Test API via Postman
   - Cek data di phpMyAdmin

4. **Dokumentasi:**
   - Screenshot setiap step
   - Catat error dan solusinya
   - Buat laporan PDF

## Kriteria Penilaian:

- **Database Design (20%):** Struktur tabel, foreign key, tipe data
- **CRUD Implementation (30%):** Semua operasi berjalan dengan benar
- **Code Quality (20%):** Clean code, mengikuti best practice
- **Functionality (20%):** Fitur lengkap dan berfungsi
- **Dokumentasi (10%):** Screenshot dan penjelasan lengkap

## Command Penting yang Harus Dikuasai:

```bash
# Database
php artisan migrate
php artisan migrate:rollback
php artisan migrate:refresh
php artisan migrate:fresh
php artisan migrate:status

# Seeder
php artisan db:seed
php artisan db:seed --class=NamaSeeder

# Tinker (REPL untuk testing)
php artisan tinker

# Model interaction di tinker
Product::all();
Product::find(1);
Product::create(['name' => 'Test']);
```

---

**Selamat Mengerjakan!** 🚀

**Estimasi Waktu:** 5-6 jam untuk semua soal

**Tips:**

- Gunakan tinker untuk testing query cepat
- Selalu backup database sebelum migrate:fresh
- Commit ke Git secara berkala
- Dokumentasi sambil mengerjakan, jangan di akhir
