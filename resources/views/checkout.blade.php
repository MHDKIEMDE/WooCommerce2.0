@extends('layouts.app')
@section('seo_title', 'Finaliser la commande')
@section('noindex')
@section('content')

    <!-- En-tête -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Finaliser la commande</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-white">Panier</a></li>
            <li class="breadcrumb-item active text-white">Paiement</li>
        </ol>
    </div>

    <div class="container-fluid py-5">
        <div class="container py-5">

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                @csrf
                <div class="row g-5">

                    {{-- ── Colonne gauche : Données de facturation ── --}}
                    <div class="col-md-12 col-lg-6 col-xl-7">
                        <h4 class="mb-4">Données de facturation</h4>

                        <div class="row g-3">

                            {{-- Prénom / Nom --}}
                            <div class="col-sm-6">
                                <label class="form-label">Prénom <sup class="text-danger">*</sup></label>
                                <input type="text" name="first_name"
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    value="{{ old('first_name', $user?->name) }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Nom <sup class="text-danger">*</sup></label>
                                <input type="text" name="last_name"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    value="{{ old('last_name') }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Pays --}}
                            <div class="col-sm-6">
                                <label class="form-label">Pays <sup class="text-danger">*</sup></label>
                                <input type="text" name="country" id="country_input"
                                    class="form-control @error('country') is-invalid @enderror"
                                    placeholder="Ex : Côte d'Ivoire"
                                    value="{{ old('country', 'Côte d\'Ivoire') }}"
                                    autocomplete="off" list="datalist-countries" required>
                                <datalist id="datalist-countries"></datalist>
                                @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Ville --}}
                            <div class="col-sm-6">
                                <label class="form-label">Ville <sup class="text-danger">*</sup></label>
                                <input type="text" name="city" id="city_input"
                                    class="form-control @error('city') is-invalid @enderror"
                                    placeholder="Ex : Abidjan, Bouaké…"
                                    value="{{ old('city', 'Abidjan') }}"
                                    autocomplete="off" list="datalist-cities" required>
                                <datalist id="datalist-cities"></datalist>
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Zone / Quartier (autocomplete sur les zones DB) --}}
                            <div class="col-12">
                                <label class="form-label">Zone / Quartier <sup class="text-danger">*</sup></label>
                                @if($zones->isEmpty())
                                    <input type="text" name="address"
                                        class="form-control @error('address') is-invalid @enderror"
                                        placeholder="Ex : Cocody, Yopougon, Plateau…"
                                        value="{{ old('address') }}" required>
                                    <div class="form-text text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Aucune zone configurée — contactez l'administrateur.
                                    </div>
                                @else
                                    <div class="position-relative">
                                        <input type="text" id="zone_search"
                                            class="form-control @error('zone_id') is-invalid @enderror"
                                            placeholder="Tapez votre quartier…"
                                            autocomplete="off"
                                            value="{{ old('zone_id') ? ($zones->firstWhere('id', old('zone_id'))?->name ?? '') : '' }}"
                                            required>
                                        <div id="zone_dropdown"
                                            class="position-absolute w-100 bg-white border rounded shadow"
                                            style="z-index:1050;display:none;max-height:220px;overflow-y:auto;top:100%;left:0;">
                                        </div>
                                    </div>
                                    <input type="hidden" name="zone_id" id="zone_id_hidden" value="{{ old('zone_id') }}">
                                    <input type="hidden" name="address" id="address_hidden" value="{{ old('address') }}">
                                    <div class="form-text text-muted" id="zone_price_hint" style="display:none;">
                                        <i class="fas fa-truck me-1 text-primary"></i>
                                        <span id="zone_price_text"></span>
                                    </div>
                                @endif
                                @error('zone_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            {{-- Téléphone / Email --}}
                            <div class="col-sm-6">
                                <label class="form-label">Téléphone <sup class="text-danger">*</sup></label>
                                <input type="tel" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}" required>
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Email <span class="text-muted small">(optionnel)</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user?->email) }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Notes --}}
                            <div class="col-12">
                                <label class="form-label">Notes de commande <span class="text-muted small">(optionnel)</span></label>
                                <textarea name="notes" class="form-control" rows="3"
                                    placeholder="Instructions de livraison, informations complémentaires…">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        {{-- Méthode de paiement --}}
                        <h4 class="mt-5 mb-3">Mode de paiement</h4>
                        <div class="border rounded p-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method"
                                    id="cod" value="cash_on_delivery"
                                    {{ old('payment_method', 'cash_on_delivery') === 'cash_on_delivery' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="cod">
                                    <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                    Paiement à la livraison
                                </label>
                            </div>
                            <p class="text-muted small ms-4 mb-0 mt-1">Réglez en espèces au moment de la livraison.</p>
                        </div>
                        <div class="border rounded p-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method"
                                    id="bank" value="bank_transfer"
                                    {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="bank">
                                    <i class="fas fa-university me-2 text-primary"></i>
                                    Virement bancaire
                                </label>
                            </div>
                            <p class="text-muted small ms-4 mb-0 mt-1">
                                Effectuez votre virement directement depuis votre banque.
                                Votre commande sera expédiée après réception du paiement.
                            </p>
                        </div>
                        @error('payment_method')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ── Colonne droite : Récapitulatif ── --}}
                    <div class="col-md-12 col-lg-6 col-xl-5">
                        <div class="bg-light rounded p-4">
                            <h4 class="mb-4">Récapitulatif</h4>
                            <table class="table table-borderless align-middle">
                                <thead class="border-bottom">
                                    <tr><th>Produit</th><th class="text-end">Total</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        @php
                                            $unitPrice = $item->product->price + ($item->variant?->price_modifier ?? 0);
                                            $lineTotal = $unitPrice * $item->quantity;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @php $img = $item->product->images->first(); @endphp
                                                    @if ($img)
                                                        <img src="{{ $img->url }}" alt="{{ $item->product->name }}"
                                                            style="width:48px;height:48px;object-fit:cover;" class="rounded">
                                                    @endif
                                                    <div>
                                                        <div class="fw-semibold" style="font-size:.9rem">{{ $item->product->name }}</div>
                                                        @if ($item->variant)
                                                            <small class="text-muted">{{ $item->variant->name }}</small>
                                                        @endif
                                                        <div class="text-muted small">× {{ $item->quantity }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">{{ number_format($lineTotal, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="border-top">
                                    <tr>
                                        <td class="text-muted">Sous-total</td>
                                        <td class="text-end">{{ number_format($totals['subtotal'], 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                    @if ($totals['discount'] > 0)
                                    <tr class="text-success">
                                        <td>Réduction @if($coupon)<span class="badge bg-success ms-1">{{ $coupon->code }}</span>@endif</td>
                                        <td class="text-end">− {{ number_format($totals['discount'], 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="text-muted">Livraison</td>
                                        <td class="text-end" id="shipping-display">
                                            <span class="text-muted fst-italic">Choisissez une zone</span>
                                        </td>
                                    </tr>
                                    <tr class="border-top fw-bold">
                                        <td>Total</td>
                                        <td class="text-end text-primary fs-5" id="total-display">
                                            {{ number_format($totals['subtotal'] - $totals['discount'], 0, ',', ' ') }} FCFA
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="d-grid mt-3">
                                <button type="submit" class="btn btn-primary btn-lg py-3">
                                    <i class="fas fa-lock me-2"></i> Confirmer la commande
                                </button>
                            </div>
                            <p class="text-center text-muted small mt-3">
                                <i class="fas fa-shield-alt me-1"></i>
                                Vos données sont sécurisées et ne seront jamais partagées.
                            </p>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

<script>
// ── Données zones (depuis DB) ─────────────────────────────────────────────────
const ZONES = @json($zones->map(fn($z) => ['id' => $z->id, 'name' => $z->name, 'price' => $z->price]));

// ── Données géographiques ─────────────────────────────────────────────────────
const GEO = {
    "Côte d'Ivoire": [
        "Abidjan","Bouaké","Daloa","Korhogo","Yamoussoukro","Man","San-Pédro",
        "Gagnoa","Abengourou","Divo","Soubré","Agboville","Adzopé","Anyama",
        "Grand-Bassam","Jacqueville","Bingerville","Dabou","Tiassalé"
    ],
    "Sénégal": ["Dakar","Thiès","Kaolack","Saint-Louis","Ziguinchor","Touba","Mbour"],
    "Mali": ["Bamako","Sikasso","Mopti","Ségou","Koutiala","Gao"],
    "Burkina Faso": ["Ouagadougou","Bobo-Dioulasso","Koudougou","Banfora","Ouahigouya"],
    "Guinée": ["Conakry","Labé","Kankan","Kindia","Nzérékoré"],
    "Bénin": ["Cotonou","Porto-Novo","Parakou","Abomey","Natitingou"],
    "Togo": ["Lomé","Sokodé","Kara","Atakpamé","Kpalimé"],
    "Niger": ["Niamey","Zinder","Maradi","Agadez","Tahoua"],
    "Cameroun": ["Yaoundé","Douala","Garoua","Bafoussam","Bamenda"],
    "Ghana": ["Accra","Kumasi","Tamale","Sekondi","Cape Coast"],
    "Nigeria": ["Lagos","Abuja","Kano","Ibadan","Enugu","Port Harcourt"],
    "France": ["Paris","Lyon","Marseille","Bordeaux","Toulouse","Nice","Nantes","Strasbourg"],
    "Belgique": ["Bruxelles","Anvers","Gand","Liège","Bruges"],
    "Suisse": ["Genève","Zurich","Berne","Lausanne","Bâle"],
    "Canada": ["Montréal","Toronto","Québec","Ottawa","Vancouver"],
    "États-Unis": ["New York","Los Angeles","Chicago","Houston","Miami","Atlanta"],
};

const ALL_COUNTRIES = Object.keys(GEO);
const baseTotal = {{ $totals['subtotal'] - $totals['discount'] }};

// ── Autocomplete Pays ─────────────────────────────────────────────────────────
(function () {
    const input    = document.getElementById('country_input');
    const datalist = document.getElementById('datalist-countries');
    if (!input) return;

    // Peupler la datalist
    ALL_COUNTRIES.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c;
        datalist.appendChild(opt);
    });

    // Mettre à jour les villes quand le pays change
    input.addEventListener('change', () => updateCities(input.value));
    input.addEventListener('input',  () => updateCities(input.value));

    // Init au chargement
    updateCities(input.value);
})();

// ── Autocomplete Ville ────────────────────────────────────────────────────────
function updateCities(country) {
    const datalist = document.getElementById('datalist-cities');
    if (!datalist) return;
    datalist.innerHTML = '';

    const cities = GEO[country] || [];
    cities.forEach(city => {
        const opt = document.createElement('option');
        opt.value = city;
        datalist.appendChild(opt);
    });
}

// ── Autocomplete Zone / Quartier ──────────────────────────────────────────────
(function () {
    const input    = document.getElementById('zone_search');
    const dropdown = document.getElementById('zone_dropdown');
    const hiddenId = document.getElementById('zone_id_hidden');
    const hiddenAd = document.getElementById('address_hidden');
    if (!input || !dropdown) return;

    function renderDropdown(query) {
        const q = query.trim().toLowerCase();
        const matches = q.length === 0
            ? ZONES
            : ZONES.filter(z => z.name.toLowerCase().includes(q));

        if (matches.length === 0) {
            dropdown.innerHTML = '<div class="px-3 py-2 text-muted small">Aucune zone trouvée</div>';
            dropdown.style.display = 'block';
            return;
        }

        dropdown.innerHTML = matches.map(z => `
            <div class="zone-option d-flex justify-content-between align-items-center px-3 py-2"
                 style="cursor:pointer;"
                 data-id="${z.id}" data-name="${z.name}" data-price="${z.price}"
                 onmouseenter="this.style.background='#f0f4ff'"
                 onmouseleave="this.style.background=''">
                <span>${z.name}</span>
                <span class="badge bg-primary ms-2">${formatFCFA(z.price)} FCFA</span>
            </div>
        `).join('');

        dropdown.querySelectorAll('.zone-option').forEach(el => {
            el.addEventListener('mousedown', e => {
                e.preventDefault();
                selectZone(el.dataset.id, el.dataset.name, parseFloat(el.dataset.price));
            });
        });

        dropdown.style.display = 'block';
    }

    function selectZone(id, name, price) {
        input.value      = name;
        hiddenId.value   = id;
        hiddenAd.value   = name;
        dropdown.style.display = 'none';
        updateShippingDisplay(price, name);
    }

    input.addEventListener('focus', () => renderDropdown(input.value));
    input.addEventListener('input', () => {
        hiddenId.value = '';   // reset si l'utilisateur retape
        renderDropdown(input.value);
    });
    input.addEventListener('blur', () => {
        setTimeout(() => { dropdown.style.display = 'none'; }, 150);
    });

    // Fermer si clic en dehors
    document.addEventListener('click', e => {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Restore si old() présent
    const oldId = hiddenId ? hiddenId.value : '';
    if (oldId) {
        const z = ZONES.find(z => z.id == oldId);
        if (z) selectZone(z.id, z.name, z.price);
    }
})();

// ── Mise à jour du récapitulatif ──────────────────────────────────────────────
function updateShippingDisplay(price, zoneName) {
    const hint = document.getElementById('zone_price_hint');
    const txt  = document.getElementById('zone_price_text');

    document.getElementById('shipping-display').textContent = formatFCFA(price) + ' FCFA';
    document.getElementById('total-display').textContent    = formatFCFA(baseTotal + price) + ' FCFA';

    if (hint && txt) {
        txt.textContent = `Livraison vers ${zoneName} : ${formatFCFA(price)} FCFA`;
        hint.style.display = '';
    }
}

function formatFCFA(n) {
    return Math.round(n).toLocaleString('fr-FR');
}
</script>
@endsection
