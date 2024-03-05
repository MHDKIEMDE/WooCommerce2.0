@extends('dashboard.admin.layout.app')

@section('title', 'Détails de la catégorie')
@section('Dashboard - Agribusiness Shop', "Détails d'un catégorie")

@section('contents')
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Détails de la catégorie</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name">Nom de la catégorie :</label>
                                        <p>{{ $categorie->name }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">Image :</label>
                                        <img src="{{ asset($categorie->image_path) }}" alt="Image de la catégorie" style="max-width: 200px;">
                                    </div>
                                    <div class="form-group">
                                        <a href="{{ route('categories.edit', $categorie->id) }}" class="btn btn-primary">Modifier</a>
                                        <form action="{{ route('categories.destroy', $categorie->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection
