@extends('dashboard.admin.layout.app')

@section('Dashboard - Agribusiness Shop', "Liste des utilisateur")

@section('contents')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Listes de tous les utilisateur</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.adminHome') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">User liste</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                <div class="container mt-5">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="{{ asset('storage/' . $maison->image) }}" class="card-img-top hover-zoom" alt="Image de la maison">
                        </div>
                        <div class="col-md-6">
                            <h2>{{ $maison->nom }}</h2>
                            <p class="card-text"><strong>Type de maison :</strong> {{ $maison->type_maison }}</p>
                            <p class="d-flex"><strong>Loyer : {{ number_format($maison->loyer, 0, ',', ' ') }}</strong> F par mois</p>
                            <p class="card-text"><strong>Quartier :</strong> {{ $maison->quartier }}</p>
                            <ul>
                                @foreach ($maison->options as $option)
                                    @if ($option === 'douche')
                                        <li><i class="fas fa-shower me-2"></i>Douche</li>
                                    @elseif ($option === 'garage')
                                        <li><i class="fas fa-car  me-2"></i>Garage</li>
                                    @elseif ($option === 'cuisine')
                                        <li><i class="fas fa-utensils  me-2"></i>Cuisine</li>
                                    @endif
                                @endforeach
                            </ul>
                            <p class="card-text"><strong>Cautions :</strong> {{ $maison->cautions }}</p>
                             <a href="www.whatssap.com" class="btn btn-primary b">(+226) 07443112</a>
                            <a href="http://wa.me/+22605279870" class="btn btn-primary b">Contact  whatasapp</a>
                        </div>
                    </div>
                    <!-- Carrousel pour montrer les aspects de la maison -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h3>Aspects de la Maison</h3>
                            <!-- ... Carrousel d'aspects ... -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
