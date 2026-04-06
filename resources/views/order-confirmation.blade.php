@extends('layouts.app')
@section('seo_title', 'Commande confirmée')
@section('noindex')
@section('content')

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Commande confirmée</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item active text-white">Confirmation</li>
        </ol>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Bannière succès -->
                <div class="text-center mb-5">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                        style="width:80px;height:80px;">
                        <i class="fas fa-check fa-2x text-white"></i>
                    </div>
                    <h2 class="fw-bold">Merci pour votre commande !</h2>
                    <p class="text-muted fs-5">
                        Votre commande <strong>{{ $order->order_number }}</strong> a bien été enregistrée.
                    </p>
                    @if ($order->payment_method === 'cash_on_delivery')
                        <p class="text-muted">
                            Vous paierez à la livraison. Nous vous contacterons pour confirmer la date.
                        </p>
                    @else
                        <p class="text-muted">
                            Veuillez effectuer le virement bancaire pour que votre commande soit traitée.
                        </p>
                    @endif
                </div>

                <!-- Détails commande -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Détails de la commande</span>
                        <span class="badge bg-warning text-dark">En attente</span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th class="text-center">Qté</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if (!empty($item->product_snapshot['image']))
                                                    <img src="{{ $item->product_snapshot['image'] }}"
                                                        alt="{{ $item->product_name }}"
                                                        style="width:48px;height:48px;object-fit:cover;" class="rounded">
                                                @endif
                                                <span>{{ $item->product_name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light border-top">
                                <tr>
                                    <td colspan="2" class="text-muted">Sous-total</td>
                                    <td class="text-end">{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                @if ($order->discount_amount > 0)
                                <tr class="text-success">
                                    <td colspan="2">Réduction</td>
                                    <td class="text-end">− {{ number_format($order->discount_amount, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="2" class="text-muted">Livraison</td>
                                    <td class="text-end">
                                        @if ($order->shipping_cost == 0)
                                            <span class="text-success">Gratuite</span>
                                        @else
                                            {{ number_format($order->shipping_cost, 0, ',', ' ') }} FCFA
                                        @endif
                                    </td>
                                </tr>
                                <tr class="fw-bold">
                                    <td colspan="2">Total</td>
                                    <td class="text-end text-primary fs-5">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Adresse de livraison -->
                <div class="card shadow-sm mb-5">
                    <div class="card-header fw-semibold">Adresse de livraison</div>
                    <div class="card-body">
                        @php $addr = $order->shipping_address; @endphp
                        <p class="mb-0">
                            <strong>{{ $addr['first_name'] }} {{ $addr['last_name'] }}</strong><br>
                            {{ $addr['address'] }}<br>
                            {{ $addr['city'] }}@if(!empty($addr['postal_code'])), {{ $addr['postal_code'] }}@endif<br>
                            {{ $addr['country'] }}<br>
                            <i class="fas fa-phone me-1 text-muted"></i> {{ $addr['phone'] }}<br>
                            <i class="fas fa-envelope me-1 text-muted"></i> {{ $addr['email'] }}
                        </p>
                    </div>
                </div>

                <!-- Bouton WhatsApp client -->
                @php
                    $shopPhone  = preg_replace('/[^0-9]/', '', config('services.whatsapp.phone', ''));
                    $addr       = $order->billing_address;
                    $clientName = ($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '');

                    $itemsText = $order->items->map(function ($i) {
                        return "• {$i->product_name} x{$i->quantity}";
                    })->implode(', ');

                    $waText = urlencode(
                        "Bonjour ! Je viens de passer la commande *{$order->order_number}*.\n"
                        . "Nom : {$clientName}\n"
                        . "Articles : {$itemsText}\n"
                        . "Total : " . number_format($order->total, 0, ',', ' ') . " FCFA"
                    );
                @endphp

                @if ($shopPhone)
                <div class="card border-success mb-4">
                    <div class="card-body text-center py-4">
                        <p class="mb-2 fw-semibold">Confirmez votre commande directement sur WhatsApp</p>
                        <p class="text-muted small mb-3">
                            Un message pré-rempli sera envoyé à notre boutique pour accélérer le traitement.
                        </p>
                        <a href="https://wa.me/{{ $shopPhone }}?text={{ $waText }}"
                           target="_blank"
                           class="btn btn-lg px-5 fw-bold"
                           style="background:#25D366;color:#fff;border-radius:50px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor"
                                viewBox="0 0 16 16" class="me-2" style="vertical-align:-3px">
                                <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                            </svg>
                            Confirmer via WhatsApp
                        </a>
                    </div>
                </div>
                @endif

                <div class="text-center">
                    <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-5 me-2">
                        Continuer mes achats
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-5">
                        Retour à l'accueil
                    </a>
                </div>

            </div>
        </div>
    </div>

@endsection
