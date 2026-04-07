{{--
  Composant carte produit réutilisable.
  Variables attendues : $product (App\Models\Product, avec images et category chargés)
--}}
@php
    $img = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
    $hasDiscount = $product->compare_price && $product->compare_price > $product->price;
    $discountPct = $hasDiscount
        ? round((($product->compare_price - $product->price) / $product->compare_price) * 100)
        : 0;
@endphp

<div class="rounded position-relative fruite-item">

    {{-- Image + badges + boutons superposés --}}
    <div class="fruite-img position-relative overflow-hidden rounded-top">
        <a href="{{ route('shop.show', $product->slug) }}">
            <img src="{{ $img ? $img->url : asset('img/fruite-item-1.jpg') }}"
                class="img-fluid w-100 rounded-top"
                style="height:180px; object-fit:cover;"
                alt="{{ $product->name }}"
                loading="lazy">
        </a>

        {{-- Badge catégorie --}}
        @if($product->category)
        <div class="text-white bg-secondary px-2 py-1 rounded position-absolute"
            style="top:8px; left:8px; font-size:.7rem;">
            {{ $product->category->name }}
        </div>
        @endif

        {{-- Badge remise --}}
        @if($hasDiscount)
        <div class="text-white bg-danger px-2 py-1 rounded position-absolute"
            style="top:8px; right:8px; font-size:.7rem;">
            -{{ $discountPct }}%
        </div>
        @endif

        {{-- Badge rupture --}}
        @if($product->stock_quantity <= 0)
        <div class="text-white bg-dark px-2 py-1 rounded position-absolute"
            style="bottom:8px; left:8px; font-size:.7rem; opacity:.85;">
            Rupture
        </div>
        @endif

        {{-- Prix + boutons en overlay sur l'image --}}
        <div class="position-absolute bottom-0 end-0 p-2 d-flex flex-column align-items-end gap-1">
            {{-- Prix --}}
            <div class="bg-white bg-opacity-90 rounded px-2 py-1 lh-1 text-end shadow-sm">
                <span class="fw-bold text-dark" style="font-size:.85rem;">
                    {{ number_format($product->price, 0, ',', ' ') }} FCFA
                    @if($product->unit)<small class="text-muted fw-normal" style="font-size:.7rem;">/ {{ $product->unit }}</small>@endif
                </span>
                @if($hasDiscount)
                <div class="text-muted text-decoration-line-through" style="font-size:.7rem;">
                    {{ number_format($product->compare_price, 0, ',', ' ') }}
                </div>
                @endif
            </div>

            {{-- Boutons --}}
            <div class="d-flex gap-1">
                @if($product->stock_quantity > 0)
                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"
                        class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center shadow"
                        style="width:32px;height:32px;padding:0;"
                        title="Ajouter au panier">
                        <i class="fa fa-shopping-bag" style="font-size:.7rem;"></i>
                    </button>
                </form>
                @endif

                <a href="{{ route('shop.show', $product->slug) }}"
                    class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center shadow"
                    style="width:32px;height:32px;padding:0;"
                    title="Voir le produit">
                    <i class="fa fa-eye text-primary" style="font-size:.7rem;"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Nom uniquement --}}
    <div class="px-3 py-2 border border-secondary border-top-0 rounded-bottom">
        <h6 class="mb-0 text-truncate">
            <a href="{{ route('shop.show', $product->slug) }}" class="text-dark text-decoration-none">
                {{ $product->name }}
            </a>
        </h6>
        @if($product->rating_count > 0)
        <div class="d-flex align-items-center gap-1 mt-1">
            @for($i = 1; $i <= 5; $i++)
                <i class="fa fa-star{{ $i <= round($product->rating_avg) ? ' text-secondary' : ' text-muted' }}"
                   style="font-size:.6rem;"></i>
            @endfor
        </div>
        @endif
    </div>

</div>
