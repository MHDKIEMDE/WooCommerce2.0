@extends('dashboard.admin.layout.app')
@section('title', 'Promotions')
@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Bannières Promotionnelles</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Promotions</li>
    </ol>

    <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Ajouter une promotion
    </a>

    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th style="width:80px">Image</th>
                        <th>Titre</th>
                        <th>Sous-titre</th>
                        <th>Ordre</th>
                        <th>Actif</th>
                        <th style="width:140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotions as $p)
                    <tr>
                        <td>
                            <img src="{{ Storage::url($p->image_path) }}" alt="{{ $p->title }}"
                                 style="width:70px;height:45px;object-fit:cover;border-radius:4px;">
                        </td>
                        <td>{{ $p->title }}</td>
                        <td>{{ $p->subtitle ?? '—' }}</td>
                        <td>{{ $p->sort_order }}</td>
                        <td><span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $p->is_active ? 'Oui' : 'Non' }}</span></td>
                        <td>
                            <a href="{{ route('admin.promotions.edit', $p) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.promotions.destroy', $p) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Supprimer ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">Aucune promotion.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
