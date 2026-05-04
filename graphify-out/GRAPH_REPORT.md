# Graph Report - /Users/massakambp12/Desktop/ShopAgri  (2026-05-02)

## Corpus Check
- 260 files · ~50,000 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 955 nodes · 1045 edges · 98 communities detected
- Extraction: 80% EXTRACTED · 20% INFERRED · 0% AMBIGUOUS · INFERRED: 204 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- [[_COMMUNITY_Admin API Controllers|Admin API Controllers]]
- [[_COMMUNITY_Product Management|Product Management]]
- [[_COMMUNITY_Dashboard & Delivery|Dashboard & Delivery]]
- [[_COMMUNITY_Category Management|Category Management]]
- [[_COMMUNITY_User Administration|User Administration]]
- [[_COMMUNITY_Settings & Notifications|Settings & Notifications]]
- [[_COMMUNITY_API Layer & Providers|API Layer & Providers]]
- [[_COMMUNITY_Promotions & Coupons|Promotions & Coupons]]
- [[_COMMUNITY_Web Admin Controllers|Web Admin Controllers]]
- [[_COMMUNITY_Coupon System|Coupon System]]
- [[_COMMUNITY_Cart Service & Models|Cart Service & Models]]
- [[_COMMUNITY_Notification System|Notification System]]
- [[_COMMUNITY_Review & Rating|Review & Rating]]
- [[_COMMUNITY_Core Models & Middleware|Core Models & Middleware]]
- [[_COMMUNITY_Address & Account|Address & Account]]
- [[_COMMUNITY_Shared Model Layer|Shared Model Layer]]
- [[_COMMUNITY_Cart API Resource|Cart API Resource]]
- [[_COMMUNITY_Database Migrations|Database Migrations]]
- [[_COMMUNITY_Authentication|Authentication]]
- [[_COMMUNITY_Product Model Core|Product Model Core]]
- [[_COMMUNITY_Community 20|Community 20]]
- [[_COMMUNITY_Community 21|Community 21]]
- [[_COMMUNITY_Community 22|Community 22]]
- [[_COMMUNITY_Community 23|Community 23]]
- [[_COMMUNITY_Community 24|Community 24]]
- [[_COMMUNITY_Community 25|Community 25]]
- [[_COMMUNITY_Community 26|Community 26]]
- [[_COMMUNITY_Community 27|Community 27]]
- [[_COMMUNITY_Community 28|Community 28]]
- [[_COMMUNITY_Community 29|Community 29]]
- [[_COMMUNITY_Community 30|Community 30]]
- [[_COMMUNITY_Community 31|Community 31]]
- [[_COMMUNITY_Community 32|Community 32]]
- [[_COMMUNITY_Community 33|Community 33]]
- [[_COMMUNITY_Community 34|Community 34]]
- [[_COMMUNITY_Community 35|Community 35]]
- [[_COMMUNITY_Community 36|Community 36]]
- [[_COMMUNITY_Community 37|Community 37]]
- [[_COMMUNITY_Community 38|Community 38]]
- [[_COMMUNITY_Community 39|Community 39]]
- [[_COMMUNITY_Community 40|Community 40]]
- [[_COMMUNITY_Community 41|Community 41]]
- [[_COMMUNITY_Community 42|Community 42]]
- [[_COMMUNITY_Community 43|Community 43]]
- [[_COMMUNITY_Community 44|Community 44]]
- [[_COMMUNITY_Community 45|Community 45]]
- [[_COMMUNITY_Community 46|Community 46]]
- [[_COMMUNITY_Community 47|Community 47]]
- [[_COMMUNITY_Community 48|Community 48]]
- [[_COMMUNITY_Community 49|Community 49]]
- [[_COMMUNITY_Community 50|Community 50]]
- [[_COMMUNITY_Community 51|Community 51]]
- [[_COMMUNITY_Community 52|Community 52]]
- [[_COMMUNITY_Community 53|Community 53]]
- [[_COMMUNITY_Community 54|Community 54]]
- [[_COMMUNITY_Community 55|Community 55]]
- [[_COMMUNITY_Community 56|Community 56]]
- [[_COMMUNITY_Community 57|Community 57]]
- [[_COMMUNITY_Community 58|Community 58]]
- [[_COMMUNITY_Community 59|Community 59]]
- [[_COMMUNITY_Community 60|Community 60]]
- [[_COMMUNITY_Community 61|Community 61]]
- [[_COMMUNITY_Community 84|Community 84]]
- [[_COMMUNITY_Community 85|Community 85]]
- [[_COMMUNITY_Community 86|Community 86]]
- [[_COMMUNITY_Community 87|Community 87]]
- [[_COMMUNITY_Community 88|Community 88]]
- [[_COMMUNITY_Community 89|Community 89]]
- [[_COMMUNITY_Community 90|Community 90]]
- [[_COMMUNITY_Community 91|Community 91]]
- [[_COMMUNITY_Community 92|Community 92]]
- [[_COMMUNITY_Community 93|Community 93]]
- [[_COMMUNITY_Community 94|Community 94]]
- [[_COMMUNITY_Community 95|Community 95]]
- [[_COMMUNITY_Community 96|Community 96]]
- [[_COMMUNITY_Community 97|Community 97]]
- [[_COMMUNITY_Community 98|Community 98]]
- [[_COMMUNITY_Community 99|Community 99]]
- [[_COMMUNITY_Community 100|Community 100]]
- [[_COMMUNITY_Community 101|Community 101]]
- [[_COMMUNITY_Community 102|Community 102]]
- [[_COMMUNITY_Community 103|Community 103]]
- [[_COMMUNITY_Community 104|Community 104]]
- [[_COMMUNITY_Community 109|Community 109]]
- [[_COMMUNITY_Community 110|Community 110]]
- [[_COMMUNITY_Community 111|Community 111]]
- [[_COMMUNITY_Community 112|Community 112]]
- [[_COMMUNITY_Community 113|Community 113]]
- [[_COMMUNITY_Community 114|Community 114]]
- [[_COMMUNITY_Community 115|Community 115]]
- [[_COMMUNITY_Community 116|Community 116]]
- [[_COMMUNITY_Community 117|Community 117]]
- [[_COMMUNITY_Community 118|Community 118]]
- [[_COMMUNITY_Community 119|Community 119]]
- [[_COMMUNITY_Community 120|Community 120]]
- [[_COMMUNITY_Community 121|Community 121]]
- [[_COMMUNITY_Community 122|Community 122]]
- [[_COMMUNITY_Community 123|Community 123]]

## God Nodes (most connected - your core abstractions)
1. `User` - 28 edges
2. `Setting` - 24 edges
3. `Admin Dashboard Web Routes` - 18 edges
4. `CartService` - 16 edges
5. `AccountController` - 14 edges
6. `Coupon` - 12 edges
7. `ProductController` - 12 edges
8. `Review` - 11 edges
9. `AuthController` - 11 edges
10. `BaseApiController` - 11 edges

## Surprising Connections (you probably didn't know these)
- `Api\V1\CartController` --conceptually_related_to--> `Web\CartController`  [INFERRED]
  app/Http/Controllers/Api/V1/CartController.php → routes/web.php
- `Web\CheckoutController` --conceptually_related_to--> `Api\V1\CheckoutController`  [INFERRED]
  app/Http/Controllers/Web/CheckoutController.php → routes/api.php
- `users table` --references--> `Broadcast user channel`  [EXTRACTED]
  database/migrations/2014_10_12_000000_create_users_table.php → routes/channels.php
- `StockAlertCommand` --shares_data_with--> `Product`  [INFERRED]
  app/Console/Commands/StockAlertCommand.php → app/Models/Product.php
- `Product` --conceptually_related_to--> `PushNotificationService`  [INFERRED]
  app/Models/Product.php → app/Services/PushNotificationService.php

## Communities

### Community 0 - "Admin API Controllers"
Cohesion: 0.04
Nodes (59): Api\V1\Admin\CategoryController, Api\V1\Admin\DashboardController, Api\V1\Admin\OrderController, Api\V1\Admin\ProductController, Api\V1\Admin\ReportController, Api\V1\Admin\ReviewController, Api\V1\Admin\SettingController, Api\V1\Admin\UserController (+51 more)

### Community 1 - "Product Management"
Cohesion: 0.05
Nodes (8): UserController, AuthController, StockAlertCommand, OrderFactory, ReviewFactory, CreateNewUser, User, UserSeeder

### Community 2 - "Dashboard & Delivery"
Cohesion: 0.05
Nodes (10): DashboardController, DeliveryZoneController, OrderController, OrderCancelled, OrderDelivered, OrderShipped, DeliveryZone, OrderItem (+2 more)

### Community 3 - "Category Management"
Cohesion: 0.05
Nodes (11): CategoryController, fmt_price(), ProductFactory, Brand, BrandResource, CategoryResource, CategorySeeder, ProductSeeder (+3 more)

### Community 4 - "User Administration"
Cohesion: 0.06
Nodes (9): HomeSettingsController, NotificationSettingsController, SettingController, ShopSettingsController, SocialSettingsController, ThemeSettingsController, Setting, SettingSeeder (+1 more)

### Community 5 - "Settings & Notifications"
Cohesion: 0.06
Nodes (9): ProductController, ReportController, StockController, ProductAttribute, ProductVariant, ProductObserver, AppServiceProvider, StockService (+1 more)

### Community 6 - "API Layer & Providers"
Cohesion: 0.06
Nodes (46): AccountController, AddressController, AppServiceProvider, BaseApiController, BrandController, CartService, CheckoutController, Controller (+38 more)

### Community 7 - "Promotions & Coupons"
Cohesion: 0.06
Nodes (7): PromotionController, SlideController, TestimonialController, Promotion, Slide, Testimonial, TestimonialController

### Community 8 - "Web Admin Controllers"
Cohesion: 0.08
Nodes (28): Web\Admin\CategoryController, Web\Admin\DashboardController, Web\Admin\DeliveryZoneController, Web\Admin\HomeSettingsController, Web\Admin\NotificationController, Web\Admin\NotificationSettingsController, Web\Admin\OrderController, Web\Admin\ProductController (+20 more)

### Community 9 - "Coupon System"
Cohesion: 0.1
Nodes (5): CouponController, Coupon, CouponSeeder, CouponService, CheckoutController

### Community 10 - "Cart Service & Models"
Cohesion: 0.15
Nodes (2): CartItem, CartService

### Community 11 - "Notification System"
Cohesion: 0.13
Nodes (4): NotificationController, NotificationLog, BroadcastServiceProvider, NotificationController

### Community 12 - "Review & Rating"
Cohesion: 0.14
Nodes (2): Address, AccountController

### Community 13 - "Core Models & Middleware"
Cohesion: 0.15
Nodes (3): ReviewController, Review, ReviewController

### Community 14 - "Address & Account"
Cohesion: 0.17
Nodes (17): Brand, Category, Admin\CategoryController, Admin\DashboardController, EnsureUserHasRole, EnsureUserIsActive, Admin\HomeSettingsController, Order (+9 more)

### Community 15 - "Shared Model Layer"
Cohesion: 0.17
Nodes (16): Address, Brand, CartItem, Coupon, Order, OrderCancelledNotification, OrderDeliveredNotification, OrderItem (+8 more)

### Community 16 - "Cart API Resource"
Cohesion: 0.13
Nodes (5): ProductAttributeResource, ProductImageResource, ProductResource, ProductVariantResource, SearchController

### Community 17 - "Database Migrations"
Cohesion: 0.14
Nodes (2): CartResource, CartController

### Community 18 - "Authentication"
Cohesion: 0.18
Nodes (12): views_count column on products, Broadcast user channel, addresses table, brands table, coupons table, notifications_log table, orders table, product_attributes table (+4 more)

### Community 19 - "Product Model Core"
Cohesion: 0.18
Nodes (1): AuthController

### Community 20 - "Community 20"
Cohesion: 0.2
Nodes (1): Product

### Community 21 - "Community 21"
Cohesion: 0.2
Nodes (1): CartController

### Community 22 - "Community 22"
Cohesion: 0.24
Nodes (2): AddressResource, AddressController

### Community 23 - "Community 23"
Cohesion: 0.28
Nodes (2): Wishlist, WishlistController

### Community 24 - "Community 24"
Cohesion: 0.29
Nodes (8): CartResource, Category, DecrementStockOnPayment, NotificationLog, Product, SendOrderNotification, SendPushNotification, Wishlist

### Community 25 - "Community 25"
Cohesion: 0.33
Nodes (7): BrandResource, CategoryResource, ProductAttributeResource, ProductCollection, ProductImageResource, ProductResource, ProductVariantResource

### Community 26 - "Community 26"
Cohesion: 0.33
Nodes (7): CategorySeeder, CouponSeeder, settings table, DatabaseSeeder, ProductSeeder, SettingSeeder, UserSeeder

### Community 27 - "Community 27"
Cohesion: 0.33
Nodes (1): Category

### Community 28 - "Community 28"
Cohesion: 0.33
Nodes (1): Order

### Community 29 - "Community 29"
Cohesion: 0.33
Nodes (2): OrderItemResource, OrderResource

### Community 30 - "Community 30"
Cohesion: 0.33
Nodes (1): OrderDeliveredNotification

### Community 31 - "Community 31"
Cohesion: 0.33
Nodes (1): OrderShippedNotification

### Community 32 - "Community 32"
Cohesion: 0.33
Nodes (1): OrderCancelledNotification

### Community 33 - "Community 33"
Cohesion: 0.33
Nodes (1): OrderPlacedNotification

### Community 34 - "Community 34"
Cohesion: 0.33
Nodes (1): UserFactory

### Community 35 - "Community 35"
Cohesion: 0.33
Nodes (1): CouponFactory

### Community 36 - "Community 36"
Cohesion: 0.4
Nodes (1): RegisterRequest

### Community 37 - "Community 37"
Cohesion: 0.4
Nodes (1): LoginRequest

### Community 38 - "Community 38"
Cohesion: 0.4
Nodes (1): UpdateAccountRequest

### Community 39 - "Community 39"
Cohesion: 0.4
Nodes (1): BaseApiController

### Community 40 - "Community 40"
Cohesion: 0.5
Nodes (1): AccountController

### Community 41 - "Community 41"
Cohesion: 0.4
Nodes (1): OrderController

### Community 42 - "Community 42"
Cohesion: 0.5
Nodes (1): SendPushNotification

### Community 43 - "Community 43"
Cohesion: 0.6
Nodes (1): PushNotificationService

### Community 44 - "Community 44"
Cohesion: 0.6
Nodes (1): InstallCommand

### Community 45 - "Community 45"
Cohesion: 0.5
Nodes (1): FortifyServiceProvider

### Community 46 - "Community 46"
Cohesion: 0.5
Nodes (1): ProductImage

### Community 47 - "Community 47"
Cohesion: 0.67
Nodes (1): UpdateUserProfileInformation

### Community 48 - "Community 48"
Cohesion: 0.5
Nodes (1): DecrementStockOnPayment

### Community 49 - "Community 49"
Cohesion: 0.5
Nodes (1): WelcomeNotification

### Community 50 - "Community 50"
Cohesion: 0.67
Nodes (1): ReviewSeeder

### Community 51 - "Community 51"
Cohesion: 0.5
Nodes (1): MarketplaceController

### Community 52 - "Community 52"
Cohesion: 0.67
Nodes (1): RedirectIfAuthenticated

### Community 53 - "Community 53"
Cohesion: 0.67
Nodes (1): Authenticate

### Community 54 - "Community 54"
Cohesion: 0.67
Nodes (1): EnsureUserHasRole

### Community 55 - "Community 55"
Cohesion: 0.67
Nodes (1): EnsureUserIsActive

### Community 56 - "Community 56"
Cohesion: 0.67
Nodes (1): TrustHosts

### Community 57 - "Community 57"
Cohesion: 0.67
Nodes (1): UserResource

### Community 58 - "Community 58"
Cohesion: 0.67
Nodes (1): ProductCollection

### Community 59 - "Community 59"
Cohesion: 0.67
Nodes (1): OrderPlaced

### Community 60 - "Community 60"
Cohesion: 0.67
Nodes (1): PaymentConfirmed

### Community 61 - "Community 61"
Cohesion: 0.67
Nodes (1): SendOrderNotification

### Community 84 - "Community 84"
Cohesion: 0.67
Nodes (1): DatabaseSeeder

### Community 85 - "Community 85"
Cohesion: 0.67
Nodes (1): CategoryFactory

### Community 86 - "Community 86"
Cohesion: 0.67
Nodes (3): guest middleware, Guest-only Web Routes, Web\AuthController

### Community 87 - "Community 87"
Cohesion: 0.67
Nodes (1): OrderItemFactory

### Community 88 - "Community 88"
Cohesion: 0.67
Nodes (1): ShopSeeder

### Community 89 - "Community 89"
Cohesion: 0.67
Nodes (1): OrderSeeder

### Community 90 - "Community 90"
Cohesion: 1.0
Nodes (1): TrimStrings

### Community 91 - "Community 91"
Cohesion: 1.0
Nodes (1): TrustProxies

### Community 92 - "Community 92"
Cohesion: 1.0
Nodes (1): ValidateSignature

### Community 93 - "Community 93"
Cohesion: 1.0
Nodes (1): PreventRequestsDuringMaintenance

### Community 94 - "Community 94"
Cohesion: 1.0
Nodes (1): EncryptCookies

### Community 95 - "Community 95"
Cohesion: 1.0
Nodes (1): Controller

### Community 96 - "Community 96"
Cohesion: 1.0
Nodes (2): Api\V1\Admin\CouponController, Coupon

### Community 97 - "Community 97"
Cohesion: 1.0
Nodes (2): Testimonial, Admin\TestimonialController

### Community 98 - "Community 98"
Cohesion: 1.0
Nodes (2): Promotion, Admin\PromotionController

### Community 99 - "Community 99"
Cohesion: 1.0
Nodes (2): Authenticate, RedirectIfAuthenticated

### Community 100 - "Community 100"
Cohesion: 1.0
Nodes (2): Slide, Admin\SlideController

### Community 101 - "Community 101"
Cohesion: 1.0
Nodes (2): LoginRequest, RegisterRequest

### Community 102 - "Community 102"
Cohesion: 1.0
Nodes (2): OrderItemResource, OrderResource

### Community 103 - "Community 103"
Cohesion: 1.0
Nodes (2): promotions table, slides table

### Community 104 - "Community 104"
Cohesion: 1.0
Nodes (1): Setting

### Community 109 - "Community 109"
Cohesion: 1.0
Nodes (1): InstallCommand

### Community 110 - "Community 110"
Cohesion: 1.0
Nodes (1): TrimStrings

### Community 111 - "Community 111"
Cohesion: 1.0
Nodes (1): TrustProxies

### Community 112 - "Community 112"
Cohesion: 1.0
Nodes (1): ValidateSignature

### Community 113 - "Community 113"
Cohesion: 1.0
Nodes (1): PreventRequestsDuringMaintenance

### Community 114 - "Community 114"
Cohesion: 1.0
Nodes (1): EncryptCookies

### Community 115 - "Community 115"
Cohesion: 1.0
Nodes (1): TrustHosts

### Community 116 - "Community 116"
Cohesion: 1.0
Nodes (1): UpdateAccountRequest

### Community 117 - "Community 117"
Cohesion: 1.0
Nodes (1): UserResource

### Community 118 - "Community 118"
Cohesion: 1.0
Nodes (1): AddressResource

### Community 119 - "Community 119"
Cohesion: 1.0
Nodes (1): ProductImage

### Community 120 - "Community 120"
Cohesion: 1.0
Nodes (1): Promotion

### Community 121 - "Community 121"
Cohesion: 1.0
Nodes (1): Testimonial

### Community 122 - "Community 122"
Cohesion: 1.0
Nodes (1): DeliveryZone

### Community 123 - "Community 123"
Cohesion: 1.0
Nodes (1): Slide

## Ambiguous Edges - Review These
- `Brand` → `ProductObserver`  [AMBIGUOUS]
  app/Models/Brand.php · relation: conceptually_related_to

## Knowledge Gaps
- **118 isolated node(s):** `TrimStrings`, `TrustProxies`, `ValidateSignature`, `PreventRequestsDuringMaintenance`, `EncryptCookies` (+113 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **Thin community `Cart Service & Models`** (22 nodes): `CartItem.php`, `CartService.php`, `CartItem`, `.product()`, `.user()`, `.variant()`, `CartService`, `.addItem()`, `.applyCoupon()`, `.buildCartData()`, `.calculateSubtotal()`, `.calculateTotals()`, `.clear()`, `.__construct()`, `.findItem()`, `.findItemById()`, `.getCart()`, `.getItems()`, `.mergeGuestCart()`, `.removeCoupon()`, `.removeItem()`, `.updateItem()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Review & Rating`** (18 nodes): `AccountController.php`, `Address.php`, `Address`, `.casts()`, `.user()`, `AccountController`, `.addresses()`, `.changePassword()`, `.destroyAddress()`, `.editProfile()`, `.orders()`, `.profile()`, `.setDefaultAddress()`, `.storeAddress()`, `.toggleWishlist()`, `.updateAddress()`, `.updateProfile()`, `.wishlist()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Database Migrations`** (14 nodes): `CartController.php`, `CartResource.php`, `CartResource`, `.toArray()`, `CartController`, `.addItem()`, `.applyCoupon()`, `.checkCoupon()`, `.clear()`, `.__construct()`, `.index()`, `.removeCoupon()`, `.removeItem()`, `.updateItem()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Product Model Core`** (11 nodes): `AuthController.php`, `AuthController`, `.login()`, `.logout()`, `.register()`, `.resetPassword()`, `.sendResetLink()`, `.showForgot()`, `.showLogin()`, `.showRegister()`, `.showReset()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 20`** (10 nodes): `Product.php`, `Product`, `.attributes()`, `.brand()`, `.casts()`, `.category()`, `.images()`, `.primaryImage()`, `.reviews()`, `.variants()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 21`** (10 nodes): `CartController.php`, `CartController`, `.add()`, `.applyCoupon()`, `.__construct()`, `.index()`, `.mini()`, `.remove()`, `.removeCoupon()`, `.update()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 22`** (10 nodes): `AddressController.php`, `AddressResource.php`, `AddressResource`, `.toArray()`, `AddressController`, `.destroy()`, `.index()`, `.setDefault()`, `.store()`, `.update()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 23`** (9 nodes): `WishlistController.php`, `Wishlist.php`, `Wishlist`, `.product()`, `.user()`, `WishlistController`, `.destroy()`, `.index()`, `.store()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 27`** (6 nodes): `Category.php`, `Category`, `.casts()`, `.children()`, `.parent()`, `.products()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 28`** (6 nodes): `Order.php`, `Order`, `.casts()`, `.coupon()`, `.items()`, `.user()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 29`** (6 nodes): `OrderItemResource.php`, `OrderResource.php`, `OrderItemResource`, `.toArray()`, `OrderResource`, `.toArray()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 30`** (6 nodes): `OrderDeliveredNotification.php`, `OrderDeliveredNotification`, `.__construct()`, `.fcmPayload()`, `.toMail()`, `.via()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 31`** (6 nodes): `OrderShippedNotification.php`, `OrderShippedNotification`, `.__construct()`, `.fcmPayload()`, `.toMail()`, `.via()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 32`** (6 nodes): `OrderCancelledNotification.php`, `OrderCancelledNotification`, `.__construct()`, `.fcmPayload()`, `.toMail()`, `.via()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 33`** (6 nodes): `OrderPlacedNotification.php`, `OrderPlacedNotification`, `.__construct()`, `.fcmPayload()`, `.toMail()`, `.via()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 34`** (6 nodes): `UserFactory.php`, `UserFactory`, `.admin()`, `.definition()`, `.inactive()`, `.unverified()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 35`** (6 nodes): `CouponFactory.php`, `CouponFactory`, `.definition()`, `.expired()`, `.fixed()`, `.percent()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 36`** (5 nodes): `RegisterRequest.php`, `RegisterRequest`, `.authorize()`, `.failedValidation()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 37`** (5 nodes): `LoginRequest.php`, `LoginRequest`, `.authorize()`, `.failedValidation()`, `.rules()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 38`** (5 nodes): `UpdateAccountRequest`, `.authorize()`, `.failedValidation()`, `.rules()`, `UpdateAccountRequest.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 39`** (5 nodes): `BaseApiController.php`, `BaseApiController`, `.error()`, `.paginated()`, `.success()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 40`** (5 nodes): `AccountController.php`, `AccountController`, `.avatar()`, `.show()`, `.update()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 41`** (5 nodes): `OrderController.php`, `OrderController`, `.index()`, `.invoice()`, `.show()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 42`** (5 nodes): `SendPushNotification.php`, `SendPushNotification`, `.__construct()`, `.handle()`, `.resolveNotificationClass()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 43`** (5 nodes): `PushNotificationService.php`, `PushNotificationService`, `.getAccessToken()`, `.send()`, `.sendToUser()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 44`** (5 nodes): `InstallCommand.php`, `InstallCommand`, `.handle()`, `.runShell()`, `.step()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 45`** (4 nodes): `FortifyServiceProvider.php`, `FortifyServiceProvider`, `.boot()`, `.register()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 46`** (4 nodes): `ProductImage.php`, `ProductImage`, `.casts()`, `.product()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 47`** (4 nodes): `UpdateUserProfileInformation.php`, `UpdateUserProfileInformation`, `.update()`, `.updateVerifiedUser()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 48`** (4 nodes): `DecrementStockOnPayment.php`, `DecrementStockOnPayment`, `.__construct()`, `.handle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 49`** (4 nodes): `WelcomeNotification.php`, `WelcomeNotification`, `.toMail()`, `.via()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 50`** (4 nodes): `ReviewSeeder.php`, `ReviewSeeder`, `.run()`, `.weightedRating()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 51`** (4 nodes): `MarketplaceController.php`, `MarketplaceController`, `.index()`, `.show()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 52`** (3 nodes): `RedirectIfAuthenticated.php`, `RedirectIfAuthenticated`, `.handle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 53`** (3 nodes): `Authenticate.php`, `Authenticate`, `.redirectTo()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 54`** (3 nodes): `EnsureUserHasRole.php`, `EnsureUserHasRole`, `.handle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 55`** (3 nodes): `EnsureUserIsActive.php`, `EnsureUserIsActive`, `.handle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 56`** (3 nodes): `TrustHosts.php`, `TrustHosts`, `.hosts()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 57`** (3 nodes): `UserResource.php`, `UserResource`, `.toArray()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 58`** (3 nodes): `ProductCollection.php`, `ProductCollection`, `.toArray()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 59`** (3 nodes): `OrderPlaced.php`, `OrderPlaced`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 60`** (3 nodes): `PaymentConfirmed.php`, `PaymentConfirmed`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 61`** (3 nodes): `SendOrderNotification.php`, `SendOrderNotification`, `.handle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 84`** (3 nodes): `DatabaseSeeder.php`, `DatabaseSeeder`, `.run()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 85`** (3 nodes): `CategoryFactory.php`, `CategoryFactory`, `.definition()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 87`** (3 nodes): `OrderItemFactory.php`, `OrderItemFactory`, `.definition()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 88`** (3 nodes): `ShopSeeder.php`, `ShopSeeder`, `.run()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 89`** (3 nodes): `OrderSeeder.php`, `OrderSeeder`, `.run()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 90`** (2 nodes): `TrimStrings.php`, `TrimStrings`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 91`** (2 nodes): `TrustProxies.php`, `TrustProxies`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 92`** (2 nodes): `ValidateSignature.php`, `ValidateSignature`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 93`** (2 nodes): `PreventRequestsDuringMaintenance.php`, `PreventRequestsDuringMaintenance`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 94`** (2 nodes): `EncryptCookies.php`, `EncryptCookies`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 95`** (2 nodes): `Controller.php`, `Controller`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 96`** (2 nodes): `Api\V1\Admin\CouponController`, `Coupon`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 97`** (2 nodes): `Testimonial`, `Admin\TestimonialController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 98`** (2 nodes): `Promotion`, `Admin\PromotionController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 99`** (2 nodes): `Authenticate`, `RedirectIfAuthenticated`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 100`** (2 nodes): `Slide`, `Admin\SlideController`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 101`** (2 nodes): `LoginRequest`, `RegisterRequest`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 102`** (2 nodes): `OrderItemResource`, `OrderResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 103`** (2 nodes): `promotions table`, `slides table`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 104`** (1 nodes): `Setting`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 109`** (1 nodes): `InstallCommand`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 110`** (1 nodes): `TrimStrings`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 111`** (1 nodes): `TrustProxies`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 112`** (1 nodes): `ValidateSignature`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 113`** (1 nodes): `PreventRequestsDuringMaintenance`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 114`** (1 nodes): `EncryptCookies`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 115`** (1 nodes): `TrustHosts`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 116`** (1 nodes): `UpdateAccountRequest`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 117`** (1 nodes): `UserResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 118`** (1 nodes): `AddressResource`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 119`** (1 nodes): `ProductImage`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 120`** (1 nodes): `Promotion`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 121`** (1 nodes): `Testimonial`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 122`** (1 nodes): `DeliveryZone`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 123`** (1 nodes): `Slide`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What is the exact relationship between `Brand` and `ProductObserver`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._
- **Why does `User` connect `Product Management` to `Notification System`, `Dashboard & Delivery`, `Product Model Core`, `Settings & Notifications`?**
  _High betweenness centrality (0.061) - this node is a cross-community bridge._
- **Why does `Setting` connect `User Administration` to `Promotions & Coupons`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `User` (e.g. with `.register()` and `.index()`) actually correct?**
  _`User` has 18 INFERRED edges - model-reasoned connections that need verification._
- **Are the 19 inferred relationships involving `Setting` (e.g. with `.downloadInvoice()` and `.home()`) actually correct?**
  _`Setting` has 19 INFERRED edges - model-reasoned connections that need verification._
- **What connects `TrimStrings`, `TrustProxies`, `ValidateSignature` to the rest of the system?**
  _118 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Admin API Controllers` be split into smaller, more focused modules?**
  _Cohesion score 0.04 - nodes in this community are weakly interconnected._