@extends('layouts.app')
@section('seo_title',       $product->meta_title       ?: $product->name)
@section('seo_description', $product->meta_description ?: ($product->short_description ?: $product->name))
@section('seo_image',       ($product->images->firstWhere('is_primary', true) ?? $product->images->first())?->url ?? '')
@section('seo_canonical',   route('shop.show', $product->slug))
@section('og_type', 'product')

@section('schema_org')
@php
    $schemaImg = ($product->images->firstWhere('is_primary', true) ?? $product->images->first())?->url;
    $schema = [
        '@context'    => 'https://schema.org/',
        '@type'       => 'Product',
        'name'        => $product->name,
        'description' => strip_tags($product->short_description ?? $product->name),
        'sku'         => $product->sku,
        'brand'       => ['@type' => 'Brand', 'name' => $product->brand?->name ?? \App\Models\Setting::get('shop_name', config('app.name'))],
        'offers'      => [
            '@type'         => 'Offer',
            'url'           => route('shop.show', $product->slug),
            'priceCurrency' => 'XOF',
            'price'         => (string) $product->price,
            'availability'  => $product->stock_quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
        ],
    ];
    if ($schemaImg)                   { $schema['image'] = [$schemaImg]; }
    if ($product->rating_count > 0)   { $schema['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => (string) $product->rating_avg, 'reviewCount' => (string) $product->rating_count]; }
@endphp
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}</script>
@endsection

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">{{ $product->name }}</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
        <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-white">Boutique</a></li>
        @if($product->category)
        <li class="breadcrumb-item">
            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="text-white">
                {{ $product->category->name }}
            </a>
        </li>
        @endif
        <li class="breadcrumb-item active text-white">{{ Str::limit($product->name, 28) }}</li>
    </ol>
</div>

{{-- ── Contenu principal ────────────────────────────────────────────────── --}}
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row g-5">

            {{-- ── Colonne gauche : galerie + onglets ──────────────────── --}}
            <div class="col-lg-8">

                {{-- Galerie + infos produit --}}
                <div class="row g-4 mb-5">

                    {{-- Galerie --}}
                    <div class="col-md-5">
                        @php
                            $allImgs    = $product->images;
                            $primaryImg = $allImgs->firstWhere('is_primary', true) ?? $allImgs->first();
                            $mainSrc    = $primaryImg?->url ?? asset('img/single-item.jpg');
                        @endphp

                        {{-- Image principale --}}
                        <div class="position-relative rounded-3 overflow-hidden border bg-light mb-3"
                             style="height:340px;">
                            {{-- Badges flottants --}}
                            @if($product->stock_quantity <= 0)
                            <span class="position-absolute top-0 start-0 m-2 badge bg-danger" style="z-index:2;font-size:.8rem;">
                                Rupture
                            </span>
                            @elseif($product->compare_price && $product->compare_price > $product->price)
                            <span class="position-absolute top-0 start-0 m-2 badge bg-danger" style="z-index:2;font-size:.8rem;">
                                -{{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                            </span>
                            @endif

                            {{-- Lien lightbox sur l'image principale --}}
                            <a id="main-lb-link"
                               href="{{ $mainSrc }}"
                               data-lightbox="product-{{ $product->id }}"
                               data-title="{{ $product->name }}">
                                <img id="main-product-img"
                                     src="{{ $mainSrc }}"
                                     class="w-100 h-100"
                                     style="object-fit:cover;transition:transform .35s ease;"
                                     alt="{{ $product->name }}">
                            </a>

                            {{-- Toutes les images en ancres cachées pour lightbox --}}
                            @foreach($allImgs as $img)
                            @if($img->id !== $primaryImg?->id)
                            <a href="{{ $img->url }}"
                               data-lightbox="product-{{ $product->id }}"
                               data-title="{{ $img->alt ?: $product->name }}"
                               style="display:none;"></a>
                            @endif
                            @endforeach
                        </div>

                        {{-- Miniatures --}}
                        @if($allImgs->count() > 1)
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($allImgs as $idx => $img)
                            <button type="button"
                                class="thumb-btn p-0 border-0 rounded-2 overflow-hidden {{ $idx === 0 ? 'ring-primary' : '' }}"
                                style="width:68px;height:68px;outline:2px solid {{ $idx === 0 ? 'var(--bs-primary)' : 'transparent' }};transition:outline-color .2s;"
                                data-src="{{ $img->url }}"
                                data-title="{{ $img->alt ?: $product->name }}"
                                onclick="switchMainImg(this)">
                                <img src="{{ $img->url }}"
                                     style="width:100%;height:100%;object-fit:cover;"
                                     alt="{{ $img->alt ?: $product->name }}">
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- Informations produit --}}
                    <div class="col-md-7">

                        {{-- Catégorie / Marque --}}
                        <div class="mb-2 d-flex flex-wrap gap-1">
                            @if($product->category)
                            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}"
                               class="badge bg-primary text-decoration-none fs-8">
                                <i class="fas fa-tag me-1"></i>{{ $product->category->name }}
                            </a>
                            @endif
                            @if($product->brand)
                            <a href="{{ route('shop.index', ['brand' => $product->brand->slug]) }}"
                               class="badge bg-secondary text-decoration-none">
                                {{ $product->brand->name }}
                            </a>
                            @endif
                        </div>

                        <h2 class="fw-bold mb-2" style="line-height:1.3;">{{ $product->name }}</h2>

                        {{-- Étoiles --}}
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="d-flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fa fa-star {{ $i <= round($product->rating_avg) ? 'text-warning' : 'text-muted' }}"
                                   style="font-size:.85rem;"></i>
                                @endfor
                            </div>
                            <span class="text-muted small">
                                {{ number_format($product->rating_avg, 1) }}
                                &bull; {{ $product->rating_count }} {{ Str::plural('avis', $product->rating_count) }}
                            </span>
                            @if($product->sku)
                            <span class="text-muted small ms-auto">
                                <i class="fas fa-barcode me-1"></i>{{ $product->sku }}
                            </span>
                            @endif
                        </div>

                        <hr class="my-3">

                        {{-- Prix --}}
                        <div class="mb-3">
                            <span class="fs-2 fw-bold text-primary lh-1">
                                {{ number_format($product->price, 0, ',', ' ') }}
                                <small class="fs-6 fw-normal">FCFA</small>
                                @if($product->unit)
                                <small class="fs-6 fw-normal text-muted">/ {{ $product->unit }}</small>
                                @endif
                            </span>
                            @if($product->compare_price && $product->compare_price > $product->price)
                            <div class="mt-1 d-flex align-items-center gap-2">
                                <span class="text-muted text-decoration-line-through">
                                    {{ number_format($product->compare_price, 0, ',', ' ') }} FCFA
                                </span>
                                <span class="badge bg-danger">
                                    Vous économisez {{ number_format($product->compare_price - $product->price, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                            @endif
                        </div>

                        {{-- Disponibilité --}}
                        <div class="mb-3">
                            @if($product->stock_quantity > 5)
                                <span class="text-success fw-semibold small">
                                    <i class="fas fa-check-circle me-1"></i>En stock
                                    <span class="text-muted">({{ $product->stock_quantity }} disponibles)</span>
                                </span>
                            @elseif($product->stock_quantity > 0)
                                <span class="text-warning fw-semibold small">
                                    <i class="fas fa-exclamation-circle me-1"></i>Stock limité
                                    <span class="text-muted">({{ $product->stock_quantity }} restant{{ $product->stock_quantity > 1 ? 's' : '' }})</span>
                                </span>
                            @else
                                <span class="text-danger fw-semibold small">
                                    <i class="fas fa-times-circle me-1"></i>Rupture de stock
                                </span>
                            @endif
                        </div>

                        {{-- Description courte --}}
                        @if($product->short_description)
                        <p class="text-muted mb-4" style="line-height:1.7;">{{ $product->short_description }}</p>
                        @endif

                        {{-- Formulaire ajout au panier --}}
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            {{-- Variantes (radio buttons) --}}
                            @if($product->variants->isNotEmpty())
                            <div class="mb-3">
                                <span class="fw-semibold d-block mb-2">Variante :</span>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($product->variants->where('is_active', true) as $variant)
                                    <div>
                                        <input type="radio" class="btn-check" name="variant_id"
                                               id="v{{ $variant->id }}" value="{{ $variant->id }}"
                                               {{ $loop->first ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-secondary rounded-pill px-3" for="v{{ $variant->id }}">
                                            {{ $variant->name }}
                                            @if($variant->price_modifier != 0)
                                            <span class="ms-1 text-muted small">
                                                ({{ $variant->price_modifier > 0 ? '+' : '' }}{{ number_format($variant->price_modifier, 0, ',', ' ') }})
                                            </span>
                                            @endif
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Quantité + bouton --}}
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="input-group" style="width:130px;">
                                    <button type="button" class="btn btn-outline-secondary" id="btn-minus">
                                        <i class="fa fa-minus fa-xs"></i>
                                    </button>
                                    <input type="number" name="quantity" id="qty-input"
                                           class="form-control text-center border-secondary fw-semibold"
                                           value="1" min="1" max="{{ max(1, $product->stock_quantity) }}">
                                    <button type="button" class="btn btn-outline-secondary" id="btn-plus">
                                        <i class="fa fa-plus fa-xs"></i>
                                    </button>
                                </div>

                                <button type="submit"
                                        class="btn btn-primary rounded-pill px-4 py-2 fw-semibold"
                                        {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                                    <i class="fa fa-shopping-bag me-2"></i>
                                    {{ $product->stock_quantity > 0 ? 'Ajouter au panier' : 'Indisponible' }}
                                </button>
                            </div>
                        </form>

                        <hr class="mt-4 mb-3">

                        {{-- Garanties --}}
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="py-2 rounded bg-light">
                                    <i class="fas fa-truck text-primary d-block mb-1"></i>
                                    <small class="text-muted" style="font-size:.75rem;">Livraison rapide</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="py-2 rounded bg-light">
                                    <i class="fas fa-shield-alt text-primary d-block mb-1"></i>
                                    <small class="text-muted" style="font-size:.75rem;">Paiement sécurisé</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="py-2 rounded bg-light">
                                    <i class="fas fa-leaf text-primary d-block mb-1"></i>
                                    <small class="text-muted" style="font-size:.75rem;">Produit frais</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ── Onglets description / caractéristiques / avis ────── --}}
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" data-bs-toggle="tab"
                                data-bs-target="#tab-desc" type="button">
                            Description
                        </button>
                    </li>
                    @if($product->attributes->isNotEmpty())
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab"
                                data-bs-target="#tab-attr" type="button">
                            Caractéristiques
                        </button>
                    </li>
                    @endif
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab"
                                data-bs-target="#tab-rev" type="button">
                            Avis
                            @if($product->rating_count > 0)
                            <span class="badge bg-secondary ms-1">{{ $product->rating_count }}</span>
                            @endif
                        </button>
                    </li>
                </ul>

                <div class="tab-content border border-top-0 rounded-bottom p-4 bg-white shadow-sm mb-4">

                    {{-- Description --}}
                    <div class="tab-pane fade show active" id="tab-desc" role="tabpanel">
                        @if($product->description)
                            <div class="text-muted" style="line-height:1.9;">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        @else
                            <p class="text-muted mb-0">Aucune description disponible pour ce produit.</p>
                        @endif
                        @if($product->weight)
                        <p class="mt-3 mb-0">
                            <span class="badge border text-dark bg-light px-3 py-2">
                                <i class="fas fa-weight-hanging text-primary me-1"></i>
                                Poids : {{ $product->weight }} kg
                            </span>
                        </p>
                        @endif
                    </div>

                    {{-- Caractéristiques --}}
                    @if($product->attributes->isNotEmpty())
                    <div class="tab-pane fade" id="tab-attr" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <tbody>
                                    @foreach($product->attributes as $attr)
                                    <tr>
                                        <th class="table-light fw-normal text-muted" style="width:40%;">
                                            {{ $attr->attribute_name }}
                                        </th>
                                        <td class="fw-semibold">{{ $attr->attribute_value }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    {{-- Avis clients --}}
                    <div class="tab-pane fade" id="tab-rev" role="tabpanel">
                        @php $approvedReviews = $product->reviews->where('status', 'approved'); @endphp

                        @if($approvedReviews->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="far fa-comment-dots fa-2x mb-3 d-block text-primary"></i>
                            Aucun avis pour ce produit. Soyez le premier à laisser un commentaire !
                        </div>
                        @else

                        {{-- Résumé note globale --}}
                        <div class="row g-3 align-items-center mb-4 pb-4 border-bottom">
                            <div class="col-auto text-center">
                                <div class="display-3 fw-bold text-primary lh-1">
                                    {{ number_format($product->rating_avg, 1) }}
                                </div>
                                <div class="d-flex justify-content-center gap-1 my-1">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="fa fa-star {{ $i <= round($product->rating_avg) ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <small class="text-muted">{{ $product->rating_count }} avis</small>
                            </div>
                            <div class="col">
                                @for($star = 5; $star >= 1; $star--)
                                @php $cnt = $approvedReviews->where('rating', $star)->count(); @endphp
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <small class="text-muted" style="width:12px;">{{ $star }}</small>
                                    <i class="fa fa-star text-warning" style="font-size:.8rem;"></i>
                                    <div class="progress flex-grow-1" style="height:7px;">
                                        <div class="progress-bar bg-warning"
                                             style="width:{{ $product->rating_count > 0 ? round($cnt / $product->rating_count * 100) : 0 }}%">
                                        </div>
                                    </div>
                                    <small class="text-muted" style="width:18px;">{{ $cnt }}</small>
                                </div>
                                @endfor
                            </div>
                        </div>

                        {{-- Liste des avis --}}
                        @foreach($approvedReviews->take(10) as $review)
                        <div class="d-flex gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0 text-white fw-bold"
                                 style="width:44px;height:44px;font-size:1.1rem;">
                                {{ strtoupper(substr($review->user->name ?? 'C', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                                    <div>
                                        <span class="fw-semibold">{{ $review->user->name ?? 'Client anonyme' }}</span>
                                        <div class="d-flex gap-1 mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                            <i class="fa fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"
                                               style="font-size:.78rem;"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $review->created_at->translatedFormat('d M Y') }}</small>
                                </div>
                                <p class="mt-2 mb-0 text-muted" style="line-height:1.7;">{{ $review->comment }}</p>
                            </div>
                        </div>
                        @endforeach

                        @endif
                    </div>
                </div>

            </div>

            {{-- ── Sidebar droite ───────────────────────────────────────── --}}
            <div class="col-lg-4">
                <div class="sticky-top" style="top:90px;">

                    {{-- Recherche --}}
                    <form action="{{ route('shop.search') }}" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="search" name="q"
                                   class="form-control py-3"
                                   placeholder="Rechercher un produit…"
                                   value="{{ request('q') }}">
                            <button type="submit" class="btn btn-primary px-3">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>

                    {{-- Catégories --}}
                    <div class="bg-white rounded-3 shadow-sm p-4 mb-4 fruite">
                        <h5 class="fw-bold mb-3 text-primary">
                            <i class="fas fa-th-large me-2"></i>Catégories
                        </h5>
                        @php
                            $sidebarCats = \App\Models\Category::whereNull('parent_id')
                                ->where('is_active', true)
                                ->orderBy('sort_order')
                                ->withCount(['products' => fn($q) => $q->where('status', 'active')])
                                ->take(9)->get();
                        @endphp
                        <ul class="list-unstyled mb-0 fruite-categorie">
                            @foreach($sidebarCats as $cat)
                            <li>
                                <div class="d-flex justify-content-between align-items-center fruite-name">
                                    <a href="{{ route('shop.index', ['category' => $cat->slug]) }}"
                                       class="{{ $product->category?->slug === $cat->slug ? 'fw-semibold text-primary' : '' }}">
                                        @if($product->category?->slug === $cat->slug)
                                        <i class="fas fa-chevron-right me-1" style="font-size:.7rem;"></i>
                                        @endif
                                        {{ $cat->name }}
                                    </a>
                                    <span class="badge bg-light text-secondary border">{{ $cat->products_count }}</span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('shop.index') }}" class="btn btn-outline-primary btn-sm rounded-pill w-100 mt-3">
                            Voir toutes les catégories
                        </a>
                    </div>

                    {{-- Bannière --}}
                    <div class="position-relative rounded-3 overflow-hidden shadow-sm">
                        <img src="{{ asset('img/banner-fruits.jpg') }}"
                             class="img-fluid w-100" alt="Nos produits frais"
                             style="height:200px;object-fit:cover;">
                        <div class="position-absolute inset-0 w-100 h-100 d-flex flex-column justify-content-end p-4"
                             style="background:linear-gradient(0deg,rgba(0,0,0,.65) 0%,transparent 100%);">
                            <h6 class="text-white fw-bold mb-1">{{ \App\Models\Setting::get('shop_tagline', __('Notre boutique')) }}</h6>
                            <a href="{{ route('shop.index') }}"
                               class="btn btn-sm btn-primary rounded-pill px-4">
                                Découvrir
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- ── Produits similaires ─────────────────────────────────────── --}}
        @if($related->isNotEmpty())
        <div class="mt-5 pt-3">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h3 class="fw-bold mb-0">Produits similaires</h3>
                @if($product->category)
                <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}"
                   class="btn btn-outline-primary btn-sm rounded-pill px-4">
                    Voir tout <i class="fas fa-arrow-right ms-1"></i>
                </a>
                @endif
            </div>
            <div class="vesitable">
                <div class="owl-carousel vegetable-carousel">
                    @foreach($related as $rel)
                    @php $relImg = $rel->images->firstWhere('is_primary', true) ?? $rel->images->first(); @endphp
                    <div class="border rounded-3 position-relative vesitable-item overflow-hidden">
                        {{-- Badge remise --}}
                        @if($rel->compare_price && $rel->compare_price > $rel->price)
                        <span class="position-absolute top-0 end-0 m-2 badge bg-danger" style="z-index:1;">
                            -{{ round((($rel->compare_price - $rel->price) / $rel->compare_price) * 100) }}%
                        </span>
                        @endif
                        <div class="vesitable-img">
                            <a href="{{ route('shop.show', $rel->slug) }}">
                                <img src="{{ $relImg ? $relImg->url : asset('img/vegetable-item-1.jpg') }}"
                                     class="img-fluid w-100"
                                     style="height:200px;object-fit:cover;"
                                     alt="{{ $rel->name }}">
                            </a>
                        </div>
                        <div class="p-3">
                            {{-- Étoiles --}}
                            @if($rel->rating_count > 0)
                            <div class="d-flex gap-1 mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fa fa-star {{ $i <= round($rel->rating_avg) ? 'text-warning' : 'text-muted' }}"
                                   style="font-size:.7rem;"></i>
                                @endfor
                            </div>
                            @endif
                            <h6 class="fw-semibold mb-1 text-truncate">
                                <a href="{{ route('shop.show', $rel->slug) }}" class="text-dark text-decoration-none">
                                    {{ $rel->name }}
                                </a>
                            </h6>
                            <div class="d-flex justify-content-between align-items-center mt-2 mb-2">
                                <div>
                                    <span class="fw-bold text-primary">
                                        {{ number_format($rel->price, 0, ',', ' ') }} FCFA
                                    </span>
                                    @if($rel->compare_price && $rel->compare_price > $rel->price)
                                    <small class="text-muted text-decoration-line-through d-block lh-1">
                                        {{ number_format($rel->compare_price, 0, ',', ' ') }} FCFA
                                    </small>
                                    @endif
                                </div>
                                <a href="{{ route('shop.show', $rel->slug) }}"
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fa fa-eye me-1"></i>Voir
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

<script>
// ── Galerie miniatures ──────────────────────────────────────────────────────
function switchMainImg(btn) {
    const src   = btn.dataset.src;
    const title = btn.dataset.title;

    // Mise à jour image principale
    document.getElementById('main-product-img').src = src;

    // Mise à jour du lien lightbox
    const lbLink = document.getElementById('main-lb-link');
    lbLink.setAttribute('href', src);
    lbLink.setAttribute('data-title', title);

    // Reset contours miniatures
    document.querySelectorAll('.thumb-btn').forEach(b => {
        b.style.outlineColor = 'transparent';
    });
    btn.style.outlineColor = 'var(--bs-primary)';
}

// ── Quantité +/- ────────────────────────────────────────────────────────────
(function () {
    const input = document.getElementById('qty-input');
    const max   = parseInt(input.getAttribute('max')) || 99;

    document.getElementById('btn-plus').addEventListener('click', function () {
        const v = parseInt(input.value) || 1;
        if (v < max) input.value = v + 1;
    });
    document.getElementById('btn-minus').addEventListener('click', function () {
        const v = parseInt(input.value) || 1;
        if (v > 1) input.value = v - 1;
    });
})();

// ── Zoom hover image principale ─────────────────────────────────────────────
(function () {
    const img = document.getElementById('main-product-img');
    if (!img) return;
    img.addEventListener('mouseenter', () => img.style.transform = 'scale(1.06)');
    img.addEventListener('mouseleave', () => img.style.transform = 'scale(1)');
})();
</script>

@endsection
