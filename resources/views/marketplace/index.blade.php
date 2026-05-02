@extends('layouts.app')
@section('seo_title', 'Marketplace — Toutes les boutiques')
@section('content')

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Marketplace</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item active text-white">Boutiques</li>
        </ol>
    </div>

    <div class="container-fluid py-5">
        <div class="container py-3">

            {{-- Statistique rapide --}}
            <div class="text-center mb-5">
                <h2 class="fw-bold">{{ $total }} boutiques actives</h2>
                <p class="text-muted">Découvrez nos vendeurs locaux sélectionnés</p>
            </div>

            {{-- Filtres --}}
            <form method="GET" action="{{ route('marketplace.index') }}" class="row g-3 mb-5">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control" placeholder="Rechercher une boutique...">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="niche" class="form-select" onchange="this.form.submit()">
                        <option value="">Toutes les niches</option>
                        @foreach($niches as $niche)
                        <option value="{{ $niche->slug }}" {{ request('niche') === $niche->slug ? 'selected' : '' }}>
                            {{ $niche->name }} ({{ $niche->shops_count }})
                        </option>
                        @endforeach
                    </select>
                </div>
                @if(request('q') || request('niche'))
                <div class="col-md-2">
                    <a href="{{ route('marketplace.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i> Réinitialiser
                    </a>
                </div>
                @endif
            </form>

            {{-- Grille boutiques --}}
            @if($shops->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-store fa-3x text-muted mb-3"></i>
                <p class="text-muted fs-5">Aucune boutique trouvée.</p>
            </div>
            @else
            <div class="row g-4">
                @foreach($shops as $shop)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                        {{-- Bannière --}}
                        <div class="position-relative" style="height:140px;background: linear-gradient(135deg, var(--bs-primary), var(--bs-secondary, #198754));">
                            @if($shop->banner)
                            <img src="{{ Storage::url($shop->banner) }}" alt="{{ $shop->name }}"
                                class="w-100 h-100" style="object-fit:cover;opacity:.6;">
                            @endif
                            {{-- Logo --}}
                            <div class="position-absolute bottom-0 start-0 ms-3 mb-n4" style="z-index:1;">
                                @if($shop->logo)
                                <img src="{{ Storage::url($shop->logo) }}" alt="{{ $shop->name }}"
                                    class="rounded-circle border border-3 border-white bg-white"
                                    style="width:64px;height:64px;object-fit:cover;">
                                @else
                                <div class="rounded-circle border border-3 border-white bg-primary d-flex align-items-center justify-content-center"
                                    style="width:64px;height:64px;">
                                    <span class="text-white fw-bold fs-4">{{ strtoupper(substr($shop->name, 0, 1)) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body pt-5 mt-2">
                            <h5 class="fw-bold mb-1">{{ $shop->name }}</h5>
                            @if($shop->template)
                            <span class="badge bg-primary-subtle text-primary mb-2">{{ $shop->template->name }}</span>
                            @endif
                            <p class="text-muted small mb-3">{{ Str::limit($shop->description, 90) }}</p>
                            <a href="{{ route('marketplace.show', $shop->slug) }}"
                                class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-store me-1"></i> Visiter la boutique
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $shops->links() }}
            </div>
            @endif

        </div>
    </div>

@endsection
