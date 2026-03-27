@extends('layouts.app')
@section('Agribusiness Shop', $product->meta_title ?: $product->name)
@section('content')
    <!-- Single Page Header start -->
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
            <li class="breadcrumb-item active text-white">{{ $product->name }}</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Single Product Start -->
    <div class="container-fluid py-5 mt-5">
        <div class="container py-5">
            <div class="row g-4 mb-5">

                {{-- Colonne principale --}}
                <div class="col-lg-8 col-xl-9">
                    <div class="row g-4 mb-5">
                        {{-- Images --}}
                        <div class="col-lg-6">
                            <div class="border rounded">
                                @php
                                    $primaryImg = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                                @endphp
                                <img src="{{ $primaryImg ? $primaryImg->url : asset('img/single-item.jpg') }}"
                                    class="img-fluid rounded w-100"
                                    style="height: 350px; object-fit: cover;"
                                    alt="{{ $product->name }}" id="main-product-img">
                            </div>
                            @if($product->images->count() > 1)
                            <div class="d-flex gap-2 mt-3">
                                @foreach($product->images as $img)
                                <img src="{{ $img->url }}"
                                    class="img-fluid rounded border"
                                    style="width:70px; height:70px; object-fit:cover; cursor:pointer;"
                                    alt="{{ $img->alt ?: $product->name }}"
                                    onclick="document.getElementById('main-product-img').src='{{ $img->url }}'">
                                @endforeach
                            </div>
                            @endif
                        </div>

                        {{-- Détails --}}
                        <div class="col-lg-6">
                            <h4 class="fw-bold mb-2">{{ $product->name }}</h4>

                            @if($product->category)
                            <p class="mb-2 text-muted">
                                Catégorie :
                                <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}">
                                    {{ $product->category->name }}
                                </a>
                                @if($product->brand)
                                · Marque :
                                <a href="{{ route('shop.index', ['brand' => $product->brand->slug]) }}">
                                    {{ $product->brand->name }}
                                </a>
                                @endif
                            </p>
                            @endif

                            {{-- Note --}}
                            <div class="d-flex align-items-center mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fa fa-star {{ $i <= round($product->rating_avg) ? 'text-secondary' : 'text-muted' }}"></i>
                                @endfor
                                <span class="ms-2 text-muted small">({{ $product->rating_count }} avis)</span>
                            </div>

                            {{-- Prix --}}
                            <div class="mb-3">
                                <span class="fs-4 fw-bold text-dark">{{ number_format($product->price, 2) }} €</span>
                                @if($product->unit)
                                <span class="text-muted ms-1"> / {{ $product->unit }}</span>
                                @endif
                                @if($product->compare_price && $product->compare_price > $product->price)
                                <span class="text-danger text-decoration-line-through ms-2">
                                    {{ number_format($product->compare_price, 2) }} €
                                </span>
                                <span class="badge bg-danger ms-1">
                                    -{{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                                </span>
                                @endif
                            </div>

                            {{-- Stock --}}
                            <p class="mb-3">
                                @if($product->stock_quantity > 0)
                                <span class="badge bg-success">En stock ({{ $product->stock_quantity }})</span>
                                @else
                                <span class="badge bg-danger">Rupture de stock</span>
                                @endif
                                @if($product->sku)
                                <span class="text-muted small ms-2">SKU : {{ $product->sku }}</span>
                                @endif
                            </p>

                            <p class="mb-4">{{ $product->short_description }}</p>

                            {{-- Variantes --}}
                            @if($product->variants->isNotEmpty())
                            <div class="mb-3">
                                <label class="fw-bold mb-2">Variante :</label>
                                <select class="form-select w-auto">
                                    @foreach($product->variants->where('is_active', true) as $variant)
                                    <option value="{{ $variant->id }}">
                                        {{ $variant->name }}
                                        @if($variant->price_modifier != 0)
                                        ({{ $variant->price_modifier > 0 ? '+' : '' }}{{ number_format($variant->price_modifier, 2) }} €)
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            {{-- Quantité + panier --}}
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="input-group quantity" style="width: 110px;">
                                    <div class="input-group-btn">
                                        <button class="btn btn-sm btn-minus rounded-circle bg-light border" type="button">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                    <input type="text" class="form-control form-control-sm text-center border-0" value="1" min="1" id="qty-input">
                                    <div class="input-group-btn">
                                        <button class="btn btn-sm btn-plus rounded-circle bg-light border" type="button">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <a href="#" class="btn border border-secondary rounded-pill px-4 py-2 text-primary">
                                    <i class="fa fa-shopping-bag me-2 text-primary"></i>Ajouter au panier
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Onglets description / attributs / avis --}}
                    <div class="col-lg-12">
                        <nav>
                            <div class="nav nav-tabs mb-3">
                                <button class="nav-link active border-white border-bottom-0" type="button"
                                    data-bs-toggle="tab" data-bs-target="#tab-description">
                                    Description
                                </button>
                                @if($product->attributes->isNotEmpty())
                                <button class="nav-link border-white border-bottom-0" type="button"
                                    data-bs-toggle="tab" data-bs-target="#tab-attributes">
                                    Caractéristiques
                                </button>
                                @endif
                                <button class="nav-link border-white border-bottom-0" type="button"
                                    data-bs-toggle="tab" data-bs-target="#tab-reviews">
                                    Avis ({{ $product->rating_count }})
                                </button>
                            </div>
                        </nav>
                        <div class="tab-content mb-5">
                            {{-- Description --}}
                            <div class="tab-pane active" id="tab-description">
                                <div class="py-3">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                                @if($product->weight)
                                <p class="text-muted small">Poids : {{ $product->weight }} kg</p>
                                @endif
                            </div>

                            {{-- Attributs --}}
                            @if($product->attributes->isNotEmpty())
                            <div class="tab-pane" id="tab-attributes">
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        @foreach($product->attributes as $attr)
                                        <div class="row py-2 {{ $loop->odd ? 'bg-light' : '' }}">
                                            <div class="col-6 fw-bold">{{ $attr->attribute_name }}</div>
                                            <div class="col-6">{{ $attr->attribute_value }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Avis --}}
                            <div class="tab-pane" id="tab-reviews">
                                @if($product->reviews->isEmpty())
                                <p class="text-muted py-3">Aucun avis pour ce produit.</p>
                                @else
                                @foreach($product->reviews->where('status','approved')->take(10) as $review)
                                <div class="d-flex mb-4">
                                    <img src="{{ asset('img/avatar.jpg') }}" class="img-fluid rounded-circle p-2"
                                        style="width:70px;height:70px;" alt="">
                                    <div class="ms-2">
                                        <p class="mb-1 text-muted small">{{ $review->created_at->format('d M Y') }}</p>
                                        <div class="d-flex justify-content-between">
                                            <h6>{{ $review->user->name ?? 'Client' }}</h6>
                                            <div class="d-flex ms-3">
                                                @for($i = 1; $i <= 5; $i++)
                                                <i class="fa fa-star {{ $i <= $review->rating ? 'text-secondary' : 'text-muted' }}" style="font-size:.8rem;"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <p>{{ $review->comment }}</p>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4 col-xl-3">
                    <div class="row g-4 fruite">
                        <div class="col-lg-12">
                            <form action="{{ route('shop.search') }}" method="GET">
                                <div class="input-group w-100 mx-auto d-flex mb-4">
                                    <input type="search" name="q" class="form-control p-3" placeholder="Recherche…">
                                    <span class="input-group-text p-3"><i class="fa fa-search"></i></span>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-4">
                                <h4>Catégories</h4>
                                <ul class="list-unstyled fruite-categorie">
                                    @php
                                        $sidebarCats = \App\Models\Category::whereNull('parent_id')
                                            ->where('is_active', true)->orderBy('sort_order')->take(8)->get();
                                    @endphp
                                    @foreach($sidebarCats as $cat)
                                    <li>
                                        <div class="d-flex justify-content-between fruite-name">
                                            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}">
                                                {{ $cat->name }}
                                            </a>
                                            <span>({{ $cat->products()->where('status','active')->count() }})</span>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="position-relative">
                                <img src="{{ asset('img/banner-fruits.jpg') }}" class="img-fluid w-100 rounded" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Produits similaires --}}
            @if($related->isNotEmpty())
            <h1 class="fw-bold mb-4">Produits similaires</h1>
            <div class="vesitable">
                <div class="owl-carousel vegetable-carousel justify-content-center">
                    @foreach($related as $rel)
                    @php $relImg = $rel->images->firstWhere('is_primary', true) ?? $rel->images->first(); @endphp
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <a href="{{ route('shop.show', $rel->slug) }}">
                                <img src="{{ $relImg ? $relImg->url : asset('img/vegetable-item-1.jpg') }}"
                                    class="img-fluid rounded-top" style="height:180px;object-fit:cover;width:100%;"
                                    alt="{{ $rel->name }}">
                            </a>
                        </div>
                        <div class="p-4 pb-0 rounded-bottom">
                            <h4>
                                <a href="{{ route('shop.show', $rel->slug) }}" class="text-dark text-decoration-none">
                                    {{ $rel->name }}
                                </a>
                            </h4>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold">{{ number_format($rel->price, 2) }} €</p>
                                <a href="{{ route('shop.show', $rel->slug) }}"
                                    class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary">
                                    <i class="fa fa-eye me-1 text-primary"></i> Voir
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    <!-- Single Product End -->

    <script>
        // Quantité +/-
        document.querySelectorAll('.btn-plus').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = btn.closest('.input-group').querySelector('input');
                input.value = parseInt(input.value) + 1;
            });
        });
        document.querySelectorAll('.btn-minus').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = btn.closest('.input-group').querySelector('input');
                if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
            });
        });
    </script>
@endsection
