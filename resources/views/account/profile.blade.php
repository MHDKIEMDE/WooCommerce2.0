@extends('layouts.app')
@section('seo_title', 'Mon compte')
@section('noindex')
@section('content')

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Mon compte</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item active text-white">Mon compte</li>
        </ol>
    </div>

    <div class="container py-5">
        <div class="row g-4">

            {{-- Sidebar --}}
            <div class="col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-4">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                class="rounded-circle mb-3" style="width:90px;height:90px;object-fit:cover;">
                        @else
                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3"
                                style="width:90px;height:90px;">
                                <span class="text-white fw-bold fs-2">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <h5 class="mb-0 fw-bold">{{ $user->name }}</h5>
                        <small class="text-muted">{{ $user->email }}</small>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('account.profile') }}"
                            class="list-group-item list-group-item-action active">
                            <i class="fas fa-user me-2"></i> Mon profil
                        </a>
                        <a href="{{ route('account.orders') }}"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-box me-2"></i> Mes commandes
                        </a>
                        <a href="{{ route('account.addresses') }}"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-map-marker-alt me-2"></i> Mes adresses
                        </a>
                        <a href="{{ route('account.wishlist') }}"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-heart me-2"></i> Ma wishlist
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

            {{-- Contenu principal --}}
            <div class="col-lg-9">

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                {{-- Infos personnelles --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Informations personnelles</span>
                        <a href="{{ route('account.edit') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="fas fa-edit me-1"></i> Modifier
                        </a>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted">Nom</dt>
                            <dd class="col-sm-8">{{ $user->name }}</dd>

                            <dt class="col-sm-4 text-muted">Email</dt>
                            <dd class="col-sm-8">{{ $user->email }}</dd>

                            <dt class="col-sm-4 text-muted">Téléphone</dt>
                            <dd class="col-sm-8">{{ $user->phone ?: '—' }}</dd>

                            <dt class="col-sm-4 text-muted">Membre depuis</dt>
                            <dd class="col-sm-8">{{ $user->created_at->translatedFormat('d F Y') }}</dd>
                        </dl>
                    </div>
                </div>

                {{-- Modifier mot de passe --}}
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Changer le mot de passe</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('account.password') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Mot de passe actuel</label>
                                <input type="password" name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror">
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nouveau mot de passe</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirmer le nouveau mot de passe</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                Mettre à jour
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
