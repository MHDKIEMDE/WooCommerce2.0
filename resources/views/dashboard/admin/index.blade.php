@extends('dashboard.admin.layout.app')
@section('title', 'Dashboard')
@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tableau de bord</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Vue d'ensemble</li>
    </ol>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Commandes</div>
                    <div class="fs-4 fw-bold">{{ $kpis['orders_total'] }}</div>
                    <div class="text-warning small">{{ $kpis['orders_pending'] }} en attente</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Revenus</div>
                    <div class="fs-5 fw-bold text-success">{{ fmt_price($kpis['revenue_total']) }}</div>
                    <div class="text-muted small">paiements confirmés</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Utilisateurs</div>
                    <div class="fs-4 fw-bold">{{ $kpis['users_total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-start border-warning border-4 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Produits actifs</div>
                    <div class="fs-4 fw-bold">{{ $kpis['products_total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-start border-danger border-4 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Stock faible</div>
                    <div class="fs-4 fw-bold text-danger">{{ $kpis['low_stock'] }}</div>
                    <div class="text-muted small">produits</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-start border-secondary border-4 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Aujourd'hui</div>
                    <div class="fs-4 fw-bold">{{ $ordersCounts[29] ?? 0 }}</div>
                    <div class="text-muted small">commandes</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="row g-4 mb-4">

        {{-- Commandes + Revenus 30j --}}
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span class="fw-semibold"><i class="fas fa-chart-line text-primary me-2"></i>Commandes & Revenus (30 derniers jours)</span>
                </div>
                <div class="card-body">
                    <canvas id="ordersChart" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- Inscriptions --}}
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-semibold">
                    <i class="fas fa-user-plus text-info me-2"></i>Nouvelles inscriptions (30j)
                </div>
                <div class="card-body">
                    <canvas id="usersChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">

        {{-- Top produits --}}
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-semibold">
                    <i class="fas fa-trophy text-warning me-2"></i>Top 5 produits vendus
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Produit</th>
                                <th class="text-end">Qté</th>
                                <th class="text-end">Revenu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $i => $p)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td class="fw-semibold small">{{ $p->product_name }}</td>
                                <td class="text-end">{{ $p->sold }}</td>
                                <td class="text-end text-success small">{{ fmt_price($p->revenue) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Aucune vente.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Dernières commandes --}}
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span class="fw-semibold"><i class="fas fa-shopping-bag text-primary me-2"></i>Dernières commandes</span>
                    <a href="{{ route('admin.home-settings.edit') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>N°</th>
                                <th>Client</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td class="font-monospace">{{ $order->order_number }}</td>
                                <td>{{ $order->user?->name ?? (is_array($order->billing_address) ? ($order->billing_address['first_name'] ?? '—') : '—') }}</td>
                                <td class="fw-semibold">{{ fmt_price($order->total) }}</td>
                                <td>
                                    @php $colors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger']; @endphp
                                    <span class="badge bg-{{ $colors[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td class="text-muted">{{ $order->created_at->format('d/m H:i') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Aucune commande.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels  = @json($labels);
const orders  = @json($ordersCounts);
const revenue = @json($revenueData);
const users   = @json($usersData);

// Commandes + Revenus
new Chart(document.getElementById('ordersChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Commandes',
                data: orders,
                backgroundColor: 'rgba(129,196,8,.7)',
                borderColor: '#81C408',
                borderWidth: 1,
                yAxisID: 'y',
            },
            {
                label: 'Revenus (FCFA)',
                data: revenue,
                type: 'line',
                borderColor: '#FFB524',
                backgroundColor: 'rgba(255,181,36,.15)',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        scales: {
            y:  { beginAtZero: true, grid: { display: false }, title: { display: true, text: 'Commandes' } },
            y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Revenus' } }
        },
        plugins: { legend: { position: 'top' } }
    }
});

// Inscriptions
new Chart(document.getElementById('usersChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Inscriptions',
            data: users,
            borderColor: '#0dcaf0',
            backgroundColor: 'rgba(13,202,240,.15)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        },
        plugins: { legend: { display: false } }
    }
});
</script>
@endsection
