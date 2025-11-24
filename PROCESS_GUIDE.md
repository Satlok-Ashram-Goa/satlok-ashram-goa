# Internal Process & Architecture Guide

## 1. The Deployment Workflow (CI/CD)
We no longer use FileZilla or edit files on the server using `nano`.

**The Cycle:**
1.  **Local:** You write code on your laptop.
2.  **Git:** You push code to GitHub (`git push origin main`).
3.  **Action:** GitHub sees the update and triggers the `Deploy to EC2` workflow.
4.  **AWS:** The server updates automatically.

**If the deployment fails:**
1.  Go to the "Actions" tab on GitHub.
2.  Click the failed run (Red X).
3.  Read the error log.
4.  Fix the error locally and push again.

## 2. Important System Files

| File Path | Purpose | Critical Note |
| :--- | :--- | :--- |
| `.github/workflows/deploy.yml` | **The Brain.** Controls how the app is deployed. | Don't touch unless changing deployment logic. |
| `.env` | **The Secrets.** Holds DB passwords. | Exists ONLY on the Server and Local laptop. Never on GitHub. |
| `composer.json` | **Dependencies.** Lists all PHP packages. | If you edit this, run `composer update` locally before pushing. |
| `app/Providers/Filament/AdminPanelProvider.php` | **Theme Config.** Controls logo, colors, and layout. | Contains `maxContentWidth(null)` for full-width layout. |

## 3. Database Schema Overview

### Inventory Relationship Flow
1.  **Book** is created.
2.  **Purchase** (Invoice) adds quantity to **Inventory**.
3.  **Sale** subtracts quantity from **Inventory**.
4.  **StockBalance** overrides **Inventory** (for corrections).

### Geography Relationship Flow
* **State** has many **Districts**.
* **District** has many **Zillas**.
* **Zilla** has many **Pincodes**.
* **Bhagat** belongs to a Zilla/State based on address.

## 4. Server Maintenance Commands
*Only use these if the site crashes and you need to restart manually via SSH.*

```bash
# SSH into Server
ssh -i "path/to/key.pem" ubuntu@your-ip-address

# Fix Permissions (Common fix for "Permission Denied")
sudo chown -R www-data:www-data /var/www/satlok_app
sudo chmod -R 775 storage bootstrap/cache

# Clear Everything
php artisan optimize:clear
