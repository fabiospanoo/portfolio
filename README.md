# portfolio

Personal developer portfolio and technical blog built with Laravel: defined routes with dedicated controllers, reusable Blade components, and dynamic project tagging. The public interface presents completed works, written posts, and a contact form with honeypot spam filtering, attachment support, and email delivery via Resend. An administrative area secured by rate limiting and time-based two-factor authentication (TOTP 2FA) provides full content management for projects and posts, alongside an audit activity log. Core functionality and security workflows are tested with an automated Pest test suite.

## Installation and setup

```bash
composer setup
npm run dev
```

Or manually:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```
