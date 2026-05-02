@extends('layouts.app')
@section('seo_title', 'Devenir vendeur — Ouvrez votre boutique')
@section('content')

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Ouvrez votre boutique</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item active text-white">Devenir vendeur</li>
        </ol>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- Avantages --}}
                <div class="row g-3 mb-5 text-center">
                    <div class="col-md-4">
                        <div class="p-4 rounded-3 bg-light h-100">
                            <i class="fas fa-store fa-2x text-primary mb-3"></i>
                            <h6 class="fw-bold">Boutique personnalisée</h6>
                            <small class="text-muted">Choisissez votre template et vos couleurs</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 rounded-3 bg-light h-100">
                            <i class="fas fa-chart-line fa-2x text-primary mb-3"></i>
                            <h6 class="fw-bold">Dashboard vendeur</h6>
                            <small class="text-muted">Suivez vos ventes et commandes en temps réel</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 rounded-3 bg-light h-100">
                            <i class="fas fa-percentage fa-2x text-primary mb-3"></i>
                            <h6 class="fw-bold">Commission 5%</h6>
                            <small class="text-muted">Seulement 5% de commission sur vos ventes</small>
                        </div>
                    </div>
                </div>

                {{-- Formulaire --}}
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-store me-2"></i> Créer ma boutique</h5>
                    </div>
                    <div class="card-body p-4">

                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <form action="{{ route('seller.register.store') }}" method="POST">
                            @csrf

                            {{-- Nom de la boutique --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Nom de la boutique <sup class="text-danger">*</sup></label>
                                <input type="text" name="shop_name"
                                    class="form-control form-control-lg @error('shop_name') is-invalid @enderror"
                                    value="{{ old('shop_name') }}"
                                    placeholder="Ex : BioFarm Abidjan"
                                    maxlength="100" required>
                                @error('shop_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Ce nom sera visible par tous les acheteurs.</small>
                            </div>

                            {{-- Description --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Description <sup class="text-danger">*</sup></label>
                                <textarea name="description" rows="3"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Décrivez votre boutique en quelques mots…"
                                    maxlength="500" required>{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Max 500 caractères.</small>
                            </div>

                            {{-- Choix du template (niche) --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Niche / Template <sup class="text-danger">*</sup></label>
                                <div class="row g-3" id="template-grid">
                                    @foreach($templates as $template)
                                    <div class="col-md-4">
                                        <label class="d-block cursor-pointer">
                                            <input type="radio" name="template_id" value="{{ $template->id }}"
                                                class="d-none template-radio"
                                                {{ old('template_id') == $template->id ? 'checked' : '' }} required>
                                            <div class="card border-2 text-center p-3 template-card h-100
                                                {{ old('template_id') == $template->id ? 'border-primary bg-primary-subtle' : 'border-light' }}">
                                                @if($template->icon)
                                                <div class="fs-2 mb-2">{{ $template->icon }}</div>
                                                @else
                                                <i class="fas fa-store fa-2x text-primary mb-2"></i>
                                                @endif
                                                <div class="fw-semibold small">{{ $template->name }}</div>
                                            </div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @error('template_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Choix de la palette (affiché après sélection template) --}}
                            <div class="mb-4" id="palette-section" style="{{ old('template_id') ? '' : 'display:none' }}">
                                <label class="form-label fw-semibold">Palette de couleurs <sup class="text-danger">*</sup></label>
                                <div class="row g-2" id="palette-grid">
                                    @foreach($templates as $template)
                                    @foreach($template->palettes as $palette)
                                    <div class="col-auto palette-option" data-template="{{ $template->id }}"
                                        style="{{ old('template_id') == $template->id ? '' : 'display:none' }}">
                                        <label class="d-block cursor-pointer">
                                            <input type="radio" name="palette_id" value="{{ $palette->id }}"
                                                class="d-none palette-radio"
                                                {{ old('palette_id') == $palette->id ? 'checked' : '' }}>
                                            <div class="rounded-circle border-3 palette-dot
                                                {{ old('palette_id') == $palette->id ? 'border border-dark' : '' }}"
                                                style="width:40px;height:40px;background:{{ $palette->primary_color ?? '#3b82f6' }};cursor:pointer;"
                                                title="{{ $palette->name ?? '' }}"></div>
                                        </label>
                                    </div>
                                    @endforeach
                                    @endforeach
                                </div>
                                @error('palette_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                    <i class="fas fa-paper-plane me-2"></i> Soumettre ma boutique
                                </button>
                            </div>

                            <p class="text-muted text-center small mt-3">
                                Votre boutique sera activée après validation par notre équipe (sous 24h).
                            </p>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('.template-radio');
        const paletteSection = document.getElementById('palette-section');

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                // Mettre à jour le style des cards
                document.querySelectorAll('.template-card').forEach(function (card) {
                    card.classList.remove('border-primary', 'bg-primary-subtle');
                    card.classList.add('border-light');
                });
                const selected = radio.closest('label').querySelector('.template-card');
                selected.classList.add('border-primary', 'bg-primary-subtle');
                selected.classList.remove('border-light');

                // Afficher les palettes correspondantes
                paletteSection.style.display = '';
                document.querySelectorAll('.palette-option').forEach(function (opt) {
                    opt.style.display = opt.dataset.template === radio.value ? '' : 'none';
                });

                // Décocher les palettes cachées
                document.querySelectorAll('.palette-radio').forEach(function (pr) {
                    const opt = pr.closest('.palette-option');
                    if (opt && opt.style.display === 'none') pr.checked = false;
                });
            });
        });

        // Style des dots de palette sélectionnés
        document.querySelectorAll('.palette-radio').forEach(function (radio) {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.palette-dot').forEach(function (dot) {
                    dot.classList.remove('border', 'border-dark');
                });
                radio.closest('label').querySelector('.palette-dot').classList.add('border', 'border-dark');
            });
        });
    });
    </script>

@endsection
