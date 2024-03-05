@extends('layouts.app')

@section('Agribusiness Shop', 'Inscription')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card" style="margin-top: 25%">
                <div class="card-header">{{ __('Inscription') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Nom') }}</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Adresse Email') }}</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Mot de passe') }}</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation"
                                class="form-label">{{ __('Confirmer le mot de passe') }}</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" required>
                        </div>
                        <div class="mb-3">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckIndeterminate">
                            <label class="form-check-label" for="flexCheckIndeterminate">
                                LEs condition de confidentialiter
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('S\'inscrire') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
