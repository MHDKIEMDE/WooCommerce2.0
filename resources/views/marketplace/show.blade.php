@extends('layouts.app')
@section('seo_title', $shop->name . ' — Boutique')
@section('content')

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">{{ $shop->name }}</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}" class="text-white">Boutiques</a></li>
            <li class="breadcrumb-item active text-white">{{ $shop->name }}</li>
        </ol>
    </div>

    <div class="container-fluid py-5">
        <div class="container py-3">

            {{-- En-tête boutique --}}
            <div class="card border-0 shadow-sm mb-5 rounded-3 overflow-hidden">
                <div class="position-relative" style="height:200px; background: linear-gradient(135deg, var(--bs-primary), var(--bs-secondary, #198754));">
                    @if($shop->banner)
                    <img src="{{ Storage::url($shop->banner) }}" alt="{{ $shop->name }}"
                        class="w-100 h-100" style="object-fit:cover;opacity:.5;">
                    @endif
                    <div class="position-absolute bottom-0 start-0 ms-4 mb-n5" style="z-index:2;">
                        @if($shop->logo)
                        <img src="{{ Storage::url($shop->logo) }}" alt="{{ $shop->name }}"
                            class="rounded-circle border border-4 border-white bg-white shadow"
                            style="width:90px;height:90px;object-fit:cover;">
                        @else
                        <div class="rounded-circle border border-4 border-white bg-primary shadow d-flex align-items-center justify-content-center"
                            style="width:90px;height:90px;">
                            <span class="text-white fw-bold display-6">{{ strtoupper(substr($shop->name, 0, 1)) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body pt-5 mt-3 ps-5">
                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                        <div>
                            <h2 class="fw-bold mb-1">{{ $shop->name }}</h2>
                            @if($shop->template)
                            <span class="badge bg-primary-subtle text-primary me-2">{{ $shop->template->name }}</span>
                            @endif
                            <p class="text-muted mt-2 mb-0" style="max-width:600px;">{{ $shop->description }}</p>
                        </div>
                        <div class="text-muted small text-end">
                            <div><i class="fas fa-user me-1"></i> Vendeur : {{ $shop->owner->name }}</div>
                            <div><i class="fas fa-calendar me-1"></i> Depuis {{ $shop->created_at->translatedFormat('F Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Produits de la boutique --}}
            <h4 class="fw-bold mb-4">
                Produits de la boutique
                <span class="text-muted fs-6 fw-normal">({{ $products->total() }} articles)</span>
            </h4>

            @if($products->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Cette boutique n'a pas encore de produits.</p>
            </div>
            @else
            <div class="row g-4">
                @foreach($products as $product)
                <div class="col-md-6 col-lg-3">
                    @include('components.product-card', ['product' => $product])
                </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
            @endif

        </div>
    </div>

@endsection
