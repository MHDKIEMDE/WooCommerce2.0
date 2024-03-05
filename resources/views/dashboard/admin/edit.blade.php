@extends('dashboard.admin.layout.app')

@section('Dashboard - Agribusiness Shop', "")

@section('contents')

    <div class="row justify-content-center m-3">
        <div class="col-md-6">
            <div class="row justify-content-center ">
                <div class="col-md-4 ">
                    <a href="{{ route('admin.storeHouse') }}" class="btn btn-info">Mes maisons</a>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.updateHouse', ['id' => $maison->id]) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT') <!-- Utilisé pour indiquer que c'est une mise à jour -->
                <div class="row">

                    <!-- Champ Ville -->
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label for="ville" class="form-label">Ville:</label>
                            <input type="text" class="form-control" id="ville" name="ville"
                                value="{{ $maison->ville }}" required>
                        </div>
                    </div>

                    <!-- Champ Quartier -->
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label for="quartier" class="form-label">Quartier:</label>
                            <input type="text" class="form-control" id="quartier" name="quartier"
                                value="{{ $maison->quartier }}" required>
                        </div>
                    </div>

                    <!-- Champ Type de maison (Sélection) -->

                    <div class="col-md-5">
                        <div class="mb-3">
                            <label for="typeMaison" class="form-label">Type de maison:</label>
                            <select class="form-select" id="typeMaison" name="typeMaison" required>
                                <option value="entrer_coucher"
                                    {{ $maison->typeMaison == 'entrer_coucher' ? 'selected' : '' }}>
                                    Entrer_Coucher</option>
                                <option value="2_pieces" {{ $maison->typeMaison == '2_pieces' ? 'selected' : '' }}>Chambre
                                    salon
                                </option>
                                <option value="3_pieces" {{ $maison->typeMaison == '3_pieces' ? 'selected' : '' }}>(2)
                                    Chambre
                                    salon</option>
                                <option value="3_pieces" {{ $maison->typeMaison == '3_pieces' ? 'selected' : '' }}>(3)
                                    Chambre
                                    salon</option>
                                <option value="2_pieces" {{ $maison->typeMaison == '2_pieces' ? 'selected' : '' }}>(4)
                                    Chambre
                                    salon</option>
                                <option value="3_pieces" {{ $maison->typeMaison == '3_pieces' ? 'selected' : '' }}>(5)
                                    Chambre
                                    salon</option>
                                <option value="3_pieces" {{ $maison->typeMaison == '3_pieces' ? 'selected' : '' }}>(6)
                                    Chambre
                                    salon</option>
                            </select>
                        </div>
                    </div>
                    <!-- Champ loyer -->
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label for="loyer" class="form-label">loyer (mois):</label>
                            <input type="text" class="form-control" id="loyer" name="loyer"
                                value="{{ $maison->loyer }}" required>
                        </div>
                    </div>
                  
                    <!-- Champs dynamiques pour les options (Douche, Garage, Cuisine) -->
                    <div class="col-md-5">
                        <div class="mb-3 d-flex justify-content-between">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="douche" name="options[]"
                                    value="douche" {{ in_array('douche', $maison->options) ? 'checked' : '' }}>
                                <label class="form-check-label" for="douche">Douche</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="garage" name="options[]"
                                    value="garage" {{ in_array('garage', $maison->options) ? 'checked' : '' }}>
                                <label class="form-check-label" for="garage">Garage</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="cuisine" name="options[]"
                                    value="cuisine" {{ in_array('cuisine', $maison->options) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cuisine">Cuisine</label>
                            </div>
                        </div>
                    </div>
                    <!-- Champ Image -->
                    <div class="col-md-5">
                        <div class="mb-3 mt-5">
                            <label for="image" class="form-label">Image:</label>
                            <input type="file" class="form-control" id="image" name="image">
                            @if ($maison->image)
                                <img src="{{ asset('storage/' . $maison->image) }}" alt="Image de la maison" class="mt-2"
                                    style="max-width: 200px;">
                            @endif
                        </div>
                    </div>
                    <div class="d-flex justify-content-around">
                        <div class="col-md-5">
                            <button type="submit" class="btn btn-primary">Valider</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
