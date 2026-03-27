# TODO — WooCommerce 2.0
> Dernière mise à jour : 27 mars 2026 (session 2)
> Stack : Laravel 13 · PHP 8.3 · MySQL 8 · Blade (Web) · Flutter (API)

---

## PROGRESSION GLOBALE

| Couche          | Progression |
|-----------------|-------------|
| Backend API     | █████░░░░░ 55% |
| Frontend Blade  | ████░░░░░░ 40% |
| Global          | ████░░░░░░ ~47% |

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

## SPRINT 2 — Catalogue Web (semaine 2-3)

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

## SPRINT 3 — Auth & Compte (semaine 4)

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
- [ ] `throttle:5,1` sur login et forgot-password
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

## SPRINT 4 — Panier & Checkout (semaine 5-6)

### Backend Services
- [ ] `CartService` :
  - [ ] `getCart()` — session (invité) ou BDD (connecté)
  - [ ] `addItem()` — vérif stock, variante
  - [ ] `updateItem()` — quantité
  - [ ] `removeItem()`
  - [ ] `clear()`
  - [ ] `mergeGuestCart()` — fusion au login
  - [ ] `applyCoupon()` — valider code
  - [ ] `removeCoupon()`
  - [ ] `calculateTotals()` — sous-total, TVA, frais port, réduction, total
- [ ] `CouponService` — validation (type, montant min, expiration, limite)
- [ ] `StockService` — `check()`, `decrement()`
- [ ] `OrderService` :
  - [ ] `createFromCart()` — snapshot produit JSON
  - [ ] `generateOrderNumber()` — CMD-2026-XXXXX
  - [ ] `calculateTax()`
  - [ ] `applyDiscount()`
- [ ] Logique `CartController` API (utilise CartService)
- [ ] Logique `CheckoutController@index` — résumé panier
- [ ] Logique `CheckoutController@store` — créer commande + PaymentIntent Stripe
- [ ] Logique `CheckoutController@webhook` — vérif signature Stripe
- [ ] Event `PaymentConfirmed` → décrémente stock
- [ ] Event `OrderPlaced`
- [ ] `OrderResource` / `OrderItemResource`
- [ ] `CartResource`
- [ ] `CouponResource`
- [ ] PDF Facture — DomPDF (OrderController@invoice)
- [ ] Form Requests : `StoreCheckoutRequest`, `CartItemRequest`, `CouponRequest`

### Frontend Blade
- [ ] Routes web panier et checkout
- [ ] `CartController` (web) — utilise CartService
- [ ] `CheckoutController` (web) — tunnel Blade
- [ ] Intégration `cart.html` → panier dynamique (sessions)
- [ ] Intégration checkout → tunnel multi-étapes (adresse → paiement → confirmation)
- [ ] Stripe.js — intégration paiement web côté client
- [ ] Page confirmation commande (success)
- [ ] Mini-cart dans le header (AJAX)

---

## SPRINT 5 — Notifications (semaine 7)

### Backend
- [ ] `PushNotificationService` — appel FCM HTTP v1
- [ ] Payload FCM : commande créée, expédiée, livrée, annulée, stock, promo
- [ ] `Notification\OrderPlacedNotification` (email + push)
- [ ] `Notification\OrderShippedNotification`
- [ ] `Notification\OrderDeliveredNotification`
- [ ] `Notification\OrderCancelledNotification`
- [ ] `Notification\WelcomeNotification` (email bienvenue)
- [ ] `Notification\PasswordResetNotification` (email OTP)
- [ ] `Listener\SendOrderNotification` (écoute Events)
- [ ] `Listener\SendPushNotification`
- [ ] Queue Redis — tous les emails et push en asynchrone
- [ ] Supervisor config pour workers Queue
- [ ] `NotificationController@index` / `@markRead` / `@readAll` API
- [ ] Alerte rupture stock — Artisan command quotidienne

### Frontend Blade
- [ ] Intégration page notifications (compte client)

---

## SPRINT 6 — Administration (semaine 8-9)

### Backend Admin API
- [ ] Logique `Admin\DashboardController` — métriques CA, commandes, clients, stock, graphique 30j
- [ ] Logique `Admin\ProductController` — CRUD complet, stock, statut, export CSV
- [ ] Logique `Admin\CategoryController` — CRUD + arborescence
- [ ] Logique `Admin\OrderController` — liste filtrée, détail, changement statut, export CSV
- [ ] Logique `Admin\UserController` — liste, détail, toggle-active
- [ ] Logique `Admin\CouponController` — CRUD + suivi utilisation
- [ ] Logique `Admin\ReviewController` — modération (approve/reject)
- [ ] Logique `Admin\SettingController` — lire/écrire config boutique
- [ ] Logique `Admin\ReportController` — CA/période, top produits, top clients, stock
- [ ] Middleware `role:admin` — vérifier role admin/super-admin
- [ ] Form Requests admin : `StoreProductRequest`, `UpdateOrderStatusRequest`...
- [ ] Resources admin (include cost_price pour admin uniquement)
- [ ] Export CSV commandes et produits

### Frontend Blade Admin
- [ ] Câbler `Admin\DashboardController` (web) avec vues existantes
- [ ] Page dashboard — graphiques CA, métriques (Chart.js ou similar)
- [ ] Coupons — CRUD admin (vues manquantes)
- [ ] Avis — modération (approuver/rejeter)
- [ ] Rapports — CA par période, top produits/clients
- [ ] Paramètres boutique — formulaire
- [ ] Commandes admin — changement statut + numéro suivi
- [ ] Gestion commandes — export CSV

---

## SPRINT 7 — API Flutter Finalisation (semaine 10)

- [ ] Vérifier toutes les API Resources (format `success/data/meta`)
- [ ] `refresh` token endpoint
- [ ] Vérifier Eager loading — 0 N+1 (Laravel Telescope)
- [ ] Tester tous les endpoints API avec Postman
- [ ] Vérifier CORS pour origines mobiles
- [ ] Dates ISO 8601 UTC sur tous les retours
- [ ] Images toujours en URL complète (jamais chemin relatif)
- [ ] Pagination uniforme (`meta.current_page`, `meta.total`...)
- [ ] Logique `WishlistController` API
- [ ] Logique `NotificationController` API
- [ ] Logique `ReviewController@store` — vérification achat

---

## SPRINT 8 — Optimisation & Tests (semaine 11)

### Performances
- [ ] Cache Redis — vérifier TTL et invalidation sur tous les modèles
- [ ] Cache settings (TTL 24h)
- [ ] Indexes BDD manquants (vérifier explain sur requêtes lentes)
- [ ] `rating_avg` — Observer `ReviewObserver` (approuve → recalcule)
- [ ] Assets Vite — build production (minifié, haché)

### Tests
- [ ] Tests unitaires `CartService` — calcul totaux, coupon, frais port
- [ ] Tests unitaires `OrderService` — création, TVA, coupon
- [ ] Tests unitaires `StockService` — dispo, décrémentation
- [ ] Tests unitaires `PushNotificationService` — payload FCM
- [ ] Tests feature Auth API — register, login, logout, OTP reset, multi-device
- [ ] Tests feature Catalogue — listing filtres, fiche, recherche
- [ ] Tests feature Panier — ajout, update, suppression, coupon, fusion
- [ ] Tests feature Checkout — création commande, webhook Stripe
- [ ] Tests feature Commandes — historique, détail, ownership (403)
- [ ] Tests feature Admin — 403 pour role customer sur toutes routes admin
- [ ] Tests feature Dashboard — structure JSON métriques
- [ ] Couverture ≥ 70% sur Services
- [ ] 0 test en échec

### Sécurité
- [ ] Audit OWASP top 10
- [ ] Policies sur toutes les ressources sensibles
- [ ] Validation MIME type sur uploads
- [ ] Jamais de `cost_price` dans Resources publiques

---

## SPRINT 9 — Recette & Deploy (semaine 12)

### Seeders & Factories
- [ ] `ProductFactory` — produits alimentaires réalistes
- [ ] `CategoryFactory`
- [ ] `OrderFactory` + `OrderItemFactory`
- [ ] `CouponFactory`
- [ ] `ReviewFactory`
- [ ] `DatabaseSeeder` — orchestrer l'ordre des seeders
- [ ] Seeders pour settings boutique (TVA, frais port)

### Documentation
- [ ] Collection Postman complète (tous les endpoints, body, exemples réponse)
- [ ] Ou Swagger/OpenAPI via `darkaonline/l5-swagger`
- [ ] `DEPLOY.md` — Nginx, PHP-FPM, Supervisor queues
- [ ] Guide intégration Flutter (auth, tokens, format erreurs)

### Infrastructure
- [ ] `.env.example` — toutes les variables documentées
- [ ] Config Nginx recommandée
- [ ] Config Supervisor (queue workers)
- [ ] Config Redis (cache + queue séparés)
- [ ] Pipeline CI/CD GitHub Actions ou `deploy.sh`
- [ ] Tests intégration avec app Flutter
- [ ] Déploiement production

---

## RÉCAPITULATIF PAR SPRINT

| Sprint | Sujet                     | Durée     | Statut       |
|--------|---------------------------|-----------|--------------|
| 1      | Foundation                | Semaine 1 | ✅ Terminé   |
| 2      | Catalogue Web             | Sem. 2-3  | 🔄 Partiel   |
| 3      | Auth & Compte             | Sem. 4    | 🔄 Partiel   |
| 4      | Panier & Checkout         | Sem. 5-6  | ⬜ À faire   |
| 5      | Notifications             | Sem. 7    | ⬜ À faire   |
| 6      | Administration            | Sem. 8-9  | ⬜ À faire   |
| 7      | API Flutter Finalisation  | Sem. 10   | ⬜ À faire   |
| 8      | Optimisation & Tests      | Sem. 11   | ⬜ À faire   |
| 9      | Recette & Deploy          | Sem. 12   | ⬜ À faire   |

**Durée totale estimée : 12 semaines**
**Début : Mars 2026 — Fin estimée : Juin 2026**

---

*Cocher chaque item au fur et à mesure. Relancer `cat TODO.md` pour voir l'état à tout moment.*
