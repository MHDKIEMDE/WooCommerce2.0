# Cahier des Charges — Plateforme E-Commerce API REST
**Stack : Laravel 13 · PHP 8.3 · MySQL 8 · Flutter (multi-clients)**
**Version : 1.0 — Mars 2026**

---

## Sommaire

1. [Présentation du projet](#1-présentation-du-projet)
2. [Objectifs](#2-objectifs)
3. [Stack technique](#3-stack-technique)
4. [Architecture générale](#4-architecture-générale)
5. [Base de données](#5-base-de-données)
6. [Authentification & Sécurité](#6-authentification--sécurité)
7. [Modules fonctionnels](#7-modules-fonctionnels)
8. [Endpoints API REST](#8-endpoints-api-rest)
9. [Notifications & Communication](#9-notifications--communication)
10. [Performances & Cache](#10-performances--cache)
11. [Tests](#11-tests)
12. [Planning de développement](#12-planning-de-développement)
13. [Livrables](#13-livrables)
14. [Critères d'acceptation](#14-critères-dacceptation)

---

## 1. Présentation du projet

### 1.1 Contexte

Le projet consiste à développer un **backend API REST** pour une plateforme de vente de produits alimentaires. Ce backend sera le serveur central consommé par plusieurs clients :

- Application mobile **Flutter** (iOS + Android) — client principal
- Futurs clients mobiles ou web (React Native, Vue.js, etc.)
- Panneau d'administration (SPA ou application Flutter dédiée)

**Laravel ne sert aucun frontend.** Il expose uniquement des endpoints JSON.

### 1.2 Philosophie

> Le backend est une API pure. Toute la logique métier vit côté serveur Laravel. Les clients (Flutter, web) ne font que consommer et afficher les données.

### 1.3 Portée

| Inclus | Hors périmètre |
|--------|---------------|
| API REST versionnée `/api/v1/` | Développement des apps Flutter |
| Authentification multi-device Sanctum | Intégration CMS |
| Catalogue produits générique | Programme de fidélité (v2) |
| Panier, commandes, paiements Stripe | Application mobile admin (v2) |
| Notifications push FCM | Marketplace multi-vendeurs (v2) |
| Dashboard admin JSON | — |

---

## 2. Objectifs

### 2.1 Objectifs fonctionnels

- Permettre la vente en ligne de produits alimentaires via une app Flutter
- Gérer le cycle complet d'une commande (panier → paiement → livraison → facture)
- Offrir une interface d'administration complète pour gérer produits, commandes et clients
- Envoyer des notifications push en temps réel sur les devices Flutter

### 2.2 Objectifs techniques

- API REST robuste, documentée et versionnée
- Architecture évolutive : adaptable à tout type de produit sans modifier le schéma SQL
- Multi-client : un seul backend pour plusieurs applications
- Multi-device : un utilisateur peut être connecté sur plusieurs appareils simultanément
- Performances : temps de réponse < 200 ms sur les endpoints catalogue (avec cache Redis)

---

## 3. Stack technique

### 3.1 Backend

| Composant | Technologie | Version | Rôle |
|-----------|-------------|---------|------|
| Framework | Laravel | 13.x | Cœur du backend |
| Langage | PHP | 8.3 | Runtime |
| Base de données | MySQL | 8.x | Stockage principal |
| ORM | Eloquent | — | Modèles, migrations, relations |
| Authentification | Laravel Sanctum | — | Tokens Bearer multi-device |
| Paiements | Laravel Cashier (Stripe) | — | Checkout, webhooks, factures |
| Push notifications | Firebase FCM (HTTP v1) | — | Notifications mobiles |
| Emails | Laravel Mailable + Queue | — | Transactionnel |
| Stockage | Laravel Storage (S3 / local) | — | Images produits, PDF |
| Cache / Queue | Redis | — | Cache, jobs asynchrones |
| Tests | PHPUnit + Pest | — | Tests unitaires et fonctionnels |

### 3.2 Clients (hors périmètre backend)

| Client | Technologie | Connexion |
|--------|-------------|-----------|
| App mobile principale | Flutter (iOS + Android) | API REST + Bearer Token |
| App admin (futur) | Flutter ou SPA | API REST + Bearer Token (role:admin) |
| Autres clients | Tout framework | API REST + Bearer Token |

### 3.3 Ce que Laravel ne fait PAS dans ce projet

- Aucun fichier `.blade.php`
- Aucune vue, layout, composant Blade
- Aucun HTML, CSS, JavaScript généré côté serveur
- Aucune session web (uniquement tokens Sanctum)
- Pas de CSRF sur les routes API

---

## 4. Architecture générale

### 4.1 Structure des répertoires Laravel

```
app/
├── Http/
│   ├── Controllers/Api/V1/
│   │   ├── Auth/
│   │   ├── Admin/
│   │   └── (controllers publics et authentifiés)
│   ├── Requests/          ← Form Requests (validation)
│   ├── Resources/         ← API Resources (transformation JSON)
│   └── Middleware/
├── Models/
├── Services/              ← Logique métier
├── Events/
├── Listeners/
├── Observers/
├── Notifications/         ← Email + Push FCM
├── Policies/
└── Jobs/

routes/
├── api.php               ← Toutes les routes API
└── console.php           ← Scheduler (Laravel 13)
```

### 4.2 Format de réponse standard

Toutes les réponses API respectent ce format uniforme, compatible Flutter :

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

### 4.3 Règles d'architecture

1. **Services** : toute la logique métier est dans des classes `App\Services\*` injectées dans les constructeurs
2. **Form Requests** : chaque action de mutation a sa propre Form Request
3. **API Resources** : tous les retours JSON passent par une Resource (jamais de `$model->toArray()`)
4. **Observers** : génération automatique de slugs, mise à jour de `rating_avg`
5. **Events / Listeners** : `OrderPlaced`, `OrderShipped`, `OrderDelivered`, `PaymentConfirmed`
6. **Policies** : autorisations par ressource — `ProductPolicy`, `OrderPolicy`, `ReviewPolicy`
7. **Versioning** : toutes les routes sous `/api/v1/` ; une future v2 coexistera sans casser v1
8. **Dates** : toutes en ISO 8601 UTC — `"2026-03-27T14:30:00Z"`
9. **Images** : toujours retournées en URL complète (jamais de chemin relatif)
10. **CORS** : configuré pour accepter toutes les origines (apps mobiles)

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

### 6.1 Laravel Sanctum — Multi-device

Chaque appareil Flutter reçoit son **propre token Bearer** nommé avec `device_name`.

```
POST /api/v1/auth/login
Body : { email, password, device_name, platform, fcm_token }
→ Retourne : { token, token_type: "Bearer", expires_at, user }
```

**Comportement :**
- Un login sur le même `device_name` révoque l'ancien token (évite l'accumulation)
- `/api/v1/auth/logout` — révoque uniquement le token du device courant
- `/api/v1/auth/logout-all` — révoque tous les tokens (changement de mot de passe, compte compromis)
- Les tokens expirent après 30 jours (configurable via `.env`)

### 6.2 Reset de mot de passe — OTP mobile

Le flow classique par lien email est inadapté aux apps mobiles. On utilise un **code OTP à 6 chiffres** :

```
1. POST /api/v1/auth/forgot-password  → envoie OTP par email (TTL 15 min, stocké Redis)
2. POST /api/v1/auth/verify-reset-code → vérifie OTP → retourne reset_token (TTL 10 min)
3. POST /api/v1/auth/reset-password   → nouveau mot de passe avec reset_token
```

### 6.3 Middleware et rôles

| Middleware | Rôle |
|-----------|------|
| `auth:sanctum` | Vérifie le token Bearer |
| `role:admin` | Vérifie que l'utilisateur est admin ou super-admin |
| `verified` | Vérifie que l'email est confirmé |
| `active` | Vérifie que le compte n'est pas suspendu (`is_active = true`) |
| `throttle:5,1` | Rate limit login (5 tentatives/minute) |

### 6.4 Sécurité générale

| Risque | Mesure |
|--------|--------|
| Injection SQL | Eloquent ORM — aucune requête brute |
| Accès non autorisé | Policies Laravel sur toutes les ressources |
| Brute force | Throttle sur login et forgot-password |
| Upload malveillant | Validation MIME type + extension |
| Données sensibles | Jamais de `cost_price` dans les Resources publiques |
| Webhook Stripe | Vérification de la signature `Stripe-Signature` |
| CORS | Configuré pour les apps mobiles (toutes origines) |
| Tokens | Aucun token en clair en base (hashés par Sanctum) |

---

## 7. Modules fonctionnels

### 7.1 Catalogue produits

- Listing paginé avec filtres : catégorie, marque, fourchette de prix, statut stock, mise en avant, recherche full-text
- Fiche produit complète : attributs dynamiques, images (URLs complètes), variantes, note moyenne
- Tri : prix croissant/décroissant, nouveautés, popularité, note
- Auto-génération du slug via Observer (unique, SEO-friendly)
- `rating_avg` et `rating_count` mis à jour automatiquement à chaque avis approuvé

### 7.2 Panier

- Panier en session (`session_id`) pour les visiteurs non connectés
- Panier en base (`user_id`) pour les utilisateurs connectés
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

- Création de commande depuis le panier (snapshot produit en JSON)
- Intégration Stripe : création d'un `PaymentIntent` → retour du `client_secret` à Flutter
- Flutter complète le paiement côté client (Stripe SDK Flutter)
- Webhook Stripe confirme le paiement → déclenche l'Event `PaymentConfirmed`
- Génération automatique du numéro de commande (`CMD-2026-XXXXX`)
- Génération de la facture PDF à la demande (DomPDF)
- Décrémentation du stock après paiement confirmé

### 7.4 Espace client (API)

- Profil : modification des informations, upload avatar
- Carnet d'adresses : CRUD + définir une adresse par défaut
- Historique des commandes paginé avec détails et téléchargement facture
- Liste de souhaits (favoris)
- Avis produits (uniquement si achat vérifié)
- Historique des notifications push reçues

### 7.5 Administration (API JSON)

Toutes les routes sous `/api/v1/admin/` avec middleware `auth:sanctum + role:admin`.

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

## 8. Endpoints API REST

### 8.1 Authentification

```
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout                 [auth]
POST   /api/v1/auth/logout-all             [auth]
POST   /api/v1/auth/refresh                [auth]
POST   /api/v1/auth/forgot-password
POST   /api/v1/auth/verify-reset-code
POST   /api/v1/auth/reset-password
POST   /api/v1/auth/verify-email           [auth]
```

### 8.2 Catalogue (public)

```
GET    /api/v1/products
GET    /api/v1/products/{slug}
GET    /api/v1/categories
GET    /api/v1/categories/{slug}/products
GET    /api/v1/brands
GET    /api/v1/search?q=...
```

### 8.3 Panier (mixte invité / connecté)

```
GET    /api/v1/cart
POST   /api/v1/cart/items
PATCH  /api/v1/cart/items/{id}
DELETE /api/v1/cart/items/{id}
POST   /api/v1/cart/coupon
DELETE /api/v1/cart/coupon
DELETE /api/v1/cart
```

### 8.4 Checkout & Commandes (authentifié)

```
GET    /api/v1/checkout
POST   /api/v1/checkout
POST   /api/v1/checkout/webhook             [sans auth — signature Stripe]
GET    /api/v1/orders
GET    /api/v1/orders/{id}
GET    /api/v1/orders/{id}/invoice
```

### 8.5 Compte client (authentifié)

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

### 8.6 Avis produits (authentifié)

```
GET    /api/v1/products/{product}/reviews
POST   /api/v1/products/{product}/reviews   [achat vérifié]
```

### 8.7 Administration (authentifié — role:admin)

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

## 9. Notifications & Communication

### 9.1 Notifications push (Firebase FCM)

Envoyées via `PushNotificationService` qui appelle l'API FCM HTTP v1.

| Déclencheur | Titre | Corps |
|-------------|-------|-------|
| Commande créée | "Commande reçue ✓" | "Votre commande CMD-2026-XXXXX est confirmée" |
| Commande expédiée | "C'est parti !" | "Votre commande est en chemin. Suivi : XXXXX" |
| Commande livrée | "Livraison effectuée" | "Votre commande a bien été livrée" |
| Commande annulée | "Commande annulée" | "Votre commande CMD-XXXXX a été annulée" |
| Rupture de stock | "Retour en stock !" | "{Produit} est à nouveau disponible" |
| Promo | "Offre spéciale" | Message configurable depuis l'admin |

### 9.2 Emails transactionnels

| Email | Déclencheur |
|-------|-------------|
| Bienvenue | Inscription |
| Vérification email | Inscription |
| Confirmation de commande | `OrderPlaced` |
| Expédition + numéro de suivi | `OrderShipped` |
| Réinitialisation mot de passe | OTP reset |
| Facture PDF | Demande client ou confirmation livraison |
| Alerte rupture de stock | Commande Artisan quotidienne (admin) |

Tous les emails sont envoyés via Laravel Queue (Redis) de manière asynchrone.

---

## 10. Performances & Cache

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

---

## 11. Tests

### 11.1 Tests unitaires (Unit)

- `CartService` : calcul des totaux avec/sans coupon, avec/sans frais de port
- `OrderService` : création commande, calcul TVA, application coupon
- `StockService` : vérification disponibilité, décrémentation
- `PushNotificationService` : formation du payload FCM

### 11.2 Tests fonctionnels (Feature)

- Auth : inscription, login, logout, OTP reset, multi-device
- Catalogue : listing avec filtres, fiche produit, recherche
- Panier : ajout, modification, suppression, coupon, fusion invité→connecté
- Checkout : création commande, webhook Stripe
- Commandes : historique, détail, ownership (accès interdit si pas le bon user)
- Admin : toutes les routes retournent 403 pour un `role:customer`
- Dashboard : structure JSON des métriques
- Changement de statut commande → Event déclenché → Email envoyé

### 11.3 Objectif de couverture

- Couverture minimale : **70%** sur les Services critiques
- 0 test en échec en environnement CI/CD

---

## 12. Planning de développement

| Sprint | Durée | Livrables |
|--------|-------|-----------|
| **Sprint 1** — Foundation | 1 semaine | Installation Laravel 13, config MySQL + Redis, migrations complètes, seeders, Sanctum, CORS |
| **Sprint 2** — Catalogue | 1-2 semaines | Models Product/Category/Brand/Attribute, API Resources, endpoints catalogue publics, upload images |
| **Sprint 3** — Auth & Compte | 1 semaine | Register, login multi-device, OTP reset, profil, adresses, wishlist |
| **Sprint 4** — Panier & Checkout | 1-2 semaines | CartService, CheckoutController, Stripe PaymentIntent, webhook, génération PDF facture |
| **Sprint 5** — Notifications | 1 semaine | FCM push (PushNotificationService), emails transactionnels, Queue Redis, Events/Listeners |
| **Sprint 6** — Administration | 1-2 semaines | Dashboard métriques, CRUD admin, gestion commandes/users/coupons, rapports, exports CSV |
| **Sprint 7** — Optimisation & Tests | 1 semaine | Cache Redis, index DB, PHPUnit/Pest (coverage 70%), audit sécurité |
| **Sprint 8** — Recette & Deploy | 1 semaine | Tests intégration avec app Flutter, corrections, documentation API, déploiement production |

**Durée totale estimée : 8 à 10 semaines**

---

## 13. Livrables

### 13.1 Code source

- Dépôt Git avec branches `main`, `develop`, `feature/*`
- Migrations Laravel complètes (ordre respecté, rollback fonctionnel)
- Seeders réalistes pour les environnements de développement et test
- Factories pour tous les models
- Fichier `.env.example` avec toutes les variables documentées

### 13.2 Documentation API

- Collection **Postman** complète (tous les endpoints, exemples de body, exemples de réponse)
- Ou documentation **Swagger / OpenAPI 3.0** générée via `darkaonline/l5-swagger`

### 13.3 Documentation technique

- `README.md` : installation, prérequis, variables d'environnement, lancement
- `DEPLOY.md` : guide de déploiement en production (Nginx, PHP-FPM, Supervisor pour les queues)
- Documentation des Events et leur payload (pour le développeur Flutter)
- Guide d'intégration Flutter : authentification, gestion des tokens, format des erreurs

### 13.4 Infrastructure

- Configuration Nginx recommandée
- Configuration Supervisor pour les workers Queue
- Configuration Redis (cache + queue)
- Script de déploiement (`deploy.sh`) ou pipeline CI/CD GitHub Actions

---

## 14. Critères d'acceptation

| Critère | Condition de validation | Priorité |
|---------|------------------------|----------|
| Sécurité | Aucune faille OWASP top 10 — Policies sur toutes les ressources sensibles | Critique |
| Auth mobile | Token Sanctum fonctionnel sur Flutter iOS et Android | Critique |
| Parcours commande | Panier → paiement Stripe → confirmation → email → push — sans erreur | Critique |
| Admin | Tous les CRUD admin fonctionnels et sécurisés (403 pour les clients) | Haute |
| Performance | Endpoints catalogue < 200 ms avec cache Redis activé | Haute |
| Tests | 0 test en échec — couverture ≥ 70% sur les Services | Haute |
| Multi-device | Un utilisateur connecté sur 3 devices simultanément sans conflit | Haute |
| Format API | Toutes les réponses respectent le format standard (success, data, meta) | Haute |
| Notifications push | Push FCM reçu sur Flutter en < 5 secondes après l'événement | Moyenne |
| Documentation | Collection Postman complète — tous les endpoints testables | Moyenne |
| Code | PSR-12, conventions Laravel 13, 0 N+1 query détecté par Telescope | Moyenne |

---

*Document version 1.0 — Mars 2026*
*Toute modification majeure du périmètre fera l'objet d'un avenant.*
