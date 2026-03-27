@extends('layouts.app')
@section('Agribusiness Shop', isset($category) ? $category->name : (isset($q) ? 'Recherche : '.$q : 'Boutique'))
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
                                            @foreach($categories as $cat)
                                            <li>
                                                <div class="d-flex justify-content-between fruite-name">
                                                    <a href="{{ route('shop.index', ['category' => $cat->slug]) }}"
                                                        class="{{ request('category') === $cat->slug ? 'fw-bold text-primary' : '' }}">
                                                        {{ $cat->name }}
                                                    </a>
                                                    <span>({{ $cat->products()->where('status','active')->count() }})</span>
                                                </div>
                                            </li>
                                            @endforeach
                                            @endisset
                                        </ul>
                                    </div>
                                </div>

                                {{-- Filtre prix --}}
                                <div class="col-lg-12">
                                    <form method="GET" action="{{ route('shop.index') }}" id="price-filter-form">
                                        @foreach(request()->except(['min_price','max_price']) as $key => $val)
                                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                                        @endforeach
                                        <div class="mb-3">
                                            <h4 class="mb-2">Prix max (€)</h4>
                                            <input type="range" class="form-range w-100" id="rangeInput" name="max_price"
                                                min="0" max="500" value="{{ request('max_price', 500) }}"
                                                oninput="amount.value=rangeInput.value">
                                            <output id="amount" for="rangeInput">{{ request('max_price', 500) }}</output> €
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100">Filtrer</button>
                                    </form>
                                </div>

                                {{-- Stock --}}
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4>Disponibilité</h4>
                                        <div class="mb-2">
                                            <a href="{{ route('shop.index', array_merge(request()->query(), ['in_stock' => 1])) }}"
                                                class="btn btn-sm {{ request('in_stock') ? 'btn-success' : 'btn-outline-success' }} rounded-pill">
                                                En stock uniquement
                                            </a>
                                        </div>
                                    </div>
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
                                @php $img = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
                                <div class="col-md-6 col-lg-6 col-xl-4">
                                    <div class="rounded position-relative fruite-item">
                                        <div class="fruite-img">
                                            <a href="{{ route('shop.show', $product->slug) }}">
                                                <img src="{{ $img ? $img->url : asset('img/fruite-item-1.jpg') }}"
                                                    class="img-fluid w-100 rounded-top"
                                                    style="height:200px; object-fit:cover;"
                                                    alt="{{ $product->name }}">
                                            </a>
                                        </div>
                                        @if($product->category)
                                        <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                            style="top: 10px; left: 10px;">{{ $product->category->name }}</div>
                                        @endif
                                        @if($product->compare_price && $product->compare_price > $product->price)
                                        <div class="text-white bg-danger px-2 py-1 rounded position-absolute"
                                            style="top: 10px; right: 10px; font-size: .75rem;">
                                            -{{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                                        </div>
                                        @endif
                                        <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                            <h4>
                                                <a href="{{ route('shop.show', $product->slug) }}" class="text-dark text-decoration-none">
                                                    {{ $product->name }}
                                                </a>
                                            </h4>
                                            <p class="text-truncate mb-2 text-muted small">{{ $product->short_description }}</p>
                                            <div class="d-flex justify-content-between flex-lg-wrap align-items-center">
                                                <div>
                                                    <span class="text-dark fs-5 fw-bold">{{ number_format($product->price, 2) }} €</span>
                                                    @if($product->compare_price && $product->compare_price > $product->price)
                                                    <span class="text-muted text-decoration-line-through ms-1 small">
                                                        {{ number_format($product->compare_price, 2) }} €
                                                    </span>
                                                    @endif
                                                    @if($product->unit)
                                                    <span class="text-muted small"> / {{ $product->unit }}</span>
                                                    @endif
                                                </div>
                                                <a href="{{ route('shop.show', $product->slug) }}"
                                                    class="btn border border-secondary rounded-pill px-3 text-primary">
                                                    <i class="fa fa-eye me-1 text-primary"></i> Voir
                                                </a>
                                            </div>
                                        </div>
                                    </div>
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
