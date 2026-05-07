# Deployment Guide

This guide covers production deployment strategies.

## 1. Environment
- **Server:** Ubuntu / Debian with Nginx.
- **Process Manager:** Supervisor (for Laravel queues).
- **Database:** Managed MySQL or PostgreSQL instances.
- **Storage:** AWS S3 or AliYun OSS (configured via `.env`).

## 2. Production Steps
1. **Build Frontend:**
   ```bash
   npm run build
   ```
2. **Cache Configuration:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
3. **Run Migrations:**
   ```bash
   php artisan migrate --force
   ```
4. **Deploy Flutter:**
   - Build using `flutter build apk` or `flutter build ipa`.
   - Distribute via App Center, Firebase App Distribution, or App Store/Play Store.
