# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Agri-Shop** (internal codename WooCommerce 2.0) is a Laravel 12 / PHP 8.3 food e-commerce platform with two parallel channels:
1. **Blade web storefront** — public shop + admin dashboard
2. **REST API (`/api/v1/`)** — for a Flutter mobile app (Sanctum-authenticated)

## Commands

### PHP / Laravel
```bash
php artisan serve          # Start development server (http://localhost:8000)
php artisan migrate        # Run database migrations
php artisan migrate:fresh --seed  # Full reset with seeders
php artisan db:seed        # Run seeders only
php artisan test           # Run all tests
php artisan test --filter TestName  # Run a single test class or method
php artisan route:list     # List all registered routes
php artisan cache:clear && php artisan config:clear && php artisan view:clear
php artisan app:stock-alert  # Manual stock alert check (also runs via cron)
php artisan queue:work        # Start Redis queue worker (required for emails/push notifications)
php artisan app:install       # Interactive first-run setup wizard
```

### Frontend
```bash
npm run dev    # Start Vite dev server with hot reload
npm run build  # Compile and minify assets to /public/build
```

### Setup
```bash
cp .env.example .env
composer install && npm install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link      # Required for product image URLs
```

### Testing
Tests run against SQLite in-memory (configured in `phpunit.xml`) — no MySQL required.
```bash
php artisan test                         # All suites
php artisan test --testsuite=Unit        # Unit only
php artisan test --testsuite=Feature     # Feature only
php artisan test --filter CartServiceTest  # Single class
```

## Architecture

### Dual-channel design
The app serves both a Blade web frontend and a REST API from the same Laravel codebase, sharing models and services.

- **Web controllers** — [app/Http/Controllers/Web/](app/Http/Controllers/Web/): `ShopController` (public storefront), `CartController`, `CheckoutController`, `AuthController`, `TestimonialController`. Admin controllers live in `Web/Admin/`.
- **API controllers** — [app/Http/Controllers/Api/V1/](app/Http/Controllers/Api/V1/): Full REST API with resources for products, categories, brands, cart, checkout, orders, reviews, wishlist, notifications. Admin sub-namespace at `Api/V1/Admin/`.
- **Services** — [app/Services/](app/Services/): `CartService`, `OrderService`, `StockService`, `CouponService`, `WhatsAppService`, `PushNotificationService`. Services are shared between Web and API controllers.

### Form Requests & Resources
- **Requests** — `app/Http/Requests/Api/` (Auth: `RegisterRequest`, `LoginRequest`, `ResetPasswordRequest`; Account sub-namespace).
- **Resources** — `app/Http/Resources/`: `ProductResource`, `ProductCollection`, `CategoryResource`, `BrandResource`, `CartResource`, `OrderResource`, `OrderItemResource`, `UserResource`, `AddressResource`, plus attribute/image/variant resources. Always use these for API output — never return raw models.

### Models
[app/Models/](app/Models/): `Product`, `Category`, `Brand`, `ProductImage`, `ProductAttribute`, `ProductVariant`, `Order`, `OrderItem`, `CartItem`, `Address`, `Coupon`, `Review`, `Wishlist`, `Testimonial`, `Slide`, `Promotion`, `Setting`, `User`, `DeviceToken`, `NotificationLog`.

`ProductObserver` (`app/Observers/`) auto-generates unique SEO slugs and invalidates the Redis catalogue cache on any product write.

### Settings system
`Setting` model ([app/Models/Setting.php](app/Models/Setting.php)) is a key-value DB store with group support and 60-minute cache. Used for all white-label configuration:
- `Setting::get('key', $default)` / `Setting::set('key', $value, 'group')`
- `Setting::getGroup('group')` / `Setting::setGroup('group', $data)`
- Cache keys: `setting:{key}` and `settings:group:{group}` — remember to `Cache::forget()` after writes.
- Groups in use: `shop` (name, tagline, email, phone, address, currency), `theme` (primary_color, primary_text_color, secondary_color, secondary_text_color), `social` (twitter, facebook, youtube, linkedin, instagram, tiktok), `notifications` (whatsapp_phone, whatsapp_apikey, whatsapp_enabled).

### Routes
- [routes/web.php](routes/web.php): Public storefront + auth + cart + checkout + admin dashboard (`/dashboard` prefix, `auth + role:admin,super-admin` middleware, named `admin.*`).
- [routes/api.php](routes/api.php): `/api/v1/` prefix. Public catalogue/cart routes + Stripe webhook. Authenticated routes use `auth:sanctum + active` middleware. Admin API uses `auth:sanctum + role:admin,super-admin`.

### Authentication
- **Web**: Session-based via [app/Http/Controllers/Web/AuthController.php](app/Http/Controllers/Web/AuthController.php). Guest cart merges into user cart on login via `CartService::mergeGuestCart()`.
- **API**: Sanctum tokens. OTP-based password reset. `EnsureUserIsActive` middleware on all protected API routes. Login requires a `device_name` field — each unique device name gets its own token, and re-using the same `device_name` revokes the previous token.

### API Response Format

All API responses use a uniform envelope:

```json
{ "success": true,  "message": "...", "data": [...], "meta": { "current_page": 1, "last_page": 8, "per_page": 15, "total": 112 } }
{ "success": false, "message": "...", "errors": { "field": ["..."] } }
```

Always wrap API responses in this structure — never return bare arrays or models.

### Frontend (Blade)
- Main layout: [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php) — injects dynamic theme CSS custom properties (`--bs-primary`, `--bs-primary-rgb`, etc.) from `Setting::getGroup('theme')` in `<head>`.
- Admin layout: [resources/views/dashboard/admin/layout/app.blade.php](resources/views/dashboard/admin/layout/app.blade.php).
- Static assets (images, vendor CSS/JS) live in `/public`. Compiled Vite assets go to `/public/build`.

### Events & Notifications
Events: `OrderPlaced`, `OrderShipped`, `OrderDelivered`, `OrderCancelled`, `PaymentConfirmed`.
Listeners: `SendOrderNotification` (email), `SendPushNotification` (FCM via `PushNotificationService`), `DecrementStockOnPayment`.
WhatsApp order alerts via CallMeBot API (`WhatsAppService`), configured from the admin dashboard.

FCM credentials are read from the file path set in `FCM_CREDENTIALS_PATH` env var (a Firebase service-account JSON). Push notifications and email listeners run through the Redis queue — `php artisan queue:work` must be running in production (configure via Supervisor; see `DEPLOY.md`).

### Payments
Stripe via Laravel Cashier. `CheckoutController@store` creates a PaymentIntent and returns `client_secret` to the client. `CheckoutController@webhook` verifies the Stripe signature (`STRIPE_WEBHOOK_SECRET`) and fires `PaymentConfirmed` to decrement stock. The webhook route is public (no Sanctum) — see `routes/api.php`.

### Redis cache TTLs
- Catalogue products: 10 min (invalidated by `ProductObserver`)
- Categories: 60 min
- Settings: 60 min (`setting:{key}` / `settings:group:{group}`)
- OTP reset codes: 15 min (stored directly in Redis)
- Reset tokens: 10 min

### Database
MySQL (`Shop`). Key relationships: `Product` → `ProductImages`, `ProductVariants`, `ProductAttributes`, `Reviews`; `Order` → `OrderItems` (with `product_snapshot` JSON column); `CartItem` supports both guest (session_id) and authenticated (user_id) carts. Product images must be accessed via `Storage::url()` to produce full URLs.

## Key Conventions

- Admin web routes: `/dashboard` prefix, named `admin.*`, guarded by `role:admin,super-admin` middleware.
- Admin API routes: `/api/v1/admin/` prefix, same role guard via Sanctum.
- Cart works for guests (session_id) and authenticated users (user_id). `CartService` handles both transparently.
- `Order->shipping_address` and `OrderItem->product_snapshot` are JSON columns — cast to array in models.
- The `Setting` cache must be cleared after any settings write; `setGroup()` does this automatically but individual `set()` calls only clear the per-key cache, not the group cache.
- Theme colors injected as Bootstrap CSS variables in `<head>` — changing `--bs-primary` propagates to all `.btn-primary`, `.bg-primary`, `.text-primary` classes automatically.
- Some old route definitions in [routes/web.php](routes/web.php) are missing `/` before `{id}` parameters — be careful when adding routes near those.
- `cost_price` must never appear in public API Resources — it is an internal admin field only.
- Image URLs must always be full absolute URLs via `Storage::url()`, never relative paths.
- Sanctum token expiry is controlled by `SANCTUM_TOKEN_EXPIRATION` in `.env` (default 43200 minutes = 30 days). Each `device_name` gets exactly one token; re-using a device_name revokes the previous token automatically.
