@extends('dashboard.admin.layout.app')
@section('title', 'Bannière & Statistiques')
@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Bannière principale & Statistiques</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Paramètres accueil</li>
    </ol>

    <form action="{{ route('admin.home-settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- Bannière --}}
        <div class="card mb-4">
            <div class="card-header fw-bold"><i class="fas fa-image me-2"></i>Bannière principale</div>
            <div class="card-body row g-3">
                @if(($banner['banner_image'] ?? null))
                <div class="col-12">
                    <img src="{{ Storage::url($banner['banner_image']) }}" alt="bannière"
                         style="height:120px;object-fit:cover;border-radius:6px;">
                </div>
                @endif
                <div class="col-md-6">
                    <label class="form-label">Titre</label>
                    <input type="text" name="banner_title" class="form-control"
                           value="{{ $banner['banner_title'] ?? '' }}" placeholder="Fruits frais exotiques">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sous-titre</label>
                    <input type="text" name="banner_subtitle" class="form-control"
                           value="{{ $banner['banner_subtitle'] ?? '' }}" placeholder="dans notre magasin">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="banner_description" class="form-control" rows="2">{{ $banner['banner_description'] ?? '' }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Texte du bouton</label>
                    <input type="text" name="banner_button_text" class="form-control"
                           value="{{ $banner['banner_button_text'] ?? 'ACHETER' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lien du bouton</label>
                    <input type="text" name="banner_button_url" class="form-control"
                           value="{{ $banner['banner_button_url'] ?? '/shop' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Image de la bannière</label>
                    <input type="file" name="banner_image" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Badge : valeur</label>
                    <input type="text" name="banner_badge_value" class="form-control"
                           value="{{ $banner['banner_badge_value'] ?? '50$' }}" placeholder="50$">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Badge : unité</label>
                    <input type="text" name="banner_badge_unit" class="form-control"
                           value="{{ $banner['banner_badge_unit'] ?? 'kg' }}" placeholder="kg">
                </div>
            </div>
        </div>

        {{-- Statistiques --}}
        <div class="card mb-4">
            <div class="card-header fw-bold"><i class="fas fa-chart-bar me-2"></i>Compteurs statistiques</div>
            <div class="card-body row g-3">
                <div class="col-md-3">
                    <label class="form-label">Clients satisfaits</label>
                    <input type="text" name="stat_customers" class="form-control"
                           value="{{ $stats['stat_customers'] ?? '1963' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Qualité du service</label>
                    <input type="text" name="stat_quality" class="form-control"
                           value="{{ $stats['stat_quality'] ?? '99%' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Certificats qualité</label>
                    <input type="text" name="stat_certificates" class="form-control"
                           value="{{ $stats['stat_certificates'] ?? '33' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Produits disponibles</label>
                    <input type="text" name="stat_products" class="form-control"
                           value="{{ $stats['stat_products'] ?? '789' }}">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary px-5">Enregistrer</button>
    </form>
</div>
@endsection
