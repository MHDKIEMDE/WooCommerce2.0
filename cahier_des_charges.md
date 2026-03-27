# Cahier des Charges — Plateforme E-Commerce
**Stack : Laravel 13 · PHP 8.3 · MySQL 8 · Blade (Web) · Flutter (Mobile)**
**Version : 2.0 — Mars 2026**

---

## Sommaire

1. [Présentation du projet](#1-présentation-du-projet)
2. [Objectifs](#2-objectifs)
3. [Stack technique](#3-stack-technique)
4. [Architecture générale](#4-architecture-générale)
5. [Base de données](#5-base-de-données)
6. [Authentification & Sécurité](#6-authentification--sécurité)
7. [Modules fonctionnels](#7-modules-fonctionnels)
8. [Frontend Web (Blade)](#8-frontend-web-blade)
9. [Endpoints API REST](#9-endpoints-api-rest)
10. [Notifications & Communication](#10-notifications--communication)
11. [Performances & Cache](#11-performances--cache)
12. [Tests](#12-tests)
13. [Planning de développement](#13-planning-de-développement)
14. [Livrables](#14-livrables)
15. [Critères d'acceptation](#15-critères-dacceptation)

---

## 1. Présentation du projet

### 1.1 Contexte

Le projet consiste à développer une **plateforme e-commerce complète** pour la vente de produits alimentaires. Laravel joue un double rôle :

- **Frontend web** via Blade + Vite — site marchand complet accessible depuis n'importe quel navigateur
- **Backend API REST** — serveur consommé par l'application mobile Flutter

Les templates HTML fournis dans `resources/HTML/` servent de base graphique pour toutes les vues Blade du site web.

### 1.2 Clients de la plateforme

| Client | Technologie | Mode de connexion |
|--------|-------------|-------------------|
| **Site web** | Laravel Blade + Vite | Sessions Laravel (web) |
| **App mobile iOS / Android** | Flutter | API REST + Bearer Token Sanctum |
| **App admin (futur v2)** | Flutter ou SPA | API REST + Bearer Token (role:admin) |

### 1.3 Philosophie

> Laravel est à la fois le moteur du site web ET le serveur API. La logique métier est centralisée dans des Services partagés, consommés aussi bien par les Controllers web (Blade) que par les Controllers API (JSON).

### 1.4 Portée

| Inclus | Hors périmètre |
|--------|----------------|
| Site web complet en Blade (templates fournis) | Développement de l'app Flutter |
| API REST versionnée `/api/v1/` | Intégration CMS |
| Authentification web (sessions) + mobile (Sanctum) | Programme de fidélité (v2) |
| Catalogue produits, panier, commandes, paiements Stripe | Marketplace multi-vendeurs (v2) |
| Notifications push FCM | — |
| Dashboard admin (web) | — |

---

## 2. Objectifs

### 2.1 Objectifs fonctionnels

- Permettre la vente en ligne via le site web Blade et l'app Flutter
- Gérer le cycle complet d'une commande (panier → paiement → livraison → facture)
- Offrir une interface d'administration complète pour gérer produits, commandes et clients
- Envoyer des notifications push en temps réel sur les devices Flutter
- Assurer une expérience web fluide, responsive et fidèle aux templates HTML fournis

### 2.2 Objectifs techniques

- Architecture **DRY** : les Services métier sont partagés entre le web et l'API
- API REST robuste, documentée et versionnée (pour Flutter)
- Multi-device : un utilisateur peut être connecté sur plusieurs appareils simultanément
- Performances : temps de réponse < 200 ms sur les pages catalogue (avec cache Redis)
- Frontend web 100% fidèle aux templates HTML de `resources/HTML/`

---

## 3. Stack technique

### 3.1 Backend

| Composant | Technologie | Version | Rôle |
|-----------|-------------|---------|------|
| Framework | Laravel | 13.x | Cœur backend + frontend web |
| Langage | PHP | 8.3 | Runtime |
| Base de données | MySQL | 8.x | Stockage principal |
| ORM | Eloquent | — | Modèles, migrations, relations |
| Auth web | Laravel Fortify / Auth | — | Sessions pour le site Blade |
| Auth API | Laravel Sanctum | — | Tokens Bearer multi-device Flutter |
| Paiements | Laravel Cashier (Stripe) | — | Checkout, webhooks, factures |
| Push notifications | Firebase FCM (HTTP v1) | — | Notifications mobiles |
| Emails | Laravel Mailable + Queue | — | Transactionnel |
| Stockage | Laravel Storage (S3 / local) | — | Images produits, PDF |
| Cache / Queue | Redis | — | Cache, jobs asynchrones |
| Frontend assets | Vite + Tailwind CSS | — | Compilation CSS/JS Blade |
| Tests | PHPUnit + Pest | — | Tests unitaires et fonctionnels |

### 3.2 Frontend Web (Blade)

| Fichier source | Vue Blade cible | Page |
|----------------|-----------------|------|
| `index.html` | `home.blade.php` | Page d'accueil |
| `shop.html` | `shop/index.blade.php` | Catalogue / listing produits |
| `single-full-width.html` | `shop/show.blade.php` | Fiche produit |
| `cart.html` | `cart/index.blade.php` | Panier |
| `checkout.html` | `checkout/index.blade.php` | Tunnel de commande |
| `my-account.html` | `account/index.blade.php` | Espace client |
| `wishlist.html` | `account/wishlist.blade.php` | Liste de souhaits |
| `track-order.html` | `account/orders/track.blade.php` | Suivi de commande |
| `contact.html` | `contact.blade.php` | Page contact |
| `about-us.html` | `about.blade.php` | À propos |
| `headers.html` | `layouts/partials/header.blade.php` | Header commun |
| `blog.html` / `single-blog.html` | `blog/` | Blog (si activé) |

---

## 4. Architecture générale

### 4.1 Structure des répertoires Laravel

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Web/                    ← Controllers Blade (sessions)
│   │   │   ├── HomeController.php
│   │   │   ├── ShopController.php
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── AccountController.php
│   │   │   └── Admin/
│   │   └── Api/V1/                 ← Controllers API JSON (Flutter)
│   │       ├── Auth/
│   │       ├── Admin/
│   │       └── (controllers publics et authentifiés)
│   ├── Requests/          ← Form Requests (partagés web + API)
│   ├── Resources/         ← API Resources (transformation JSON — API only)
│   └── Middleware/
├── Models/
├── Services/              ← Logique métier partagée (web + API)
│   ├── CartService.php
│   ├── OrderService.php
│   ├── StockService.php
│   ├── CouponService.php
│   └── PushNotificationService.php
├── Events/
├── Listeners/
├── Observers/
├── Notifications/
├── Policies/
└── Jobs/

resources/
├── views/                 ← Blade templates
│   ├── layouts/
│   │   ├── app.blade.php
│   │   └── admin.blade.php
│   ├── home.blade.php
│   ├── shop/
│   ├── cart/
│   ├── checkout/
│   ├── account/
│   └── admin/
├── HTML/                  ← Templates HTML source (référence graphique)
├── css/
└── js/

routes/
├── web.php               ← Routes Blade (sessions)
├── api.php               ← Routes API REST /api/v1/
└── console.php           ← Scheduler
```

### 4.2 Principe du partage des Services

```
                    ┌──────────────────────────────┐
                    │         Services/             │
                    │  CartService, OrderService... │
                    └────────────┬─────────────────┘
                                 │
              ┌──────────────────┴──────────────────┐
              │                                      │
   ┌──────────▼──────────┐               ┌──────────▼──────────┐
   │  Web/Controllers    │               │  Api/V1/Controllers  │
   │  (session + Blade)  │               │  (token + JSON)      │
   └─────────────────────┘               └──────────────────────┘
              │                                      │
   ┌──────────▼──────────┐               ┌──────────▼──────────┐
   │  Blade Templates    │               │  API Resources JSON  │
   │  (HTML → .blade.php)│               │  (pour Flutter)      │
   └─────────────────────┘               └──────────────────────┘
```

### 4.3 Format de réponse API standard (Flutter)

**Succès**
```json
{
  "success": true,
  "message": "Produits récupérés avec succès",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 8,
    "per_page": 15,
    "total": 112
  }
}
```

**Erreur**
```json
{
  "success": false,
  "message": "Les données sont invalides",
  "errors": {
    "email": ["L'adresse email est déjà utilisée"],
    "password": ["Le mot de passe doit contenir au moins 8 caractères"]
  }
}
```

### 4.4 Règles d'architecture

1. **Services** : toute la logique métier est dans des classes `App\Services\*` — partagées entre web et API
2. **Form Requests** : chaque action de mutation a sa propre Form Request
3. **API Resources** : tous les retours JSON API passent par une Resource
4. **Blade** : les vues web consomment les données directement depuis les Controllers (pas de Resource)
5. **Observers** : génération automatique de slugs, mise à jour de `rating_avg`
6. **Events / Listeners** : `OrderPlaced`, `OrderShipped`, `OrderDelivered`, `PaymentConfirmed`
7. **Policies** : autorisations par ressource — `ProductPolicy`, `OrderPolicy`, `ReviewPolicy`
8. **Versioning API** : toutes les routes API sous `/api/v1/`
9. **Dates** : toutes en ISO 8601 UTC — `"2026-03-27T14:30:00Z"`
10. **Images** : toujours retournées en URL complète
11. **CORS** : configuré pour les apps mobiles (origines multiples)

---

## 5. Base de données

### 5.1 Schéma des tables

#### `users`
Extension de la table par défaut Laravel.

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint PK | — |
| name | string | Nom complet |
| email | string unique | — |
| password | string | Hash bcrypt |
| role | enum(customer, admin, super-admin) | default: customer |
| phone | string nullable | — |
| avatar | string nullable | Chemin fichier |
| fcm_token | string nullable | Token Firebase principal |
| is_active | boolean | default: true |
| last_login_at | timestamp nullable | — |
| email_verified_at | timestamp nullable | — |

#### `device_tokens`
Gestion multi-device Flutter.

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint PK | — |
| user_id | FK → users | — |
| token | string | Token FCM |
| platform | enum(android, ios, web) | — |
| device_name | string | Ex : "iPhone 15 Pro de Jean" |
| last_used_at | timestamp | — |

#### `categories`

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint PK | — |
| name | string | — |
| slug | string unique | URL-friendly |
| description | text nullable | — |
| image_url | string nullable | URL complète |
| parent_id | FK nullable self | Arborescence |
| sort_order | int | default: 0 |
| is_active | boolean | default: true |

#### `products`

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint PK | — |
| name | string | — |
| slug | string unique | Auto-généré via Observer |
| description | longText | — |
| short_description | text nullable | Affiché en listing |
| price | decimal(10,2) | Prix de vente HT |
| compare_price | decimal nullable | Prix barré |
| cost_price | decimal nullable | Prix de revient (admin only) |
| sku | string unique nullable | Code article |
| stock_quantity | int | default: 0 |
| low_stock_threshold | int | default: 5 |
| category_id | FK → categories | — |
| brand_id | FK nullable → brands | — |
| status | enum(active, draft, archived) | default: draft |
| featured | boolean | default: false |
| rating_avg | decimal(3,2) | Dénormalisé, mis à jour par Observer |
| rating_count | int | default: 0 |
| weight | decimal nullable | En grammes |
| unit | string | default: 'pièce' |
| vat_rate | decimal(5,2) | default: 20.00 |
| meta_title | string nullable | SEO |
| meta_description | text nullable | SEO |
| deleted_at | timestamp nullable | SoftDeletes |

#### `product_attributes`
Système d'attributs dynamiques — clé de l'adaptabilité multi-produits.

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint PK | — |
| product_id | FK → products | — |
| attribute_name | string(100) | Ex : "Allergènes", "DLC", "Poids" |
| attribute_value | text | Ex : "Gluten, Lait", "30 jours", "500g" |
| sort_order | int | default: 0 |

> **Index** : `(product_id, attribute_name)`

#### `product_variants`

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint PK | — |
| product_id | FK → products | — |
| name | string | Ex : "500g", "1kg", "Pack x6" |
| sku | string unique nullable | — |
| price_modifier | decimal | Ajout/réduction par rapport au prix de base |
| stock_quantity | int | — |
| attributes | JSON | Ex : `{"poids": "500g", "bio": true}` |
| is_active | boolean | — |

#### `orders`

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint PK | — |
| user_id | FK nullable (set null) | — |
| order_number | string unique | Ex : "CMD-2026-00042" |
| status | enum(pending, processing, shipped, delivered, cancelled, refunded) | — |
| subtotal | decimal | — |
| shipping_cost | decimal | default: 0 |
| tax_amount | decimal | — |
| discount_amount | decimal | default: 0 |
| total | decimal | — |
| shipping_address | JSON | Snapshot adresse |
| billing_address | JSON | Snapshot adresse |
| payment_method | string | Ex : "stripe" |
| payment_status | enum(pending, paid, failed, refunded) | — |
| payment_reference | string nullable | ID Stripe |
| coupon_id | FK nullable | — |
| tracking_number | string nullable | — |
| notes | text nullable | — |
| shipped_at | timestamp nullable | — |
| delivered_at | timestamp nullable | — |
| cancelled_at | timestamp nullable | — |

#### `order_items`

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint PK | — |
| order_id | FK cascade | — |
| product_id | FK nullable (set null) | — |
| variant_id | FK nullable (set null) | — |
| product_name | string | Snapshot nom |
| product_sku | string nullable | Snapshot SKU |
| quantity | int | — |
| unit_price | decimal | Snapshot prix |
| total_price | decimal | — |
| vat_rate | decimal | Snapshot TVA |
| product_snapshot | JSON | Snapshot complet du produit au moment de l'achat |

#### Autres tables

| Table | Colonnes clés | Rôle |
|-------|--------------|------|
| `brands` | id, name, slug, logo_url | Marques produits |
| `cart_items` | user_id nullable, session_id nullable, product_id, variant_id, quantity | Panier (invité + connecté) |
| `addresses` | user_id, type(shipping/billing), label, first_name, last_name, street, city, zip, country, is_default | Carnet d'adresses |
| `wishlists` | user_id, product_id (UNIQUE pair) | Favoris |
| `coupons` | code unique, type(percent/fixed), value, min_order, usage_limit, used_count, expires_at | Codes promo |
| `reviews` | product_id, user_id, order_id nullable, rating(1-5), comment, approved_at | Avis vérifiés |
| `notifications_log` | user_id, type, title, body, data JSON, read_at | Historique push |
| `product_images` | product_id, url, alt, sort_order | Images produits multiples |
| `settings` | key unique, value, group | Config boutique |

### 5.2 Adaptabilité multi-produits

Le système est conçu pour ne jamais nécessiter de migration SQL lors d'un changement de secteur :

| Secteur | Attributs dynamiques (`product_attributes`) | Variantes (`product_variants.name`) |
|---------|---------------------------------------------|-------------------------------------|
| Alimentaire | Poids, Allergènes, DLC, Origine, Bio, Conservation | 500g, 1kg, Pack x6 |
| Textile | Taille, Couleur, Matière, Guide des tailles | S, M, L, XL |
| High-tech | Référence, Garantie, Compatibilité, RAM | 128 Go, 256 Go |
| Cosmétique | Volume, Type de peau, Ingrédients | 50mL, 100mL |

---

## 6. Authentification & Sécurité

### 6.1 Authentification Web (Blade — sessions)

L'authentification du site web utilise les **sessions Laravel** (cookies) via Laravel Fortify ou le système Auth intégré.

```
POST /login          → Session + redirection
POST /register       → Création compte + session
POST /logout         → Destruction session
GET  /email/verify   → Vérification email
POST /forgot-password → Lien reset par email
POST /reset-password  → Nouveau mot de passe
```

### 6.2 Authentification API (Flutter — Sanctum tokens)

Chaque appareil Flutter reçoit son **propre token Bearer** nommé avec `device_name`.

```
POST /api/v1/auth/login
Body : { email, password, device_name, platform, fcm_token }
→ Retourne : { token, token_type: "Bearer", expires_at, user }
```

**Comportement :**
- Un login sur le même `device_name` révoque l'ancien token
- `/api/v1/auth/logout` — révoque uniquement le token du device courant
- `/api/v1/auth/logout-all` — révoque tous les tokens
- Les tokens expirent après 30 jours (configurable via `.env`)

### 6.3 Reset de mot de passe API — OTP mobile

```
1. POST /api/v1/auth/forgot-password  → envoie OTP par email (TTL 15 min, stocké Redis)
2. POST /api/v1/auth/verify-reset-code → vérifie OTP → retourne reset_token (TTL 10 min)
3. POST /api/v1/auth/reset-password   → nouveau mot de passe avec reset_token
```

### 6.4 Middleware et rôles

| Middleware | Rôle |
|-----------|------|
| `auth` | Authentification web (session) |
| `auth:sanctum` | Vérifie le token Bearer API |
| `role:admin` | Vérifie que l'utilisateur est admin ou super-admin |
| `verified` | Vérifie que l'email est confirmé |
| `active` | Vérifie que le compte n'est pas suspendu (`is_active = true`) |
| `throttle:5,1` | Rate limit login (5 tentatives/minute) |

### 6.5 Sécurité générale

| Risque | Mesure |
|--------|--------|
| Injection SQL | Eloquent ORM — aucune requête brute |
| XSS | Blade échappe automatiquement `{{ }}` |
| CSRF | Protection CSRF active sur toutes les routes web |
| Accès non autorisé | Policies Laravel sur toutes les ressources |
| Brute force | Throttle sur login et forgot-password |
| Upload malveillant | Validation MIME type + extension |
| Données sensibles | Jamais de `cost_price` dans les Resources publiques |
| Webhook Stripe | Vérification de la signature `Stripe-Signature` |
| CORS | Configuré pour les apps mobiles (origines multiples) |
| Tokens API | Aucun token en clair en base (hashés par Sanctum) |

---

## 7. Modules fonctionnels

### 7.1 Catalogue produits

- Listing paginé avec filtres : catégorie, marque, fourchette de prix, statut stock, mise en avant, recherche full-text
- Fiche produit complète : attributs dynamiques, images multiples, variantes, note moyenne
- Tri : prix croissant/décroissant, nouveautés, popularité, note
- Auto-génération du slug via Observer (unique, SEO-friendly)
- `rating_avg` et `rating_count` mis à jour automatiquement à chaque avis approuvé

### 7.2 Panier

- Panier en session (`session_id`) pour les visiteurs non connectés (web)
- Panier en base (`user_id`) pour les utilisateurs connectés (web + API)
- Fusion automatique du panier invité au moment du login
- Vérification du stock à chaque ajout et au moment du checkout
- Support des variantes (`variant_id`)
- Application d'un code promo avec validation (type, montant minimum, expiration, limite d'utilisation)
- Calcul en temps réel : sous-total, réduction, TVA, frais de port, total

### 7.3 Commandes & Checkout

**Workflow de statuts :**
```
pending → processing → shipped → delivered
                    ↘ cancelled
                              ↘ refunded
```

**Web :** tunnel Blade multi-étapes (adresse → paiement → confirmation)
**API :** création commande → `client_secret` Stripe → paiement côté Flutter

- Création de commande depuis le panier (snapshot produit en JSON)
- Intégration Stripe : `PaymentIntent` pour web et mobile
- Webhook Stripe confirme le paiement → déclenche l'Event `PaymentConfirmed`
- Génération automatique du numéro de commande (`CMD-2026-XXXXX`)
- Génération de la facture PDF à la demande (DomPDF)
- Décrémentation du stock après paiement confirmé

### 7.4 Espace client

- Profil : modification des informations, upload avatar
- Carnet d'adresses : CRUD + définir une adresse par défaut
- Historique des commandes paginé avec détails et téléchargement facture
- Suivi de commande (numéro de suivi)
- Liste de souhaits (favoris)
- Avis produits (uniquement si achat vérifié)
- Historique des notifications push reçues

### 7.5 Administration (dashboard web)

Toutes les routes sous `/admin/` avec middleware `auth + role:admin`.

| Module | Actions |
|--------|---------|
| Dashboard | Métriques CA, commandes, clients, ruptures de stock, graphique 30j |
| Produits | CRUD complet, upload images multiples, gestion stock, export CSV |
| Catégories | CRUD avec arborescence parent/enfant |
| Commandes | Liste filtrée, détail, changement de statut, export CSV |
| Clients | Liste, détail, suspension, historique achats |
| Coupons | CRUD, suivi utilisation |
| Avis | Modération (approbation/rejet) |
| Paramètres | Configuration boutique, TVA, frais de port, moyens de paiement |
| Rapports | CA par période, top produits, top clients, rapport stock |

---

## 8. Frontend Web (Blade)

### 8.1 Principe d'intégration HTML → Blade

Les templates HTML fournis dans `resources/HTML/` sont intégrés en Blade selon ces règles :

1. **Layout principal** : `resources/views/layouts/app.blade.php` — header + footer communs extraits de `headers.html`
2. **Sections** : chaque page utilise `@extends('layouts.app')` + `@section('content')`
3. **Composants** : éléments répétitifs (carte produit, pagination, mini-cart) en `@component` ou Blade components
4. **Assets** : CSS/JS du template compilés via Vite depuis `resources/css/` et `resources/js/`
5. **Images** : assets statiques déplacés dans `public/` ou référencés via `asset()`

### 8.2 Pages web et routes associées

| Route | Vue Blade | Template source |
|-------|-----------|-----------------|
| `GET /` | `home.blade.php` | `index.html` |
| `GET /shop` | `shop/index.blade.php` | `shop.html` |
| `GET /shop/{slug}` | `shop/show.blade.php` | `single-full-width.html` |
| `GET /cart` | `cart/index.blade.php` | `cart.html` |
| `GET /checkout` | `checkout/index.blade.php` | *(dérivé de cart.html)* |
| `GET /checkout/success` | `checkout/success.blade.php` | *(page confirmation)* |
| `GET /account` | `account/index.blade.php` | `my-account.html` |
| `GET /account/orders` | `account/orders/index.blade.php` | `my-account.html` |
| `GET /account/orders/{id}/track` | `account/orders/track.blade.php` | `track-order.html` |
| `GET /wishlist` | `account/wishlist.blade.php` | `wishlist.html` |
| `GET /contact` | `contact.blade.php` | `contact.html` |
| `GET /about` | `about.blade.php` | `about-us.html` |
| `GET /admin/dashboard` | `admin/dashboard.blade.php` | *(interface admin)* |

### 8.3 Routes web (Blade)

```
GET  /                         → HomeController@index
GET  /shop                     → ShopController@index
GET  /shop/{slug}              → ShopController@show
GET  /search                   → ShopController@search

POST /cart/add                 → CartController@add       [web auth optional]
GET  /cart                     → CartController@index
PATCH /cart/items/{id}         → CartController@update
DELETE /cart/items/{id}        → CartController@remove
POST /cart/coupon              → CartController@applyCoupon
DELETE /cart/coupon            → CartController@removeCoupon

GET  /checkout                 → CheckoutController@index  [auth]
POST /checkout                 → CheckoutController@store  [auth]
GET  /checkout/success         → CheckoutController@success
POST /checkout/webhook         → CheckoutController@webhook [sans auth]

GET  /orders                   → AccountController@orders  [auth]
GET  /orders/{id}              → AccountController@order   [auth]
GET  /orders/{id}/invoice      → AccountController@invoice [auth]

GET  /account                  → AccountController@index   [auth]
PATCH /account                 → AccountController@update  [auth]
POST /account/avatar           → AccountController@avatar  [auth]
GET  /account/addresses        → AccountController@addresses [auth]
POST /account/addresses        → AccountController@storeAddress [auth]

GET  /wishlist                 → WishlistController@index  [auth]
POST /wishlist/{product}       → WishlistController@toggle [auth]

GET  /contact                  → ContactController@index
POST /contact                  → ContactController@send
GET  /about                    → PageController@about

GET  /admin/dashboard          → Admin\DashboardController@index [admin]
GET  /admin/products           → Admin\ProductController@index   [admin]
...
```

---

## 9. Endpoints API REST

Destinés exclusivement à l'application **Flutter** (et futurs clients mobiles).

### 9.1 Authentification

```
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout                 [auth:sanctum]
POST   /api/v1/auth/logout-all             [auth:sanctum]
POST   /api/v1/auth/forgot-password
POST   /api/v1/auth/verify-reset-code
POST   /api/v1/auth/reset-password
POST   /api/v1/auth/verify-email           [auth:sanctum]
```

### 9.2 Catalogue (public)

```
GET    /api/v1/products
GET    /api/v1/products/{slug}
GET    /api/v1/categories
GET    /api/v1/categories/{slug}/products
GET    /api/v1/brands
GET    /api/v1/search?q=...
```

### 9.3 Panier (mixte invité / connecté)

```
GET    /api/v1/cart
POST   /api/v1/cart/items
PATCH  /api/v1/cart/items/{id}
DELETE /api/v1/cart/items/{id}
POST   /api/v1/cart/coupon
DELETE /api/v1/cart/coupon
DELETE /api/v1/cart
```

### 9.4 Checkout & Commandes (authentifié)

```
GET    /api/v1/checkout
POST   /api/v1/checkout
POST   /api/v1/checkout/webhook             [sans auth — signature Stripe]
GET    /api/v1/orders
GET    /api/v1/orders/{id}
GET    /api/v1/orders/{id}/invoice
```

### 9.5 Compte client (authentifié)

```
GET    /api/v1/account
PATCH  /api/v1/account
POST   /api/v1/account/avatar
GET    /api/v1/account/addresses
POST   /api/v1/account/addresses
PATCH  /api/v1/account/addresses/{id}
DELETE /api/v1/account/addresses/{id}
PATCH  /api/v1/account/addresses/{id}/default
GET    /api/v1/wishlist
POST   /api/v1/wishlist
DELETE /api/v1/wishlist/{product}
GET    /api/v1/notifications
PATCH  /api/v1/notifications/{id}/read
POST   /api/v1/notifications/read-all
```

### 9.6 Avis produits (authentifié)

```
GET    /api/v1/products/{product}/reviews
POST   /api/v1/products/{product}/reviews   [achat vérifié]
```

### 9.7 Administration API (role:admin)

```
GET    /api/v1/admin/dashboard
GET    /api/v1/admin/products
POST   /api/v1/admin/products
GET    /api/v1/admin/products/{id}
PATCH  /api/v1/admin/products/{id}
DELETE /api/v1/admin/products/{id}
PATCH  /api/v1/admin/products/{id}/stock
PATCH  /api/v1/admin/products/{id}/status
GET    /api/v1/admin/products/export
GET    /api/v1/admin/categories
POST   /api/v1/admin/categories
PATCH  /api/v1/admin/categories/{id}
DELETE /api/v1/admin/categories/{id}
GET    /api/v1/admin/orders
GET    /api/v1/admin/orders/{id}
PATCH  /api/v1/admin/orders/{id}/status
GET    /api/v1/admin/orders/export
GET    /api/v1/admin/users
GET    /api/v1/admin/users/{id}
PATCH  /api/v1/admin/users/{id}/toggle-active
GET    /api/v1/admin/coupons
POST   /api/v1/admin/coupons
PATCH  /api/v1/admin/coupons/{id}
DELETE /api/v1/admin/coupons/{id}
GET    /api/v1/admin/reviews
PATCH  /api/v1/admin/reviews/{id}/approve
DELETE /api/v1/admin/reviews/{id}
GET    /api/v1/admin/settings
PATCH  /api/v1/admin/settings
GET    /api/v1/admin/reports/sales
GET    /api/v1/admin/reports/products
GET    /api/v1/admin/reports/customers
GET    /api/v1/admin/reports/stock
```

---

## 10. Notifications & Communication

### 10.1 Notifications push (Firebase FCM)

Envoyées via `PushNotificationService` qui appelle l'API FCM HTTP v1.

| Déclencheur | Titre | Corps |
|-------------|-------|-------|
| Commande créée | "Commande reçue ✓" | "Votre commande CMD-2026-XXXXX est confirmée" |
| Commande expédiée | "C'est parti !" | "Votre commande est en chemin. Suivi : XXXXX" |
| Commande livrée | "Livraison effectuée" | "Votre commande a bien été livrée" |
| Commande annulée | "Commande annulée" | "Votre commande CMD-XXXXX a été annulée" |
| Retour en stock | "Retour en stock !" | "{Produit} est à nouveau disponible" |
| Promo | "Offre spéciale" | Message configurable depuis l'admin |

### 10.2 Emails transactionnels

| Email | Déclencheur |
|-------|-------------|
| Bienvenue | Inscription |
| Vérification email | Inscription |
| Confirmation de commande | `OrderPlaced` |
| Expédition + numéro de suivi | `OrderShipped` |
| Réinitialisation mot de passe | Reset (lien web / OTP mobile) |
| Facture PDF | Demande client ou confirmation livraison |
| Alerte rupture de stock | Commande Artisan quotidienne (admin) |

Tous les emails sont envoyés via Laravel Queue (Redis) de manière asynchrone.

---

## 11. Performances & Cache

| Stratégie | Implémentation | TTL |
|-----------|---------------|-----|
| Cache catalogue | `Cache::remember('products.index.{hash}', ...)` | 10 minutes |
| Cache catégories | `Cache::remember('categories.tree', ...)` | 60 minutes |
| Cache settings | `Cache::remember('settings.all', ...)` | 24 heures |
| Invalidation | Observer Product/Category → `Cache::forget(...)` | — |
| Index DB | Colonnes : slug, status, category_id, user_id, order_number, session_id | — |
| rating_avg | Dénormalisé sur products (pas de sous-requête à chaque appel) | — |
| Queue | Emails et push notifications en asynchrone via Redis | — |
| Eager loading | `with(['category', 'images', 'attributes'])` pour éviter N+1 | — |
| Assets Blade | Vite en production (CSS/JS minifiés, hachés) | — |

---

## 12. Tests

### 12.1 Tests unitaires (Unit)

- `CartService` : calcul des totaux avec/sans coupon, avec/sans frais de port
- `OrderService` : création commande, calcul TVA, application coupon
- `StockService` : vérification disponibilité, décrémentation
- `PushNotificationService` : formation du payload FCM

### 12.2 Tests fonctionnels (Feature)

- Auth web : inscription, login, logout, reset mot de passe
- Auth API : inscription, login multi-device Sanctum, OTP reset
- Catalogue web : listing, fiche produit, filtres, recherche
- Catalogue API : mêmes cas, format JSON
- Panier : ajout, modification, suppression, coupon, fusion invité→connecté
- Checkout web et API : création commande, webhook Stripe
- Commandes : historique, détail, ownership (accès interdit si pas le bon user)
- Admin : toutes les routes retournent 403 pour un `role:customer`
- Dashboard : structure JSON des métriques
- Changement de statut commande → Event déclenché → Email envoyé

### 12.3 Objectif de couverture

- Couverture minimale : **70%** sur les Services critiques
- 0 test en échec en environnement CI/CD

---

## 13. Planning de développement

| Sprint | Durée | Livrables |
|--------|-------|-----------|
| **Sprint 1** — Foundation | 1 semaine | Installation Laravel 13, config MySQL + Redis, migrations complètes, seeders, Sanctum, CORS, Vite |
| **Sprint 2** — Catalogue web | 1-2 semaines | Intégration HTML→Blade (home, shop, fiche produit), Models, upload images, cache |
| **Sprint 3** — Auth & Compte | 1 semaine | Auth web (sessions), Auth API (Sanctum), profil, adresses, wishlist |
| **Sprint 4** — Panier & Checkout | 1-2 semaines | CartService, Blade cart/checkout, API cart/checkout, Stripe, PDF facture |
| **Sprint 5** — Notifications | 1 semaine | FCM push, emails transactionnels, Queue Redis, Events/Listeners |
| **Sprint 6** — Administration | 1-2 semaines | Dashboard Blade admin, CRUD produits/commandes/users/coupons, rapports, exports CSV |
| **Sprint 7** — API Flutter | 1 semaine | Finalisation tous les endpoints API, API Resources, format JSON uniforme |
| **Sprint 8** — Optimisation & Tests | 1 semaine | Cache Redis, index DB, PHPUnit/Pest (coverage 70%), audit sécurité |
| **Sprint 9** — Recette & Deploy | 1 semaine | Tests intégration Flutter + web, corrections, documentation, déploiement production |

**Durée totale estimée : 9 à 11 semaines**

---

## 14. Livrables

### 14.1 Code source

- Dépôt Git avec branches `main`, `develop`, `feature/*`
- Migrations Laravel complètes (ordre respecté, rollback fonctionnel)
- Seeders réalistes pour les environnements de développement et test
- Factories pour tous les models
- Fichier `.env.example` avec toutes les variables documentées

### 14.2 Documentation API

- Collection **Postman** complète (tous les endpoints, exemples de body, exemples de réponse)
- Ou documentation **Swagger / OpenAPI 3.0** générée via `darkaonline/l5-swagger`

### 14.3 Documentation technique

- `README.md` : installation, prérequis, variables d'environnement, lancement
- `DEPLOY.md` : guide de déploiement en production (Nginx, PHP-FPM, Supervisor)
- Documentation des Events et leur payload (pour le développeur Flutter)
- Guide d'intégration Flutter : authentification, gestion des tokens, format des erreurs

### 14.4 Infrastructure

- Configuration Nginx recommandée
- Configuration Supervisor pour les workers Queue
- Configuration Redis (cache + queue)
- Script de déploiement (`deploy.sh`) ou pipeline CI/CD GitHub Actions

---

## 15. Critères d'acceptation

| Critère | Condition de validation | Priorité |
|---------|------------------------|----------|
| Sécurité | Aucune faille OWASP top 10 — Policies sur toutes les ressources sensibles | Critique |
| Fidélité visuelle | Site web identique aux templates HTML fournis | Critique |
| Auth mobile | Token Sanctum fonctionnel sur Flutter iOS et Android | Critique |
| Auth web | Session Laravel fonctionnelle (login, register, reset) | Critique |
| Parcours commande web | Panier → paiement Stripe → confirmation → email — sans erreur | Critique |
| Parcours commande API | Même parcours via Flutter — sans erreur | Critique |
| Admin | Tous les CRUD admin fonctionnels et sécurisés (403 pour les clients) | Haute |
| Performance | Pages catalogue < 200 ms avec cache Redis activé | Haute |
| Tests | 0 test en échec — couverture ≥ 70% sur les Services | Haute |
| Multi-device | Un utilisateur connecté sur 3 devices simultanément sans conflit | Haute |
| Format API | Toutes les réponses respectent le format standard (success, data, meta) | Haute |
| Notifications push | Push FCM reçu sur Flutter en < 5 secondes après l'événement | Moyenne |
| Documentation | Collection Postman complète — tous les endpoints testables | Moyenne |
| Code | PSR-12, conventions Laravel 13, 0 N+1 query détecté par Telescope | Moyenne |

---

*Document version 2.0 — Mars 2026*
*Toute modification majeure du périmètre fera l'objet d'un avenant.*
