@extends('layouts.app')
@section('seo_title', isset($category) ? $category->name : (isset($q) ? 'Recherche : '.$q : 'Boutique'))
@section('seo_description', isset($category) ? 'Découvrez notre sélection de '.$category->name.' — produits frais de qualité.' : 'Parcourez notre catalogue de produits frais.')
@section('seo_canonical', isset($category) ? route('shop.category', $category->slug) : route('shop.index'))
@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">
            {{ isset($category) ? $category->name : (isset($q) ? 'Résultats : "'.$q.'"' : 'Boutique') }}
        </h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item active text-white">Boutique</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-12">

                    {{-- Barre de recherche + tri --}}
                    <form method="GET" action="{{ route('shop.index') }}" class="row g-4 mb-4">
                        <div class="col-xl-3">
                            <div class="input-group w-100 mx-auto d-flex">
                                <input type="search" name="q" class="form-control p-3"
                                    placeholder="Recherche…" value="{{ request('q') }}"
                                    aria-describedby="search-icon-1">
                                <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                            </div>
                        </div>
                        <div class="col-6"></div>
                        <div class="col-xl-3">
                            <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between mb-4">
                                <label for="sort">Trier par :</label>
                                <select id="sort" name="sort" class="border-0 form-select-sm bg-light me-3"
                                    onchange="this.form.submit()">
                                    <option value="">Récent</option>
                                    <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>Prix ↑</option>
                                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Prix ↓</option>
                                    <option value="name_asc"   {{ request('sort') === 'name_asc'   ? 'selected' : '' }}>Nom A-Z</option>
                                    <option value="popular"    {{ request('sort') === 'popular'    ? 'selected' : '' }}>Populaire</option>
                                    <option value="top_rated"  {{ request('sort') === 'top_rated'  ? 'selected' : '' }}>Mieux noté</option>
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="row g-4">
                        {{-- Sidebar filtres --}}
                        <div class="col-lg-3">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4>Catégories</h4>
                                        <ul class="list-unstyled fruite-categorie">
                                            <li>
                                                <div class="d-flex justify-content-between fruite-name">
                                                    <a href="{{ route('shop.index') }}"
                                                        class="{{ !request('category') ? 'fw-bold text-primary' : '' }}">
                                                        Tous les produits
                                                    </a>
                                                </div>
                                            </li>
                                            @isset($categories)
                                            @foreach($categories->take(5) as $cat)
                                            <li>
                                                <div class="d-flex justify-content-between fruite-name">
                                                    <a href="{{ route('shop.index', ['category' => $cat->slug]) }}"
                                                        class="{{ request('category') === $cat->slug ? 'fw-bold text-primary' : '' }}">
                                                        {{ $cat->name }}
                                                    </a>
                                                    <span>({{ $cat->products_count }})</span>
                                                </div>
                                            </li>
                                            @endforeach

                                            @if($categories->count() > 5)
                                            <div id="extra-categories" style="display:none;">
                                                @foreach($categories->skip(5) as $cat)
                                                <li>
                                                    <div class="d-flex justify-content-between fruite-name">
                                                        <a href="{{ route('shop.index', ['category' => $cat->slug]) }}"
                                                            class="{{ request('category') === $cat->slug ? 'fw-bold text-primary' : '' }}">
                                                            {{ $cat->name }}
                                                        </a>
                                                        <span>({{ $cat->products_count }})</span>
                                                    </div>
                                                </li>
                                                @endforeach
                                            </div>
                                            <li class="mt-1">
                                                <button type="button" id="toggle-categories"
                                                    class="btn btn-link p-0 text-primary small fw-semibold text-decoration-none"
                                                    onclick="toggleCategories()">
                                                    <i class="fa fa-chevron-down me-1" id="toggle-icon" style="font-size:.7rem;"></i>
                                                    Voir plus ({{ $categories->count() - 5 }})
                                                </button>
                                            </li>
                                            @endif
                                            @endisset
                                        </ul>
                                    </div>
                                    <script>
                                    function toggleCategories() {
                                        const el   = document.getElementById('extra-categories');
                                        const btn  = document.getElementById('toggle-categories');
                                        const icon = document.getElementById('toggle-icon');
                                        const open = el.style.display === 'none';
                                        el.style.display  = open ? 'block' : 'none';
                                        icon.className    = open ? 'fa fa-chevron-up me-1' : 'fa fa-chevron-down me-1';
                                        icon.style.fontSize = '.7rem';
                                        @isset($categories)
                                        btn.innerHTML = (open ? '<i class="fa fa-chevron-up me-1" style="font-size:.7rem;"></i>Voir moins' : '<i class="fa fa-chevron-down me-1" style="font-size:.7rem;"></i>Voir plus ({{ $categories->count() - 5 }})');
                                        @endisset
                                    }
                                    @isset($categories)
                                    @if($categories->count() > 5 && $categories->skip(5)->contains('slug', request('category')))
                                    document.addEventListener('DOMContentLoaded', () => toggleCategories());
                                    @endif
                                    @endisset
                                    </script>
                                </div>

                                {{-- Filtre prix --}}
                                <div class="col-lg-12">
                                    <form method="GET" action="{{ route('shop.index') }}" id="price-filter-form">
                                        @foreach(request()->except(['min_price','max_price']) as $key => $val)
                                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                                        @endforeach
                                        <div class="mb-3">
                                            <h4 class="mb-2">Prix max (FCFA)</h4>
                                            <input type="range" class="form-range w-100" id="rangeInput" name="max_price"
                                                min="0" max="100000" step="1000" value="{{ request('max_price', 100000) }}"
                                                oninput="amount.value=Number(rangeInput.value).toLocaleString('fr-FR')">
                                            <output id="amount" for="rangeInput">{{ number_format(request('max_price', 100000), 0, ',', ' ') }}</output> FCFA
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100">Filtrer</button>
                                    </form>
                                </div>

                                {{-- Marques --}}
                                @isset($brands)
                                @if($brands->isNotEmpty())
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4>Marques</h4>
                                        <ul class="list-unstyled fruite-categorie">
                                            @foreach($brands as $brand)
                                            <li>
                                                <div class="d-flex justify-content-between fruite-name">
                                                    <a href="{{ route('shop.index', ['brand' => $brand->slug]) }}"
                                                        class="{{ request('brand') === $brand->slug ? 'fw-bold text-primary' : '' }}">
                                                        {{ $brand->name }}
                                                    </a>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @endif
                                @endisset
                            </div>
                        </div>

                        {{-- Grille produits --}}
                        <div class="col-lg-9">
                            <div class="row g-4 justify-content-center">
                                @forelse($products as $product)
                                <div class="col-6 col-md-6 col-lg-6 col-xl-4">
                                    @include('components.product-card', ['product' => $product])
                                </div>
                                @empty
                                <div class="col-12 text-center py-5">
                                    <p class="text-muted fs-5">Aucun produit trouvé.</p>
                                    <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-4">
                                        Voir tous les produits
                                    </a>
                                </div>
                                @endforelse
                            </div>

                            {{-- Pagination --}}
                            @if(isset($products) && method_exists($products, 'links'))
                            <div class="col-12">
                                <div class="pagination d-flex justify-content-center mt-5">
                                    @if($products->onFirstPage())
                                        <a class="rounded disabled">&laquo;</a>
                                    @else
                                        <a href="{{ $products->previousPageUrl() }}" class="rounded">&laquo;</a>
                                    @endif
                                    @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                        <a href="{{ $url }}"
                                            class="rounded{{ $page == $products->currentPage() ? ' active' : '' }}">{{ $page }}</a>
                                    @endforeach
                                    @if($products->hasMorePages())
                                        <a href="{{ $products->nextPageUrl() }}" class="rounded">&raquo;</a>
                                    @else
                                        <a class="rounded disabled">&raquo;</a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fruits Shop End-->
@endsection
