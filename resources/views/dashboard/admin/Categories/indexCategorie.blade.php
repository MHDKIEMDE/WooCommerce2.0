@extends('dashboard.admin.layout.app')

@section('Dashboard - Agribusiness Shop', 'Liste des catégories')

@section('contents')
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Liste des catégories</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Nom</th>
                                                    <th>Image</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($categories as $categorie)
                                                    <tr>
                                                        <td>{{ $categorie->name }}</td>
                                                        <td><img src="{{ asset($categorie->image_path) }}"
                                                                alt="Image de la catégorie" style="max-width: 100px;"></td>
                                                        <td>
                                                            <a href="{{ route('categories.show', $categorie->id) }}"
                                                                class="btn btn-info">Voir</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
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
