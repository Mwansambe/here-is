# TechVault Implementation Roadmap

---

## Phase 0 — Design & UI Improvements

**Goal:** Polish the storefront look and feel before wiring real data so the UI is solid from day one.

### 0.1 Layout & Spacing
- [ ] Increase hero/slider height for desktop (currently too short on wide screens)
- [ ] Add a max-width container wrapper (`max-width: 1280px; margin: 0 auto`) so content doesn't stretch on large monitors
- [ ] Improve mobile padding consistency across sections
- [ ] Fix bottom nav active state — active item should have a visible pill/underline indicator

### 0.2 Product Cards
- [ ] ~~Card hover bounce~~ — **Done** (lift `-3px`, ease, no cubic-bezier overshoot)
- [ ] ~~Single product stretching full width~~ — **Done** (`max-width: 260px` on lone card)
- [ ] Add a subtle skeleton loader placeholder while products are loading (grey shimmer boxes)
- [ ] Show a "No products found" empty state with an icon when a category has 0 results
- [ ] Truncate long product names at 2 lines (`display:-webkit-box; -webkit-line-clamp:2`)

### 0.3 Typography & Colors
- [ ] Audit font sizes — product price should be visually heavier than the product name
- [ ] Ensure sufficient contrast on muted text (`var(--muted)`) in both light and dark mode (WCAG AA)
- [ ] Use consistent heading scale: section titles → `18px/700`, card titles → `13px/600`

### 0.4 Navigation & Header
- [ ] Add a sticky header that shrinks slightly on scroll (reduce padding, not full collapse)
- [ ] Cart badge count should animate (pop scale) when item is added
- [ ] Search bar: add a clear (✕) button when there's text in the field
- [ ] Active nav item highlight should use `var(--primary)` color, not just bold text

### 0.5 Slider / Banner
- [ ] ~~Image fills full slider box~~ — **Done**
- [ ] Add dot indicators below the slider so users know how many slides exist
- [ ] Auto-play should pause on hover
- [ ] Add a subtle gradient fade on left/right edges to hint at scrollability

### 0.6 Forms & Modals
- [ ] Auth modal (login/register): add proper input focus ring using `var(--primary)`
- [ ] Show a spinner inside the submit button while a request is in flight (disable button too)
- [ ] Form validation errors should appear inline below each field, not just a toast
- [ ] Password field: add a show/hide toggle eye icon

### 0.7 Order & Cart
- [ ] Cart sidebar: show a product thumbnail next to each item
- [ ] Empty cart state: show a shopping bag icon + "Your cart is empty" message
- [ ] Order confirmation page: show a large ✓ icon with order number and estimated delivery note

### 0.8 Dark Mode
- [ ] Audit all hardcoded `#fff` / `#000` colors — replace with CSS variables for dark mode compatibility
- [ ] Admin panel cards background should use `var(--card)` not a fixed color
- [ ] Toast notification should respect dark mode border color

### 0.9 Responsive / Mobile
- [ ] On screens < 360px wide, product grid should force single column
- [ ] Admin table: make horizontally scrollable on mobile (`overflow-x: auto`)
- [ ] Order detail view: stack columns vertically on mobile

---

## Phase 1 — Wire Frontend to Real API (Highest Priority)

**Goal:** Replace all hardcoded/localStorage logic in `public/index.html` with real API calls.

### 1.1 Config & Base URL
- [x] Add a `const API = ''` base URL at top of the `<script>` block (empty string = same origin, works locally and on Railway)
- [x] Add a `getToken()` helper that reads the Sanctum token from `localStorage`
- [x] Add an `authHeaders()` helper returning `{ Authorization: Bearer <token>, Accept: application/json }`

### 1.2 Categories & Products
- [x] On page load, replace hardcoded `CATEGORIES` array with `fetch('/api/categories')`
- [x] Replace hardcoded `PRODUCTS` array with `fetch('/api/products')` (supports `?category_id=` and `?search=` filters)
- [x] Replace hardcoded `BANNERS` array with `fetch('/api/banners')`
- [x] Wire `renderSlider()`, `renderCatSections()` to fire after the above fetches resolve
- [x] Added skeleton loader (shimmer cards) while catalog loads

### 1.3 Auth
- [x] `login()` → `POST /api/auth/login` with `{ login, password }`, store token + user in `localStorage`
- [x] `register()` → `POST /api/auth/register`, then auto-login
- [x] `logout()` → `POST /api/auth/logout` with token, clear `localStorage`
- [x] On boot, if token exists in storage call `GET /api/user` to restore session; if 401, clear token
- [x] Spinner on submit buttons during in-flight requests
- [x] Password show/hide toggle on all password fields
- [x] Search bar clear (✕) button

### 1.4 Cart & Orders
- [x] `placeOrder()` → `POST /api/orders` with cart items as `{ items: [{product_id, qty}], shipping_... }`
- [x] Order history page → `GET /api/orders` with token (real API, not localStorage)

### 1.5 Admin Panel
- [x] Replace fake `window._adminLogin()` with real login gated by `user.role === 'admin'`
- [x] Admin nav link auto-appears on login if role = admin
- [x] Admin stats → `GET /api/admin/products`, `GET /api/admin/orders`
- [x] Admin orders table → real API data

### 1.6 Dead Code Cleanup
- [x] Removed all hardcoded `CATEGORIES` / `PRODUCTS` / `BANNERS` arrays
- [x] `public/app.js` still exists but is unused — can be deleted in Phase 2

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
