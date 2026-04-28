# 🏗️ Architettura del Codice - Mercatino Libri

## Panoramica dell'Architettura

Questo progetto segue il pattern **MVC (Model-View-Controller)** di Laravel con una struttura moderna e modulare.

```
Request HTTP
    ↓
Routes (routes/web.php)
    ↓
Controller (app/Http/Controllers/)
    ↓
Model (app/Models/) ← Database Query
    ↓
View (resources/views/)
    ↓
HTML Response
```

## 📂 Struttura Directory

### `/app`
**Logica di business dell'applicazione**

```
app/
├── Http/
│   ├── Controllers/         # Gestori delle richieste HTTP
│   │   ├── HomeController       # Home page del sito
│   │   ├── BookController       # Operazioni sui libri (CRUD)
│   │   ├── TransactionController # Gestione vendite
│   │   └── UserController       # Profilo utente
│   │
│   ├── Requests/            # Form Request Validation
│   ├── Resources/           # API Resources
│   └── Middleware/          # Custom Middleware
│
├── Models/                  # Eloquent Models
│   ├── User.php                # Utente del sistema
│   ├── Book.php                # Modello per i libri
│   └── Transaction.php         # Modello per le vendite
│
├── Services/                # Business Logic (optional)
│   ├── BookService.php         # Logica per i libri
│   └── PaymentService.php      # Integrazione pagamenti
│
└── Traits/                  # Comportamenti riutilizzabili
```

### `/resources`
**Frontend e UI**

```
resources/
├── css/
│   └── app.css             # Tailwind CSS import
│
├── js/
│   └── app.js              # JavaScript entry point
│
└── views/                  # Blade Templates
    ├── layouts/
    │   └── app.blade.php       # Layout principale
    ├── home.blade.php          # Home page
    ├── books/
    │   ├── index.blade.php     # Elenco libri
    │   ├── show.blade.php      # Dettagli libro
    │   └── create.blade.php    # Form creazione libro
    └── auth/
        ├── login.blade.php     # Login
        └── register.blade.php  # Registrazione
```

### `/database`
**Gestione Database**

```
database/
├── migrations/              # Schemi database
│   ├── 2024_04_28_000001_create_users_table.php
│   ├── 2024_04_28_000003_create_books_table.php
│   └── 2024_04_28_000004_create_transactions_table.php
│
├── seeders/                 # Dati di test
│   ├── DatabaseSeeder.php
│   ├── UserSeeder.php
│   └── BookSeeder.php
│
└── factories/               # Factory per testing
    ├── UserFactory.php
    ├── BookFactory.php
    └── TransactionFactory.php
```

### `/routes`
**Definizione delle Rotte**

```
routes/
└── web.php                 # Web routes (HTML responses)

# Struttura tipica:
GET    /                    → HomeController@index
GET    /books               → BookController@index
GET    /books/{id}          → BookController@show
POST   /books               → BookController@store
GET    /books/{id}/edit     → BookController@edit
PUT    /books/{id}          → BookController@update
DELETE /books/{id}          → BookController@destroy
```

---

## 🎯 Modelli Principali (Models)

### User Model
```php
class User extends Authenticatable {
    // Relazioni
    public function booksForSale()       // Libri in vendita
    public function purchasedBooks()    // Libri acquistati
    public function soldTransactions()  // Vendite effettuate
    public function receivedTransactions() // Acquisti effettuati
}
```

### Book Model
```php
class Book extends Model {
    // Relazioni
    public function seller()            // Chi vende
    public function transactions()      // Storico vendite
    
    // Scopes
    public function scopeAvailable()    // Solo disponibili
    public function scopeBySubject($subject) // Per materia
}
```

### Transaction Model
```php
class Transaction extends Model {
    // Relazioni
    public function buyer()
    public function seller()
    public function book()
    
    // Status workflow
    // pending → paid → shipped → delivered → completed
}
```

---

## 🔄 Flusso Dati Tipico

### Scenario: Acquista un Libro

```
1. Utente clicca "Compra"
   ↓
2. BookController@show riceve richiesta
   ↓
3. Controller recupera: Book, Seller, Reviews
   Model.php $book = Book::with('seller')->find($id)
   ↓
4. View (resources/views/books/show.blade.php) riceve dati
   ↓
5. Utente compila form di acquisto
   ↓
6. TransactionController@store riceve richiesta POST
   ↓
7. Validazione dati:
   - Utente autenticato?
   - Libro ancora disponibile?
   - Metodo pagamento valido?
   ↓
8. Transaction creata con status 'pending'
   ↓
9. PaymentService elabora pagamento
   ↓
10. Se pagamento riuscito:
    - Transaction status = 'paid'
    - Book status = 'reserved'
    - Email confermata a buyer e seller
    ↓
11. Redirect a success page con numero ordine
```

---

## 🔐 Autenticazione e Autorizzazione

### Autenticazione
- **Provider**: Eloquent (default Laravel)
- **Verificazione Email**: Richiesta tramite email scolastica
- **Password Hash**: bcrypt

### Autorizzazione (Policies)
```php
// Esempio: Solo il venditore può eliminare il suo libro
public function delete(User $user, Book $book)
{
    return $user->id === $book->seller_id;
}

// Usage in controller:
$this->authorize('delete', $book);
```

### Middleware personalizzati
```php
// app/Http/Middleware/VerifySchoolEmail.php
- Verifica che email termini con dominio della scuola
- Rifiuta registrazioni con email esterne
```

---

## 📋 Form Validation

### Request Classes
```php
// app/Http/Requests/StoreBookRequest.php
class StoreBookRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'isbn' => 'nullable|isbn',
            'price' => 'required|numeric|min:0.01|max:999.99',
            'condition' => 'required|in:like-new,good,fair,poor',
            'images' => 'array|max:5',
            'images.*' => 'image|max:2048'
        ];
    }
}

// Usage nel Controller:
public function store(StoreBookRequest $request)
{
    $validated = $request->validated();
    // Dati già validati!
}
```

---

## 🗃️ Database Relations

### Relazioni One-to-Many
```
Users (1) ──→ (Many) Books
User (1) ──→ (Many) Transactions (come buyer)
User (1) ──→ (Many) Transactions (come seller)
```

### Relazioni Many-to-Many (Futures)
```
Users ←──→ Books (Favorites - futuro)
Users ←──→ Users (Followers/Following - futuro)
```

### Eloquent Relationship Examples
```php
// Un utente ha molti libri
$user->books()          // Tutti i libri
$user->booksForSale()   // Solo libri disponibili

// Un libro appartiene a un utente
$book->seller()         // Ottieni il venditore

// Eager Loading (Ottimizzazione N+1 queries)
$books = Book::with('seller', 'transactions')->get();
// Singola query anziché N+1 queries
```

---

## 🎨 Blade Templates - Sintassi Essenziale

### Variables
```blade
{{ $variabile }}        {{-- Escapat per XSS --}}
{!! $html !!}           {{-- Non escapato, HTML puro --}}
```

### Condizioni
```blade
@if ($book->status === 'available')
    <p>Disponibile</p>
@elseif ($book->status === 'sold')
    <p>Venduto</p>
@else
    <p>Riservato</p>
@endif
```

### Cicli
```blade
@foreach ($books as $book)
    <div>{{ $book->title }}</div>
    @if ($loop->last) <p>Ultimo libro</p> @endif
@endforeach

@forelse ($books as $book)
    <p>{{ $book->title }}</p>
@empty
    <p>Nessun libro trovato</p>
@endforelse
```

### Componenti e Include
```blade
@include('partials.book-card', ['book' => $book])

<x-alert type="success" message="Libro venduto!" />
```

### Auth
```blade
@auth
    <p>Benvenuto, {{ Auth::user()->name }}</p>
@endauth

@guest
    <a href="/login">Accedi</a>
@endguest
```

---

## 🚀 Controller Best Practices

### Naming Convention
```php
// Singolare per risorse
BookController          // per libri
UserController          // per utenti
TransactionController   // per transazioni

// Metodi standard (CRUD)
index()         // GET /resource               - Lista
create()        // GET /resource/create        - Form creazione
store()         // POST /resource              - Salva
show()          // GET /resource/{id}          - Mostra dettagli
edit()          // GET /resource/{id}/edit     - Form modifica
update()        // PUT/PATCH /resource/{id}    - Aggiorna
destroy()       // DELETE /resource/{id}       - Elimina
```

### Esempio Controller
```php
class BookController extends Controller
{
    /**
     * Visualizza elenco libri
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $books = Book::available()
                      ->with('seller')
                      ->paginate(15);
        
        return view('books.index', compact('books'));
    }

    /**
     * Visualizza dettagli libro
     * @param Book $book
     * @return \Illuminate\View\View
     */
    public function show(Book $book)
    {
        $book->increment('views');
        
        return view('books.show', [
            'book' => $book->load('seller', 'transactions'),
            'related' => Book::where('subject', $book->subject)
                             ->limit(4)
                             ->get()
        ]);
    }

    /**
     * Crea nuovo libro
     * @param StoreBookRequest $request
     * @return \Illuminate\Routing\Redirector
     */
    public function store(StoreBookRequest $request)
    {
        $book = Auth::user()->books()->create(
            $request->validated()
        );

        return redirect()->route('books.show', $book)
                        ->with('success', 'Libro creato!');
    }
}
```

---

## 🧪 Testing

### Unit Test Example
```php
<?php

namespace Tests\Unit;

use App\Models\Book;
use Tests\TestCase;

class BookTest extends TestCase
{
    public function test_book_belongs_to_seller()
    {
        $book = Book::factory()->create();
        
        $this->assertTrue($book->seller()->exists());
    }

    public function test_available_scope_filters_books()
    {
        Book::factory()->create(['status' => 'sold']);
        Book::factory()->create(['status' => 'available']);

        $available = Book::available()->get();

        $this->assertCount(1, $available);
    }
}
```

### Feature Test Example
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use Tests\TestCase;

class BookCreationTest extends TestCase
{
    public function test_authenticated_user_can_create_book()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                        ->post('/books', [
                            'title' => 'Matematica 4',
                            'price' => 15.00,
                            'condition' => 'good'
                        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('books', ['title' => 'Matematica 4']);
    }
}

// Esegui con: php artisan test
```

---

## 🔄 Workflow di Sviluppo

### Aggiungere una Nuova Feature

1. **Crea Migration**
   ```bash
   php artisan make:migration add_discount_to_books_table
   php artisan migrate
   ```

2. **Crea Model** (se necessario)
   ```bash
   php artisan make:model Discount -m
   ```

3. **Crea Controller**
   ```bash
   php artisan make:controller DiscountController -r
   # -r flag crea i 7 metodi CRUD standard
   ```

4. **Aggiungi Routes**
   ```php
   Route::resource('discounts', DiscountController::class);
   ```

5. **Crea View**
   ```bash
   mkdir resources/views/discounts
   # Crea: index.blade.php, show.blade.php, etc.
   ```

6. **Scrivi Tests**
   ```bash
   php artisan make:test DiscountTest
   ```

7. **Test Localmente**
   ```bash
   php artisan serve
   npm run dev
   ```

---

## 📚 Resources Utili

- **Laravel Docs**: https://laravel.com/docs
- **Tailwind CSS**: https://tailwindcss.com
- **Blade Components**: https://laravel.com/docs/blade
- **Eloquent ORM**: https://laravel.com/docs/eloquent
- **Testing**: https://laravel.com/docs/testing

---

## 💡 Tips & Tricks

### Query Optimization
```php
// ❌ MALE - N+1 queries
$books = Book::all();
foreach ($books as $book) {
    echo $book->seller->name;  // Query per ogni libro!
}

// ✅ BENE - Eager Loading
$books = Book::with('seller')->get();  // 2 queries totali
```

### Middleware Personalizzato
```php
// app/Http/Middleware/LogActivity.php
public function handle(Request $request, Closure $next)
{
    Activity::create([
        'user_id' => Auth::id(),
        'action' => $request->path(),
    ]);

    return $next($request);
}

// Registra in app/Http/Kernel.php
protected $middleware = [
    LogActivity::class,
];
```

### Helper Functions
```php
// app/Helpers/BookHelper.php
if (!function_exists('format_price')) {
    function format_price($price)
    {
        return '€' . number_format($price, 2, ',', '.');
    }
}

// Usage
{{ format_price($book->price) }}  // € 15,50
```

---

**Versione**: 1.0.0  
**Ultima modifica**: Aprile 2026
