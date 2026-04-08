@extends('layouts.app')
@section('seo_title', 'À propos')
@section('seo_description', 'Découvrez notre histoire, nos valeurs et notre engagement pour des produits frais de qualité.')
@section('content')

@php $shop = \App\Models\Setting::getGroup('shop'); @endphp

<!-- Header -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">À propos</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
        <li class="breadcrumb-item active text-white">À propos</li>
    </ol>
</div>

<!-- Notre histoire -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="position-relative overflow-hidden rounded">
                    <img class="img-fluid w-100" src="{{ asset('img/hero.jpg') }}" alt="À propos de {{ $shop['shop_name'] ?? config('app.name') }}" style="object-fit:cover; max-height:450px;">
                </div>
            </div>
            <div class="col-lg-6">
                <h4 class="text-primary mb-3">Notre histoire</h4>
                <h1 class="display-5 mb-4">{{ $shop['shop_name'] ?? config('app.name') }}</h1>
                <p class="text-muted mb-4">{{ $shop['shop_tagline'] ?? 'Produits frais de qualité, livrés directement chez vous.' }}</p>
                <p class="mb-4">Nous sommes engagés à vous offrir les meilleurs produits frais, directement sélectionnés auprès de producteurs locaux de confiance. Notre mission est de rendre accessible une alimentation saine, naturelle et de qualité à tous.</p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-primary fs-4 me-3"></i>
                            <span>Produits frais garantis</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-primary fs-4 me-3"></i>
                            <span>Producteurs locaux</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-primary fs-4 me-3"></i>
                            <span>Livraison rapide</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-primary fs-4 me-3"></i>
                            <span>Service client disponible</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Nos valeurs -->
<div class="container-fluid bg-light py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h4 class="text-primary">Pourquoi nous choisir ?</h4>
            <h1 class="display-6">Nos valeurs</h1>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <i class="bi bi-leaf text-primary display-4 mb-3"></i>
                    <h5 class="mb-3">Qualité naturelle</h5>
                    <p class="text-muted">Nous sélectionnons rigoureusement chaque produit pour vous garantir fraîcheur et qualité à chaque commande.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <i class="bi bi-truck text-primary display-4 mb-3"></i>
                    <h5 class="mb-3">Livraison fiable</h5>
                    <p class="text-muted">Votre commande est livrée rapidement et en parfait état, directement à votre domicile.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <i class="bi bi-people text-primary display-4 mb-3"></i>
                    <h5 class="mb-3">Producteurs locaux</h5>
                    <p class="text-muted">Nous soutenons les agriculteurs de notre région en valorisant leurs productions et leur savoir-faire.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact rapide -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h4 class="text-primary">Contactez-nous</h4>
            <h1 class="display-6">Nous sommes là pour vous</h1>
        </div>
        <div class="row g-4 justify-content-center">
            @if(!empty($shop['shop_address']))
            <div class="col-md-4 text-center">
                <i class="bi bi-geo-alt text-primary display-5 mb-3"></i>
                <h5>Adresse</h5>
                <p class="text-muted">{{ $shop['shop_address'] }}</p>
            </div>
            @endif
            @if(!empty($shop['shop_phone']))
            <div class="col-md-4 text-center">
                <i class="bi bi-telephone text-primary display-5 mb-3"></i>
                <h5>Téléphone</h5>
                <p class="text-muted">{{ $shop['shop_phone'] }}</p>
            </div>
            @endif
            @if(!empty($shop['shop_email']))
            <div class="col-md-4 text-center">
                <i class="bi bi-envelope text-primary display-5 mb-3"></i>
                <h5>Email</h5>
                <p class="text-muted">{{ $shop['shop_email'] }}</p>
            </div>
            @endif
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill py-3 px-5">
                Découvrir nos produits
            </a>
        </div>
    </div>
</div>

@endsection
