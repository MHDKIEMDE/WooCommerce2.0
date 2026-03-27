# Guide de déploiement — Agri-Shop

## Prérequis serveur

| Composant | Version minimale |
|-----------|-----------------|
| PHP       | 8.3+            |
| MySQL     | 8.0+            |
| Redis     | 7.0+            |
| Nginx     | 1.24+           |
| Node.js   | 20+ (build uniquement) |

---

## 1. Déploiement initial

```bash
# Cloner le dépôt
git clone https://github.com/<org>/WooCommerce2.0.git /var/www/agri-shop
cd /var/www/agri-shop

# Dépendances PHP (sans dev)
composer install --no-dev --optimize-autoloader

# Variables d'environnement
cp .env.example .env
# Éditer .env : DB_, REDIS_, STRIPE_, FCM_, MAIL_
nano .env

# Clé applicative
php artisan key:generate

# Migrations + seeders
php artisan migrate --force
php artisan db:seed --force

# Build frontend
npm ci && npm run build

# Permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Cache de configuration (production)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Lien symbolique stockage
php artisan storage:link
```

---

## 2. Configuration Nginx

```nginx
server {
    listen 80;
    server_name agri-shop.fr www.agri-shop.fr;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name agri-shop.fr www.agri-shop.fr;

    root /var/www/agri-shop/public;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/agri-shop.fr/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/agri-shop.fr/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Sécurité — ne jamais exposer .env
    location ~ /\.env {
        deny all;
    }
}
```

---

## 3. Supervisor — Queue Workers

Créer `/etc/supervisor/conf.d/agri-shop-worker.conf` :

```ini
[program:agri-shop-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/agri-shop/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/agri-shop-worker.log
stopwaitsecs=3600
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start agri-shop-worker:*
```

---

## 4. Cron (scheduler Laravel)

Ajouter à la crontab de `www-data` :

```
* * * * * php /var/www/agri-shop/artisan schedule:run >> /dev/null 2>&1
```

Commandes planifiées :
- `app:stock-alert` — tous les jours à 08h00 (alerte email stock critique)

---

## 5. Redis — configuration recommandée

Fichier `/etc/redis/redis.conf` (extraits) :

```
maxmemory 256mb
maxmemory-policy allkeys-lru
save ""              # Désactiver la persistance pour le cache uniquement
```

Utiliser deux bases Redis séparées :
- `REDIS_CACHE_DB=0` → cache application (TTL auto)
- `REDIS_QUEUE_DB=1` → queue (persistance nécessaire)

---

## 6. Mises à jour (zero-downtime)

```bash
cd /var/www/agri-shop
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
supervisorctl restart agri-shop-worker:*
```

---

## 7. Variables d'environnement production

| Variable | Valeur recommandée |
|----------|-------------------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `LOG_LEVEL` | `error` |
| `CACHE_STORE` | `redis` |
| `QUEUE_CONNECTION` | `redis` |
| `SESSION_DRIVER` | `redis` |

---

## 8. Intégration Flutter — guide rapide

### Authentification
```
POST /api/v1/auth/register   → { name, email, password, password_confirmation, device_name }
POST /api/v1/auth/login      → { email, password, device_name } → { token }
POST /api/v1/auth/logout     → Bearer token requis
POST /api/v1/auth/refresh    → Bearer token → nouveau token
```

### Headers requis pour les requêtes authentifiées
```
Authorization: Bearer <token>
Accept: application/json
Content-Type: application/json
```

### Format des réponses
```json
{
  "success": true,
  "message": "OK",
  "data": { ... },
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 73
  }
}
```

### Gestion des erreurs
| Code | Signification |
|------|--------------|
| 401  | Token manquant ou invalide |
| 403  | Compte inactif ou rôle insuffisant |
| 422  | Erreur de validation (`errors` dans la réponse) |
| 404  | Ressource introuvable |
| 429  | Trop de requêtes (throttle) |

### Reset mot de passe (OTP)
```
POST /api/v1/auth/forgot-password   → { email }           → OTP envoyé par email
POST /api/v1/auth/verify-reset-code → { email, otp }      → { reset_token }
POST /api/v1/auth/reset-password    → { reset_token, password, password_confirmation }
```

### Notifications push
- Envoyer le FCM token à l'API au login/register via le champ `fcm_token`
- Le serveur gère automatiquement la mise à jour du token en base
