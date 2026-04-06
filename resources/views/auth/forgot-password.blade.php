@extends('layouts.app')
@section('seo_title', 'Mot de passe oublié')
@section('noindex')
@section('content')
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Mot de passe oublié</h1>
    </div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow rounded-3 p-4">
                    <h4 class="fw-bold mb-3 text-center">Réinitialiser le mot de passe</h4>
                    <p class="text-muted text-center small mb-4">Entrez votre email pour recevoir un lien de réinitialisation.</p>

                    @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">Envoyer le lien</button>
                    </form>
                    <p class="text-center mt-3 mb-0">
                        <a href="{{ route('login') }}" class="text-primary small">Retour à la connexion</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
