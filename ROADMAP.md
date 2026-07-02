# TechVault Implementation Roadmap

---

## Phase 1 — Wire Frontend to Real API (Highest Priority)

**Goal:** Replace all hardcoded/localStorage logic in `public/index.html` with real API calls.

### 1.1 Config & Base URL
- [ ] Add a `const API = ''` base URL at top of the `<script>` block (empty string = same origin, works locally and on Railway)
- [ ] Add a `getToken()` helper that reads the Sanctum token from `localStorage`
- [ ] Add an `authHeaders()` helper returning `{ Authorization: Bearer <token>, Accept: application/json }`

### 1.2 Categories & Products
- [ ] On page load, replace hardcoded `CATEGORIES` array with `fetch('/api/categories')`
- [ ] Replace hardcoded `PRODUCTS` array with `fetch('/api/products')` (supports `?category_id=` and `?search=` filters)
- [ ] Replace hardcoded `BANNERS` array with `fetch('/api/banners')`
- [ ] Wire `renderSlider()`, `renderCatSections()` to fire after the above fetches resolve

### 1.3 Auth
- [ ] `login()` → `POST /api/auth/login` with `{ login, password }`, store token + user in `localStorage`
- [ ] `register()` → `POST /api/auth/register`, then auto-login
- [ ] `logout()` → `POST /api/auth/logout` with token, clear `localStorage`
- [ ] On boot, if token exists in storage call `GET /api/user` to restore session; if 401, clear token

### 1.4 Cart & Orders
- [ ] `placeOrder()` → `POST /api/orders` with cart items as `{ items: [{product_id, qty, price}] }`
- [ ] Order history page → `GET /api/orders` with token
- [ ] Order detail → `GET /api/orders/{id}`

### 1.5 Admin Panel
- [ ] Replace fake `window._adminLogin()` with real login gated by `user.role === 'admin'`
- [ ] Admin stats → `GET /api/admin/products`, `/api/admin/orders`, compute counts client-side
- [ ] Admin orders table → `GET /api/admin/orders`
- [ ] Admin product CRUD → `POST/PUT/DELETE /api/admin/products`
- [ ] Admin category CRUD → `POST/PUT/DELETE /api/admin/categories`
- [ ] Admin banner CRUD → `POST/DELETE /api/admin/banners`

### 1.6 Dead Code Cleanup
- [ ] Delete or fully rewrite `public/app.js` — currently unused with hardcoded `127.0.0.1`

---

## Phase 2 — Fix Production Blockers

### 2.1 Database Seeder on Deploy
- [ ] In `nixpacks.toml` start command, remove `php artisan db:seed --force` (or wrap in a one-time flag)
- [ ] In `DatabaseSeeder.php`, change `updateOrCreate` to only set password on **create**, not update:
  ```php
  User::firstOrCreate(
      ['email' => 'admin@techvault.co.tz'],
      ['name' => 'Admin TechVault', 'phone' => '+255700000001', 'password' => 'admin123', 'role' => 'admin']
  );
  ```

### 2.2 Git / Secrets
- [ ] Run `git rm --cached .env` so `.env` stops being tracked
- [ ] Run `php artisan key:generate` and update Railway env var `APP_KEY`
- [ ] Never commit real passwords — update seeder to use `env('ADMIN_PASSWORD', 'admin123')`

### 2.3 File Storage (Images)
- [ ] Fill in Railway env vars: `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_BUCKET`, `AWS_DEFAULT_REGION`
- [ ] Set `FILESYSTEM_DISK=s3` in Railway so uploaded images survive redeploys
- [ ] Alternatively use Cloudflare R2 (S3-compatible, free 10GB)

### 2.4 CORS
- [ ] Run `php artisan config:publish --tag=cors` (Laravel 13) or create `config/cors.php`
- [ ] Set `allowed_origins` to your Railway domain and local dev origin

### 2.5 Railway Config Conflict
- [ ] Choose **one**: keep `Dockerfile` (more reliable) or `nixpacks.toml` — delete the other
- [ ] Recommended: keep `Dockerfile`, delete `nixpacks.toml`

---

## Phase 3 — Complete the Admin API

### 3.1 Order Management
- [ ] Add `PUT /api/admin/orders/{order}` route to update `order_status` and `payment_status`
- [ ] Add `OrderController@adminUpdate` method with validation

### 3.2 Banner Update
- [ ] Add `PUT /api/admin/banners/{banner}` route and `BannerController@update`

### 3.3 Product Images
- [ ] `ProductController@store/update` should accept `images[]` file upload and store to disk/S3
- [ ] Return full image URLs in API responses

---

## Phase 4 — Password Reset (OTP Delivery)

**Current state:** OTP is generated but never sent in production.

- [ ] Choose delivery method: **email** (free via Mailtrap/Resend) or **SMS** (Africa's Talking for TZ)
- [ ] For email: set `MAIL_MAILER=smtp` + SMTP credentials in `.env`, use `Mail::to()->send(new OtpMail($code))`
- [ ] For SMS: install Africa's Talking SDK, send via their API
- [ ] Create `OtpMail` Mailable or SMS notification class
- [ ] Call it in `AuthController::forgotPasswordSendCode()`

---

## Phase 5 — Payment Gateway

**Current state:** `payment_method` is stored as a string, `payment_status` stays `pending` forever.

- [ ] Choose provider: **Azam Pay** (most common in TZ) or **Stripe** (international)
- [ ] Install SDK: `composer require azampay/azampay-php` or `composer require stripe/stripe-php`
- [ ] Add `POST /api/orders/{order}/pay` route
- [ ] On success callback, update `payment_status = 'paid'` and `order_status = 'processing'`
- [ ] Store transaction reference in orders table (add `transaction_ref` column via migration)

---

## Phase 6 — Tests

- [ ] `tests/Feature/AuthTest.php` — register, login, logout, forgot password
- [ ] `tests/Feature/CatalogTest.php` — list categories, products, banners, search
- [ ] `tests/Feature/OrderTest.php` — place order, view orders, admin update status
- [ ] `tests/Feature/AdminTest.php` — CRUD categories, products, banners (admin-only)
- [ ] Run: `php artisan test`

---

## Quick Wins (Do Anytime)

| Fix | File | Effort |
|-----|------|--------|
| Product card bounce animation smoothed | `public/index.html` | ✅ Done |
| Slider image fills full box | `public/index.html` | ✅ Done |
| APP_URL set correctly | `.env` | ✅ Done |
| Migrations clean | DB | ✅ Done |
| Admin login tested & working | API | ✅ Done |

---

## Start Order Recommendation

```
Phase 1 (1.1 → 1.3) → Phase 2.1 → Phase 2.2 → Phase 1 (1.4 → 1.6) → Phase 2.3-2.5 → Phase 3 → Phase 4 → Phase 5 → Phase 6
```
