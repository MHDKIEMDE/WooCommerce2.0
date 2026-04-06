@extends('layouts.app')
@section('seo_title', \App\Models\Setting::get('shop_name', config('app.name')))
@section('seo_description', \App\Models\Setting::get('shop_tagline', 'Produits frais livrés chez vous.'))
@section('content')
    <!-- Hero Start -->
    <div class="container-fluid py-5 mb-5 hero-header">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-12 col-lg-7 order-2 order-lg-1">
                    <h4 class="mb-3 text-secondary">Aliments 100 % biologiques</h4>
                    <h1 class="mb-5 display-3 text-primary">Aliments biologiques à base de fruits</h1>
                    <form action="{{ route('shop.search') }}" method="GET">
                        <div class="position-relative w-100">
                            <input class="form-control border-2 border-secondary w-100 py-3 ps-4 pe-5 rounded-pill"
                                type="text" name="q" placeholder="Rechercher un produit...">
                            <button type="submit"
                                class="btn btn-primary border-0 position-absolute rounded-pill text-white fw-bold"
                                style="top: 6px; right: 6px; bottom: 6px; padding: 0 24px;">
                                <span class="d-none d-sm-inline">Rechercher</span>
                                <i class="fas fa-search d-inline d-sm-none"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-12 col-lg-5 order-1 order-lg-2">
                    <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                        <div class="carousel-inner" role="listbox">
                            @forelse($slides as $index => $slide)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }} rounded">
                                <img src="{{ Storage::url($slide->image_path) }}"
                                     class="img-fluid w-100 rounded"
                                     style="height:300px;object-fit:cover;"
                                     alt="{{ $slide->title ?? 'Slide' }}">
                                @if($slide->button_text)
                                <a href="{{ $slide->button_url ?? '#' }}"
                                   class="btn px-4 py-2 text-white rounded btn-primary"
                                   style="position:absolute;bottom:20px;left:20px;">
                                    {{ $slide->button_text }}
                                </a>
                                @endif
                            </div>
                            @empty
                            <div class="carousel-item active rounded">
                                <img src="img/hero-img-1.png" class="img-fluid w-100 h-100 bg-secondary rounded" alt="Slide">
                            </div>
                            @endforelse
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Précédent</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselId"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Suivant</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <!-- Featurs Section Start -->
    <div class="container-fluid featurs py-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fas fa-car-side fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>Livraison Possible</h5>
                            <p class="mb-0">Gratuit à partir de 300 $ d'achat</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fas fa-user-shield fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>Paiement de sécurité</h5>
                            <p class="mb-0">Paiement sécurisé à 100</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fas fa-exchange-alt fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>Retour sous 1 jours</h5>
                            <p class="mb-0">Garantie de 1h</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                            <i class="fa fa-phone-alt fa-3x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>Assistance 24/7</h5>
                            <p class="mb-0">Un soutien toujours rapide</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Featurs Section End -->
    
    <!-- Fruits Shop Start-->
    @if($categories->isNotEmpty())
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <div class="tab-class text-center">
                <div class="row g-4">
                    <div class="col-lg-4 text-start">
                        <h1>Nos Produits Bio</h1>
                    </div>
                    <div class="col-lg-8 text-end">
                        <ul class="nav nav-pills d-inline-flex text-center mb-5">
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill active" data-bs-toggle="pill" href="#tab-1">
                                    <span class="text-dark" style="width: 130px;">Vedettes</span>
                                </a>
                            </li>
                            @foreach($categories->take(5) as $cat)
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-cat-{{ $cat->id }}">
                                    <span class="text-dark" style="width: 130px;">{{ $cat->name }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    {{-- Tab 1: Produits vedettes --}}
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    @forelse($featured as $product)
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        @include('components.product-card', ['product' => $product])
                                    </div>
                                    @empty
                                    <div class="col-12 text-center py-4">
                                        <p class="text-muted">Aucun produit vedette pour le moment.</p>
                                        <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-4">Voir la boutique</a>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Tabs catégories dynamiques --}}
                    @foreach($categories->take(5) as $cat)
                    <div id="tab-cat-{{ $cat->id }}" class="tab-pane fade show p-0">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    @forelse($cat->products()->with(['images','category'])->where('status','active')->take(8)->get() as $product)
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        @include('components.product-card', ['product' => $product])
                                    </div>
                                    @empty
                                    <div class="col-12 text-center py-4">
                                        <p class="text-muted">Aucun produit dans cette catégorie.</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- Fruits Shop End-->
    <!-- Featurs Start -->
    @if($promotions->isNotEmpty())
    <div class="container-fluid service py-5">
        <div class="container py-5">
            <div class="row g-4 justify-content-center">
                @foreach($promotions as $promo)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ $promo->link_url ?? '#' }}">
                        <div class="service-item {{ $promo->bg_color }} rounded border {{ $promo->bg_color }}">
                            <img src="{{ Storage::url($promo->image_path) }}"
                                 class="img-fluid rounded-top w-100"
                                 style="height:200px;object-fit:cover;"
                                 alt="{{ $promo->title }}">
                            <div class="px-4 rounded-bottom">
                                <div class="service-content {{ $promo->text_theme === 'dark' ? 'bg-light' : 'bg-primary' }} text-center p-4 rounded">
                                    <h5 class="{{ $promo->text_theme === 'dark' ? 'text-primary' : 'text-white' }}">
                                        {{ $promo->title }}
                                    </h5>
                                    @if($promo->subtitle)
                                    <h3 class="mb-0">{{ $promo->subtitle }}</h3>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <!-- Featurs End -->
    <!-- Vesitable Shop Start-->
    @if($newArrivals->isNotEmpty())
    <div class="container-fluid vesitable py-5">
        <div class="container py-5">
            <h1 class="mb-0">Nouveautés</h1>
            <div class="owl-carousel vegetable-carousel justify-content-center">
                @foreach($newArrivals as $product)
                @php $img = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
                <div class="border border-primary rounded position-relative vesitable-item">
                    <div class="vesitable-img">
                        <img src="{{ $img ? $img->url : 'img/vegetable-item-1.jpg' }}"
                            class="img-fluid w-100 rounded-top" alt="{{ $product->name }}">
                    </div>
                    @if($product->category)
                    <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                        style="top: 10px; right: 10px;">{{ $product->category->name }}</div>
                    @endif
                    <div class="p-4 rounded-bottom">
                        <h4>{{ $product->name }}</h4>
                        <p class="text-truncate mb-2">{{ $product->short_description }}</p>
                        <div class="d-flex justify-content-between flex-lg-wrap">
                            <p class="text-dark fs-5 fw-bold mb-0">
                                {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                @if($product->unit) / {{ $product->unit }} @endif
                            </p>
                            <a href="{{ route('shop.show', $product->slug) }}"
                                class="btn border border-secondary rounded-pill px-3 text-primary">
                                <i class="fa fa-shopping-bag me-2 text-primary"></i> Voir
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <!-- Vesitable Shop End -->
    <!-- Banner Section Start-->
    <div class="container-fluid banner bg-secondary my-5">
        <div class="container py-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="py-4">
                        <h1 class="display-3 text-white">{{ $banner['banner_title'] ?? 'Fruits frais exotiques' }}</h1>
                        <p class="fw-normal display-3 text-dark mb-4">{{ $banner['banner_subtitle'] ?? 'dans notre magasin' }}</p>
                        <p class="mb-4 text-dark">{{ $banner['banner_description'] ?? '' }}</p>
                        <a href="{{ $banner['banner_button_url'] ?? '/shop' }}"
                            class="banner-btn btn border-2 border-white rounded-pill text-dark py-3 px-5">
                            {{ $banner['banner_button_text'] ?? 'ACHETER' }}
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative">
                        @php $bannerImg = isset($banner['banner_image']) ? Storage::url($banner['banner_image']) : asset('img/baner-1.png'); @endphp
                        <img src="{{ $bannerImg }}" class="img-fluid w-100 rounded" alt="bannière">
                        @if(isset($banner['banner_badge_value']) || isset($banner['banner_badge_unit']))
                        <div class="d-flex align-items-center justify-content-center bg-white rounded-circle position-absolute"
                            style="width:140px;height:140px;top:0;left:0;">
                            <div class="d-flex flex-column text-center">
                                <span class="h2 mb-0 fw-bold">{{ $banner['banner_badge_value'] ?? '50$' }}</span>
                                <span class="h4 text-muted mb-0">{{ $banner['banner_badge_unit'] ?? 'kg' }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Banner Section End -->
    <!-- Bestsaler Product Start -->
    @if($bestsellers->isNotEmpty())
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5" style="max-width: 700px;">
                <h1 class="display-4">Les produits les plus vus</h1>
                <p>Découvrez les produits que nos clients consultent le plus.</p>
            </div>
            <div class="row g-4">
                @foreach($bestsellers as $bp)
                @php $bpImg = $bp->images->firstWhere('is_primary', true) ?? $bp->images->first(); @endphp
                <div class="col-lg-6 col-xl-4">
                    <div class="p-4 rounded bg-light">
                        <div class="row align-items-center">
                            <div class="col-5">
                                <a href="{{ route('shop.show', $bp->slug) }}">
                                    <img src="{{ $bpImg ? $bpImg->url : asset('img/best-product-1.jpg') }}"
                                         class="img-fluid rounded-circle w-100"
                                         style="height:120px;object-fit:cover;"
                                         alt="{{ $bp->name }}">
                                </a>
                            </div>
                            <div class="col-7">
                                <a href="{{ route('shop.show', $bp->slug) }}" class="h6 text-dark text-decoration-none">
                                    {{ $bp->name }}
                                </a>
                                {{-- Étoiles --}}
                                <div class="d-flex my-2">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= round($bp->rating_avg) ? 'text-primary' : 'text-muted' }}" style="font-size:.75rem;"></i>
                                    @endfor
                                    <span class="text-muted ms-1" style="font-size:.75rem;">({{ $bp->rating_count }})</span>
                                </div>
                                {{-- Stats --}}
                                <div class="d-flex gap-2 mb-2 flex-wrap">
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-eye me-1"></i>{{ number_format($bp->views_count) }} vues
                                    </span>
                                    <span class="badge bg-light text-muted border" style="font-size:.7rem;">
                                        {{ $bp->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <h5 class="mb-2 text-dark">{{ number_format($bp->price, 0, ',', ' ') }} FCFA
                                    @if($bp->unit)<small class="text-muted fs-6">/ {{ $bp->unit }}</small>@endif
                                </h5>
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $bp->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit"
                                        class="btn btn-sm border border-secondary rounded-pill px-3 text-primary"
                                        {{ $bp->stock_quantity <= 0 ? 'disabled' : '' }}>
                                        <i class="fa fa-shopping-bag me-1 text-primary"></i>
                                        {{ $bp->stock_quantity > 0 ? 'Ajouter' : 'Rupture' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <!-- Bestsaler Product End -->
    <!-- Fact Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="bg-light p-5 rounded">
                <div class="row g-4 justify-content-center">
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>Des clients satisfaits</h4>
                            <h1>{{ $stats['stat_customers'] ?? '1963' }}</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-thumbs-up text-secondary"></i>
                            <h4>La qualité du service</h4>
                            <h1>{{ $stats['stat_quality'] ?? '99%' }}</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-certificate text-secondary"></i>
                            <h4>Certificats de qualité</h4>
                            <h1>{{ $stats['stat_certificates'] ?? '33' }}</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-box text-secondary"></i>
                            <h4>Produits disponibles</h4>
                            <h1>{{ $stats['stat_products'] ?? '789' }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fact End -->
    <!-- Tastimonial Start -->
    @if($testimonials->isNotEmpty())
    <div class="container-fluid testimonial py-5">
        <div class="container py-5">
            <div class="testimonial-header text-center">
                <h4 class="text-primary">Témoignage</h4>
                <h1 class="display-5 mb-5 text-dark">Ce que disent nos clients !</h1>
            </div>
            <div class="owl-carousel testimonial-carousel">
                @foreach($testimonials as $t)
                <div class="testimonial-item img-border-radius bg-light rounded p-4">
                    <div class="position-relative">
                        <i class="fa fa-quote-right fa-2x text-secondary position-absolute"
                            style="bottom: 30px; right: 0;"></i>
                        <div class="mb-4 pb-4 border-bottom border-secondary">
                            <p class="mb-0">{{ $t->description }}</p>
                        </div>
                        <div class="d-flex align-items-center flex-nowrap">
                            <div class="bg-secondary rounded">
                                <img src="{{ $t->photo_url }}" class="img-fluid rounded"
                                    style="width:100px;height:100px;object-fit:cover;" alt="{{ $t->name }}">
                            </div>
                            <div class="ms-4 d-block">
                                <h4 class="text-dark">{{ $t->name }}</h4>
                                <p class="m-0 pb-3">{{ $t->profession }}</p>
                                <div class="d-flex pe-5">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $t->rating ? 'text-primary' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <!-- Tastimonial End -->
@endsection
