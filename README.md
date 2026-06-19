# FoodHub — An Online Food Delivery System

FoodHub is a Laravel-based food delivery web application inspired by [FoodPanda](https://www.foodpanda.com.bd/) and [Proiojon](https://proiojon.com/). Users can browse restaurants, view menus, add items to a cart, and place an order — built as a university Web Lab project.

## Features (current milestone)

- Browse a list of restaurants with rating, cuisine, and delivery info
- View a restaurant's menu grouped by category
- Add items to a session-based cart
- Update quantities or remove items from the cart
- Checkout with subtotal, delivery fee, and total calculation
- Order confirmation page

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2)
- **Database**: SQLite
- **Frontend**: Blade templates + Tailwind CSS (via Vite)

## Project Structure

- `app/Models` — `Restaurant`, `Category`, `MenuItem`
- `app/Http/Controllers` — `RestaurantController`, `CartController`
- `database/migrations` & `database/seeders` — schema and demo data (4 sample restaurants with menus)
- `resources/views` — `restaurants/`, `cart/`, shared `layouts/app.blade.php`
- `routes/web.php` — application routes

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js + npm

### Setup

```bash
git clone https://github.com/I-Jani/FoodHub---an-online-food-delivery-system.git
cd FoodHub---an-online-food-delivery-system

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate
php artisan db:seed

npm run build
php artisan serve
```

Visit `http://127.0.0.1:8000` in your browser.

## Roadmap

- [ ] User registration & login
- [ ] Order history per user
- [ ] Restaurant owner dashboard (manage menu items)
- [ ] Admin panel
- [ ] Payment integration

## License

This project is built for academic purposes as part of a Web Lab course.
