<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Api\V1\BaseApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StripeConnectController extends BaseApiController
{
    // POST /api/v1/seller/stripe/connect
    // Crée un compte Stripe Express et retourne le lien d'onboarding
    public function connect(Request $request): JsonResponse
    {
        $shop = $request->user()->shop;

        if (! $shop) {
            return $this->error('Aucune boutique associée à ce compte.', 404);
        }

        if (! class_exists(\Stripe\StripeClient::class)) {
            return $this->error('Stripe non configuré sur ce serveur.', 503);
        }

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        try {
            // Créer le compte Express s'il n'existe pas encore
            if (! $shop->stripe_account_id) {
                $account = $stripe->accounts->create([
                    'type'         => 'express',
                    'country'      => 'FR',
                    'email'        => $request->user()->email,
                    'capabilities' => [
                        'card_payments' => ['requested' => true],
                        'transfers'     => ['requested' => true],
                    ],
                    'business_profile' => [
                        'name' => $shop->name,
                    ],
                    'metadata' => ['shop_id' => $shop->id],
                ]);

                $shop->update(['stripe_account_id' => $account->id]);
            }

            // Générer le lien d'onboarding
            $accountLink = $stripe->accountLinks->create([
                'account'     => $shop->stripe_account_id,
                'refresh_url' => config('app.url') . '/api/v1/seller/stripe/connect',
                'return_url'  => config('app.url') . '/api/v1/seller/stripe/status',
                'type'        => 'account_onboarding',
            ]);

            return $this->success([
                'onboarding_url'    => $accountLink->url,
                'stripe_account_id' => $shop->stripe_account_id,
            ], 'Lien d\'onboarding Stripe généré.');

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return $this->error('Erreur Stripe : ' . $e->getMessage(), 500);
        }
    }

    // GET /api/v1/seller/stripe/status
    // Vérifie si le compte Stripe Express est actif et peut recevoir des paiements
    public function status(Request $request): JsonResponse
    {
        $shop = $request->user()->shop;

        if (! $shop) {
            return $this->error('Aucune boutique associée à ce compte.', 404);
        }

        if (! $shop->stripe_account_id) {
            return $this->success([
                'connected'        => false,
                'charges_enabled'  => false,
                'payouts_enabled'  => false,
                'stripe_account_id'=> null,
            ], 'Compte Stripe non connecté.');
        }

        if (! class_exists(\Stripe\StripeClient::class)) {
            return $this->success([
                'connected'        => true,
                'charges_enabled'  => true,
                'payouts_enabled'  => true,
                'stripe_account_id'=> $shop->stripe_account_id,
            ]);
        }

        try {
            $stripe  = new \Stripe\StripeClient(config('services.stripe.secret'));
            $account = $stripe->accounts->retrieve($shop->stripe_account_id);

            return $this->success([
                'connected'         => true,
                'charges_enabled'   => $account->charges_enabled,
                'payouts_enabled'   => $account->payouts_enabled,
                'details_submitted' => $account->details_submitted,
                'stripe_account_id' => $shop->stripe_account_id,
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return $this->error('Erreur Stripe : ' . $e->getMessage(), 500);
        }
    }

    // DELETE /api/v1/seller/stripe/disconnect
    public function disconnect(Request $request): JsonResponse
    {
        $shop = $request->user()->shop;

        if (! $shop) {
            return $this->error('Aucune boutique associée à ce compte.', 404);
        }

        $shop->update(['stripe_account_id' => null]);

        return $this->success(null, 'Compte Stripe déconnecté.');
    }
}
