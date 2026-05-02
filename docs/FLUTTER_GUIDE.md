# Guide d'intégration Flutter — Monghetto API

**Base URL :** `https://shop.monghetto.com/api/v1`  
**Version :** v1  
**Auth :** Bearer Token (Laravel Sanctum)

---

## 1. Authentification

### Inscription
```
POST /auth/register
Content-Type: application/json

{
  "name": "Marie Kouassi",
  "email": "marie@example.com",
  "password": "motdepasse",
  "password_confirmation": "motdepasse",
  "device_name": "iPhone 15 Pro",
  "fcm_token": "fcm-token-ici"
}
```

**Réponse :**
```json
{
  "success": true,
  "message": "Compte créé avec succès.",
  "data": {
    "token": "1|abcdef...",
    "user": { "id": 42, "name": "Marie Kouassi", "email": "marie@example.com", "role": "buyer" }
  }
}
```

> **Important :** stocker le token et l'inclure dans tous les appels suivants.

### Connexion
```
POST /auth/login

{
  "email": "marie@example.com",
  "password": "motdepasse",
  "device_name": "iPhone 15 Pro",
  "fcm_token": "fcm-token-ici"
}
```

> Un même `device_name` révoque l'ancien token. Utiliser un identifiant stable par appareil (ex. UUID persistant).

### Déconnexion
```
POST /auth/logout
Authorization: Bearer <token>
```

### Déconnexion de tous les appareils
```
POST /auth/logout-all
Authorization: Bearer <token>
```

### Refresh token
```
POST /auth/refresh
Authorization: Bearer <token>
```

Retourne un nouveau token et révoque l'ancien. À appeler avant expiration (30 jours par défaut).

### Réinitialisation du mot de passe (OTP)
```
# Étape 1 — Demander l'OTP
POST /auth/forgot-password
{ "email": "marie@example.com" }

# Étape 2 — Vérifier l'OTP (valable 15 min)
POST /auth/verify-reset-code
{ "email": "marie@example.com", "otp": "123456" }
→ { "reset_token": "xyz..." }

# Étape 3 — Nouveau mot de passe (token valable 10 min)
POST /auth/reset-password
{ "reset_token": "xyz...", "password": "nouveau", "password_confirmation": "nouveau" }
```

---

## 2. Headers requis

```
Authorization: Bearer <token>      ← tous les endpoints protégés
Accept: application/json           ← obligatoire pour recevoir du JSON
Content-Type: application/json     ← pour les requêtes POST/PATCH
```

---

## 3. Format de réponse

### Succès
```json
{
  "success": true,
  "message": "Produits récupérés.",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 8,
    "per_page": 15,
    "total": 112
  }
}
```

### Erreur de validation
```json
{
  "success": false,
  "message": "Les données fournies sont invalides.",
  "errors": {
    "email": ["L'adresse e-mail est déjà utilisée."],
    "password": ["Le mot de passe doit contenir au moins 8 caractères."]
  }
}
```

### Codes HTTP

| Code | Signification |
|------|--------------|
| 200  | Succès |
| 201  | Ressource créée |
| 401  | Token manquant ou invalide → rediriger vers login |
| 403  | Compte inactif ou rôle insuffisant |
| 404  | Ressource introuvable |
| 422  | Erreur de validation (voir `errors`) |
| 429  | Trop de requêtes — attendre avant de retenter |
| 500  | Erreur serveur |

---

## 4. Catalogue

### Produits
```
GET /products                          # Liste paginée
GET /products?category=fruits          # Filtrer par catégorie (slug)
GET /products?brand=bio-farm           # Filtrer par marque (slug)
GET /products?search=mango             # Recherche plein texte
GET /products?sort=price_asc           # Tri : price_asc | price_desc | newest | rating
GET /products?featured=1               # Produits mis en avant
GET /products?page=2&per_page=20       # Pagination

GET /products/{slug}                   # Fiche produit complète
```

### Catégories & Marques
```
GET /categories
GET /categories/{slug}/products
GET /brands
```

### Recherche
```
GET /search?q=ananas
```

---

## 5. Panier

Le panier est lié au token Bearer. Un panier guest (non authentifié) peut être fusionné à la connexion.

```
GET    /cart                           # Contenu du panier
POST   /cart/items                     # Ajouter un article
       { "product_id": 5, "quantity": 2, "variant_id": null }
PATCH  /cart/items/{id}               # Modifier la quantité
       { "quantity": 3 }
DELETE /cart/items/{id}               # Supprimer un article
DELETE /cart                          # Vider le panier

POST   /cart/coupon                   # Appliquer un coupon
       { "code": "PROMO10" }
DELETE /cart/coupon                   # Retirer le coupon
POST   /cart/coupon/check             # Vérifier sans appliquer
       { "code": "PROMO10" }
```

---

## 6. Checkout & Paiement (Stripe)

### Étape 1 — Récapitulatif
```
GET /checkout
```

### Étape 2 — Créer la commande + obtenir le PaymentIntent
```
POST /checkout
{
  "address_id": 3,
  "notes": "Laisser en bas de l'immeuble"
}
```

**Réponse :**
```json
{
  "success": true,
  "data": {
    "order_id": 18,
    "order_number": "CMD-2026-00018",
    "client_secret": "pi_xxxxx_secret_yyyyy",
    "amount": 12500,
    "currency": "xof"
  }
}
```

### Étape 3 — Confirmer le paiement côté client Flutter

```dart
final paymentIntent = await Stripe.instance.confirmPayment(
  paymentIntentClientSecret: clientSecret,
  data: PaymentMethodParams.card(
    paymentMethodData: PaymentMethodData(
      billingDetails: BillingDetails(name: user.name),
    ),
  ),
);
```

> Le webhook Stripe confirme la commande côté serveur automatiquement. Ne pas décrmenter le stock côté client.

---

## 7. Commandes

```
GET /orders                            # Historique des commandes
GET /orders/{id}                       # Détail d'une commande
GET /orders/{id}/invoice               # PDF de la facture (retourne un binaire PDF)
```

**Statuts possibles :** `pending` → `processing` → `shipped` → `delivered` | `cancelled`

---

## 8. Compte & Adresses

```
GET   /account                         # Profil utilisateur
PATCH /account                         # Modifier le profil
      { "name": "Marie K.", "phone": "+2250601010101" }
POST  /account/avatar                  # Upload avatar (multipart/form-data, champ: avatar)

GET    /account/addresses              # Liste des adresses
POST   /account/addresses             # Ajouter une adresse
PATCH  /account/addresses/{id}        # Modifier
DELETE /account/addresses/{id}        # Supprimer
PATCH  /account/addresses/{id}/default # Définir comme adresse par défaut
```

---

## 9. Wishlist

```
GET    /wishlist                        # Liste des favoris
POST   /wishlist                        # Ajouter { "product_id": 5 }
DELETE /wishlist/{product_id}          # Retirer
```

---

## 10. Avis

```
GET  /products/{product}/reviews       # Avis approuvés d'un produit
POST /products/{product}/reviews       # Laisser un avis (achat vérifié requis)
     { "rating": 5, "comment": "Excellent !" }
```

---

## 11. Notifications

```
GET   /notifications                   # Liste des notifications
GET   /notifications/unread-count      # Nombre de non lues
PATCH /notifications/{id}/read         # Marquer comme lue
POST  /notifications/read-all          # Tout marquer comme lu
```

---

## 12. Marketplace

```
GET /marketplace                       # Page d'accueil marketplace (featured shops + niches)
GET /marketplace/shops                 # Liste des boutiques actives
GET /marketplace/niches                # Niches disponibles

GET /shops/{slug}                      # Fiche boutique publique
GET /shops/{slug}/products             # Produits d'une boutique
GET /templates                         # Templates de boutique disponibles
GET /templates/{slug}/palettes         # Palettes d'un template
```

### Créer sa boutique (vendeur)
```
POST /shops
{
  "name": "Ma Boutique",
  "description": "Description courte",
  "template_id": 1,
  "palette_id": 2
}
```

---

## 13. Espace Vendeur (`/seller/*`)

Tous les endpoints `/seller/*` requièrent `role: seller`.

```
GET   /seller/dashboard                # KPIs : CA, commandes, produits, avis
GET   /seller/shop                     # Fiche de sa boutique
PATCH /seller/shop                     # Modifier les infos
PATCH /seller/shop/template            # Changer template/palette

GET    /seller/products                # Ses produits
POST   /seller/products                # Créer un produit
GET    /seller/products/{id}           # Détail
PATCH  /seller/products/{id}           # Modifier
DELETE /seller/products/{id}           # Supprimer
POST   /seller/products/{id}/images    # Ajouter des images (multipart)
DELETE /seller/products/{id}/images/{imageId}

GET    /seller/disputes                # Litiges de sa boutique
GET    /seller/disputes/{id}           # Détail d'un litige
POST   /seller/disputes/{id}/messages  # Répondre à un litige
```

### Stripe Connect (paiements vendeur)
```
POST   /seller/stripe/connect          # Lancer l'onboarding Stripe Express
GET    /seller/stripe/status           # Statut du compte Connect
DELETE /seller/stripe/disconnect       # Déconnecter Stripe
```

---

## 14. Litiges (acheteur)

```
GET  /disputes                         # Mes litiges
POST /disputes                         # Ouvrir un litige
     { "order_id": 18, "reason": "Produit non reçu", "description": "..." }
GET  /disputes/{id}                    # Détail
POST /disputes/{id}/messages          # Ajouter un message
```

---

## 15. Notifications push (FCM)

- Envoyer le `fcm_token` au login/register (champ `fcm_token`)
- Le serveur met à jour le token automatiquement
- Notifications envoyées pour : nouvelle commande, expédition, livraison, annulation

**Structure d'une notification FCM :**
```json
{
  "title": "Commande expédiée",
  "body": "Votre commande CMD-2026-00018 est en route !",
  "data": {
    "type": "order_shipped",
    "order_id": "18"
  }
}
```

---

## 16. Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Admin | `admin@example.com` | `admin2026!` |
| Vendeur | `vendeur@example.com` | `vendeur2026!` |
| Acheteur | `client@example.com` | `client2026!` |
