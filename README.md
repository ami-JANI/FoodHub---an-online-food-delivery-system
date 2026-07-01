# FoodHub — Online Food Delivery System

FoodHub is a full-stack food delivery platform where customers order from nearby restaurants, restaurants manage their menus and orders, riders deliver and earn per kilometer, and admins moderate the marketplace.

## Features

**Customers**
- Browse restaurants near you (5 km radius), filter by cuisine/category, sort by rating, distance, or delivery fee
- Set your delivery location manually on a map (or auto-detect) — even a location you're not currently at
- View menus with photos; unavailable items show faded and can't be ordered
- Session cart with a conflict guard when switching restaurants
- Checkout with saved addresses, a pinned location, or a new address; distance-based delivery fee
- Live order tracking: segmented progress bar, per-step animations, live rider map, and rider chat
- Cancel an order before the restaurant starts preparing
- Rate and review completed orders (with photos, optionally anonymous)
- Favorite restaurants

**Restaurants**
- Registration with map location and admin approval
- Manage menu items and categories (owner edits go through admin approval)
- Toggle item availability and edit opening/closing hours and delivery time instantly (no approval)
- Accept and prepare incoming orders; live order queue
- Get notified of admin edits; contact the admin

**Riders**
- Registration with vehicle/qualification and admin approval (admin sets hourly wage)
- See nearby pickup requests within 2 km with estimated earnings
- Delivery route map (rider → restaurant → customer), trip distance, and per-km earnings (Tk 5/km)
- Broadcast live location and chat with the customer

**Admins**
- Approve/reject restaurants, menu items, profile changes, and riders
- Edit or remove restaurant menus and categories
- Remove/restore restaurants and resolve restaurant messages

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2), multi-guard auth (customer, restaurant, rider, admin)
- **Database**: SQLite
- **Frontend**: Blade + Tailwind CSS (Vite), dark/light theme
- **Maps**: Leaflet + OpenStreetMap, Nominatim geocoding (no API keys)

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js + npm

### Setup

```bash
git clone https://github.com/ami-JANI/FoodHub---an-online-food-delivery-system.git
cd FoodHub---an-online-food-delivery-system

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed

npm run build
php artisan serve
```

Visit `http://127.0.0.1:8000` in your browser.

## License

Released under the MIT License.
