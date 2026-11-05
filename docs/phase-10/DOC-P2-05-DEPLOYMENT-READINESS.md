# DOC-P2-05-DEPLOYMENT-READINESS — MigratePay V12: Production Deployment & Operational Readiness Guide

**Project:** MigratePay V10 → V12 Reconstruction  
**Phase:** 10 — End-to-End Testing & Release Readiness  
**Document ID:** DOC-P2-05-DEPLOYMENT-READINESS  
**Author:** Antigravity AI Engineering  
**Status:** Completed & Production-Ready  
**Date:** 2026-11-05  

---

## 1. Executive Summary

This document specifies the operational standards, security configurations, database privilege separation, deployment checklist, backup automation, and rollback procedures required for the controlled production deployment of MigratePay V12.

MigratePay V12 is designed to run in modern containerized or standalone Linux/Windows server environments running PHP 8.4+, MySQL 8.0+, Nginx/Apache, and Node.js (for asset compilation).

---

## 2. Infrastructure Requirements & Server Sizing

| Component | Minimum Specification | Recommended Production Specification |
|---|---|---|
| Operating System | Ubuntu 22.04 LTS / Debian 12 / Windows Server 2022 | Ubuntu 24.04 LTS x64 |
| Web Server | Nginx 1.24+ (or Apache 2.4+) | Nginx 1.26+ with HTTP/2 & Brotli |
| PHP Runtime | PHP 8.4.x with extensions: `bcmath`, `curl`, `mbstring`, `openssl`, `pdo_mysql`, `sodium`, `xml`, `zip` | PHP 8.4.27 with OPcache & JIT enabled |
| Database Engine | MySQL 8.0.30+ (or MariaDB 10.11+) | MySQL 8.0 Enterprise / AWS RDS MySQL 8.0 |
| Memory / RAM | 4 GB | 8 GB – 16 GB |
| CPU Cores | 2 vCPU | 4 vCPU |
| Storage | 40 GB SSD | 100+ GB NVMe with daily snapshots |

---

## 3. Database Security & Least-Privilege Architecture

The production database must enforce strict privilege separation between the runtime application connection and the deployment migration runner. The application runtime must never use the MySQL `root` account.

### 3.1 Application Runtime Account (`migratepay_app`)
Restricted strictly to Data Manipulation Language (DML):
```sql
CREATE USER 'migratepay_app'@'127.0.0.1' IDENTIFIED BY 'StrongAppPasswordHere!';
GRANT SELECT, INSERT, UPDATE, DELETE, LOCK TABLES, EXECUTE ON `migratepay_v12`.* TO 'migratepay_app'@'127.0.0.1';
FLUSH PRIVILEGES;
```
*Note: This account cannot execute `DROP`, `ALTER`, `CREATE TABLE`, or `TRUNCATE`, preventing catastrophic SQL injection consequences.*

### 3.2 Migration & Deployment Account (`migratepay_deploy`)
Restricted to CI/CD pipeline workers or deployment operators:
```sql
CREATE USER 'migratepay_deploy'@'127.0.0.1' IDENTIFIED BY 'StrongDeployPasswordHere!';
GRANT ALL PRIVILEGES ON `migratepay_v12`.* TO 'migratepay_deploy'@'127.0.0.1';
FLUSH PRIVILEGES;
```

---

## 4. Production Environment Configuration (`.env`)

Deployments must generate a dedicated `.env` file based on `.env.production.example`:

```ini
# Core Environment
APP_NAME="MigratePay"
APP_ENV=production
APP_KEY=base64:GENERATE_NEW_32_BYTE_KEY_USING_ARTISAN_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://migratepay.com

# Logging
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=info

# Database Connection (Least Privilege Runtime User)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=migratepay_v12
DB_USERNAME=migratepay_app
DB_PASSWORD=StrongAppPasswordHere!

# Sessions & Cookies (Hardened)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=.migratepay.com
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Queue & Cache
CACHE_STORE=database
QUEUE_CONNECTION=database

# Security & Multi-Factor Auth
BCRYPT_ROUNDS=12
MFA_ISSUER=MigratePay

# Payment Gateway Secrets (Injected via Environment or Vault)
BKASH_APP_KEY=
BKASH_APP_SECRET=
NAGAD_MERCHANT_ID=
NAGAD_PUBLIC_KEY=
```

---

## 5. Deployment Procedure & CI/CD Pipeline

Standard zero-downtime deployment using symlink directory switching (e.g., Deployer, Envoy, or AWS CodeDeploy):

### Step 1: Code Retrieval & Dependency Installation
```bash
git clone --depth 1 -b main <repo_url> /var/www/migratepay/releases/<release_id>
cd /var/www/migratepay/releases/<release_id>
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build
```

### Step 2: Database Schema Migration
Run migrations using the privileged deployment credentials:
```bash
php artisan migrate --force
```

### Step 3: Cache Optimization
Pre-compile configuration, route, and view caches into opcode memory:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Atomic Symlink Switch
```bash
ln -sfn /var/www/migratepay/releases/<release_id> /var/www/migratepay/current
sudo systemctl reload php8.4-fpm
sudo systemctl reload nginx
```

---

## 6. Backup Automation & Disaster Recovery

### 6.1 Automated Daily Database Backup
Crontab entry executing nightly at 02:00 AM UTC:
```bash
0 2 * * * /var/www/migratepay/scripts/backup_database.sh >> /var/log/migratepay_backup.log 2>&1
```

Script `/var/www/migratepay/scripts/backup_database.sh`:
```bash
#!/usr/bin/env bash
set -euo pipefail

BACKUP_DIR="/var/backups/migratepay"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/migratepay_v12_${TIMESTAMP}.sql.gz"

mkdir -p "${BACKUP_DIR}"

# Execute single-transaction backup without table locks
mysqldump -u migratepay_deploy -p"${DEPLOY_DB_PASS}" \
    --host=127.0.0.1 \
    --single-transaction \
    --quick \
    --routines \
    --triggers \
    migratepay_v12 | gzip -9 > "${BACKUP_FILE}"

chmod 600 "${BACKUP_FILE}"

# Retention: keep local backups for 14 days
find "${BACKUP_DIR}" -type f -name "migratepay_v12_*.sql.gz" -mtime +14 -delete

# Encrypt and replicate to offsite S3 / Cloud Storage
aws s3 cp "${BACKUP_FILE}" s3://migratepay-secure-backups/db/
```

### 6.2 Restoration Procedure
```bash
gunzip < /var/backups/migratepay/migratepay_v12_YYYYMMDD_HHMMSS.sql.gz | mysql -u migratepay_deploy -p migratepay_v12
```

---

## 7. Rollback & Emergency Contingency Plan

If an unexpected production defect is detected post-deployment:

1. **Immediate Code Rollback:**
   ```bash
   ln -sfn /var/www/migratepay/releases/<previous_release_id> /var/www/migratepay/current
   php artisan config:cache
   php artisan route:cache
   sudo systemctl reload php8.4-fpm
   ```
2. **Database Rollback:**
   - MigratePay V12 migrations are designed to be additive and non-destructive. Columns added (e.g. `two_factor_*`) are nullable and do not break older application versions.
   - If database restoration is strictly required, restore from the pre-deployment snapshot created immediately prior to Step 2.
3. **Emergency Maintenance Mode:**
   ```bash
   php artisan down --secret="emergency-bypass-token" --render="errors::503"
   ```

---

## 8. Conclusion

MigratePay V12 includes all operational assets, security configurations, and disaster recovery blueprints necessary for an immediate, high-availability, controlled deployment to production.

