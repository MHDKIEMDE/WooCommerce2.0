@extends('dashboard.admin.layout.app')
@section('title', 'Niches / Templates marketplace')

@section('contents')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
        <h1>Niches / Templates</h1>
        <a href="{{ route('admin.templates.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> Nouveau template
        </a>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Templates</li>
    </ol>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        @forelse($templates as $tpl)
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-2">
                        @if($tpl->icon)
                        <span class="fs-4">{{ $tpl->icon }}</span>
                        @else
                        <i class="fas fa-store text-primary"></i>
                        @endif
                        <div>
                            <div class="fw-bold">{{ $tpl->name }}</div>
                            <code class="small text-muted">{{ $tpl->slug }}</code>
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.templates.edit', $tpl) }}"
                           class="btn btn-sm btn-outline-primary" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.templates.destroy', $tpl) }}"
                              onsubmit="return confirm('Supprimer ce template ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body pt-2">
                    <div class="d-flex gap-3 mb-3">
                        <span class="badge bg-primary-subtle text-primary rounded-pill">
                            <i class="fas fa-palette me-1"></i>{{ $tpl->palettes_count }} palettes
                        </span>
                        <span class="badge bg-success-subtle text-success rounded-pill">
                            <i class="fas fa-store me-1"></i>{{ $tpl->shops_count }} boutiques
                        </span>
                    </div>
                    {{-- Aperçu des palettes --}}
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($tpl->palettes as $pal)
                        <div title="{{ $pal->name }} — {{ $pal->ambiance }}"
                             style="width:28px;height:28px;border-radius:50%;background:{{ $pal->color_primary }};border:2px solid #dee2e6;cursor:default;"></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5 text-muted">
                <i class="fas fa-store fa-3x mb-3"></i>
                <p>Aucun template. <a href="{{ route('admin.templates.create') }}">Créer le premier</a></p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
