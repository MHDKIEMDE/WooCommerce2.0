# WooCommerce 2.0 — Plateforme E-Commerce

Backend API REST pour une plateforme de vente de produits alimentaires.
Conçu pour être consommé par une application mobile **Flutter** (iOS & Android) et un panneau d'administration.

---

## Stack technique

| Composant | Technologie |
|-----------|-------------|
| Framework | Laravel 12 · PHP 8.3 |
| Base de données | MySQL 8 |
| Authentification | Laravel Sanctum (tokens Bearer multi-device) |
| Paiements | Laravel Cashier · Stripe |
| Notifications push | Firebase FCM HTTP v1 |
| Cache / Queue | Redis |
| Tests | PHPUnit · Pest |
| Build frontend | Vite |

---

## Prérequis

- PHP 8.2+
- Composer 2
- MySQL 8
- Redis
- Node.js + npm

---

## Installation

```bash
# 1. Cloner et installer les dépendances
git clone <repo>
cd WooCommerce2.0
composer install
npm install

# 2. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 3. Configurer la base de données dans .env
# DB_CONNECTION=mysql
# DB_DATABASE=Shop
# DB_USERNAME=...
# DB_PASSWORD=...

# 4. Migrations et seeders
php artisan migrate
php artisan db:seed

# 5. Lancer les serveurs
php artisan serve        # API : http://localhost:8000
npm run dev              # Assets Vite
```

---

## Variables d'environnement clés

```env
APP_NAME=WooCommerce
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_DATABASE=Shop

SANCTUM_TOKEN_EXPIRATION=43200   # 30 jours en minutes

STRIPE_KEY=pk_...
STRIPE_SECRET=sk_...
STRIPE_WEBHOOK_SECRET=whsec_...

FIREBASE_PROJECT_ID=...
FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

QUEUE_CONNECTION=redis
CACHE_STORE=redis
```

---

## Architecture

```
app/
├── Http/
│   ├── Controllers/Api/V1/
│   │   ├── Auth/          ← Inscription, login, OTP reset
│   │   ├── Admin/         ← Dashboard, produits, commandes, rapports
│   │   └── ...            ← Catalogue, panier, compte client
│   ├── Requests/          ← Validation (Form Requests)
│   ├── Resources/         ← Transformation JSON (API Resources)
│   └── Middleware/
├── Models/
├── Services/              ← Logique métier (Cart, Order, Stock, Push...)
├── Events/ · Listeners/   ← OrderPlaced, OrderShipped, PaymentConfirmed...
├── Observers/             ← Slug auto, rating_avg
├── Notifications/         ← Emails + Push FCM
├── Policies/              ← Autorisations par ressource
└── Jobs/                  ← Emails et push en asynchrone

routes/
├── api.php                ← Tous les endpoints /api/v1/
└── console.php            ← Tâches planifiées
```

---

## Endpoints principaux

### Authentification
```
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout              [auth]
POST   /api/v1/auth/logout-all          [auth]
POST   /api/v1/auth/forgot-password
POST   /api/v1/auth/verify-reset-code
POST   /api/v1/auth/reset-password
```

### Catalogue (public)
```
GET    /api/v1/products
GET    /api/v1/products/{slug}
GET    /api/v1/categories
GET    /api/v1/categories/{slug}/products
GET    /api/v1/brands
GET    /api/v1/search?q=...
```

### Panier & Commandes
```
GET    /api/v1/cart
POST   /api/v1/cart/items
POST   /api/v1/checkout
POST   /api/v1/checkout/webhook         [signature Stripe — sans auth]
GET    /api/v1/orders
GET    /api/v1/orders/{id}/invoice
```

### Administration `[auth · role:admin]`
```
GET    /api/v1/admin/dashboard
GET    /api/v1/admin/products           + POST, PATCH, DELETE
GET    /api/v1/admin/orders             + PATCH /{id}/status
GET    /api/v1/admin/users              + PATCH /{id}/toggle-active
GET    /api/v1/admin/reports/sales
```

> Voir [cahier_des_charges.md](cahier_des_charges.md) pour la liste complète des 60+ endpoints.

---

## Format de réponse API

Toutes les réponses respectent ce format uniforme :

**Succès**
```json
{
  "success": true,
  "message": "Produits récupérés avec succès",
  "data": [...],
  "meta": { "current_page": 1, "last_page": 8, "per_page": 15, "total": 112 }
}
```

**Erreur**
```json
{
  "success": false,
  "message": "Les données sont invalides",
  "errors": { "email": ["L'adresse email est déjà utilisée"] }
}
```

---

## Authentification multi-device (Flutter)

Chaque appareil Flutter reçoit son propre token Bearer. Un même `device_name` révoque l'ancien token.

```json
POST /api/v1/auth/login
{
  "email": "user@example.com",
  "password": "...",
  "device_name": "iPhone 15 Pro de Jean",
  "platform": "ios",
  "fcm_token": "..."
}
```

---

## Commandes utiles

```bash
php artisan test                        # Tous les tests
php artisan test --filter NomDuTest     # Un test précis
php artisan route:list                  # Liste des routes
php artisan queue:work                  # Démarrer le worker Redis
php artisan cache:clear                 # Vider le cache
php artisan config:clear                # Vider le cache de config
```

---

## Tests

Objectif de couverture : **≥ 70%** sur les Services critiques.

```bash
php artisan test
php artisan test --coverage
```

---

*Voir [cahier_des_charges.md](cahier_des_charges.md) pour les spécifications complètes.*
*Développé par MHDKIEMDE — Mars 2026*
