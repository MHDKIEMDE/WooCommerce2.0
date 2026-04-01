@extends('dashboard.admin.layout.app')

@section('title', 'Liste des catégories')

@section('contents')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
        <h1>Catégories</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> Nouvelle catégorie
        </a>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Catégories</li>
    </ol>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:70px">Image</th>
                            <th>Nom</th>
                            <th>Slug</th>
                            <th class="text-center">Produits actifs</th>
                            <th class="text-center">Ordre</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>
                                    @if ($category->image_url)
                                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}"
                                            style="width:50px;height:50px;object-fit:cover;" class="rounded">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                            style="width:50px;height:50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $category->name }}</td>
                                <td><code>{{ $category->slug }}</code></td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill">{{ $category->products_count }}</span>
                                </td>
                                <td class="text-center">{{ $category->sort_order ?? 0 }}</td>
                                <td class="text-center">
                                    @if ($category->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Supprimer la catégorie « {{ $category->name }} » ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Aucune catégorie trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
