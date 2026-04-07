# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WooCommerce 2.0 is a Laravel 12 / PHP 8.3 e-commerce application with two parallel channels:
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
```

## Architecture

### Dual-channel design
The app serves both a Blade web frontend and a REST API from the same Laravel codebase, sharing models and services.

- **Web controllers** — [app/Http/Controllers/Web/](app/Http/Controllers/Web/): `ShopController` (public storefront), `CartController`, `CheckoutController`, `AuthController`, `TestimonialController`. Admin controllers live in `Web/Admin/`.
- **API controllers** — [app/Http/Controllers/Api/V1/](app/Http/Controllers/Api/V1/): Full REST API with resources for products, categories, brands, cart, checkout, orders, reviews, wishlist, notifications. Admin sub-namespace at `Api/V1/Admin/`.
- **Services** — [app/Services/](app/Services/): `CartService`, `OrderService`, `StockService`, `CouponService`, `WhatsAppService`, `PushNotificationService`. Services are shared between Web and API controllers.

### Models
[app/Models/](app/Models/): `Product`, `Category`, `Brand`, `ProductImage`, `ProductAttribute`, `ProductVariant`, `Order`, `OrderItem`, `CartItem`, `Address`, `Coupon`, `Review`, `Wishlist`, `Testimonial`, `Slide`, `Promotion`, `Setting`, `User`, `DeviceToken`, `NotificationLog`.

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

### Database
MySQL (`Shop`). 24 migrations. Key relationships: `Product` → `ProductImages`, `ProductVariants`, `ProductAttributes`, `Reviews`; `Order` → `OrderItems` (with `product_snapshot` JSON column); `CartItem` supports both guest (session_id) and authenticated (user_id) carts.

## Key Conventions

- Admin web routes: `/dashboard` prefix, named `admin.*`, guarded by `role:admin,super-admin` middleware.
- Admin API routes: `/api/v1/admin/` prefix, same role guard via Sanctum.
- Cart works for guests (session_id) and authenticated users (user_id). `CartService` handles both transparently.
- `Order->shipping_address` and `OrderItem->product_snapshot` are JSON columns — cast to array in models.
- The `Setting` cache must be cleared after any settings write; `setGroup()` does this automatically but individual `set()` calls only clear the per-key cache, not the group cache.
- Theme colors injected as Bootstrap CSS variables in `<head>` — changing `--bs-primary` propagates to all `.btn-primary`, `.bg-primary`, `.text-primary` classes automatically.
- Some old route definitions in [routes/web.php](routes/web.php) are missing `/` before `{id}` parameters — be careful when adding routes near those.
