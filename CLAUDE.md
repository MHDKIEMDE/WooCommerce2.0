# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WooCommerce 2.0 is a Laravel 10 e-commerce application with Blade templates and Vite for asset compilation. It supports a public storefront (products, cart, checkout, testimonials) and an admin dashboard for managing products, categories, orders, and user content.

## Commands

### PHP / Laravel
```bash
php artisan serve          # Start development server (http://localhost:8000)
php artisan migrate        # Run database migrations
php artisan migrate:fresh  # Drop all tables and re-run migrations
php artisan db:seed        # Run database seeders
php artisan test           # Run all tests
php artisan test --filter TestName  # Run a single test class or method
php artisan route:list     # List all registered routes
php artisan cache:clear    # Clear application cache
php artisan config:clear   # Clear config cache
php artisan view:clear     # Clear compiled views
```

### Frontend
```bash
npm run dev    # Start Vite dev server with hot reload
npm run build  # Compile and minify assets to /public/build
```

### Setup
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install
```

## Architecture

### Backend (Laravel 10)
- **Controllers** — [app/Http/Controllers/](app/Http/Controllers/): 11 controllers split between user-facing and admin logic. `AdminController` handles all dashboard CRUD. `UserController` handles public-facing pages. `ProductController` handles cart/checkout. Separate controllers for `Commande`, `Payment`, `Testimonial`, `Comment`, `Search`, `addToCart`.
- **Models** — [app/Models/](app/Models/): `Product`, `Categorie`, `User`, `Commande`, `Payment`, `Testimonial`, `Comment`, `addToCart`, `ProductImage`, `Search`.
- **Routes** — [routes/web.php](routes/web.php): All routes in one file, grouped by user routes, admin/dashboard routes, testimonials, and search. No auth middleware guards are explicitly applied in routes — access control lives in controllers.
- **Authentication** — Laravel Fortify (login, registration, password reset, 2FA) + Laravel Sanctum (API tokens).

### Frontend (Blade + Vite)
- **Views** — [resources/views/](resources/views/): ~50 Blade templates. Main layout at [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php).
- **Assets** — Entry points: `resources/css/app.css` and `resources/js/app.js`. Compiled output goes to `/public/build`. Static assets (images, vendor CSS/JS) live directly in `/public`.
- **No JS framework** — Frontend is Blade-rendered with Axios for async requests; no React/Vue/Alpine.

### Database
MySQL database named `Shop`. Schema defined via 13 migrations. Key tables: `products`, `categories`, `users`, `commandes`, `payments`, `add_to_carts`, `testimonials`, `comments`, `searches`, `product_images`.

## Key Conventions

- Admin routes are prefixed with `/dashboard` and handled by `AdminController`.
- Public routes use `UserController` for pages and `ProductController` for product/cart views.
- Some route definitions in [routes/web.php](routes/web.php) have missing `/` separators before `{id}` (e.g., `Route::put('/testimonial{id}'...)`) — be careful when adding or modifying these routes.
- Models use standard Eloquent conventions. `addToCart` model/controller uses camelCase naming (inconsistent with Laravel convention).
