# CCC Ops — Phase 1 Setup Guide

Run every command from the project root (`constructive-ops/`).

---

## Step 1 — Install Composer Packages

```bash
composer require \
  livewire/livewire:^3.0 \
  spatie/laravel-permission:^6.0 \
  spatie/laravel-activitylog:^4.0 \
  spatie/laravel-medialibrary:^11.0 \
  prism-php/prism:^0.70 \
  league/flysystem-aws-s3-v3:^3.0 \
  aws/aws-sdk-php:^3.0
```

> **Laravel Boost**: Not a published Composer package — the project uses Livewire + Alpine.js + Tailwind v4 as the "boost" layer.  
> **Laravel Nightwatch**: Currently in beta/preview. Install when your account is ready: `composer require laravel/nightwatch`  
> **Laravel AI SDK**: Using `prism-php/prism` — the most mature and Laravel-native AI SDK.

---

## Step 2 — Publish Vendor Configs & Migrations

```bash
# Spatie Permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Spatie Activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"

# Spatie Media Library
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"

# Livewire
php artisan livewire:publish --config
```

---

## Step 3 — Run All Migrations

```bash
php artisan migrate
```

---

## Step 4 — Run Seeders

```bash
php artisan db:seed
```

This seeds:
- All 10 roles and all permissions
- **Developer user**: `developer@ccc-ops.local` / `Dev@CCC#2025!`
- **Admin user**: `admin@constructivecleaningco.com` / `Admin@CCC#2025!`
- Default company settings
- Sample guided tours (dashboard + settings)

> ⚠️ **Change passwords** via `.env` before running on production:
> ```env
> DEVELOPER_PASSWORD=YourStrongPasswordHere
> ADMIN_PASSWORD=AnotherStrongPasswordHere
> ```

---

## Step 5 — Storage Link

```bash
php artisan storage:link
```

---

## Step 6 — Install npm & Build Assets

```bash
npm install
npm run build
```

For local development with hot reload:
```bash
npm run dev
```

---

## Step 7 — Run Laravel Pint (Code Formatting)

```bash
./vendor/bin/pint
```

---

## Step 8 — Run Tests

```bash
php artisan test
```

---

## Step 9 — Set Up Git Remote & Push

```bash
git remote add origin https://github.com/Jevon420/ccc_ltd.git
git add .
git commit -m "feat: Phase 1 — CCC Ops foundation"
git push -u origin main
```

---

## Step 10 — Start the Dev Server

```bash
composer dev
# or individually:
php artisan serve
npm run dev
php artisan queue:listen
```

---

## 🔐 Default Login Credentials

| Role      | Email                                 | Password          |
|-----------|---------------------------------------|-------------------|
| Developer | `developer@ccc-ops.local`             | `Dev@CCC#2025!`   |
| Admin     | `admin@constructivecleaningco.com`    | `Admin@CCC#2025!` |

**These are the same users for prod** — just update the passwords via `.env` before deploying.

---

## 🌍 Environment Variables Required

All already in your `.env`. For reference:

| Key                      | Purpose                            |
|--------------------------|------------------------------------|
| `WASABI_*`               | Wasabi S3 storage (already set)    |
| `WIPAY_*`                | WiPay payment gateway (already set)|
| `ANTHROPIC_API_KEY`      | For AI features (Phase 2)          |
| `DEVELOPER_PASSWORD`     | Developer user password            |
| `ADMIN_PASSWORD`         | Admin user password                |

---

## 📦 Packages Decision Log

| Package | Choice | Reason |
|---------|--------|--------|
| Auth | Custom Blade/Livewire | Full control over enterprise UI without Breeze overriding layout |
| AI SDK | `prism-php/prism` | Best Laravel-native AI SDK, supports Anthropic + OpenAI + others |
| Storage | `league/flysystem-aws-s3-v3` | S3-compatible, works with Wasabi out of the box |
| Payments | Custom `WiPayService` | WiPay has no official Laravel package — clean service class is best |
| Tours | `driver.js` | Lightweight, no-dependency tour library, works perfectly with Alpine.js |
| Logging | `spatie/laravel-activitylog` | Industry standard for Laravel audit trails |
| Permissions | `spatie/laravel-permission` | Industry standard, works perfectly with Livewire + Blade |

---

## 🚀 Phase 2 — What's Next

When ready, prompt Phase 2:

> "We are starting Phase 2 of CCC Ops. Build: Clients, Service Types, Job Requests, Jobs (with status workflow), 
> Work Orders, Job Assignments. Include Livewire components for data tables, modals, and filters. 
> Use Spatie Media Library for job photos. Seed sample data."
