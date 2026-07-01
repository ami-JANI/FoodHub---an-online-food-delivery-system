# FoodHub — Progress

## Project goal
A Laravel-based online food delivery web app, modeled after services like FoodPanda/প্রয়োজন.com. Customers browse nearby restaurants, order food, and track delivery live; restaurants manage their own menu/orders; riders deliver orders and broadcast live location; admins approve/moderate everything.

Repo: `https://github.com/ami-JANI/FoodHub---an-online-food-delivery-system.git`

## Stack & key decisions (do not deviate without reason)
- Laravel 12, PHP 8.2 (XAMPP), **SQLite** (`database/database.sqlite`). Composer is local `composer.phar`, not on PATH.
- Tailwind CSS v4, manual dark/light theme via `@custom-variant dark` + `partials/theme-toggle.blade.php` + `partials/theme-script.blade.php`.
- **Four separate auth guards**: `web` (customer), `restaurant`, `rider`, `admin` — each with its own provider/model in `config/auth.php`. Admins are **not** publicly registrable (created manually).
- Maps/geo: **Leaflet.js + OpenStreetMap** tiles (no API key) for pickers/live maps; **Nominatim** for reverse-geocoding; **Haversine** formula for distance (`Restaurant::distanceFromKm()`). All free, no paid API keys — keep it that way.
- File uploads use a custom **`uploads`** filesystem disk (`config/filesystems.php`) instead of `storage:link`, to avoid Windows symlink issues.
- No websockets — live rider location and chat use plain polling (`fetch` + `setInterval`): rider posts location every ~15s, customer polls every ~10s; chat polls every ~5s.
- App timezone is UTC (`config/app.php`), but restaurant business hours are entered/compared in **Asia/Dhaka** local time explicitly (`now('Asia/Dhaka')`) — do not switch this back to bare `now()`.
- Workflow preference: build → verify with curl/tinker → leave changes uncommitted until explicitly told "commit" → push only when explicitly confirmed. **Never add a Claude/Anthropic co-author trailer to commits.**

## Completed features (by area)

### Customers
- Browse restaurants filtered to a 5km radius (`Restaurant::VISIBILITY_RADIUS_KM`), with cuisine/menu-category/sort filters and search (`RestaurantController@index`).
- Restaurant detail page with categorized menu, images, open/closed status (`restaurants/show.blade.php`).
- Cart (session-based, `CartController`) with conflict warning when adding items from a different restaurant than what's already in the cart.
- Address book with map picker (`AddressController`, `partials/map-picker.blade.php`) and dynamic reverse-geocoded location display on the homepage (no more hardcoded "Dhaka, Bangladesh").
- Checkout requiring address + phone + confirmation (`CheckoutController`), with distance-based delivery fee (Tk3/km, `Restaurant::deliveryFeeFor()`).
- Full order tracking: unique tracking codes, 8-step status pipeline (`Order::STEPS`), live rider location on map, in-order chat with the rider (`TrackOrderController`, `track/index.blade.php`, `track/show.blade.php`).
- Account page, profile editing.

### Restaurants (merchants)
- Registration requiring address (map picker) + admin approval before going live.
- Dashboard: pending order management, menu item CRUD (image upload, admin-approval gated), profile edits (also admin-approval gated) — `RestaurantDashboardController`, `MenuItemDashboardController`, `RestaurantProfileController`, `RestaurantOrderController`.
- Logo + cover/banner image upload (registration + profile), displayed on listing cards and detail page header.
- Messaging to admin if removed from the app (`RestaurantMessageController`, `restaurant_messages` table).
- **Store hours**: opening/closing time fields + manual "close now/reopen" toggle, bypassing admin approval since it's time-sensitive (`RestaurantHoursController`, `Restaurant::isCurrentlyOpen()`).

### Riders
- Registration (qualification, vehicle type) requiring admin approval with admin-set hourly wage.
- Dashboard showing assignable/active orders within a 2km pickup radius (`Rider::PICKUP_RADIUS_KM`), live location broadcasting, navigation to customer (`RiderDashboardController`).

### Admin
- Approve/reject: new restaurants, menu items, profile-change requests, rider applications (`AdminApprovalController`).
- Soft-delete ("remove") a restaurant from listings — owner keeps login, sees a "no longer listed" banner, can message admin; admin can restore.
- Dashboard stats, restaurant message inbox (`AdminDashboardController`).

### UI/UX
- 3-layer sticky header: row 1 (utility bar: location pill, Track Order, theme toggle) scrolls away normally; rows 2–3 (nav/search/cart/sign-in, and a per-page filter bar via `@yield('filterbar')`) are sticky. Trimmed item counts on rows 1 and 3 to stop horizontal overflow/sliding.
- Dark/light theme toggle, elegant stone/rose/amber color palette (deliberately moved away from bright colors).
- Closed/unavailable restaurants shown faded (`opacity-60`) with a "Currently unavailable" overlay in the listing; still clickable, but "Add to cart" is blocked both client-side (JS alert) and server-side (`CartController@add` checks `isCurrentlyOpen()`).
- Logout from any of the 4 roles redirects to the homepage, not to a sign-in page.

## In progress
Nothing actively in progress — the last requested feature set (logout redirects, filter bar trim, restaurant hours/manual close/unavailable-overlay) was implemented, verified via curl/tinker, committed (`7044b5c`), and pushed to `origin/main`.

## Remaining / not yet built
- [ ] Payment integration (currently cash-on-delivery / no real payment gateway)
- [ ] Order ratings/reviews from customers
- [ ] Email/SMS notifications (order placed, status changes, approvals)
- [ ] Search/autocomplete improvements (currently basic name/cuisine match)
- [ ] Automated test suite (work has been verified manually via curl + Tinker, not via PHPUnit/Pest)
- [ ] Pagination for restaurant/menu listings (currently loads all matching rows)
- [ ] Rider earnings/payout summary view
- [ ] Production deployment configuration (currently dev-only via XAMPP + SQLite)

## Key files reference
- Models: `app/Models/{Restaurant,Order,OrderItem,Rider,Address,User,Admin,Category,MenuItem,RestaurantUpdateRequest,RestaurantMessage,OrderMessage}.php`
- Controllers: `app/Http/Controllers/{CartController,CheckoutController,RestaurantController,RestaurantHoursController,RestaurantDashboardController,RestaurantProfileController,RestaurantOrderController,RestaurantMessageController,MenuItemDashboardController,RiderDashboardController,TrackOrderController,AdminApprovalController,AdminDashboardController,LocationController,ProfileController,AddressController}.php`, `app/Http/Controllers/Auth/{CustomerAuthController,RestaurantAuthController,RiderAuthController,AdminAuthController}.php`
- Routes: `routes/web.php`
- Views: `resources/views/{layouts,partials,restaurants,cart,checkout,account,track,restaurant,rider,admin,auth,errors}/...`
- Migrations: `database/migrations/*` (latest: `2026_06_20_060500_add_hours_to_restaurants_table.php`)
- Seeder: `database/seeders/RestaurantSeeder.php`
