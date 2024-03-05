@extends('layouts.app')
@section('Agribusiness Shop', 'Modifier mon Profiles')
@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Modifier mon Profiles</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Accueil</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Modifier mon Profiles</li>
        </ol>
    </div>

    <div class="container-fluid fruite py-5">
        <!-- edit.blade.php -->
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $user->name) }}" required autofocus>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="last_name" name="last_name"
                            value="{{ old('last_name', $user->last_name) }}">
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Image de Profil</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image">
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="address" class="form-label">Ville</label>
                        <input type="text" class="form-control" id="address" name="address"
                            value="{{ old('address', $user->address) }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="quarter" class="form-label">Quartier</label>
                        <input type="text" class="form-control" id="quarter" name="quarter"
                            value="{{ old('quarter', $user->quarter) }}">
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Numéro de Téléphone</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number"
                            value="{{ old('phone_number', $user->phone_number) }}">
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="secondary_phone_number" class="form-label">Deuxième Numéro de Téléphone
                            (optionnel)</label>
                        <input type="text" class="form-control" id="secondary_phone_number" name="secondary_phone_number"
                            value="{{ old('secondary_phone_number', $user->secondary_phone_number) }}">
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Annuler</button>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </div>
        </form>
    </div>
@endsection
