# Setup Guide

This document outlines how to set up the development environment for both the Laravel backend and the Flutter mobile application.

## 1. Prerequisites
- PHP 8.2+
- Composer
- Node.js 20+
- Flutter SDK (latest stable)
- MySQL/PostgreSQL

## 2. Backend Setup
1. **Clone the repository.**
2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```
3. **Environment Configuration:**
   - Copy `.env.example` to `.env`.
   - Configure database credentials (`DB_CONNECTION`, `DB_HOST`, etc.).
   - Run `php artisan key:generate`.
4. **Database Migration & Seeding:**
   ```bash
   php artisan migrate --seed
   ```
5. **Start Servers:**
   ```bash
   php artisan serve
   npm run dev
   ```

## 3. Mobile (Flutter) Setup
1. **Navigate to mobile directory:**
   ```bash
   cd mobile
   ```
2. **Get dependencies:**
   ```bash
   flutter pub get
   ```
3. **Configure API URL:**
   - Update `lib/config/api_config.dart` with the backend API URL.
4. **Run App:**
   ```bash
   flutter run
   ```
