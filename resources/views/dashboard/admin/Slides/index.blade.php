@extends('dashboard.admin.layout.app')

@section('Dashboard - Agribusiness Shop', 'Slides')

@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Carrousel — Slides</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Slides</li>
    </ol>

    <a href="{{ route('admin.slides.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Ajouter un slide
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
                        <th style="width:160px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($slides as $slide)
                    <tr>
                        <td>
                            <img src="{{ Storage::url($slide->image_path) }}"
                                 alt="{{ $slide->title }}"
                                 style="width:70px;height:45px;object-fit:cover;border-radius:4px;">
                        </td>
                        <td>{{ $slide->title ?? '—' }}</td>
                        <td>{{ $slide->subtitle ?? '—' }}</td>
                        <td>{{ $slide->sort_order }}</td>
                        <td>
                            @if($slide->is_active)
                                <span class="badge bg-success">Oui</span>
                            @else
                                <span class="badge bg-secondary">Non</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.slides.edit', $slide) }}"
                               class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.slides.destroy', $slide) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Supprimer ce slide ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Aucun slide pour l'instant.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
