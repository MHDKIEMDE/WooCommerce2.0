@extends('dashboard.admin.layout.app')

@section('title', 'Informations boutique')

@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Informations de la boutique</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Boutique</li>
    </ol>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">
                    <i class="fas fa-store me-2 text-primary"></i> Identité de la boutique
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.shop-settings.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Nom de la boutique <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="shop_name" class="form-control @error('shop_name') is-invalid @enderror"
                                value="{{ old('shop_name', $shop['shop_name'] ?? 'Agribusiness shop') }}" required>
                            @error('shop_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Affiché dans le header, le footer et les titres des pages.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slogan</label>
                            <input type="text" name="shop_tagline" class="form-control"
                                placeholder="ex: Produit local"
                                value="{{ old('shop_tagline', $shop['shop_tagline'] ?? '') }}">
                            <div class="form-text">Affiché sous le nom dans le footer.</div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email de contact</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="shop_email" class="form-control"
                                    placeholder="contact@maboutique.com"
                                    value="{{ old('shop_email', $shop['shop_email'] ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" name="shop_phone" class="form-control"
                                    placeholder="+226 07 44 31 12"
                                    value="{{ old('shop_phone', $shop['shop_phone'] ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Adresse</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" name="shop_address" class="form-control"
                                    placeholder="1200 Logement, Ouagadougou, Burkina Faso"
                                    value="{{ old('shop_address', $shop['shop_address'] ?? '') }}">
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold mb-3"><i class="fas fa-coins text-primary me-2"></i>Devise & Format des prix</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Symbole</label>
                                <input type="text" name="shop_currency" class="form-control"
                                    placeholder="FCFA"
                                    value="{{ old('shop_currency', $shop['shop_currency'] ?? 'FCFA') }}">
                                <div class="form-text">Ex : FCFA, €, $, £, XOF</div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Position</label>
                                <select name="currency_position" class="form-select">
                                    <option value="after"  {{ ($shop['currency_position'] ?? 'after')  === 'after'  ? 'selected' : '' }}>Après (1 500 FCFA)</option>
                                    <option value="before" {{ ($shop['currency_position'] ?? 'after')  === 'before' ? 'selected' : '' }}>Avant (€ 1 500)</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Décimales</label>
                                <select name="currency_decimals" class="form-select">
                                    <option value="0" {{ ($shop['currency_decimals'] ?? '0') === '0' ? 'selected' : '' }}>0 (1 500)</option>
                                    <option value="2" {{ ($shop['currency_decimals'] ?? '0') === '2' ? 'selected' : '' }}>2 (1 500,00)</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Séparateur milliers</label>
                                <select name="currency_thou_sep" class="form-select">
                                    <option value=" "  {{ ($shop['currency_thou_sep'] ?? ' ') === ' '  ? 'selected' : '' }}>Espace (1 500)</option>
                                    <option value="."  {{ ($shop['currency_thou_sep'] ?? ' ') === '.'  ? 'selected' : '' }}>Point (1.500)</option>
                                    <option value=","  {{ ($shop['currency_thou_sep'] ?? ' ') === ','  ? 'selected' : '' }}>Virgule (1,500)</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Séparateur décimal</label>
                                <select name="currency_dec_sep" class="form-select">
                                    <option value="," {{ ($shop['currency_dec_sep'] ?? ',') === ',' ? 'selected' : '' }}>Virgule (1 500,00)</option>
                                    <option value="." {{ ($shop['currency_dec_sep'] ?? ',') === '.' ? 'selected' : '' }}>Point (1 500.00)</option>
                                </select>
                            </div>
                        </div>
                        <div class="alert alert-info py-2 px-3 small mb-0">
                            <i class="fas fa-eye me-1"></i>
                            <strong>Aperçu :</strong>
                            <span id="currency-preview">{{ fmt_price(15000) }}</span>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-eye text-primary me-2"></i>Aperçu</h6>
                    <div class="bg-white rounded p-3 border">
                        <div class="fw-bold text-success fs-5">{{ $shop['shop_name'] ?? 'Agribusiness shop' }}</div>
                        <div class="text-warning small">{{ $shop['shop_tagline'] ?? 'Produit local' }}</div>
                        <hr class="my-2">
                        @if(!empty($shop['shop_address'] ?? ''))
                            <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $shop['shop_address'] }}</div>
                        @endif
                        @if(!empty($shop['shop_phone'] ?? ''))
                            <div class="small text-muted"><i class="fas fa-phone me-1"></i>{{ $shop['shop_phone'] }}</div>
                        @endif
                        @if(!empty($shop['shop_email'] ?? ''))
                            <div class="small text-muted"><i class="fas fa-envelope me-1"></i>{{ $shop['shop_email'] }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
