# Ecommerce — PHP + MySQL Full Stack

A local e-commerce demo built for XAMPP. Node.js/Express is not required.

## Stack
- Frontend: HTML, CSS, JavaScript
- Backend: PHP 8.2+
- Database: MySQL/MariaDB
- Server: Apache (XAMPP)

## Folder
Place the folder exactly here:

`C:\xampp\htdocs\ecommerce`

The public site is:

`http://localhost/ecommerce/`

## 1. Start XAMPP
Start **Apache** and **MySQL**.

## 2. Import database
Open `http://localhost/phpmyadmin/`, create/import the database using:

`database/ecommerce.sql`

The SQL creates the `ecommerce` database automatically.

## 3. Open the site
Use:

`http://localhost/ecommerce/`

Do not open the HTML file directly from `C:\` or with `file:///` because PHP sessions/API requests need Apache.

## Demo accounts
Customer:
- Username: `customer_1`
- Password: `password_1`

Admin:
- Username: `admin`
- Password: `admin123`

## Login system
Login now creates a real PHP session using `ECOMMERCE_SESSION`. The frontend also calls `backend/api/me.php` after page load to restore the authenticated session.

Existing SQL demo accounts use SHA-256 hashes. New registrations use PHP `password_hash()` and are verified with `password_verify()`.

## APIs
- `backend/api/products.php` — product catalogue
- `backend/api/login.php` — sign in + PHP session
- `backend/api/register.php` — customer registration
- `backend/api/me.php` — current session
- `backend/api/logout.php` — logout
- `backend/api/checkout.php` — authenticated checkout
- `backend/api/orders.php` — customer orders

## If login still fails
1. Confirm Apache and MySQL are green in XAMPP.
2. Confirm `ecommerce` database exists in phpMyAdmin.
3. Open `http://localhost/ecommerce/backend/api/products.php`. It should return JSON.
4. Open the browser DevTools → Network and inspect `login.php`.
5. If PHP shows a database error, check `backend/config/config.php` (default XAMPP MySQL user is `root` with an empty password).
