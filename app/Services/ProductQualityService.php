<?php

namespace App\Services;

use App\Models\Product;

/**
 * Calcule un score qualité 0-100 pour chaque produit.
 *
 * Critères                         Points
 * ─────────────────────────────────────────
 * Au moins 1 image                   +20
 * Image principale définie            +5
 * 3 images ou plus                   +10
 * Description > 80 caractères        +15
 * Description > 250 caractères        +5 (bonus)
 * Description courte renseignée      +10
 * Catégorie assignée                 +10
 * SKU renseigné                       +5
 * Au moins 1 avis approuvé           +10
 * Note moyenne ≥ 4                    +5 (bonus)
 * Stock disponible (> 0)              +5
 * ─────────────────────────────────────────
 * TOTAL MAX                         100
 */
class ProductQualityService
{
    public function calculate(Product $product): array
    {
        $score = 0;
        $hints = [];   // conseils d'amélioration
        $done  = [];   // points validés

        // ── Images ─────────────────────────────────────────────────────────
        $images = $product->relationLoaded('images') ? $product->images : $product->images()->get();

        if ($images->isNotEmpty()) {
            $score += 20;
            $done[] = '✅ A au moins une image (+20)';
        } else {
            $hints[] = '📸 Ajouter au moins une photo du produit (+20 pts)';
        }

        if ($images->where('is_primary', true)->isNotEmpty()) {
            $score += 5;
            $done[] = '✅ Image principale définie (+5)';
        } else {
            $hints[] = '🖼️ Définir une image principale (+5 pts)';
        }

        if ($images->count() >= 3) {
            $score += 10;
            $done[] = '✅ 3 images ou plus (+10)';
        } else {
            $hints[] = '📷 Ajouter ' . (3 - $images->count()) . ' photo(s) supplémentaire(s) pour atteindre 3 (+10 pts)';
        }

        // ── Description ────────────────────────────────────────────────────
        $descLen = mb_strlen(strip_tags($product->description ?? ''));

        if ($descLen > 80) {
            $score += 15;
            $done[] = '✅ Description renseignée (+15)';
        } else {
            $hints[] = '✍️ Rédiger une description d\'au moins 80 caractères (+15 pts) — actuellement ' . $descLen;
        }

        if ($descLen > 250) {
            $score += 5;
            $done[] = '✅ Description détaillée (+5 bonus)';
        } else {
            $hints[] = '📝 Enrichir la description à 250+ caractères pour le bonus (+5 pts)';
        }

        if (! empty(trim($product->short_description ?? ''))) {
            $score += 10;
            $done[] = '✅ Description courte renseignée (+10)';
        } else {
            $hints[] = '💬 Ajouter une description courte (accroche) (+10 pts)';
        }

        // ── Catégorie ──────────────────────────────────────────────────────
        if ($product->category_id) {
            $score += 10;
            $done[] = '✅ Catégorie assignée (+10)';
        } else {
            $hints[] = '🏷️ Assigner une catégorie au produit (+10 pts)';
        }

        // ── SKU ────────────────────────────────────────────────────────────
        if (! empty($product->sku)) {
            $score += 5;
            $done[] = '✅ SKU renseigné (+5)';
        } else {
            $hints[] = '🔖 Ajouter une référence SKU (+5 pts)';
        }

        // ── Avis ───────────────────────────────────────────────────────────
        $reviews = $product->relationLoaded('reviews') ? $product->reviews : $product->reviews()->get();
        $approved = $reviews->whereNotNull('approved_at');

        if ($approved->count() > 0) {
            $score += 10;
            $done[] = '✅ A des avis clients (+10)';
        } else {
            $hints[] = '⭐ Encourager les premiers avis clients (+10 pts)';
        }

        if ((float) $product->rating_avg >= 4.0 && $approved->count() > 0) {
            $score += 5;
            $done[] = '✅ Note ≥ 4/5 (+5 bonus)';
        }

        // ── Stock ──────────────────────────────────────────────────────────
        if ($product->stock_quantity > 0) {
            $score += 5;
            $done[] = '✅ Produit en stock (+5)';
        } else {
            $hints[] = '📦 Remettre le produit en stock (+5 pts)';
        }

        return [
            'score' => min(100, $score),
            'hints' => $hints,
            'done'  => $done,
            'label' => $this->label(min(100, $score)),
            'color' => $this->color(min(100, $score)),
        ];
    }

    public function recalculate(Product $product): void
    {
        $result = $this->calculate($product);
        $product->updateQuietly([
            'quality_score' => $result['score'],
            'quality_hints' => $result['hints'],
        ]);
    }

    public function label(int $score): string
    {
        return match (true) {
            $score >= 85 => 'Excellent',
            $score >= 65 => 'Bon',
            $score >= 40 => 'Moyen',
            default      => 'Faible',
        };
    }

    public function color(int $score): string
    {
        return match (true) {
            $score >= 85 => 'success',
            $score >= 65 => 'info',
            $score >= 40 => 'warning',
            default      => 'danger',
        };
    }
}
