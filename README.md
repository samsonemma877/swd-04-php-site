# Prime Cleaners Ltd

A responsive multi-page website for a fictional cleaning business, built as a PHP 8 learning project. It includes shared page templates, data-driven services and team profiles, a validated contact form, and a password-protected local messages page.

## Features

- Shared PHP header, footer, and site configuration.
- Responsive navigation that marks the current page.
- Data-driven service cards and individual service detail pages.
- Team profiles rendered from an array.
- Contact form with server-side validation, sticky values, CSRF protection, and JSON Lines storage.
- Messages page with password sign-in, newest-first ordering, and escaped output.
- Responsive styling for desktop and mobile layouts.

## Tech stack

- PHP 8 with strict typing
- HTML5
- CSS3
- Apache through XAMPP
- JSON Lines (`.jsonl`) for local contact form submissions

## Run with XAMPP

1. Install and start **Apache** in the XAMPP Control Panel.
2. Keep the project in this exact folder:

   `C:\xampp\htdocs\swd\04-php-site`

3. Open the site at:

   `http://localhost/swd/04-php-site/`

4. Visit the messages page at:

   `http://localhost/swd/04-php-site/messages.php`

   Before using the messages page locally, replace the `SET_A_LOCAL_PASSWORD` placeholder assigned to `$messagesPassword` in `includes/config.php` with a password of your choice.

The contact form creates `storage/messages.jsonl` the first time a valid message is submitted. The `storage/.htaccess` file asks Apache to deny direct web access to that folder.

## Project structure

```text
04-php-site/
├── about.php
├── contact.php
├── index.php
├── info.php
├── messages.php
├── README.md
├── service.php
├── services.php
├── assets/
│   └── css/
│       └── style.css
├── includes/
│   ├── config.php
│   ├── data.php
│   ├── footer.php
│   ├── functions.php
│   └── header.php
└── storage/
    ├── .htaccess
    └── messages.jsonl  (created locally; ignored by Git)
```

The generated `storage/messages.jsonl` file is excluded from Git because it can contain personal contact details.

## Local learning security note

The messages password is hard-coded in a PHP configuration file and is shared by anyone who can read the project files. This is acceptable only for a local learning exercise. It is not production-grade authentication: there is no user account system, password hashing/rotation workflow, rate limiting, or broader access-control setup. A real deployment should use proper authentication, secure secret storage, HTTPS, and server-level protection for submission data. Keep `storage` outside the public web root when possible; `.htaccess` protection depends on Apache being configured to honor it.

The existing `info.php` is a PHP configuration diagnostic page. Do not leave it accessible on a public server because it can reveal server and PHP environment details.

## What I learned

- I learned how to: **[fill this in]**
- A PHP feature I understand better now: **[fill this in]**
- Something I would improve next: **[fill this in]**
