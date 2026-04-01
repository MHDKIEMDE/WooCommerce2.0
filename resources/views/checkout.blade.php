@extends('layouts.app')
@section('Agribusiness Shop', 'Paiement')
@section('content')

    <!-- En-tête -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Finaliser la commande</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-white">Panier</a></li>
            <li class="breadcrumb-item active text-white">Paiement</li>
        </ol>
    </div>

    <div class="container-fluid py-5">
        <div class="container py-5">

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf
                <div class="row g-5">

                    {{-- ── Colonne gauche : Données de facturation ── --}}
                    <div class="col-md-12 col-lg-6 col-xl-7">
                        <h4 class="mb-4">Données de facturation</h4>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">Prénom <sup class="text-danger">*</sup></label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                    value="{{ old('first_name', $user?->name) }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Nom <sup class="text-danger">*</sup></label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                    value="{{ old('last_name') }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Adresse <sup class="text-danger">*</sup></label>
                                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                                    placeholder="Numéro, rue, quartier…"
                                    value="{{ old('address') }}" required>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Ville <sup class="text-danger">*</sup></label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                                    value="{{ old('city') }}" required>
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Code postal</label>
                                <input type="text" name="postal_code" class="form-control"
                                    value="{{ old('postal_code') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Pays <sup class="text-danger">*</sup></label>
                                <input type="text" name="country" class="form-control @error('country') is-invalid @enderror"
                                    value="{{ old('country', 'Côte d\'Ivoire') }}" required>
                                @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Téléphone <sup class="text-danger">*</sup></label>
                                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}" required>
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Email <sup class="text-danger">*</sup></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user?->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes de commande (optionnel)</label>
                                <textarea name="notes" class="form-control" rows="4"
                                    placeholder="Instructions de livraison, informations complémentaires…">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        {{-- Méthode de paiement --}}
                        <h4 class="mt-5 mb-3">Mode de paiement</h4>

                        <div class="border rounded p-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method"
                                    id="cod" value="cash_on_delivery"
                                    {{ old('payment_method', 'cash_on_delivery') === 'cash_on_delivery' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="cod">
                                    <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                    Paiement à la livraison
                                </label>
                            </div>
                            <p class="text-muted small ms-4 mb-0 mt-1">
                                Réglez en espèces au moment de la livraison.
                            </p>
                        </div>

                        <div class="border rounded p-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method"
                                    id="bank" value="bank_transfer"
                                    {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="bank">
                                    <i class="fas fa-university me-2 text-primary"></i>
                                    Virement bancaire
                                </label>
                            </div>
                            <p class="text-muted small ms-4 mb-0 mt-1">
                                Effectuez votre virement directement depuis votre banque.
                                Votre commande sera expédiée après réception du paiement.
                            </p>
                        </div>
                        @error('payment_method')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ── Colonne droite : Récapitulatif commande ── --}}
                    <div class="col-md-12 col-lg-6 col-xl-5">
                        <div class="bg-light rounded p-4">
                            <h4 class="mb-4">Récapitulatif</h4>

                            <table class="table table-borderless align-middle">
                                <thead class="border-bottom">
                                    <tr>
                                        <th>Produit</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        @php
                                            $unitPrice = $item->product->price + ($item->variant?->price_modifier ?? 0);
                                            $lineTotal = $unitPrice * $item->quantity;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @php $img = $item->product->images->first(); @endphp
                                                    @if ($img)
                                                        <img src="{{ $img->url }}" alt="{{ $item->product->name }}"
                                                            style="width:48px;height:48px;object-fit:cover;" class="rounded">
                                                    @endif
                                                    <div>
                                                        <div class="fw-semibold" style="font-size:.9rem">{{ $item->product->name }}</div>
                                                        @if ($item->variant)
                                                            <small class="text-muted">{{ $item->variant->name }}</small>
                                                        @endif
                                                        <div class="text-muted small">× {{ $item->quantity }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">{{ number_format($lineTotal, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="border-top">
                                    <tr>
                                        <td class="text-muted">Sous-total</td>
                                        <td class="text-end">{{ number_format($totals['subtotal'], 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                    @if ($totals['discount'] > 0)
                                    <tr class="text-success">
                                        <td>
                                            Réduction
                                            @if ($coupon)
                                                <span class="badge bg-success ms-1">{{ $coupon->code }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">− {{ number_format($totals['discount'], 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="text-muted">Livraison</td>
                                        <td class="text-end">
                                            @if ($totals['shippingCost'] == 0)
                                                <span class="text-success">Gratuite</span>
                                            @else
                                                {{ number_format($totals['shippingCost'], 0, ',', ' ') }} FCFA
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="border-top fw-bold">
                                        <td>Total</td>
                                        <td class="text-end text-primary fs-5">
                                            {{ number_format($totals['total'], 0, ',', ' ') }} FCFA
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="d-grid mt-3">
                                <button type="submit" class="btn btn-primary btn-lg py-3">
                                    <i class="fas fa-lock me-2"></i> Confirmer la commande
                                </button>
                            </div>
                            <p class="text-center text-muted small mt-3">
                                <i class="fas fa-shield-alt me-1"></i>
                                Vos données sont sécurisées et ne seront jamais partagées.
                            </p>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

@endsection
