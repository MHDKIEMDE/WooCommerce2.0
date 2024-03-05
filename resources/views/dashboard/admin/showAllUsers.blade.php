@extends('dashboard.admin.layout.app')

@section('Dashboard - Agribusiness Shop', "")

@section('contents')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Listes de tous les utilisateur</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.adminHome') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">User liste</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Prenom</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Quartier</th>
                            <th>Ville</th>
                            <th>Details</th>
                            <th>Supprimer</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone_number }}</td>
                                <td>{{ $user->quarter }}</td>
                                <td>{{ $user->address }}</td>
                                <td>
                                    <!-- Bouton pour voir le profil de l'utilisateur -->
                                    <form action="{{ route('admin.viewUsersProfile', ['id' => $user->id]) }}" method="get">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">Voir</button>
                                    </form>
                                </td>
                               <td> <form action="{{ route('admin.deleteUser', ['id' => $user->id]) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form></td>
                            </tr>
                           
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection






