# Laravel E-Commerce API

## Setup Instructions

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env`
4. Configure your database credentials
5. Run the following commands:
   - `php artisan migrate --seed`
   - `php artisan storage:link`
6. Run tests (optional): `php artisan test`
7. Import the Postman collection and environment file from the `postman/` directory

---

## Docker Setup (Laravel Sail)

> If you're using Docker with Sail instead of a local PHP/MySQL setup:

1. Run `./vendor/bin/sail up -d` to start Laravel in Docker
2. Run migrations and seeder:
   - `./vendor/bin/sail artisan migrate --seed`
   - `./vendor/bin/sail artisan storage:link`
3. Visit the app at [http://localhost](http://localhost)
4. If port `80` or `3306` is already in use (e.g. by XAMPP), update your:
   - `.env`:
     ```
     APP_PORT=8080
     DB_PORT=3307
     DB_HOST=mysql
     DB_USERNAME=sail
     DB_PASSWORD=password
     ```
   - `docker-compose.yml`: change port mappings for app & MySQL
5. Stop XAMPP’s Apache and MySQL services to avoid port conflicts

---

## Admin Credentials

- Email: `admin@example.com`
- Password: `password`

---

## API Features

### Authentication
- Laravel Sanctum-based token authentication
- Role-based access control: `admin` and `customer`

### Product & Category Management (Admin Only)
- Create, update, delete products with multiple images
- Product listing with pagination and category filtering
- Soft delete support for products
- Category CRUD operations
- Cannot delete a product/category if used in any orders
- Input validation and error handling on all endpoints

### Orders (Customer)
- Authenticated customers can place orders
- Order request accepts multiple items
- System validates stock before creating order
- Product stock is **automatically reduced** on successful order
- Customers can view their order history and specific order details

### Other Features
- Image upload to `public` disk with validation
- Product listing is **cached** by page/category to optimize response
  - Cache is cleared on product create/update/delete
- API documented using Postman collection

---

## Testing
- Uses SQLite in-memory for safe, fast testing
- Unit tests for:
  - ProductService
  - ProductServiceAdditional (category sync + images)
  - CategoryService
  - OrderService
- Feature tests for:
  - Product listing (customer)
  - Admin creating a product
  - Customer placing an order

---

## Notes

- This is an **API-only** backend built with Laravel.
- **CSRF protection** is not required since this app uses **token-based authentication** and has no web routes.
- **Postman collection** and **environment file** are included in the `postman/` folder at the root of this project.

---
