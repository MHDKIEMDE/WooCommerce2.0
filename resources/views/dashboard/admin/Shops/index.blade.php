@extends('dashboard.admin.layout.app')
@section('title', 'Boutiques Marketplace')

@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Boutiques Marketplace</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Boutiques</li>
    </ol>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filtres statut --}}
    <div class="mb-3 d-flex gap-2 flex-wrap">
        @foreach(['all' => 'Toutes', 'pending' => 'En attente', 'active' => 'Actives', 'suspended' => 'Suspendues'] as $val => $label)
        <a href="{{ request()->fullUrlWithQuery(['status' => $val]) }}"
           class="btn btn-sm {{ request('status', 'all') === $val ? 'btn-dark' : 'btn-outline-secondary' }}">
            {{ $label }}
            @if($val === 'pending')
                <span class="badge bg-warning text-dark ms-1">{{ \App\Models\Shop::where('status','pending')->count() }}</span>
            @endif
        </a>
        @endforeach
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Boutique</th>
                            <th>Vendeur</th>
                            <th>Niche</th>
                            <th class="text-center">Produits</th>
                            <th class="text-center">Stripe</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Créée</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shops as $shop)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $shop->name }}</div>
                                <small class="text-muted font-monospace">{{ $shop->slug }}</small>
                            </td>
                            <td>
                                <div>{{ $shop->owner?->name ?? '—' }}</div>
                                <small class="text-muted">{{ $shop->owner?->email }}</small>
                            </td>
                            <td>
                                @if($shop->template)
                                    <span class="badge bg-info text-dark">{{ $shop->template->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $shop->products_count }}</td>
                            <td class="text-center">
                                @if($shop->stripe_account_id)
                                    <span class="text-success"><i class="fab fa-stripe-s fa-lg"></i></span>
                                @else
                                    <span class="text-muted small">Non connecté</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php $colors = ['active'=>'success','pending'=>'warning','suspended'=>'danger']; @endphp
                                <span class="badge bg-{{ $colors[$shop->status] ?? 'secondary' }}">{{ ucfirst($shop->status) }}</span>
                            </td>
                            <td class="text-center text-muted small">{{ $shop->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('admin.shops.products', $shop) }}" class="btn btn-sm btn-outline-primary" title="Voir les produits"><i class="fas fa-box"></i>@if($shop->products_count > 0)<span class="badge bg-primary ms-1">{{ $shop->products_count }}</span>@endif</a>
                                    @if($shop->status === 'pending')
                                    <form method="POST" action="{{ route('admin.shops.approve', $shop) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success" title="Approuver">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($shop->status !== 'suspended')
                                    <button class="btn btn-sm btn-outline-danger" title="Suspendre"
                                        onclick="suspendShop({{ $shop->id }}, '{{ addslashes($shop->name) }}')">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    @endif
                                    @if($shop->status === 'suspended')
                                    <form method="POST" action="{{ route('admin.shops.approve', $shop) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-success" title="Réactiver">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Aucune boutique.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($shops->hasPages())
        <div class="card-footer">
            {{ $shops->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal suspension --}}
<div class="modal fade" id="suspendModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="suspendForm">
            @csrf @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="fas fa-ban me-2"></i>Suspendre la boutique</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Boutique : <strong id="suspendShopName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Raison (optionnel)</label>
                        <textarea name="reason" class="form-control" rows="3"
                            placeholder="Non-conformité, contenu interdit…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-ban me-1"></i>Suspendre</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function suspendShop(id, name) {
    document.getElementById('suspendShopName').textContent = name;
    document.getElementById('suspendForm').action = '/dashboard/shops/' + id + '/suspend';
    new bootstrap.Modal(document.getElementById('suspendModal')).show();
}
</script>
@endsection
