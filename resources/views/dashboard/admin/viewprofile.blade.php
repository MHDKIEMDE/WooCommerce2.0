@extends('dashboard.admin.layout.app')

@section('Dashboard - Agribusiness Shop', "Profiles de utilisateur selectionner")

@section('contents')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Profile de l'utilisateur selectionner</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.adminHome') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">User Profile</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                <h2>Nom : {{ $user->name }} {{ $user->last_name }}</h2>
                <p>Téléphone : {{ $user->phone_number }}</p>
                <p>Quartier : {{ $user->quarter }}</p>
                <p>Adresse : {{ $user->address }}</p>
                <p>Email : {{ $user->email }}</p>
            </div>
            {{-- <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Photo de profil de l'utilisateur" class="img-fluid rounded-circle" style="max-width: 150px; max-height: 150px;"> --}}
        </div>

    </div>
@endsection
