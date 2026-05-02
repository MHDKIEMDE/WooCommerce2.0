@extends('dashboard.admin.layout.app')
@section('title', 'Modifier — ' . $template->name)

@section('contents')
<div class="container-fluid px-4">
    <div class="mt-4 mb-2">
        <h1>{{ $template->icon }} {{ $template->name }}</h1>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.templates.index') }}">Templates</a></li>
        <li class="breadcrumb-item active">Modifier</li>
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

        {{-- ── Modifier le template ── --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Informations du template</div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.templates.update', $template) }}">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nom <sup class="text-danger">*</sup></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $template->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Icône (emoji)</label>
                            <input type="text" name="icon" class="form-control"
                                   value="{{ old('icon', $template->icon) }}" maxlength="10">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Palettes existantes ── --}}
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold">
                    Palettes de couleurs
                    <span class="badge bg-secondary ms-1">{{ $template->palettes->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($template->palettes->isEmpty())
                    <p class="text-muted p-4 mb-0">Aucune palette. Ajoutez-en une ci-dessous.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Aperçu</th>
                                    <th>Nom</th>
                                    <th>Primaire</th>
                                    <th>Accent</th>
                                    <th>Fond</th>
                                    <th>Texte</th>
                                    <th>Ambiance</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($template->palettes as $pal)
                                <tr>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <div style="width:18px;height:18px;border-radius:50%;background:{{ $pal->color_primary }};border:1px solid #ccc;"></div>
                                            <div style="width:18px;height:18px;border-radius:50%;background:{{ $pal->color_accent }};border:1px solid #ccc;"></div>
                                            <div style="width:18px;height:18px;border-radius:50%;background:{{ $pal->color_bg }};border:1px solid #ccc;"></div>
                                        </div>
                                    </td>
                                    <td class="fw-semibold">{{ $pal->name }}</td>
                                    <td><code style="font-size:.75rem;">{{ $pal->color_primary }}</code></td>
                                    <td><code style="font-size:.75rem;">{{ $pal->color_accent }}</code></td>
                                    <td><code style="font-size:.75rem;">{{ $pal->color_bg }}</code></td>
                                    <td><code style="font-size:.75rem;">{{ $pal->color_text }}</code></td>
                                    <td class="text-muted small">{{ $pal->ambiance }}</td>
                                    <td>
                                        <form method="POST"
                                              action="{{ route('admin.templates.palettes.destroy', [$template, $pal]) }}"
                                              onsubmit="return confirm('Supprimer cette palette ?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
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

            {{-- ── Ajouter une palette ── --}}
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Ajouter une palette</div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.templates.palettes.store', $template) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nom <sup class="text-danger">*</sup></label>
                                <input type="text" name="name" class="form-control" placeholder="Vert Marché" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ambiance</label>
                                <input type="text" name="ambiance" class="form-control" placeholder="Fraîcheur & bio">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold">Couleur primaire</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="color" name="color_primary" class="form-control form-control-color" value="#3b82f6">
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold">Accent</label>
                                <input type="color" name="color_accent" class="form-control form-control-color" value="#10b981">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold">Fond</label>
                                <input type="color" name="color_bg" class="form-control form-control-color" value="#ffffff">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold">Texte</label>
                                <input type="color" name="color_text" class="form-control form-control-color" value="#111111">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i> Ajouter la palette
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
