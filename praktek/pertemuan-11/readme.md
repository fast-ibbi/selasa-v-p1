# Praktikum Pertemuan 11 - Relationship Database (One To One, One To Many, Many To Many)

## Soal 1: Implementasi One To One - User dan Profile

**Tujuan:** Membuat relasi One To One antara User dan Profile

**Instruksi:**

1. Buat model dan migration untuk Profile:

   ```bash
   php artisan make:model Profile -m
   ```

2. Edit migration `create_profiles_table`:

   ```php
   public function up(): void
   {
       Schema::create('profiles', function (Blueprint $table) {
           $table->id();
           $table->foreignId('user_id')->constrained()->onDelete('cascade');
           $table->string('phone')->nullable();
           $table->text('address')->nullable();
           $table->text('bio')->nullable();
           $table->date('birth_date')->nullable();
           $table->enum('gender', ['male', 'female'])->nullable();
           $table->string('avatar')->nullable();
           $table->timestamps();
       });
   }
   ```

3. Jalankan migration:

   ```bash
   php artisan migrate
   ```

4. Update model User (app/Models/User.php):

   ```php
   public function profile()
   {
       return $this->hasOne(Profile::class);
   }
   ```

5. Update model Profile (app/Models/Profile.php):

   ```php
   <?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;

   class Profile extends Model
   {
       use HasFactory;

       protected $fillable = [
           'user_id',
           'phone',
           'address',
           'bio',
           'birth_date',
           'gender',
           'avatar'
       ];

       public function user()
       {
           return $this->belongsTo(User::class);
       }
   }
   ```

6. Buat ProfileController:

   ```bash
   php artisan make:controller ProfileController
   ```

7. Implementasi ProfileController:

   ```php
   <?php

   namespace App\Http\Controllers;

   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;

   class ProfileController extends Controller
   {
       public function show()
       {
           $user = Auth::user();
           $profile = $user->profile;

           return view('profile.show', compact('user', 'profile'));
       }

       public function edit()
       {
           $user = Auth::user();
           $profile = $user->profile;

           return view('profile.edit', compact('user', 'profile'));
       }

       public function update(Request $request)
       {
           $validated = $request->validate([
               'phone' => 'nullable|string|max:20',
               'address' => 'nullable|string',
               'bio' => 'nullable|string|max:500',
               'birth_date' => 'nullable|date',
               'gender' => 'nullable|in:male,female',
           ]);

           $user = Auth::user();

           if ($user->profile) {
               $user->profile->update($validated);
           } else {
               $user->profile()->create($validated);
           }

           return redirect()->route('profile.show')
               ->with('success', 'Profile updated successfully!');
       }
   }
   ```

8. Buat routes di `routes/web.php`:

   ```php
   Route::middleware('auth')->group(function () {
       Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
       Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
       Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
   });
   ```

9. Buat view `resources/views/profile/show.blade.php`:

   ```html
   @extends('layouts.app') @section('content')
   <div class="container">
     <h2>My Profile</h2>

     @if(session('success'))
     <div class="alert alert-success">{{ session('success') }}</div>
     @endif

     <div class="card">
       <div class="card-body">
         <h4>{{ $user->name }}</h4>
         <p><strong>Email:</strong> {{ $user->email }}</p>

         @if($profile)
         <p><strong>Phone:</strong> {{ $profile->phone ?? 'Not set' }}</p>
         <p><strong>Address:</strong> {{ $profile->address ?? 'Not set' }}</p>
         <p><strong>Bio:</strong> {{ $profile->bio ?? 'Not set' }}</p>
         <p>
           <strong>Birth Date:</strong> {{ $profile->birth_date ?? 'Not set' }}
         </p>
         <p><strong>Gender:</strong> {{ $profile->gender ?? 'Not set' }}</p>
         @else
         <p class="text-muted">Profile information not completed yet.</p>
         @endif

         <a href="{{ route('profile.edit') }}" class="btn btn-primary"
           >Edit Profile</a
         >
       </div>
     </div>
   </div>
   @endsection
   ```

10. Buat view `resources/views/profile/edit.blade.php` untuk form edit

11. Test dengan Tinker:

    ```bash
    php artisan tinker
    ```

    ```php
    $user = User::find(1);
    $user->profile()->create([
        'phone' => '081234567890',
        'address' => 'Jakarta',
        'bio' => 'Web Developer',
        'birth_date' => '2000-01-01',
        'gender' => 'male'
    ]);

    // Akses profile dari user
    $user->profile->phone;

    // Akses user dari profile
    $profile = Profile::find(1);
    $profile->user->name;
    ```

**Deliverable:**

- Screenshot migration profiles table
- Screenshot model User dan Profile dengan relasi
- Screenshot ProfileController
- Screenshot routes
- Screenshot halaman profile show
- Screenshot halaman profile edit
- Screenshot database showing users dan profiles table
- Screenshot testing di Tinker
- Video demo CRUD profile (1-2 menit)

---

## Soal 2: Implementasi One To Many - User dan Posts

**Tujuan:** Membuat relasi One To Many antara User dan Posts

**Instruksi:**

1. Buat model dan migration untuk Post:

   ```bash
   php artisan make:model Post -m
   ```

2. Edit migration `create_posts_table`:

   ```php
   public function up(): void
   {
       Schema::create('posts', function (Blueprint $table) {
           $table->id();
           $table->foreignId('user_id')->constrained()->onDelete('cascade');
           $table->string('title');
           $table->string('slug')->unique();
           $table->text('content');
           $table->string('featured_image')->nullable();
           $table->enum('status', ['draft', 'published'])->default('draft');
           $table->timestamp('published_at')->nullable();
           $table->integer('views')->default(0);
           $table->timestamps();
       });
   }
   ```

3. Jalankan migration

4. Update model User:

   ```php
   public function posts()
   {
       return $this->hasMany(Post::class);
   }
   ```

5. Update model Post:

   ```php
   <?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;
   use Illuminate\Support\Str;

   class Post extends Model
   {
       use HasFactory;

       protected $fillable = [
           'user_id',
           'title',
           'slug',
           'content',
           'featured_image',
           'status',
           'published_at',
           'views'
       ];

       protected $casts = [
           'published_at' => 'datetime',
       ];

       public function user()
       {
           return $this->belongsTo(User::class);
       }

       // Auto generate slug
       protected static function boot()
       {
           parent::boot();

           static::creating(function ($post) {
               if (!$post->slug) {
                   $post->slug = Str::slug($post->title);
               }
           });
       }
   }
   ```

6. Buat PostController dengan resource:

   ```bash
   php artisan make:controller PostController --resource
   ```

7. Implementasi PostController (lengkap):

   ```php
   <?php

   namespace App\Http\Controllers;

   use App\Models\Post;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;
   use Illuminate\Support\Str;

   class PostController extends Controller
   {
       public function index()
       {
           // Eager loading untuk menghindari N+1 problem
           $posts = Post::with('user')->latest()->paginate(10);
           return view('posts.index', compact('posts'));
       }

       public function create()
       {
           return view('posts.create');
       }

       public function store(Request $request)
       {
           $validated = $request->validate([
               'title' => 'required|string|max:255',
               'content' => 'required|string',
               'status' => 'required|in:draft,published',
           ]);

           $validated['user_id'] = Auth::id();
           $validated['slug'] = Str::slug($validated['title']);

           if ($validated['status'] === 'published') {
               $validated['published_at'] = now();
           }

           Post::create($validated);

           return redirect()->route('posts.index')
               ->with('success', 'Post created successfully!');
       }

       public function show(Post $post)
       {
           // Increment views
           $post->increment('views');

           return view('posts.show', compact('post'));
       }

       public function edit(Post $post)
       {
           // Authorization
           if ($post->user_id !== Auth::id()) {
               abort(403, 'Unauthorized');
           }

           return view('posts.edit', compact('post'));
       }

       public function update(Request $request, Post $post)
       {
           // Authorization
           if ($post->user_id !== Auth::id()) {
               abort(403, 'Unauthorized');
           }

           $validated = $request->validate([
               'title' => 'required|string|max:255',
               'content' => 'required|string',
               'status' => 'required|in:draft,published',
           ]);

           $validated['slug'] = Str::slug($validated['title']);

           if ($validated['status'] === 'published' && !$post->published_at) {
               $validated['published_at'] = now();
           }

           $post->update($validated);

           return redirect()->route('posts.index')
               ->with('success', 'Post updated successfully!');
       }

       public function destroy(Post $post)
       {
           // Authorization
           if ($post->user_id !== Auth::id()) {
               abort(403, 'Unauthorized');
           }

           $post->delete();

           return redirect()->route('posts.index')
               ->with('success', 'Post deleted successfully!');
       }

       // Custom method: My posts
       public function myPosts()
       {
           $posts = Auth::user()->posts()->latest()->paginate(10);
           return view('posts.my-posts', compact('posts'));
       }
   }
   ```

8. Buat routes:

   ```php
   Route::middleware('auth')->group(function () {
       Route::resource('posts', PostController::class);
       Route::get('/my-posts', [PostController::class, 'myPosts'])->name('posts.my');
   });
   ```

9. Buat views untuk posts (index, create, edit, show)

10. Test dengan Tinker:

    ```php
    // Buat posts untuk user
    $user = User::find(1);

    $user->posts()->create([
        'title' => 'Belajar Laravel Relationships',
        'content' => 'Artikel tentang database relationships...',
        'status' => 'published',
        'published_at' => now()
    ]);

    // Get all posts dari user
    $user->posts;

    // Count posts
    $user->posts()->count();

    // Filter published posts
    $user->posts()->where('status', 'published')->get();
    ```

**Deliverable:**

- Screenshot migration posts table
- Screenshot model Post dan User dengan relasi
- Screenshot PostController lengkap
- Screenshot routes
- Screenshot halaman posts index (dengan eager loading)
- Screenshot halaman create post
- Screenshot halaman edit post
- Screenshot halaman show post
- Screenshot my posts (filter by user)
- Screenshot database showing posts table with user_id
- Video demo CRUD posts lengkap (2-3 menit)

---

## Soal 3: Implementasi Many To Many - Posts dan Tags

**Tujuan:** Membuat relasi Many To Many antara Posts dan Tags dengan pivot table

**Instruksi:**

1. Buat model dan migration untuk Tag:

   ```bash
   php artisan make:model Tag -m
   ```

2. Edit migration `create_tags_table`:

   ```php
   public function up(): void
   {
       Schema::create('tags', function (Blueprint $table) {
           $table->id();
           $table->string('name')->unique();
           $table->string('slug')->unique();
           $table->string('color')->default('#3490dc');
           $table->timestamps();
       });
   }
   ```

3. Buat pivot table migration:

   ```bash
   php artisan make:migration create_post_tag_table
   ```

4. Edit migration `create_post_tag_table`:

   ```php
   public function up(): void
   {
       Schema::create('post_tag', function (Blueprint $table) {
           $table->id();
           $table->foreignId('post_id')->constrained()->onDelete('cascade');
           $table->foreignId('tag_id')->constrained()->onDelete('cascade');
           $table->timestamps();

           // Prevent duplicate entries
           $table->unique(['post_id', 'tag_id']);
       });
   }
   ```

5. Jalankan migration

6. Update model Post:

   ```php
   public function tags()
   {
       return $this->belongsToMany(Tag::class)->withTimestamps();
   }
   ```

7. Update model Tag:

   ```php
   <?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;
   use Illuminate\Support\Str;

   class Tag extends Model
   {
       use HasFactory;

       protected $fillable = ['name', 'slug', 'color'];

       public function posts()
       {
           return $this->belongsToMany(Post::class)->withTimestamps();
       }

       protected static function boot()
       {
           parent::boot();

           static::creating(function ($tag) {
               if (!$tag->slug) {
                   $tag->slug = Str::slug($tag->name);
               }
           });
       }
   }
   ```

8. Buat TagController:

   ```bash
   php artisan make:controller TagController --resource
   ```

9. Implementasi TagController:

   ```php
   <?php

   namespace App\Http\Controllers;

   use App\Models\Tag;
   use Illuminate\Http\Request;
   use Illuminate\Support\Str;

   class TagController extends Controller
   {
       public function index()
       {
           $tags = Tag::withCount('posts')->get();
           return view('tags.index', compact('tags'));
       }

       public function create()
       {
           return view('tags.create');
       }

       public function store(Request $request)
       {
           $validated = $request->validate([
               'name' => 'required|string|max:50|unique:tags',
               'color' => 'required|string|max:7',
           ]);

           $validated['slug'] = Str::slug($validated['name']);

           Tag::create($validated);

           return redirect()->route('tags.index')
               ->with('success', 'Tag created successfully!');
       }

       public function show(Tag $tag)
       {
           $posts = $tag->posts()->with('user')->latest()->paginate(10);
           return view('tags.show', compact('tag', 'posts'));
       }

       public function destroy(Tag $tag)
       {
           $tag->delete();

           return redirect()->route('tags.index')
               ->with('success', 'Tag deleted successfully!');
       }
   }
   ```

10. Update PostController untuk handle tags:

    ```php
    public function create()
    {
        $tags = Tag::all();
        return view('posts.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']);

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $post = Post::create($validated);

        // Attach tags
        if ($request->has('tags')) {
            $post->tags()->attach($request->tags);
        }

        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $tags = Tag::all();
        return view('posts.edit', compact('post', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        if ($validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        // Sync tags (replace existing)
        $post->tags()->sync($request->tags ?? []);

        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully!');
    }
    ```

11. Update view create/edit post untuk checkbox tags

12. Test dengan Tinker:

    ```php
    // Buat tags
    $tag1 = Tag::create(['name' => 'Laravel', 'color' => '#ff2d20']);
    $tag2 = Tag::create(['name' => 'PHP', 'color' => '#777bb4']);
    $tag3 = Tag::create(['name' => 'Web Development', 'color' => '#3490dc']);

    // Attach tags ke post
    $post = Post::find(1);
    $post->tags()->attach([1, 2, 3]);

    // Detach satu tag
    $post->tags()->detach(2);

    // Sync tags (replace all)
    $post->tags()->sync([1, 3]);

    // Get all tags dari post
    $post->tags;

    // Get all posts dari tag
    $tag = Tag::find(1);
    $tag->posts;
    ```

**Deliverable:**

- Screenshot migration tags dan post_tag table
- Screenshot model Post dan Tag dengan relasi
- Screenshot TagController
- Screenshot PostController dengan tag handling
- Screenshot routes
- Screenshot halaman tags index dengan post count
- Screenshot halaman create post dengan checkbox tags
- Screenshot halaman edit post dengan tags
- Screenshot halaman show tag dengan list posts
- Screenshot database showing pivot table post_tag
- Video demo attach/detach/sync tags (2-3 menit)

---

## Soal 4: Eager Loading dan N+1 Query Problem

**Tujuan:** Memahami dan mengatasi N+1 Query Problem dengan Eager Loading

**Instruksi:**

1. Install Laravel Debugbar untuk monitoring query:

   ```bash
   composer require barryvdh/laravel-debugbar --dev
   ```

2. Buat TestController untuk demo N+1 problem:

   ```bash
   php artisan make:controller TestController
   ```

3. Implementasi demo N+1 problem:

   ```php
   <?php

   namespace App\Http\Controllers;

   use App\Models\Post;
   use Illuminate\Support\Facades\DB;

   class TestController extends Controller
   {
       public function badQuery()
       {
           // BAD: N+1 Query Problem
           DB::enableQueryLog();

           $posts = Post::all(); // 1 query

           foreach($posts as $post) {
               echo $post->title . ' by ' . $post->user->name . '<br>'; // N queries
           }

           $queries = DB::getQueryLog();
           dd([
               'total_queries' => count($queries),
               'queries' => $queries
           ]);
       }

       public function goodQuery()
       {
           // GOOD: Eager Loading
           DB::enableQueryLog();

           $posts = Post::with('user')->get(); // 2 queries only

           foreach($posts as $post) {
               echo $post->title . ' by ' . $post->user->name . '<br>';
           }

           $queries = DB::getQueryLog();
           dd([
               'total_queries' => count($queries),
               'queries' => $queries
           ]);
       }

       public function multipleRelations()
       {
           // Eager load multiple relationships
           DB::enableQueryLog();

           $posts = Post::with(['user', 'tags'])->get();

           foreach($posts as $post) {
               echo $post->title . ' by ' . $post->user->name;
               echo ' - Tags: ' . $post->tags->pluck('name')->join(', ') . '<br>';
           }

           $queries = DB::getQueryLog();
           dd([
               'total_queries' => count($queries),
               'queries' => $queries
           ]);
       }

       public function nestedRelations()
       {
           // Nested eager loading
           DB::enableQueryLog();

           $posts = Post::with('user.profile')->get();

           foreach($posts as $post) {
               $phone = $post->user->profile->phone ?? 'N/A';
               echo $post->title . ' by ' . $post->user->name . ' (Phone: ' . $phone . ')<br>';
           }

           $queries = DB::getQueryLog();
           dd([
               'total_queries' => count($queries),
               'queries' => $queries
           ]);
       }

       public function conditionalLoading()
       {
           // Conditional eager loading
           $includeTags = true;

           $posts = Post::with('user')
               ->when($includeTags, function($query) {
                   $query->with('tags');
               })
               ->get();

           return $posts;
       }

       public function lazyEagerLoading()
       {
           // Lazy eager loading
           $posts = Post::all();

           // Later decide to load relationship
           $posts->load('user');

           return $posts;
       }

       public function withCount()
       {
           // Count related records
           $posts = Post::withCount('tags')->get();

           foreach($posts as $post) {
               echo $post->title . ' - Tags: ' . $post->tags_count . '<br>';
           }
       }
   }
   ```

4. Buat routes untuk testing:

   ```php
   Route::get('/test/bad-query', [TestController::class, 'badQuery']);
   Route::get('/test/good-query', [TestController::class, 'goodQuery']);
   Route::get('/test/multiple-relations', [TestController::class, 'multipleRelations']);
   Route::get('/test/nested-relations', [TestController::class, 'nestedRelations']);
   Route::get('/test/conditional-loading', [TestController::class, 'conditionalLoading']);
   Route::get('/test/lazy-eager-loading', [TestController::class, 'lazyEagerLoading']);
   Route::get('/test/with-count', [TestController::class, 'withCount']);
   ```

5. Seed database dengan data test:

   ```php
   // Buat seeder
   php artisan make:seeder TestDataSeeder
   ```

   ```php
   public function run()
   {
       // Buat 10 users dengan profile
       $users = User::factory(10)->create();

       foreach($users as $user) {
           $user->profile()->create([
               'phone' => fake()->phoneNumber(),
               'address' => fake()->address(),
               'bio' => fake()->sentence(),
           ]);
       }

       // Buat 50 posts
       foreach(range(1, 50) as $i) {
           Post::create([
               'user_id' => $users->random()->id,
               'title' => fake()->sentence(),
               'content' => fake()->paragraphs(3, true),
               'status' => fake()->randomElement(['draft', 'published']),
           ]);
       }

       // Buat tags
       $tags = [];
       foreach(['Laravel', 'PHP', 'JavaScript', 'Vue', 'React', 'Database'] as $tagName) {
           $tags[] = Tag::create(['name' => $tagName, 'color' => fake()->hexColor()]);
       }

       // Attach random tags to posts
       $posts = Post::all();
       foreach($posts as $post) {
           $post->tags()->attach(
               collect($tags)->random(rand(1, 4))->pluck('id')
           );
       }
   }
   ```

6. Test semua routes dan compare query count

**Deliverable:**

- Screenshot Laravel Debugbar showing N+1 queries (bad example)
- Screenshot Laravel Debugbar showing 2-3 queries (good example)
- Screenshot TestController lengkap
- Screenshot output dari semua test routes
- Screenshot comparison: with vs without eager loading
- Screenshot nested eager loading
- Screenshot withCount result
- Dokumentasi perbandingan performance (before/after eager loading)
- Video demo showing debugbar query count (2-3 menit)

---

## Soal 5: Advanced Relationship - Comments dengan Nested Relations

**Tujuan:** Membuat sistem komentar dengan nested relationship (User → Post → Comment)

**Instruksi:**

1. Buat model dan migration untuk Comment:

   ```bash
   php artisan make:model Comment -m
   ```

2. Edit migration:

   ```php
   public function up(): void
   {
       Schema::create('comments', function (Blueprint $table) {
           $table->id();
           $table->foreignId('post_id')->constrained()->onDelete('cascade');
           $table->foreignId('user_id')->constrained()->onDelete('cascade');
           $table->text('content');
           $table->boolean('approved')->default(false);
           $table->timestamps();
       });
   }
   ```

3. Model Comment:

   ```php
   <?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;

   class Comment extends Model
   {
       use HasFactory;

       protected $fillable = ['post_id', 'user_id', 'content', 'approved'];

       public function post()
       {
           return $this->belongsTo(Post::class);
       }

       public function user()
       {
           return $this->belongsTo(User::class);
       }
   }
   ```

4. Update model Post:

   ```php
   public function comments()
   {
       return $this->hasMany(Comment::class);
   }

   public function approvedComments()
   {
       return $this->hasMany(Comment::class)->where('approved', true);
   }
   ```

5. Update model User:

   ```php
   public function comments()
   {
       return $this->hasMany(Comment::class);
   }
   ```

6. Buat CommentController:

   ```bash
   php artisan make:controller CommentController
   ```

7. Implementasi CommentController:

   ```php
   <?php

   namespace App\Http\Controllers;

   use App\Models\Post;
   use App\Models\Comment;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;

   class CommentController extends Controller
   {
       public function store(Request $request, Post $post)
       {
           $validated = $request->validate([
               'content' => 'required|string|max:1000',
           ]);

           $post->comments()->create([
               'user_id' => Auth::id(),
               'content' => $validated['content'],
               'approved' => true, // Auto approve for now
           ]);

           return redirect()->route('posts.show', $post)
               ->with('success', 'Comment added successfully!');
       }

       public function destroy(Comment $comment)
       {
           // Only comment owner or post owner can delete
           if ($comment->user_id !== Auth::id() && $comment->post->user_id !== Auth::id()) {
               abort(403, 'Unauthorized');
           }

           $comment->delete();

           return back()->with('success', 'Comment deleted successfully!');
       }
   }
   ```

8. Routes:

   ```php
   Route::middleware('auth')->group(function () {
       Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
       Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
   });
   ```

9. Update view posts/show.blade.php untuk tampilkan dan form comments

10. Test nested eager loading:

    ```php
    // Get posts with user, tags, and comments with user
    $posts = Post::with(['user', 'tags', 'comments.user'])->get();

    foreach($posts as $post) {
        echo "Post: {$post->title} by {$post->user->name}\n";
        echo "Tags: " . $post->tags->pluck('name')->join(', ') . "\n";
        echo "Comments:\n";

        foreach($post->comments as $comment) {
            echo "  - {$comment->user->name}: {$comment->content}\n";
        }
    }
    ```

**Deliverable:**

- Screenshot migration comments table
- Screenshot model Comment, Post, User dengan relasi
- Screenshot CommentController
- Screenshot routes
- Screenshot halaman show post dengan comments
- Screenshot form add comment
- Screenshot nested eager loading di Tinker
- Screenshot database showing comments table
- Video demo add/delete comment (2 menit)

---

## Soal 6: Polymorphic Relations - Likes System

**Tujuan:** Implementasi polymorphic relationship untuk sistem like yang bisa digunakan di Post dan Comment

**Instruksi:**

1. Buat model dan migration untuk Like:

   ```bash
   php artisan make:model Like -m
   ```

2. Edit migration (polymorphic structure):

   ```php
   public function up(): void
   {
       Schema::create('likes', function (Blueprint $table) {
           $table->id();
           $table->foreignId('user_id')->constrained()->onDelete('cascade');
           $table->morphs('likeable'); // Creates likeable_id and likeable_type
           $table->timestamps();

           // Prevent duplicate likes
           $table->unique(['user_id', 'likeable_id', 'likeable_type']);
       });
   }
   ```

3. Model Like:

   ```php
   <?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;

   class Like extends Model
   {
       use HasFactory;

       protected $fillable = ['user_id', 'likeable_id', 'likeable_type'];

       public function likeable()
       {
           return $this->morphTo();
       }

       public function user()
       {
           return $this->belongsTo(User::class);
       }
   }
   ```

4. Update model Post:

   ```php
   public function likes()
   {
       return $this->morphMany(Like::class, 'likeable');
   }

   public function isLikedBy(User $user)
   {
       return $this->likes()->where('user_id', $user->id)->exists();
   }

   public function likesCount()
   {
       return $this->likes()->count();
   }
   ```

5. Update model Comment:

   ```php
   public function likes()
   {
       return $this->morphMany(Like::class, 'likeable');
   }

   public function isLikedBy(User $user)
   {
       return $this->likes()->where('user_id', $user->id)->exists();
   }
   ```

6. Update model User:

   ```php
   public function likes()
   {
       return $this->hasMany(Like::class);
   }

   public function like($likeable)
   {
       return $this->likes()->firstOrCreate([
           'likeable_id' => $likeable->id,
           'likeable_type' => get_class($likeable),
       ]);
   }

   public function unlike($likeable)
   {
       return $this->likes()
           ->where('likeable_id', $likeable->id)
           ->where('likeable_type', get_class($likeable))
           ->delete();
   }
   ```

7. Buat LikeController:

   ```php
   <?php

   namespace App\Http\Controllers;

   use App\Models\Post;
   use App\Models\Comment;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;

   class LikeController extends Controller
   {
       public function likePost(Post $post)
       {
           Auth::user()->like($post);

           return back()->with('success', 'Post liked!');
       }

       public function unlikePost(Post $post)
       {
           Auth::user()->unlike($post);

           return back()->with('success', 'Post unliked!');
       }

       public function likeComment(Comment $comment)
       {
           Auth::user()->like($comment);

           return back()->with('success', 'Comment liked!');
       }

       public function unlikeComment(Comment $comment)
       {
           Auth::user()->unlike($comment);

           return back()->with('success', 'Comment unliked!');
       }
   }
   ```

8. Routes:

   ```php
   Route::middleware('auth')->group(function () {
       Route::post('/posts/{post}/like', [LikeController::class, 'likePost'])->name('posts.like');
       Route::delete('/posts/{post}/unlike', [LikeController::class, 'unlikePost'])->name('posts.unlike');
       Route::post('/comments/{comment}/like', [LikeController::class, 'likeComment'])->name('comments.like');
       Route::delete('/comments/{comment}/unlike', [LikeController::class, 'unlikeComment'])->name('comments.unlike');
   });
   ```

9. Update views untuk show like button

10. Test polymorphic relations:

    ```php
    $user = User::find(1);
    $post = Post::find(1);
    $comment = Comment::find(1);

    // Like post
    $user->like($post);

    // Like comment
    $user->like($comment);

    // Check if liked
    $post->isLikedBy($user); // true

    // Get all likes for post
    $post->likes;

    // Get likeable from like
    $like = Like::find(1);
    $like->likeable; // Post atau Comment object
    ```

**Deliverable:**

- Screenshot migration likes table dengan morphs
- Screenshot model Like, Post, Comment dengan polymorphic relations
- Screenshot LikeController
- Screenshot User model dengan like/unlike methods
- Screenshot routes
- Screenshot halaman post dengan like button
- Screenshot database showing likes table dengan likeable_type
- Screenshot testing polymorphic di Tinker
- Video demo like/unlike post dan comment (2 menit)

---

## Soal 7: Query Optimization dengan Relationship

**Tujuan:** Optimasi query complex menggunakan relationship methods

**Instruksi:**

1. Buat StatisticsController:

   ```bash
   php artisan make:controller StatisticsController
   ```

2. Implementasi berbagai optimasi query:

   ```php
   <?php

   namespace App\Http\Controllers;

   use App\Models\User;
   use App\Models\Post;
   use App\Models\Tag;
   use Illuminate\Support\Facades\DB;

   class StatisticsController extends Controller
   {
       public function dashboard()
       {
           // Efficient counting
           $stats = [
               'total_users' => User::count(),
               'total_posts' => Post::count(),
               'total_comments' => DB::table('comments')->count(),
               'total_tags' => Tag::count(),
           ];

           // Users with post count (efficient)
           $topAuthors = User::withCount('posts')
               ->orderBy('posts_count', 'desc')
               ->take(5)
               ->get();

           // Posts with multiple counts
           $posts = Post::withCount(['comments', 'likes'])
               ->with('user')
               ->latest()
               ->take(10)
               ->get();

           return view('statistics.dashboard', compact('stats', 'topAuthors', 'posts'));
       }

       public function userAnalytics($userId)
       {
           $user = User::with([
               'posts' => function($query) {
                   $query->latest()->take(5);
               },
               'comments' => function($query) {
                   $query->latest()->take(5);
               }
           ])
           ->withCount(['posts', 'comments', 'likes'])
           ->findOrFail($userId);

           return view('statistics.user', compact('user'));
       }

       public function tagAnalytics()
       {
           $tags = Tag::withCount('posts')
               ->orderBy('posts_count', 'desc')
               ->get();

           return view('statistics.tags', compact('tags'));
       }

       public function advancedQueries()
       {
           // Users yang punya lebih dari 5 posts
           $productiveUsers = User::has('posts', '>', 5)->get();

           // Posts yang punya minimal 1 comment
           $commentedPosts = Post::has('comments')->get();

           // Users yang punya posts dengan tag tertentu
           $laravelAuthors = User::whereHas('posts', function($query) {
               $query->whereHas('tags', function($q) {
                   $q->where('name', 'Laravel');
               });
           })->get();

           // Posts tanpa comments
           $uncommentedPosts = Post::doesntHave('comments')->get();

           // Posts dengan published status dan punya minimal 3 comments
           $popularPosts = Post::where('status', 'published')
               ->has('comments', '>=', 3)
               ->withCount('comments')
               ->get();

           return compact(
               'productiveUsers',
               'commentedPosts',
               'laravelAuthors',
               'uncommentedPosts',
               'popularPosts'
           );
       }

       public function aggregateQueries()
       {
           // Total views per user
           $userViews = User::join('posts', 'users.id', '=', 'posts.user_id')
               ->select('users.id', 'users.name', DB::raw('SUM(posts.views) as total_views'))
               ->groupBy('users.id', 'users.name')
               ->orderBy('total_views', 'desc')
               ->get();

           // Average comments per post
           $avgComments = Post::withCount('comments')
               ->get()
               ->avg('comments_count');

           // Most liked posts
           $mostLikedPosts = Post::withCount('likes')
               ->orderBy('likes_count', 'desc')
               ->take(10)
               ->get();

           return compact('userViews', 'avgComments', 'mostLikedPosts');
       }

       public function complexRelations()
       {
           // Get posts with all related data in minimal queries
           $posts = Post::with([
               'user.profile',
               'tags',
               'comments' => function($query) {
                   $query->where('approved', true)->with('user');
               },
               'likes.user'
           ])
           ->withCount(['comments', 'likes'])
           ->get();

           return view('statistics.complex', compact('posts'));
       }
   }
   ```

3. Routes:

   ```php
   Route::middleware('auth')->group(function () {
       Route::get('/statistics/dashboard', [StatisticsController::class, 'dashboard'])->name('statistics.dashboard');
       Route::get('/statistics/user/{userId}', [StatisticsController::class, 'userAnalytics'])->name('statistics.user');
       Route::get('/statistics/tags', [StatisticsController::class, 'tagAnalytics'])->name('statistics.tags');
       Route::get('/statistics/advanced', [StatisticsController::class, 'advancedQueries'])->name('statistics.advanced');
       Route::get('/statistics/aggregate', [StatisticsController::class, 'aggregateQueries'])->name('statistics.aggregate');
       Route::get('/statistics/complex', [StatisticsController::class, 'complexRelations'])->name('statistics.complex');
   });
   ```

4. Buat views untuk menampilkan statistics

**Deliverable:**

- Screenshot StatisticsController lengkap
- Screenshot dashboard dengan statistics
- Screenshot top authors dengan post count
- Screenshot user analytics page
- Screenshot tag analytics dengan post count
- Screenshot advanced queries result
- Screenshot aggregate queries result
- Screenshot Laravel Debugbar showing query optimization
- Dokumentasi comparison query count (before/after optimization)
- Video demo statistics dashboard (2-3 menit)

---

## Soal 8: Soft Deletes dengan Relationships

**Tujuan:** Implementasi soft delete yang mempertimbangkan relationships

**Instruksi:**

1. Update migration posts untuk soft deletes:

   ```bash
   php artisan make:migration add_soft_deletes_to_posts_table
   ```

   ```php
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Database\Migrations\Migration;

   public function up()
   {
       Schema::table('posts', function (Blueprint $table) {
           $table->softDeletes();
       });
   }

   public function down()
   {
       Schema::table('posts', function (Blueprint $table) {
           $table->dropSoftDeletes();
       });
   }
   ```

2. Update model Post:

   ```php
   <?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;
   use Illuminate\Database\Eloquent\SoftDeletes;

   class Post extends Model
   {
       use HasFactory, SoftDeletes;

       // ... existing code ...

       // Include trashed posts in specific queries
       public function scopeWithTrashed($query)
       {
           return $query->withTrashed();
       }

       // Only trashed posts
       public function scopeOnlyTrashed($query)
       {
           return $query->onlyTrashed();
       }
   }
   ```

3. Update PostController untuk handle soft deletes:

   ```php
   public function destroy(Post $post)
   {
       if ($post->user_id !== Auth::id()) {
           abort(403, 'Unauthorized');
       }

       // Soft delete
       $post->delete();

       return redirect()->route('posts.index')
           ->with('success', 'Post moved to trash!');
   }

   public function trash()
   {
       // Show trashed posts
       $trashedPosts = Auth::user()->posts()->onlyTrashed()->get();

       return view('posts.trash', compact('trashedPosts'));
   }

   public function restore($id)
   {
       $post = Post::onlyTrashed()->findOrFail($id);

       if ($post->user_id !== Auth::id()) {
           abort(403, 'Unauthorized');
       }

       $post->restore();

       return redirect()->route('posts.trash')
           ->with('success', 'Post restored successfully!');
   }

   public function forceDelete($id)
   {
       $post = Post::onlyTrashed()->findOrFail($id);

       if ($post->user_id !== Auth::id()) {
           abort(403, 'Unauthorized');
       }

       // Permanently delete
       $post->forceDelete();

       return redirect()->route('posts.trash')
           ->with('success', 'Post permanently deleted!');
   }
   ```

4. Routes:

   ```php
   Route::middleware('auth')->group(function () {
       Route::get('/posts/trash', [PostController::class, 'trash'])->name('posts.trash');
       Route::post('/posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
       Route::delete('/posts/{id}/force-delete', [PostController::class, 'forceDelete'])->name('posts.force-delete');
   });
   ```

5. Buat view untuk trash page

6. Test soft deletes behavior:

   ```php
   $post = Post::find(1);

   // Soft delete
   $post->delete();

   // Post masih ada di database tapi deleted_at terisi
   Post::find(1); // null
   Post::withTrashed()->find(1); // masih ada

   // Restore
   $post->restore();

   // Force delete (permanent)
   $post->forceDelete();
   ```

**Deliverable:**

- Screenshot migration add soft deletes
- Screenshot model Post dengan SoftDeletes trait
- Screenshot PostController dengan trash methods
- Screenshot routes
- Screenshot halaman trash showing deleted posts
- Screenshot restore post functionality
- Screenshot force delete (permanent)
- Screenshot database showing deleted_at column
- Video demo soft delete flow (2 menit)

---

## Soal 9: Database Seeder dengan Relationships

**Tujuan:** Membuat seeder lengkap untuk testing dengan relationships yang kompleks

**Instruksi:**

1. Buat factories untuk semua models:

   ```bash
   php artisan make:factory ProfileFactory
   php artisan make:factory PostFactory
   php artisan make:factory TagFactory
   php artisan make:factory CommentFactory
   ```

2. Implementasi ProfileFactory:

   ```php
   <?php

   namespace Database\Factories;

   use App\Models\User;
   use Illuminate\Database\Eloquent\Factories\Factory;

   class ProfileFactory extends Factory
   {
       public function definition(): array
       {
           return [
               'user_id' => User::factory(),
               'phone' => fake()->phoneNumber(),
               'address' => fake()->address(),
               'bio' => fake()->paragraph(),
               'birth_date' => fake()->date(),
               'gender' => fake()->randomElement(['male', 'female']),
           ];
       }
   }
   ```

3. Implementasi PostFactory:

   ```php
   public function definition(): array
   {
       $title = fake()->sentence();
       return [
           'user_id' => User::factory(),
           'title' => $title,
           'slug' => \Str::slug($title),
           'content' => fake()->paragraphs(5, true),
           'status' => fake()->randomElement(['draft', 'published']),
           'published_at' => fake()->optional()->dateTime(),
           'views' => fake()->numberBetween(0, 1000),
       ];
   }
   ```

4. Implementasi TagFactory:

   ```php
   public function definition(): array
   {
       $name = fake()->unique()->word();
       return [
           'name' => ucfirst($name),
           'slug' => \Str::slug($name),
           'color' => fake()->hexColor(),
       ];
   }
   ```

5. Implementasi CommentFactory:

   ```php
   use App\Models\Post;

   public function definition(): array
   {
       return [
           'post_id' => Post::factory(),
           'user_id' => User::factory(),
           'content' => fake()->paragraph(),
           'approved' => fake()->boolean(80), // 80% approved
       ];
   }
   ```

6. Buat DatabaseSeeder lengkap:

   ```php
   <?php

   namespace Database\Seeders;

   use Illuminate\Database\Seeder;
   use App\Models\User;
   use App\Models\Post;
   use App\Models\Tag;
   use App\Models\Comment;

   class DatabaseSeeder extends Seeder
   {
       public function run(): void
       {
           // Truncate tables
           \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
           User::truncate();
           Post::truncate();
           Tag::truncate();
           Comment::truncate();
           \DB::table('post_tag')->truncate();
           \DB::table('likes')->truncate();
           \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

           // Create 20 users with profiles
           $users = User::factory(20)
               ->hasProfile()
               ->create();

           // Create specific tags
           $tagNames = ['Laravel', 'PHP', 'JavaScript', 'Vue.js', 'React', 'Database', 'API', 'Backend', 'Frontend', 'DevOps'];
           $tags = collect($tagNames)->map(function($name) {
               return Tag::create([
                   'name' => $name,
                   'slug' => \Str::slug($name),
                   'color' => fake()->hexColor(),
               ]);
           });

           // Create 100 posts
           $posts = collect();
           foreach(range(1, 100) as $i) {
               $post = Post::factory()
                   ->for($users->random())
                   ->create();

               // Attach 1-4 random tags to each post
               $post->tags()->attach(
                   $tags->random(rand(1, 4))->pluck('id')
               );

               $posts->push($post);
           }

           // Create 500 comments
           foreach(range(1, 500) as $i) {
               Comment::factory()
                   ->for($posts->random())
                   ->for($users->random())
                   ->create();
           }

           // Create 1000 likes (random posts and comments)
           foreach(range(1, 1000) as $i) {
               $user = $users->random();

               if(rand(0, 1)) {
                   // Like post
                   $likeable = $posts->random();
               } else {
                   // Like comment
                   $likeable = Comment::inRandomOrder()->first();
               }

               try {
                   $user->like($likeable);
               } catch(\Exception $e) {
                   // Duplicate like, skip
               }
           }

           $this->command->info('Database seeded successfully!');
           $this->command->info('Users: ' . User::count());
           $this->command->info('Posts: ' . Post::count());
           $this->command->info('Tags: ' . Tag::count());
           $this->command->info('Comments: ' . Comment::count());
           $this->command->info('Likes: ' . \DB::table('likes')->count());
       }
   }
   ```

7. Run seeder:

   ```bash
   php artisan db:seed
   ```

8. Buat command untuk reset dan seed:

   ```bash
   php artisan make:command ResetDatabase
   ```

   ```php
   <?php

   namespace App\Console\Commands;

   use Illuminate\Console\Command;

   class ResetDatabase extends Command
   {
       protected $signature = 'db:reset-seed';
       protected $description = 'Drop all tables, run migrations and seed';

       public function handle()
       {
           if ($this->confirm('This will delete all data. Are you sure?')) {
               $this->call('migrate:fresh');
               $this->call('db:seed');
               $this->info('Database reset and seeded successfully!');
           }
       }
   }
   ```

**Deliverable:**

- Screenshot semua factories
- Screenshot DatabaseSeeder lengkap
- Screenshot command ResetDatabase
- Screenshot running db:seed
- Screenshot database showing seeded data
- Screenshot count of all tables
- Screenshot sample data di browser
- Video demo seeding process (2 menit)

---

## Soal 10: Complete Blog System dengan All Relationships

**Tujuan:** Membuat sistem blog lengkap yang menggunakan semua jenis relasi yang sudah dipelajari

**Instruksi:**

1. **Fitur yang harus diimplementasikan:**

   - User authentication (login/register)
   - User profile (One To One)
   - Create/Edit/Delete posts (One To Many)
   - Tag management (Many To Many)
   - Comments system (One To Many nested)
   - Like system (Polymorphic)
   - Dashboard statistics
   - Eager loading optimization
   - Soft deletes

2. **Routes structure:**

   ```php
   // Public routes
   Route::get('/', [HomeController::class, 'index'])->name('home');
   Route::get('/posts/{post:slug}', [HomeController::class, 'show'])->name('posts.public.show');
   Route::get('/tags/{tag:slug}', [HomeController::class, 'byTag'])->name('posts.by-tag');
   Route::get('/authors/{user}', [HomeController::class, 'byAuthor'])->name('posts.by-author');

   // Auth routes
   Route::middleware('auth')->group(function () {
       // Profile
       Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
       Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

       // Posts management
       Route::resource('posts', PostController::class)->except(['index', 'show']);
       Route::get('/my-posts', [PostController::class, 'myPosts'])->name('posts.my');
       Route::get('/posts/trash', [PostController::class, 'trash'])->name('posts.trash');
       Route::post('/posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');

       // Comments
       Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
       Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

       // Likes
       Route::post('/posts/{post}/like', [LikeController::class, 'likePost'])->name('posts.like');
       Route::delete('/posts/{post}/unlike', [LikeController::class, 'unlikePost'])->name('posts.unlike');

       // Tags (admin only)
       Route::resource('tags', TagController::class)->middleware('role:admin');

       // Dashboard
       Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
   });
   ```

3. **Database structure complete:**

   - users (id, name, email, password, role, remember_token)
   - profiles (id, user_id, phone, address, bio, birth_date, gender, avatar)
   - posts (id, user_id, title, slug, content, featured_image, status, published_at, views, deleted_at)
   - tags (id, name, slug, color)
   - post_tag (id, post_id, tag_id)
   - comments (id, post_id, user_id, content, approved)
   - likes (id, user_id, likeable_id, likeable_type)

4. **Views yang harus dibuat:**

   - Home page (list all published posts)
   - Post detail (with comments and like button)
   - Posts by tag
   - Posts by author
   - User profile page
   - Edit profile page
   - Dashboard (statistics)
   - My posts page
   - Create post page
   - Edit post page
   - Trash page
   - Admin: Tag management

5. **Features checklist:**

   - ✅ User registration & login
   - ✅ User can create/edit/delete own posts
   - ✅ User can soft delete posts
   - ✅ User can restore deleted posts
   - ✅ User can add/delete comments
   - ✅ User can like/unlike posts
   - ✅ User can like/unlike comments
   - ✅ View count increment
   - ✅ Posts filtering by tag
   - ✅ Posts filtering by author
   - ✅ Dashboard with statistics
   - ✅ Eager loading implementation
   - ✅ Authorization (only owner can edit/delete)
   - ✅ Responsive design
   - ✅ Search functionality (bonus)
   - ✅ Pagination

6. **Advanced features (bonus):**
   - File upload for featured image
   - File upload for avatar
   - Rich text editor (TinyMCE/CKEditor)
   - Share to social media
   - Reading time estimate
   - Related posts
   - Popular posts widget
   - Tag cloud widget
   - Recent comments widget
   - Email notification

**Deliverable:**

- Complete source code (organized)
- All migrations
- All models with relationships
- All controllers
- All views (responsive)
- Routes file
- Database seeder
- README.md dengan:
  - Installation guide
  - Features list
  - Database ERD
  - Screenshots semua halaman
  - API documentation (jika ada)
- Screenshots (minimal 30):
  - Home page
  - Post detail
  - Comments section
  - Like functionality
  - User profile
  - Dashboard statistics
  - Create post
  - Edit post
  - Delete post (trash)
  - Restore post
  - Tag management
  - Posts by tag
  - Posts by author
  - Mobile responsive views
- Video demo lengkap (5-10 menit):
  - Registration & login
  - Create post with tags
  - Add comments
  - Like/unlike
  - Edit profile
  - Dashboard overview
  - Tag filtering
  - Soft delete & restore
- Performance report:
  - Query count per page (with debugbar)
  - Page load time
  - Database size
- Unit tests (bonus)

**Kriteria Penilaian:**

- **Database Design (20%):** ERD, normalization, foreign keys
- **Relationships (25%):** Correct implementation of all relationship types
- **Functionality (25%):** All features working properly
- **Code Quality (15%):** Clean code, organized, comments
- **UI/UX (10%):** User-friendly, responsive, attractive
- **Documentation (5%):** Complete and clear

---

## Catatan Pengerjaan

**Setup Requirements:**

- Laravel 10+
- PHP 8.1+
- MySQL/MariaDB
- Composer
- Node.js & NPM (untuk frontend assets)

**Best Practices:**

- Gunakan eager loading untuk avoid N+1 query
- Foreign key constraints untuk data integrity
- Validation di setiap form input
- Authorization untuk protect resources
- Soft deletes untuk data yang penting
- Naming convention yang konsisten
- Comment code yang kompleks

**Testing Checklist:**

- Test semua CRUD operations
- Test relationships (create, read, update, delete)
- Test authorization (user can only edit own posts)
- Test edge cases (delete user, cascade delete, etc)
- Test dengan multiple users
- Test performance dengan large dataset

**Command Penting:**

```bash
# Migration
php artisan make:migration create_table_name
php artisan migrate
php artisan migrate:fresh --seed

# Model
php artisan make:model ModelName -m
php artisan make:model ModelName -mf  # with migration and factory

# Controller
php artisan make:controller ControllerName
php artisan make:controller ControllerName --resource

# Seeder & Factory
php artisan make:seeder SeederName
php artisan make:factory FactoryName
php artisan db:seed

# Tinker
php artisan tinker

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

**Selamat Mengerjakan!** 🚀

**Estimasi Waktu:**

- Soal 1-3: Basic Relationships (3-4 jam)
- Soal 4-6: Advanced Relationships (3-4 jam)
- Soal 7-9: Optimization & Advanced Features (3-4 jam)
- Soal 10: Complete Project (6-8 jam)

**Tips:**

- Kerjakan berurutan dari soal 1
- Test setiap relationship sebelum lanjut
- Gunakan Laravel Debugbar untuk monitoring
- Commit git per soal
- Buat documentation yang baik
- Focus on relationships correctness!
