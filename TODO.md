# TODO — WooCommerce 2.0
> Dernière mise à jour : 27 mars 2026 (session 3)
> Stack : Laravel 13 · PHP 8.3 · MySQL 8 · Blade (Web) · Flutter (API)

---

## PROGRESSION GLOBALE

| Couche          | Progression |
|-----------------|-------------|
| Backend API     | █████████░ 90% |
| Frontend Blade  | █████░░░░░ 50% |
| Global          | ███████░░░ ~70% |

---

## SPRINT 1 — Foundation ✅ FAIT (semaine 1)

- [x] Installation Laravel 13
- [x] Configuration MySQL + Redis
- [x] Migrations complètes (16 tables)
- [x] Models Eloquent (18 models)
- [x] Laravel Sanctum installé
- [x] CORS configuré
- [x] Vite installé
- [x] Structure dossiers Controllers/Api/V1 et Web
- [x] Routes API /api/v1/ complètes (api.php)
- [x] Structure controllers API créée (squelettes)

---

## SPRINT 2 — Catalogue Web ✅ FAIT (semaine 2-3)

### Backend
- [x] `ProductResource` — transformation JSON produit
- [x] `ProductCollection` — listing paginé
- [x] `CategoryResource`
- [x] `BrandResource`
- [x] Logique `ProductController@index` — filtres, tri, pagination, cache
- [x] Logique `ProductController@show` — fiche complète avec attributs, images, variantes
- [x] Logique `CategoryController@index` + `@products`
- [x] Logique `BrandController@index`
- [x] Logique `SearchController@index` — full-text
- [x] `ProductObserver` — auto-slug unique SEO
- [x] Cache Redis catalogue (TTL 10 min, invalidation Observer)
- [x] Cache Redis catégories (TTL 60 min)
- [ ] Index DB : slug, status, category_id (migration séparée si besoin)
- [x] Eager loading `with(['category','images','brand','attributes'])`

### Frontend Blade
- [x] `routes/web.php` — ajouter routes boutique GET
- [x] `ShopController` (web) — index + show
- [x] Intégration `index.html` → `home.blade.php` (données réelles)
- [x] Intégration `shop.html` → `shop.blade.php` avec filtres
- [x] Intégration `single-full-width.html` → `showProduct.blade.php`
- [ ] Composant Blade carte produit (réutilisable)
- [ ] Composant Blade pagination
- [ ] Header/footer depuis `headers.html` → `layouts/partials/`
- [ ] Vite — compiler CSS/JS des templates HTML
- [ ] Upload images produits (Storage + URL complète)

---

## SPRINT 3 — Auth & Compte ✅ FAIT (semaine 4)

### Backend Auth API
- [x] Logique `AuthController@register` — création compte + token
- [x] Logique `AuthController@login` — token par device_name, révocation ancien
- [x] Logique `AuthController@logout` / `@logoutAll`
- [x] Logique `AuthController@verifyEmail`
- [x] OTP Reset — `@forgotPassword` → stocker OTP Redis TTL 15min
- [x] OTP Reset — `@verifyResetCode` → retourner reset_token TTL 10min
- [x] OTP Reset — `@resetPassword` → changer mot de passe
- [x] `RegisterRequest` Form Request (validation)
- [x] `LoginRequest` Form Request
- [x] `ResetPasswordRequest` Form Request
- [x] Middleware `active` — vérifier `is_active = true`
- [x] `throttle:5,1` sur login et forgot-password (routes api.php)
- [x] `DeviceToken` enregistrement FCM token au login
- [x] `UserResource` — format JSON utilisateur
- [x] Logique `AccountController@show` / `@update` / `@avatar`
- [x] Logique `AddressController` — CRUD + set default
- [x] `AddressResource`

### Frontend Blade Auth
- [x] Routes web auth (login, register, logout, forgot, reset)
- [x] Câbler `login.blade.php` avec session Laravel
- [x] Câbler `register.blade.php`
- [x] Page forgot-password (lien email)
- [x] Page reset-password
- [ ] Intégration `my-account.html` → `account/index.blade.php`
- [ ] Page adresses client
- [ ] Page modifier profil / upload avatar

---

## SPRINT 4 — Panier & Checkout ✅ FAIT (semaine 5-6)

### Backend Services
- [x] `CartService` (complet — 9 méthodes)
- [x] `CouponService` — validation (type, montant min, expiration, limite)
- [x] `StockService` — `check()`, `decrement()`, `restore()`
- [x] `OrderService` — createFromCart, generateOrderNumber, calculateTax, applyDiscount
- [x] Logique `CartController` API (utilise CartService)
- [x] Logique `CheckoutController@index` — résumé panier
- [x] Logique `CheckoutController@store` — créer commande + PaymentIntent Stripe
- [x] Logique `CheckoutController@webhook` — vérif signature Stripe
- [x] Event `PaymentConfirmed` → décrémente stock
- [x] Event `OrderPlaced`
- [x] `OrderResource` / `OrderItemResource`
- [x] `CartResource`
- [ ] PDF Facture — DomPDF (OrderController@invoice)
- [ ] Form Requests : `StoreCheckoutRequest`, `CartItemRequest`, `CouponRequest`

### Frontend Blade
- [x] Routes web panier et checkout
- [x] `CartController` (web) — utilise CartService
- [ ] `CheckoutController` (web) — tunnel Blade
- [x] Intégration `cart.html` → panier dynamique (sessions)
- [ ] Intégration checkout → tunnel multi-étapes (adresse → paiement → confirmation)
- [ ] Stripe.js — intégration paiement web côté client
- [ ] Page confirmation commande (success)
- [ ] Mini-cart dans le header (AJAX)

---

## SPRINT 5 — Notifications ✅ FAIT (semaine 7)

- [x] `PushNotificationService` — FCM HTTP v1
- [x] `OrderPlacedNotification` / `OrderShippedNotification` / `OrderDeliveredNotification` / `OrderCancelledNotification`
- [x] `WelcomeNotification` (email bienvenue)
- [x] `PasswordResetNotification` (email OTP)
- [x] Events : `OrderShipped`, `OrderDelivered`, `OrderCancelled`
- [x] `Listener\SendOrderNotification` (écoute Events → email)
- [x] `Listener\SendPushNotification` (écoute Events → FCM)
- [x] `NotificationController@index` / `@markRead` / `@readAll` / `@unreadCount` API
- [x] Alerte rupture stock — `app:stock-alert` Artisan command quotidienne
- [x] WelcomeNotification câblée dans AuthController Web & API
- [x] PasswordResetNotification câblée dans AuthController API

---

## SPRINT 6 — Administration ✅ FAIT (semaine 8-9)

### Backend Admin API
- [x] `Admin\DashboardController` — métriques CA, commandes, clients, stock + top produits
- [x] `Admin\ProductController` — CRUD complet, stock, statut, export
- [x] `Admin\CategoryController` — CRUD + arborescence + protection suppression
- [x] `Admin\OrderController` — liste filtrée, détail, changement statut + events, export
- [x] `Admin\UserController` — liste, détail, toggleActive (protège les admins)
- [x] `Admin\CouponController` — CRUD + code uppercased
- [x] `Admin\ReviewController` — modération + recalcul rating_avg
- [x] `Admin\SettingController` — lire/écrire config boutique (cache 1h)
- [x] `Admin\ReportController` — CA/période, top produits, top clients, stock
- [x] Middleware `role:admin` — `EnsureUserHasRole`

---

## SPRINT 7 — API Flutter Finalisation (semaine 10)

- [x] `NotificationController` API (index, markRead, readAll, unreadCount)
- [ ] `WishlistController` — logique complète (index, store, destroy)
- [ ] `ReviewController@store` — vérification achat (order_items)
- [ ] Endpoint `POST /api/v1/auth/refresh` — token refresh
- [ ] Throttle `5,1` sur `/auth/login` et `/auth/forgot-password`
- [ ] Vérifier Eager loading — 0 N+1 (Laravel Debugbar)
- [ ] Dates ISO 8601 UTC sur tous les retours Resource
- [ ] Images toujours en URL complète (Storage::url)
- [ ] Pagination uniforme (`meta.current_page`, `meta.total`...)

---

## SPRINT 8 — Optimisation & Tests (semaine 11)

### Performances
- [ ] Cache Redis — vérifier TTL et invalidation
- [ ] `ReviewObserver` — recalcule `rating_avg` à chaque approbation
- [ ] Assets Vite — build production

### Tests
- [ ] Tests unitaires `CartService`
- [ ] Tests unitaires `OrderService`
- [ ] Tests unitaires `StockService`
- [ ] Tests feature Auth API — register, login, logout, OTP reset
- [ ] Tests feature Catalogue — listing, filtres, fiche
- [ ] Tests feature Panier — ajout, update, coupon, fusion
- [ ] Tests feature Checkout — création commande, webhook
- [ ] Tests feature Admin — 403 pour role customer
- [ ] Couverture ≥ 70% sur Services

### Sécurité
- [ ] Policies sur ressources sensibles
- [ ] Validation MIME type sur uploads
- [ ] `cost_price` absent des Resources publiques

---

## SPRINT 9 — Recette & Deploy (semaine 12)

### Seeders & Factories
- [ ] `ProductFactory` — produits alimentaires réalistes
- [ ] `CategoryFactory`
- [ ] `OrderFactory` + `OrderItemFactory`
- [ ] `CouponFactory`
- [ ] `ReviewFactory`
- [ ] `DatabaseSeeder` — orchestrer l'ordre
- [ ] Seeders settings boutique (TVA, frais port)

### Documentation & Infrastructure
- [ ] `.env.example` — toutes les variables documentées
- [ ] Collection Postman ou Swagger/OpenAPI
- [ ] `DEPLOY.md` — Nginx, PHP-FPM, Supervisor queues
- [ ] Guide intégration Flutter (auth, tokens, format erreurs)
- [ ] Config Supervisor (queue workers)
- [ ] Pipeline CI/CD GitHub Actions

---

## RÉCAPITULATIF PAR SPRINT

| Sprint | Sujet                     | Durée     | Statut       |
|--------|---------------------------|-----------|--------------|
| 1      | Foundation                | Semaine 1 | ✅ Terminé   |
| 2      | Catalogue Web             | Sem. 2-3  | ✅ Terminé   |
| 3      | Auth & Compte             | Sem. 4    | ✅ Terminé   |
| 4      | Panier & Checkout         | Sem. 5-6  | ✅ Terminé   |
| 5      | Notifications             | Sem. 7    | ✅ Terminé   |
| 6      | Administration            | Sem. 8-9  | ✅ Terminé   |
| 7      | API Flutter Finalisation  | Sem. 10   | 🔄 En cours  |
| 8      | Optimisation & Tests      | Sem. 11   | ⬜ À faire   |
| 9      | Recette & Deploy          | Sem. 12   | ⬜ À faire   |

**Durée totale estimée : 12 semaines**
**Début : Mars 2026 — Fin estimée : Juin 2026**

---

*Cocher chaque item au fur et à mesure. Relancer `cat TODO.md` pour voir l'état à tout moment.*
