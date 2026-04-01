@extends('dashboard.admin.layout.app')

@section('title', 'Thème & Couleurs')

@section('contents')
<div class="container-fluid px-4">
    <h1 class="mt-4">Thème & Couleurs</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Thème</li>
    </ol>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.theme-settings.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- ── Couleur primaire ── --}}
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3" style="width:80px;height:80px;border-radius:50%;margin:0 auto;background:{{ $theme['primary_color'] ?? '#81C408' }}"></div>
                        <h6 class="fw-bold">Couleur principale</h6>
                        <p class="text-muted small mb-3">Boutons, liens, titres, badges</p>
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <input type="color" name="primary_color" id="primaryColor"
                                class="form-control form-control-color border-0"
                                value="{{ $theme['primary_color'] ?? '#81C408' }}"
                                style="width:50px;height:40px;padding:2px;cursor:pointer;">
                            <input type="text" id="primaryHex" class="form-control form-control-sm font-monospace"
                                value="{{ $theme['primary_color'] ?? '#81C408' }}"
                                style="width:90px;" maxlength="7"
                                oninput="syncColor(this,'primaryColor')">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Texte sur primaire ── --}}
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3 d-flex align-items-center justify-content-center rounded-pill fw-bold"
                            style="width:120px;height:40px;margin:0 auto 20px;background:{{ $theme['primary_color'] ?? '#81C408' }};color:{{ $theme['primary_text_color'] ?? '#ffffff' }}">
                            Bouton
                        </div>
                        <h6 class="fw-bold">Texte sur fond principal</h6>
                        <p class="text-muted small mb-3">Texte dans les boutons primaires</p>
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <input type="color" name="primary_text_color" id="primaryTextColor"
                                class="form-control form-control-color border-0"
                                value="{{ $theme['primary_text_color'] ?? '#ffffff' }}"
                                style="width:50px;height:40px;padding:2px;cursor:pointer;">
                            <input type="text" id="primaryTextHex" class="form-control form-control-sm font-monospace"
                                value="{{ $theme['primary_text_color'] ?? '#ffffff' }}"
                                style="width:90px;" maxlength="7"
                                oninput="syncColor(this,'primaryTextColor')">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Couleur secondaire ── --}}
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3" style="width:80px;height:80px;border-radius:50%;margin:0 auto;background:{{ $theme['secondary_color'] ?? '#FFB524' }}"></div>
                        <h6 class="fw-bold">Couleur secondaire</h6>
                        <p class="text-muted small mb-3">Accents, prix, étoiles, badges</p>
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <input type="color" name="secondary_color" id="secondaryColor"
                                class="form-control form-control-color border-0"
                                value="{{ $theme['secondary_color'] ?? '#FFB524' }}"
                                style="width:50px;height:40px;padding:2px;cursor:pointer;">
                            <input type="text" id="secondaryHex" class="form-control form-control-sm font-monospace"
                                value="{{ $theme['secondary_color'] ?? '#FFB524' }}"
                                style="width:90px;" maxlength="7"
                                oninput="syncColor(this,'secondaryColor')">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Texte sur secondaire ── --}}
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3 d-flex align-items-center justify-content-center rounded-pill fw-bold"
                            style="width:120px;height:40px;margin:0 auto 20px;background:{{ $theme['secondary_color'] ?? '#FFB524' }};color:{{ $theme['secondary_text_color'] ?? '#ffffff' }}">
                            Accent
                        </div>
                        <h6 class="fw-bold">Texte sur fond secondaire</h6>
                        <p class="text-muted small mb-3">Texte sur badges, boutons accent</p>
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <input type="color" name="secondary_text_color" id="secondaryTextColor"
                                class="form-control form-control-color border-0"
                                value="{{ $theme['secondary_text_color'] ?? '#ffffff' }}"
                                style="width:50px;height:40px;padding:2px;cursor:pointer;">
                            <input type="text" id="secondaryTextHex" class="form-control form-control-sm font-monospace"
                                value="{{ $theme['secondary_text_color'] ?? '#ffffff' }}"
                                style="width:90px;" maxlength="7"
                                oninput="syncColor(this,'secondaryTextColor')">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Présets rapides ── --}}
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Présets rapides</div>
                    <div class="card-body d-flex flex-wrap gap-3">
                        @php
                        $presets = [
                            ['name'=>'Agri (défaut)',  'primary'=>'#81C408','primary_text'=>'#ffffff','secondary'=>'#FFB524','secondary_text'=>'#ffffff'],
                            ['name'=>'Océan',          'primary'=>'#0077B6','primary_text'=>'#ffffff','secondary'=>'#00B4D8','secondary_text'=>'#ffffff'],
                            ['name'=>'Coucher de soleil','primary'=>'#E63946','primary_text'=>'#ffffff','secondary'=>'#F4A261','secondary_text'=>'#ffffff'],
                            ['name'=>'Forêt',          'primary'=>'#2D6A4F','primary_text'=>'#ffffff','secondary'=>'#74C69D','secondary_text'=>'#1B4332'],
                            ['name'=>'Violet',         'primary'=>'#6A0572','primary_text'=>'#ffffff','secondary'=>'#D4A5E0','secondary_text'=>'#3a0140'],
                            ['name'=>'Sombre',         'primary'=>'#212529','primary_text'=>'#ffffff','secondary'=>'#6C757D','secondary_text'=>'#ffffff'],
                        ];
                        @endphp

                        @foreach($presets as $p)
                        <button type="button"
                            class="btn btn-outline-secondary d-flex align-items-center gap-2 preset-btn"
                            data-primary="{{ $p['primary'] }}"
                            data-primary-text="{{ $p['primary_text'] }}"
                            data-secondary="{{ $p['secondary'] }}"
                            data-secondary-text="{{ $p['secondary_text'] }}">
                            <span style="width:16px;height:16px;border-radius:50%;background:{{ $p['primary'] }};display:inline-block;border:2px solid rgba(0,0,0,.15)"></span>
                            <span style="width:16px;height:16px;border-radius:50%;background:{{ $p['secondary'] }};display:inline-block;border:2px solid rgba(0,0,0,.15)"></span>
                            {{ $p['name'] }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-success btn-lg px-5">
                    <i class="fas fa-save me-2"></i> Appliquer le thème
                </button>
                <p class="text-muted small mt-2">
                    <i class="fas fa-info-circle me-1"></i>
                    Les changements sont immédiatement visibles sur toutes les pages du site.
                </p>
            </div>

        </div>
    </form>
</div>

<script>
// Sync color picker ↔ input texte
document.querySelectorAll('input[type="color"]').forEach(picker => {
    picker.addEventListener('input', function () {
        const hexId = this.id.replace('Color', 'Hex').replace(/([A-Z])/g, m => m);
        // map picker id → hex input id
        const map = {
            primaryColor: 'primaryHex',
            primaryTextColor: 'primaryTextHex',
            secondaryColor: 'secondaryHex',
            secondaryTextColor: 'secondaryTextHex',
        };
        const hexInput = document.getElementById(map[this.id]);
        if (hexInput) hexInput.value = this.value;
    });
});

function syncColor(hexInput, pickerId) {
    const val = hexInput.value;
    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
        document.getElementById(pickerId).value = val;
    }
}

// Présets
document.querySelectorAll('.preset-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const set = (pickerId, hexId, val) => {
            document.getElementById(pickerId).value = val;
            document.getElementById(hexId).value   = val;
        };
        set('primaryColor',       'primaryHex',       this.dataset.primary);
        set('primaryTextColor',   'primaryTextHex',   this.dataset.primaryText);
        set('secondaryColor',     'secondaryHex',     this.dataset.secondary);
        set('secondaryTextColor', 'secondaryTextHex', this.dataset.secondaryText);
    });
});
</script>
@endsection
