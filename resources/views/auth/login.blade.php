@extends('layouts.app')
@section('seo_title', 'Connexion')
@section('noindex')
@section('content')
    <!-- Page Header -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Connexion</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item active text-white">Connexion</li>
        </ol>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow rounded-3 p-4">
                    <h4 class="fw-bold mb-4 text-center">Se connecter</h4>

                    @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">Se souvenir de moi</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="text-primary small">Mot de passe oublié ?</a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">Se connecter</button>
                    </form>

                    <p class="text-center mt-4 mb-0">
                        Pas encore de compte ?
                        <a href="{{ route('register') }}" class="text-primary fw-bold">S'inscrire</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
