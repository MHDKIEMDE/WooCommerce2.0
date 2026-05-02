# Monghetto — Marketplace Multi-Vendeurs

Plateforme marketplace multi-vendeurs construite sur **Laravel 12 / PHP 8.3**.  
Deux canaux parallèles : un **dashboard admin Blade** et une **API REST `/api/v1/`** pour l'application mobile Flutter.

---

## Stack technique

| Composant | Technologie |
|-----------|-------------|
| Framework | Laravel 12 · PHP 8.3 |
| Base de données | SQLite (dev) · MySQL 8 (prod optionnel) |
| Authentification | Laravel Sanctum — tokens Bearer multi-device |
| Paiements | Stripe Connect (Express accounts) |
| Notifications push | Firebase FCM HTTP v1 |
| Cache / Queue | Array/Sync (dev) · Redis (prod) |
| Tests | PHPUnit — 63 tests |
| Build frontend | Vite |

---

## Comptes de démonstration

| Rôle | Email | Mot de passe | Accès |
|------|-------|-------------|-------|
| Admin | `admin@example.com` | `admin2026!` | `http://localhost:8000/dashboard` |
| Vendeur | `vendeur@example.com` | `vendeur2026!` | API `/api/v1/seller/*` |
| Acheteur | `client@example.com` | `client2026!` | API `/api/v1/account` |

---

## Installation locale (SQLite)

```bash
# 1. Cloner et installer les dépendances
git clone <repo>
cd ShopAgri
composer install
npm install

# 2. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 3. Base SQLite + migrations + seeders
touch database/database.sqlite
php artisan migrate --seed

# 4. Lien de stockage pour les images
php artisan storage:link

# 5. Lancer les serveurs
php artisan serve        # http://localhost:8000
npm run dev              # Vite hot reload
```

> Pour MySQL : modifier `.env` avec `DB_CONNECTION=mysql`, `DB_DATABASE=Shop`, `DB_USERNAME=...`, `DB_PASSWORD=...`

---

## Variables d'environnement clés

```env
APP_NAME=Monghetto
APP_URL=http://localhost:8000
ROOT_DOMAIN=monghetto.com          # Détection sous-domaines boutiques *.monghetto.com

# Base de données
DB_CONNECTION=sqlite               # sqlite (dev) | mysql (prod)
DB_DATABASE=/chemin/vers/database/database.sqlite

# Cache & Queue
CACHE_STORE=array                  # array (dev) | redis (prod)
QUEUE_CONNECTION=sync              # sync (dev) | redis (prod)

# Stripe Connect
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Firebase FCM
FCM_PROJECT_ID=your-project-id
FCM_CREDENTIALS_PATH=/chemin/vers/firebase-service-account.json

# Sanctum
SANCTUM_TOKEN_EXPIRATION=43200     # 30 jours en minutes
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
```

---

## Commandes utiles

```bash
# Développement
php artisan serve                               # Démarrer le serveur local
php artisan migrate:fresh --seed               # Reset complet DB + seeders
php artisan db:seed --class=ShopTemplateSeeder # Seeder des 7 niches + 35 palettes
php artisan storage:link                       # Lien public/storage (images)

# Tests
php artisan test                               # Tous les tests (63)
php artisan test --filter CartServiceTest      # Un test précis
php artisan test --testsuite=Unit              # Unit uniquement
php artisan test --testsuite=Feature           # Feature uniquement

# Maintenance
php artisan route:list                         # Lister toutes les routes
php artisan cache:clear                        # Vider le cache
php artisan config:clear                       # Vider le cache de config
php artisan view:clear                         # Vider les vues compilées
php artisan queue:work                         # Démarrer le worker Redis (prod)
php artisan app:stock-alert                    # Lancer les alertes stock manuellement

# Marketplace
php artisan marketplace:migrate-shop           # Migrer la boutique legacy vers le marketplace
```

---

## Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/V1/                ← API REST Flutter
│   │   │   ├── Auth/              ← Inscription, login, OTP reset
│   │   │   ├── Admin/             ← Dashboard, produits, commandes, litiges
│   │   │   ├── Seller/            ← Espace vendeur (produits, Stripe Connect)
│   │   │   └── ...                ← Marketplace, catalogue, panier, compte
│   │   └── Web/
│   │       ├── Admin/             ← Dashboard Blade admin
│   │       └── ...                ← Storefront public Blade
│   ├── Middleware/
│   │   ├── DetectShop.php         ← Résout le sous-domaine → boutique active
│   │   ├── EnsureShopOwner.php    ← Vérifie que l'utilisateur est propriétaire
│   │   └── EnsureIsSeller.php     ← Vérifie le rôle seller
│   ├── Requests/Api/              ← Validation Form Requests
│   └── Resources/                 ← Transformation JSON (API Resources)
├── Models/
│   ├── Shop.php                   ← Boutique (template, palette, sections, Stripe)
│   ├── ShopTemplate.php           ← 7 niches de boutique
│   ├── ShopPalette.php            ← 35 palettes couleur (5 par niche)
│   ├── ShopSection.php            ← Sections de page boutique (hero, produits…)
│   ├── Product.php · Order.php    ← Multi-boutique via shop_id
│   ├── Dispute.php                ← Litiges acheteur / vendeur / admin
│   └── AbandonedCart.php          ← Suivi paniers abandonnés
├── Services/
│   ├── CartService.php            ← Panier invité + connecté + groupByShop()
│   ├── OrderService.php           ← createForShop() — une Order par boutique
│   ├── StockService.php           ← Vérification et décrémentation du stock
│   ├── CouponService.php          ← Calcul des remises coupons
│   ├── PushNotificationService.php← Envoi FCM Firebase
│   └── WhatsAppService.php        ← Notifications WhatsApp via CallMeBot
├── Events/ · Listeners/           ← OrderPlaced, ShopApproved, DisputeOpened…
├── Console/Commands/
│   ├── TrackAbandonedCartsCommand.php        ← Tâche horaire
│   └── SendAbandonedCartRemindersCommand.php ← Tâche horaire
└── Notifications/                 ← Emails + Push (commandes, boutiques, litiges)

routes/
├── api.php                        ← 110+ endpoints /api/v1/
└── web.php                        ← Dashboard admin Blade + storefront public
```

---

## Rôles utilisateurs

| Rôle | Description | Routes |
|------|-------------|--------|
| `buyer` | Acheteur — catalogue, panier, commandes, litiges, wishlist | `/api/v1/*` |
| `seller` | Vendeur — gestion boutique, produits, Stripe Connect | `/api/v1/seller/*` |
| `admin` | Administrateur — validation boutiques, litiges, rapports | `/api/v1/admin/*` · `/dashboard` |

---

## Marketplace — Fonctionnement

### Boutiques et niches
- **7 templates** : mode, alimentation, digital, artisanat, tech, beauté, générique
- **35 palettes couleur** (5 par niche) avec couleurs primaires, accent, fond et ambiance
- Chaque boutique obtient un sous-domaine `slug.monghetto.com` détecté automatiquement par le middleware `DetectShop`

### Checkout multi-boutique
- Le panier regroupe les articles par boutique avec `CartService::groupByShop()`
- Une `Order` + un `PaymentIntent` Stripe créés **par boutique** au checkout
- Stripe Connect Express : les fonds sont reversés automatiquement au vendeur moins la commission de la plateforme (`application_fee_amount`)

### Flux création boutique (vendeur)
1. Inscription avec `role: seller`
2. `POST /api/v1/shops` → boutique créée, statut `pending`, email de confirmation envoyé
3. Admin approuve depuis `/dashboard/shops` → statut `active`, email vendeur
4. Vendeur connecte Stripe via `POST /api/v1/seller/stripe/connect` → lien onboarding Stripe
5. Vendeur ajoute ses produits `POST /api/v1/seller/products`

---

## Endpoints principaux

### Marketplace (public)
```
GET  /api/v1/marketplace                  ← Accueil : boutiques vedettes, produits, stats
GET  /api/v1/marketplace/shops            ← Liste boutiques (filtre: niche, search, sort)
GET  /api/v1/marketplace/niches           ← 7 niches avec nombre de boutiques
```

### Authentification
```
POST /api/v1/auth/register                ← Champs: name, email, password, role (buyer|seller)
POST /api/v1/auth/login                   ← Champs: email, password, device_name
POST /api/v1/auth/logout          [auth]
POST /api/v1/auth/forgot-password
POST /api/v1/auth/verify-reset-code
POST /api/v1/auth/reset-password
```

### Catalogue (public)
```
GET  /api/v1/products                     ← Filtres: category, brand, shop, niche, min_rating
GET  /api/v1/products/{slug}
GET  /api/v1/categories
GET  /api/v1/search?q=...                 ← Filtres: niche, shop, min_price, max_price
GET  /api/v1/marketplace/niches
```

### Espace vendeur `[auth · role:seller]`
```
GET    /api/v1/seller/dashboard
GET    /api/v1/seller/shop
PATCH  /api/v1/seller/shop
PATCH  /api/v1/seller/shop/template
POST   /api/v1/seller/stripe/connect      ← Lien onboarding Stripe Express
GET    /api/v1/seller/stripe/status
GET    /api/v1/seller/products            ← CRUD produits vendeur
POST   /api/v1/seller/products
PATCH  /api/v1/seller/products/{id}
DELETE /api/v1/seller/products/{id}
GET    /api/v1/seller/disputes
```

### Administration API `[auth · role:admin]`
```
GET    /api/v1/admin/dashboard
GET    /api/v1/admin/shops
PATCH  /api/v1/admin/shops/{id}/approve
PATCH  /api/v1/admin/shops/{id}/suspend
GET    /api/v1/admin/disputes
PATCH  /api/v1/admin/disputes/{id}/resolve
GET    /api/v1/admin/reports/sales
GET    /api/v1/admin/reports/products
GET    /api/v1/admin/reports/customers
```

> Voir `routes/api.php` pour la liste complète des 110+ endpoints.

---

## Format de réponse API

```json
// Succès
{
  "success": true,
  "message": "Produits récupérés.",
  "data": [...],
  "meta": { "current_page": 1, "last_page": 8, "per_page": 15, "total": 112 }
}

// Erreur
{
  "success": false,
  "message": "Données invalides.",
  "errors": { "email": ["L'email est déjà utilisé."] }
}
```

---

## Tests

```bash
php artisan test                           # 63 tests · 0 échec
php artisan test --testsuite=Unit          # CartService, StockService, OrderService, CouponService
php artisan test --testsuite=Feature       # Auth, Catalogue, Panier, Marketplace, Admin
php artisan test --filter MarketplaceApiTest  # Un fichier précis
```

---

## Déploiement VPS (production)

Voir `DEPLOY.md` pour la configuration complète : Nginx wildcard `*.monghetto.com`, SSL Let's Encrypt, Supervisor queue worker, Docker.

```bash
# Première mise en production sur le VPS
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Démarrer le worker de queue (ou via Supervisor)
php artisan queue:work --daemon
```

Variables à renseigner dans `.env` avant déploiement :
- `STRIPE_KEY` / `STRIPE_SECRET` / `STRIPE_WEBHOOK_SECRET`
- `MAIL_HOST` / `MAIL_USERNAME` / `MAIL_PASSWORD`
- `FCM_PROJECT_ID` / `FCM_CREDENTIALS_PATH`
- `WHATSAPP_PHONE` / `WHATSAPP_CALLMEBOT_APIKEY`

---

*Développé par MHDKIEMDE — 2026*
