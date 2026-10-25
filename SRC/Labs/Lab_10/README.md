# Syaha (سياحة) – a travel agency website

Syaha is the demo project of the *Web Development* course (ENSIA): the website of an Algerian travel agency, in
Arabic (right to left). Visitors discover the trips, book them and send messages; customers have an account with
their bookings; the agency manages trips, bookings and messages in an admin area.

It was built step by step in the labs of the course: HTML and CSS (Lab 2), responsive design (Lab 3), JavaScript
(Lab 4), MySQL and PDO (Lab 5), server-side validation and uploads (Lab 6), sessions and accounts (Lab 7) and
security (Lab 9). This folder is the final version (Lab 10).

## Features

| For | Feature |
| --- | --- |
| Visitors | home page with a slider, list of trips with search and sorting, trip booking (with or without JavaScript), contact form with a map, favourite trips (in the browser) |
| Customers | registration, login and logout, "remember my e-mail", their bookings, cancellation of a pending booking, forms pre-filled with their name |
| Admins | dashboard, trips (create, edit with photo upload, publish, delete), bookings (confirm, cancel), messages (read, delete) |
| Security | prepared statements, escaped output, CSRF tokens, hashed passwords, login throttling, session hardening, Content-Security-Policy and security headers, safe uploads, secrets out of Git |

## Technologies

HTML5, CSS3 (Flexbox, Grid, media queries), JavaScript (no framework), PHP 8.1+ (PDO), MySQL / MariaDB, Apache
(XAMPP). Fonts: Cairo (Google Fonts); icons: Font Awesome 6.

## Installation (about 10 minutes)

1. Install [XAMPP](https://www.apachefriends.org/) (PHP 8.1 or newer) and start **Apache** and **MySQL**.
2. Clone the course repository into `C:\xampp\htdocs` (Syaha uses the CSS of `SRC/Labs/Lab_2` and `Lab_3`, the
   scripts of `SRC/Labs/Lab_4+` and the images of `SRC/IMAGES`):

   ```bash
   cd C:\xampp\htdocs
   git clone <address of the repository> web_course
   ```

3. Open phpMyAdmin (<http://localhost/phpmyadmin>), tab **Import**, and import
   `SRC/Labs/Lab_10/database/syaha.sql`. It creates the `syaha` database with its tables and demo data.
4. *(Optional on your computer, required on a server)* copy `includes/config.local.example.php` to
   `includes/config.local.php` and write the settings of the database in it. This file is ignored by Git.
5. Check the installation:

   ```bash
   cd C:\xampp\htdocs\web_course\SRC\Labs\Lab_10
   C:\xampp\php\php.exe tools/check_install.php
   ```

6. Open <http://localhost/web_course/SRC/Labs/Lab_10/index.php>.

### Demo accounts

| Role | E-mail | Password |
| --- | --- | --- |
| Admin | `admin@syaha.example` | `Syaha_Admin1` |
| Customer | `amine@example.com` | `Syaha_2025` |

Change these passwords (or delete the accounts) before putting the site online.

## Tests

With Apache and MySQL started, the smoke test requests every page like a browser and checks the answers, the
protected pages, the CSRF protection and the security headers. It changes nothing in the database.

```bash
C:\xampp\php\php.exe tools/smoke_test.php http://localhost/web_course/SRC/Labs/Lab_10/
```

The manual test plan (accounts, bookings, admin, attacks) is in Lab 10 of the course.

## Structure

```text
Lab_10/
├── index.php, services.php, about_us.php, contact_us.php    public pages
├── book.php, login.php, register.php, logout.php            forms
├── my_bookings.php, cancel_booking.php                      customer area
├── 404.php
├── admin/            admin area (every page includes guard.php: admins only)
├── includes/         code included by the pages, never opened directly
│   ├── config.php            settings (+ config.local.php, not in Git)
│   ├── security.php          errors, security headers, CSRF tokens
│   ├── session.php, auth.php sessions, accounts, protected pages, flash messages
│   ├── db.php, queries.php   PDO connection and every SQL query
│   ├── validation.php, upload.php
│   └── header.php, footer.php and the parts of the pages
├── database/syaha.sql   tables and demo data
├── uploads/trips/       photos uploaded in the admin area (scripts cannot run there)
└── tools/               check_install.php, smoke_test.php (command line only)
```

## Team

| Member | Main contributions |
| --- | --- |
| *Name 1* | pages and responsive design |
| *Name 2* | database and admin area |
| *Name 3* | accounts, sessions and security |
| *Name 4* | JavaScript, validation and tests |

## License

Educational project of the Web Development course, ENSIA. The photos belong to their authors.
