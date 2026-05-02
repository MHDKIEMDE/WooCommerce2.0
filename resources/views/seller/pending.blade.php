@extends('layouts.app')
@section('seo_title', 'Boutique en attente de validation')
@section('noindex')
@section('content')

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Demande reçue !</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item active text-white">Ma boutique</li>
        </ol>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="mb-4">
                    @if($shop && $shop->status === 'active')
                        <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                            style="width:90px;height:90px;">
                            <i class="fas fa-check fa-3x text-white"></i>
                        </div>
                        <h2 class="fw-bold text-success">Boutique active !</h2>
                        <p class="text-muted fs-5 mb-4">
                            Votre boutique <strong>{{ $shop->name }}</strong> est en ligne.
                        </p>
                        <a href="{{ route('marketplace.show', $shop->slug) }}" class="btn btn-success btn-lg me-2">
                            <i class="fas fa-store me-2"></i> Voir ma boutique
                        </a>
                    @else
                        <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                            style="width:90px;height:90px;">
                            <i class="fas fa-clock fa-3x text-white"></i>
                        </div>
                        <h2 class="fw-bold">En attente de validation</h2>
                        <p class="text-muted fs-5 mb-4">
                            Votre boutique <strong>{{ $shop?->name ?? 'sans nom' }}</strong> a bien été soumise.
                            Notre équipe l'examine et vous enverra une confirmation sous <strong>24h</strong>.
                        </p>
                    @endif
                </div>

                <div class="row g-3 text-start mb-5">
                    <div class="col-6">
                        <div class="border rounded-3 p-3">
                            <small class="text-muted d-block">Nom</small>
                            <strong>{{ $shop?->name ?? '—' }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-3 p-3">
                            <small class="text-muted d-block">Statut</small>
                            @if($shop?->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($shop?->status === 'pending')
                                <span class="badge bg-warning text-dark">En attente</span>
                            @else
                                <span class="badge bg-secondary">—</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('home') }}" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i> Retour à l'accueil
                    </a>
                    <a href="{{ route('marketplace.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-store me-2"></i> Voir les boutiques
                    </a>
                </div>

            </div>
        </div>
    </div>

@endsection
