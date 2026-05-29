#!/bin/bash
# CCC Ops — Git commit & push helper
# Run from project root: bash GIT_COMMIT.sh

set -e

# 1. Remove stale lock file (created by sandbox, can only be removed on your machine)
if [ -f ".git/index.lock" ]; then
    echo "Removing stale .git/index.lock..."
    rm -f .git/index.lock
fi

# 2. Stage everything
git add -A

# 3. Commit
git commit -m "feat: Phase 1 complete — public site, auth, dashboard, SEO, logo, preloader

## What's in this commit

### Core Foundation
- Laravel 13.12 + PHP 8.4 confirmed working
- Livewire v3.8, Spatie Permission v6.25, Activitylog, MediaLibrary
- laravel/ai + laravel/boost installed

### Public Website (SEO-ready)
- Home, About, Services, Projects, Contact pages
- x-seo Blade component: title, description, OG, Twitter Cards, JSON-LD
- LocalBusiness + WebSite + WebPage structured data (schema.org)
- Dynamic sitemap at /sitemap.xml
- robots.txt with proper allow/disallow + AI bot blocking

### Logo & Favicon
- public/images/ccc_ops_logo.png used across all layouts
- PNG favicon + apple-touch-icon on all layouts (public, auth, dashboard)
- Real logo in sidebar, nav, footer, auth screen

### Preloader
- Full-screen site-entry preloader (sessionStorage gated)
- Animated logo + progress bar
- Auto-hides on window load, never shows between page navigations
- 4s safety timeout prevents user being blocked

### Auth
- Login, Forgot Password, Register (admin-only notice) pages
- Seeded: developer@ccc-ops.local + admin@constructivecleaningco.com

### Dashboard
- Livewire StatsOverview (auto-refreshes every 60s)
- Livewire SettingsForm (reactive toggles, live save spinner)
- Sidebar with real logo, collapsible, mobile-friendly
- Audit Log, System Health, AI Tools, Guided Tours pages

### Services, WiPay, AI
- WiPayService (sandbox/live, TTD, callback)
- AiService (laravel/ai stub, safety-gated)
- Wasabi S3 disk configured

### Seeders
- 10 roles, 100+ permissions (22 groups)
- Developer + Admin users
- Company settings (with logo path)
- Guided tours (dashboard + settings)

### Tests
- AuthTest, PublicPagesTest, RolesAndPermissionsTest"

# 4. Push
git push -u origin main

echo ""
echo "✅ Pushed to https://github.com/Jevon420/ccc_ltd.git"
