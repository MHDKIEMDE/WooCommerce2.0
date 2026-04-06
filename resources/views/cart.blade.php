@extends('layouts.app')
@section('seo_title', 'Mon Panier')
@section('noindex')
@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Mon Panier</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item active text-white">Panier</li>
        </ol>
    </div>
    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">

            {{-- Messages --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @error('cart')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            @if($items->isEmpty())
            <div class="text-center py-5">
                <i class="fa fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">Votre panier est vide</h4>
                <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-5 mt-3">
                    Continuer les achats
                </a>
            </div>
            @else

            {{-- Tableau des articles --}}
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Produit</th>
                            <th scope="col">Nom</th>
                            <th scope="col">Prix unitaire</th>
                            <th scope="col">Quantité</th>
                            <th scope="col">Total</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        @php
                            $img       = $item->product?->images?->firstWhere('is_primary', true)
                                      ?? $item->product?->images?->first();
                            $unitPrice = (float)$item->product?->price + (float)($item->variant?->price_modifier ?? 0);
                            $lineTotal = round($unitPrice * $item->quantity, 2);
                        @endphp
                        <tr>
                            <td>
                                <img src="{{ $img ? $img->url : asset('img/vegetable-item-3.png') }}"
                                    class="img-fluid rounded-circle" style="width:70px;height:70px;object-fit:cover;"
                                    alt="{{ $item->product?->name }}">
                            </td>
                            <td>
                                <p class="mb-0 fw-semibold">{{ $item->product?->name }}</p>
                                @if($item->variant)
                                <small class="text-muted">{{ $item->variant->name }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold">{{ number_format($unitPrice, 0, ',', ' ') }} FCFA</span>
                                @if($item->product?->unit)
                                <small class="text-muted"> / {{ $item->product->unit }}</small>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('cart.update', $item->id) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <div class="input-group quantity" style="width: 110px;">
                                        <button type="button" class="btn btn-sm btn-minus rounded-circle bg-light border">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                        <input type="text" name="quantity"
                                            class="form-control form-control-sm text-center border-0 qty-input"
                                            value="{{ $item->quantity }}" min="1" max="99">
                                        <button type="button" class="btn btn-sm btn-plus rounded-circle bg-light border">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </form>
                            </td>
                            <td>
                                <span class="fw-bold">{{ number_format($lineTotal, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm rounded-circle bg-light border text-danger" type="submit"
                                        title="Supprimer">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Code promo --}}
            <div class="mt-4 mb-5">
                @if($coupon)
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-success fs-6">Code <strong>{{ $coupon->code }}</strong> appliqué !</span>
                    <form method="POST" action="{{ route('cart.coupon.remove') }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger rounded-pill">Retirer</button>
                    </form>
                </div>
                @else
                <form method="POST" action="{{ route('cart.coupon') }}" class="d-flex align-items-center gap-3">
                    @csrf
                    <input type="text" name="code"
                        class="form-control border-0 border-bottom rounded @error('coupon') is-invalid @enderror"
                        placeholder="Code promo" style="max-width:220px;" value="{{ old('code') }}">
                    <button class="btn border-secondary rounded-pill px-4 py-2 text-primary" type="submit">
                        Appliquer
                    </button>
                    @error('coupon')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </form>
                @endif
            </div>

            {{-- Récapitulatif totaux --}}
            <div class="row g-4 justify-content-end">
                <div class="col-sm-8 col-md-7 col-lg-5 col-xl-4">
                    <div class="bg-light rounded">
                        <div class="p-4">
                            <h4 class="mb-4">Récapitulatif</h4>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Sous-total</span>
                                <strong>{{ number_format($totals['subtotal'], 0, ',', ' ') }} FCFA</strong>
                            </div>

                            @if($totals['discount'] > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Réduction</span>
                                <strong>-{{ number_format($totals['discount'], 0, ',', ' ') }} FCFA</strong>
                            </div>
                            @endif

                            <div class="d-flex justify-content-between mb-2">
                                <span>Livraison</span>
                                <strong>
                                    {{ $totals['shippingCost'] == 0 ? 'Gratuit' : number_format($totals['shippingCost'], 0, ',', ' ').' FCFA' }}
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-muted small">
                                <span>TVA (20%)</span>
                                <span>{{ number_format($totals['taxAmount'], 0, ',', ' ') }} FCFA</span>
                            </div>
                        </div>
                        <div class="py-4 border-top border-bottom d-flex justify-content-between px-4">
                            <h5 class="mb-0 fw-bold">Total TTC</h5>
                            <h5 class="mb-0 fw-bold text-primary">{{ number_format($totals['total'], 0, ',', ' ') }} FCFA</h5>
                        </div>
                        <div class="p-4">
                            <a href="{{ route('checkout.show') }}" class="btn btn-primary rounded-pill w-100 py-2 text-uppercase fw-bold">
                                Procéder au paiement
                            </a>
                            <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary rounded-pill w-100 py-2 mt-2">
                                Continuer les achats
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @endif
        </div>
    </div>
    <!-- Cart Page End -->

    <script>
        // Quantité +/-  → submit form automatiquement
        document.querySelectorAll('.btn-plus, .btn-minus').forEach(btn => {
            btn.addEventListener('click', function () {
                const form  = this.closest('form');
                const input = form.querySelector('.qty-input');
                let val     = parseInt(input.value) || 1;
                if (this.classList.contains('btn-plus'))  val = Math.min(val + 1, 99);
                if (this.classList.contains('btn-minus')) val = Math.max(val - 1, 1);
                input.value = val;
                form.submit();
            });
        });
    </script>
@endsection
