# Satlok Ashram Goa - Management Application

![Deployment Status](https://github.com/AkashSiwach/satlok-ashram-goa/actions/workflows/laravel.yml/badge.svg)

A comprehensive ERP solution for managing the inventory, members (Bhagats), and financial records of Satlok Ashram Goa. Built with **Laravel 11** and **FilamentPHP v3**, deployed on **AWS EC2**.

## 🚀 Tech Stack
* **Framework:** Laravel 11
* **Admin Panel:** FilamentPHP v3
* **Frontend:** Livewire + TailwindCSS
* **Database:** MySQL
* **Server:** AWS EC2 (Ubuntu/Nginx)
* **CI/CD:** GitHub Actions

---

## 📂 Modules & Features

### 1. 📚 Book Inventory System
Manages the end-to-end lifecycle of books from purchase to sale.
* **Book Master:** Catalog of all available literature.
* **Purchase Records:** Invoice management with auto-calculated totals and stock injection.
* **Stock Balances:** Periodic physical stock verification sessions with user tracking.
* **Inventory (Live):** Single source of truth for current stock levels.
* **Counter Sales:** POS system for distributing books (Sales, SMS, Free distribution).

### 2. 🧘 Bhagat Management (Members)
Database of ashram members.
* **Bhagat Profiles:** Name, Namdan logic, contact info.
* **Geography Logic:** Dynamic address selection (State -> District/Zilla -> Pincode).

### 3. 💰 Finance
* **Donations:** Tracking financial contributions and generating receipts.

### 4. ⚙️ System Settings
* **User Management:** Admin access controls.
* **Geography Master:** Configurable States, Districts, Zillas, and Pincodes.

---

## 🛠️ Local Development Setup (How to run this on your laptop)

If you have cloned this repository for the first time, follow these steps:

1.  **Clone the Repo**
    ```bash
    git clone [https://github.com/AkashSiwach/satlok-ashram-goa.git](https://github.com/AkashSiwach/satlok-ashram-goa.git)
    cd satlok-ashram-goa
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    npm install && npm run build
    ```

3.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Update your `.env` file with your local database credentials.*

4.  **Database Setup**
    ```bash
    php artisan migrate --seed
    ```

5.  **Filament Optimization (Crucial)**
    ```bash
    php artisan filament:optimize
    ```

6.  **Run Server**
    ```bash
    php artisan serve
    ```
    Access the admin panel at: `http://127.0.0.1:8000/admin`

---

## 🚀 Deployment (CI/CD)

**DO NOT EDIT FILES DIRECTLY ON THE SERVER.**

This project uses **GitHub Actions** for automated deployment.
1.  Make changes locally.
2.  Push to the `main` branch.
3.  GitHub automatically:
    * Logs into AWS EC2.
    * Pulls the code.
    * Runs Migrations.
    * Optimizes Filament assets.
    * Clears caches.

**Deployment Script:** `.github/workflows/laravel.yml`

---

## 📁 Key File Structure

### Book Module
* **Models:** `app/Models/Book.php`, `Purchase.php`, `StockBalance.php`, `Sale.php`
* **Resources:** `app/Filament/Resources/BookResource.php` (+ Purchase, Sale, StockBalance)

### Bhagat Module
* **Model:** `app/Models/Bhagat.php`
* **Resource:** `app/Filament/Resources/BhagatResource.php`

### Settings Module
* **Models:** `User.php`, `State.php`, `District.php`, `Zilla.php`
* **Resources:** Managed in `app/Filament/Resources/`

---

## 🔒 Security Note
The `.env` file containing database passwords and AWS keys is **excluded** from this repository. If you need access to production credentials, contact the project administrator.

---
**Maintainer:** Akash Siwach (akash@classicgroup.asia)
