# 📚 Mercatino Libri - Piattaforma per Libri Scolastici Usati

Una moderna piattaforma web per la compravendita di libri scolastici usati tra studenti della stessa scuola.

## ✨ Features

- ✅ **Home Page Moderna e Responsive** - Design accattivante con Tailwind CSS
- 🛒 **Compra Libri** - Sfoglia e acquista libri usati a prezzi convenienti
- 💰 **Vendi Libri** - Carica i tuoi libri e guadagna
- 👥 **Community** - Connettiti con studenti della tua scuola
- 🔒 **Sicurezza** - Autenticazione tramite email della scuola
- 📱 **Responsive Design** - Perfetto su desktop, tablet e mobile
- 🎨 **SEO Optimized** - Meta tags e structured data
- 🌙 **Dark Mode Ready** - Supporto per tema scuro

## 🛠️ Tech Stack

- **Backend**: Laravel 11+ (PHP)
- **Frontend**: Tailwind CSS 4.0, Blade Templates
- **Database**: PostgreSQL
- **Build Tool**: Vite 5
- **Language**: Italian 🇮🇹

## 📋 Requisiti di Sistema

- PHP >= 8.2
- Composer
- Node.js >= 18
- PostgreSQL >= 12
- npm o yarn

## 🚀 Installazione e Setup

### 1. Clone Repository
```bash
git clone <repository-url>
cd mercatino
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install JavaScript Dependencies
```bash
npm install
```

### 4. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Modifica il file `.env` con le tue credenziali database:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mercatino_libri
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 5. Database Setup
```bash
php artisan migrate
php artisan seed
```

### 6. Build Assets
```bash
npm run build
```

Per development con hot reload:
```bash
npm run dev
```

### 7. Start Development Server
```bash
php artisan serve
```

Accedi all'applicazione su: `http://localhost:8000`

## 📁 Struttura Progetto

```
mercatino/
├── app/
│   ├── Http/Controllers/    # Controller (HomeController, etc)
│   └── Models/             # Eloquent Models
├── resources/
│   ├── css/                # Tailwind CSS
│   ├── js/                 # JavaScript
│   └── views/              # Blade templates
│       ├── layouts/        # Layout principale
│       └── home.blade.php  # Home page
├── routes/
│   └── web.php             # Web routes
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/            # Data seeders
├── config/                 # Configuration files
└── public/                 # Public assets
```

## 🎨 Personalizzazione Design

I colori e le font sono configurati in:
- `tailwind.config.js` - Configurazione Tailwind
- `resources/css/app.css` - CSS global
- `resources/views/layouts/app.blade.php` - CSS inline personalizzato

### Cambiare Colori Primari
Nel file `tailwind.config.js`:
```javascript
colors: {
  primary: '#667eea',      // Cambia questo
  secondary: '#764ba2',    // E questo
}
```

E nel file `resources/views/layouts/app.blade.php`:
```css
.gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

## 📱 Responsive Design

La home page è completamente responsive:
- 📱 Mobile First approach
- Breakpoints: sm (640px), md (768px), lg (1024px), xl (1280px)
- Tutti i componenti sono testati su dispositivi reali

## 🔐 SEO Optimization

Implementato:
- ✅ Meta tags per descrizione e keywords
- ✅ Open Graph tags per social sharing
- ✅ Semantic HTML5
- ✅ Schema.org structured data ready
- ✅ Mobile-friendly viewport
- ✅ Fast loading times con Vite

Per migliorare ulteriormente:
```bash
php artisan make:model BlogPost --migration  # Per creare sezioni blog
```

## 🧪 Testing

```bash
# Unit tests
php artisan test

# Feature tests con Pest
./vendor/bin/pest
```

## 🚢 Deployment

### Docker
```bash
docker-compose up -d
```

### Production Build
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Migration (Production)
```bash
php artisan migrate --force
```

## 📝 Struttura Database

### Users Table
- id (PK)
- email (unique)
- password
- school_name
- class
- profile_image
- bio
- created_at
- updated_at

### Books Table (upcoming)
- id (PK)
- user_id (FK)
- title
- author
- isbn
- condition
- price
- description
- images
- status (available/sold)
- created_at
- updated_at

## 🔄 API Routes (Coming Soon)
```
GET    /api/books           - Lista libri
POST   /api/books           - Crea libro
GET    /api/books/{id}      - Dettagli libro
PUT    /api/books/{id}      - Aggiorna libro
DELETE /api/books/{id}      - Elimina libro
POST   /api/auth/login      - Login
POST   /api/auth/register   - Registrazione
```

## 🤝 Contribuire

1. Fork il repository
2. Crea un feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit i cambiamenti (`git commit -m 'Add some AmazingFeature'`)
4. Push al branch (`git push origin feature/AmazingFeature`)
5. Apri una Pull Request

## 📄 Licenza

Questo progetto è distribuito sotto la licenza MIT. Vedi il file `LICENSE` per i dettagli.

## 📞 Supporto

- 📧 Email: info@mercatinolibri.it
- 💬 Chat di supporto: disponibile sulla piattaforma
- 🐛 Bug report: GitHub Issues

## 🙏 Credits

Creato con ❤️ per gli studenti che vogliono risparmiare sui libri.

### Tech Credits
- [Laravel](https://laravel.com) - PHP Framework
- [Tailwind CSS](https://tailwindcss.com) - Utility CSS Framework
- [Vite](https://vitejs.dev) - Build tool
- [PostgreSQL](https://www.postgresql.org) - Database

---

**Versione**: 1.0.0  
**Ultimo aggiornamento**: Aprile 2026  
**Autore**: Mercatino Libri Team
