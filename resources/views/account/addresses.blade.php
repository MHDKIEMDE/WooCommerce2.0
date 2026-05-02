@extends('layouts.app')
@section('seo_title', 'Mes adresses')
@section('noindex')
@section('content')

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Mes adresses</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('account.profile') }}" class="text-white">Mon compte</a></li>
            <li class="breadcrumb-item active text-white">Adresses</li>
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
                        <a href="{{ route('account.profile') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2"></i> Mon profil
                        </a>
                        <a href="{{ route('account.orders') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-box me-2"></i> Mes commandes
                        </a>
                        <a href="{{ route('account.addresses') }}" class="list-group-item list-group-item-action active">
                            <i class="fas fa-map-marker-alt me-2"></i> Mes adresses
                        </a>
                        <a href="{{ route('account.wishlist') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-heart me-2"></i> Ma wishlist
                        </a>
                        <a href="{{ route('account.edit') }}" class="list-group-item list-group-item-action">
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

            {{-- Contenu --}}
            <div class="col-lg-9">

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                {{-- Liste des adresses --}}
                <div class="row g-3 mb-4">
                    @forelse($addresses as $address)
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100 {{ $address->is_default ? 'border-primary' : '' }}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">
                                    {{ $address->label ?: ($address->type === 'billing' ? 'Facturation' : 'Livraison') }}
                                </span>
                                @if($address->is_default)
                                <span class="badge bg-primary">Principale</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <p class="mb-0">
                                    <strong>{{ $address->first_name }} {{ $address->last_name }}</strong><br>
                                    {{ $address->street }}<br>
                                    {{ $address->city }}@if($address->zip), {{ $address->zip }}@endif<br>
                                    {{ $address->country }}<br>
                                    @if($address->phone)
                                    <i class="fas fa-phone me-1 text-muted"></i> {{ $address->phone }}
                                    @endif
                                </p>
                            </div>
                            <div class="card-footer d-flex gap-2 flex-wrap">
                                @if(!$address->is_default)
                                <form method="POST" action="{{ route('account.addresses.default', $address->id) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-primary rounded-pill">
                                        Définir par défaut
                                    </button>
                                </form>
                                @endif
                                <button class="btn btn-sm btn-outline-secondary rounded-pill"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $address->id }}">
                                    <i class="fas fa-edit me-1"></i> Modifier
                                </button>
                                <form method="POST" action="{{ route('account.addresses.destroy', $address->id) }}"
                                    onsubmit="return confirm('Supprimer cette adresse ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill">
                                        <i class="fas fa-trash me-1"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Modal édition --}}
                    <div class="modal fade" id="editModal{{ $address->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('account.addresses.update', $address->id) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Modifier l'adresse</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        @include('account._address-form', ['a' => $address])
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-4 text-muted">
                        <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                        <p>Aucune adresse enregistrée.</p>
                    </div>
                    @endforelse
                </div>

                {{-- Ajouter une adresse --}}
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">
                        <i class="fas fa-plus me-2"></i> Ajouter une adresse
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('account.addresses.store') }}">
                            @csrf
                            @include('account._address-form', ['a' => null])
                            <button type="submit" class="btn btn-primary rounded-pill px-5 mt-3">
                                Ajouter
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
