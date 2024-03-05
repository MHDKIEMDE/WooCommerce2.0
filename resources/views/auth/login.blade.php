@extends('layouts.app')
@section('Agribusiness Shop', 'Connexion')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card" style="margin-top: 25%">
                <div class="card-header">Connexion</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mot de passe:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3 justify-content-around">
                            <button type="submit" class="btn btn-primary">Se connecter</button>
                            <a href="{{ route('register') }}"><span>S’inscrire </span></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
