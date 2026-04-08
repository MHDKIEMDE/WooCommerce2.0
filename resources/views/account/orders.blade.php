@extends('layouts.app')
@section('seo_title', 'Mes commandes')
@section('noindex')
@section('content')

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Mes commandes</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.profile') }}" class="text-white">Mon compte</a></li>
            <li class="breadcrumb-item active text-white">Commandes</li>
        </ol>
    </div>

    <div class="container py-5">
        <div class="row g-4">

            {{-- Sidebar --}}
            <div class="col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-4">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                                class="rounded-circle mb-3" style="width:90px;height:90px;object-fit:cover;">
                        @else
                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3"
                                style="width:90px;height:90px;">
                                <span class="text-white fw-bold fs-2">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <h5 class="mb-0 fw-bold">{{ auth()->user()->name }}</h5>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('account.profile') }}"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2"></i> Mon profil
                        </a>
                        <a href="{{ route('account.orders') }}"
                            class="list-group-item list-group-item-action active">
                            <i class="fas fa-box me-2"></i> Mes commandes
                        </a>
                        <a href="{{ route('account.addresses') }}"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-map-marker-alt me-2"></i> Mes adresses
                        </a>
                        <a href="{{ route('account.edit') }}"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-edit me-2"></i> Modifier le profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="list-group-item list-group-item-action text-danger border-0 w-100 text-start">
                                <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Liste des commandes --}}
            <div class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Historique des commandes</div>
                    <div class="card-body p-0">

                        @if($orders->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                <p>Vous n'avez encore passé aucune commande.</p>
                                <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-4">
                                    Découvrir la boutique
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>N° commande</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th class="text-end">Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $order)
                                        <tr>
                                            <td class="fw-semibold">{{ $order->order_number }}</td>
                                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                @php
                                                    $badges = [
                                                        'pending'    => 'warning',
                                                        'processing' => 'info',
                                                        'shipped'    => 'primary',
                                                        'delivered'  => 'success',
                                                        'cancelled'  => 'danger',
                                                    ];
                                                    $labels = [
                                                        'pending'    => 'En attente',
                                                        'processing' => 'En cours',
                                                        'shipped'    => 'Expédiée',
                                                        'delivered'  => 'Livrée',
                                                        'cancelled'  => 'Annulée',
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $badges[$order->status] ?? 'secondary' }}">
                                                    {{ $labels[$order->status] ?? $order->status }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-semibold">
                                                {{ number_format($order->total, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('checkout.confirmation', $order->order_number) }}"
                                                    class="btn btn-sm btn-outline-primary rounded-pill">
                                                    Détails
                                                </a>
                                                <a href="{{ route('account.orders.invoice', $order->id) }}"
                                                    class="btn btn-sm btn-outline-secondary rounded-pill ms-1"
                                                    title="Télécharger la facture">
                                                    <i class="bi bi-file-earmark-pdf"></i> Facture
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($orders->hasPages())
                            <div class="p-3">
                                {{ $orders->links() }}
                            </div>
                            @endif
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
