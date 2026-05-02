@extends('dashboard.admin.layout.app')
@section('title', 'Zones de livraison')

@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Zones de livraison</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Zones de livraison</li>
    </ol>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- ── Ajouter une zone ── --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">
                    <i class="fas fa-plus-circle me-2 text-primary"></i>Ajouter une zone
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.delivery-zones.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Quartier / Zone <sup class="text-danger">*</sup></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Ex : Cocody, Yopougon, Plateau…"
                                value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix de livraison (FCFA) <sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                                    placeholder="1500" min="0" step="100"
                                    value="{{ old('price') }}" required>
                                <span class="input-group-text">FCFA</span>
                            </div>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ordre d'affichage</label>
                            <input type="number" name="sort_order" class="form-control"
                                placeholder="0" min="0" value="{{ old('sort_order', 0) }}">
                            <div class="form-text">Les valeurs basses s'affichent en premier.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-1"></i> Ajouter
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Liste des zones ── --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-truck me-2 text-primary"></i>Zones configurées ({{ $zones->count() }})</span>
                    @if($zones->isEmpty())
                    <span class="badge bg-warning text-dark">Aucune zone — livraison non calculable</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($zones->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-map-marker-alt fa-3x mb-3 d-block"></i>
                        Aucune zone configurée.<br>Ajoutez des quartiers pour activer le calcul de livraison.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Zone / Quartier</th>
                                    <th class="text-end">Prix livraison</th>
                                    <th class="text-center">Ordre</th>
                                    <th class="text-center">Actif</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($zones as $zone)
                                {{-- Ligne lecture --}}
                                <tr id="view-{{ $zone->id }}">
                                    <td><span class="fw-semibold">{{ $zone->name }}</span></td>
                                    <td class="text-end">
                                        <span class="badge bg-primary fs-6">{{ number_format($zone->price, 0, ',', ' ') }} FCFA</span>
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">{{ $zone->sort_order }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if($zone->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                            onclick="editZone({{ $zone->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.delivery-zones.destroy', $zone) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Supprimer « {{ $zone->name }} » ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Ligne édition (cachée par défaut) --}}
                                <tr id="edit-{{ $zone->id }}" style="display:none; background:#f8f9fa;">
                                    <td colspan="5" class="p-3">
                                        <form method="POST" action="{{ route('admin.delivery-zones.update', $zone) }}">
                                            @csrf @method('PUT')
                                            <div class="row g-2 align-items-end">
                                                <div class="col-sm-4">
                                                    <label class="form-label small mb-1">Zone / Quartier</label>
                                                    <input type="text" name="name" class="form-control form-control-sm"
                                                        value="{{ $zone->name }}" required>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label class="form-label small mb-1">Prix (FCFA)</label>
                                                    <input type="number" name="price" class="form-control form-control-sm"
                                                        value="{{ $zone->price }}" min="0" step="100" required>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label class="form-label small mb-1">Ordre</label>
                                                    <input type="number" name="sort_order" class="form-control form-control-sm"
                                                        value="{{ $zone->sort_order }}" min="0">
                                                </div>
                                                <div class="col-auto text-center">
                                                    <label class="form-label small mb-1 d-block">Actif</label>
                                                    <input class="form-check-input mt-1" type="checkbox" name="is_active"
                                                        value="1" {{ $zone->is_active ? 'checked' : '' }}>
                                                </div>
                                                <div class="col d-flex gap-1 justify-content-end">
                                                    <button type="submit" class="btn btn-sm btn-success px-3">
                                                        <i class="fas fa-check me-1"></i> Enregistrer
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        onclick="cancelEdit({{ $zone->id }})">
                                                        Annuler
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Info résumé --}}
            @if($zones->isNotEmpty())
            <div class="card mt-3 border-0 bg-light">
                <div class="card-body py-2 px-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1 text-primary"></i>
                        Ces zones apparaissent dans le formulaire de commande.
                        Le prix de livraison est ajouté automatiquement au total selon la zone choisie par le client.
                    </small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function editZone(id) {
    document.getElementById('view-' + id).style.display = 'none';
    document.getElementById('edit-' + id).style.display = '';
}
function cancelEdit(id) {
    document.getElementById('edit-' + id).style.display = 'none';
    document.getElementById('view-' + id).style.display = '';
}
</script>
@endsection
