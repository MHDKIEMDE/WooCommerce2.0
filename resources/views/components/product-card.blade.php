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
    <div class="fruite-img">
        <a href="{{ route('shop.show', $product->slug) }}">
            <img src="{{ $img ? $img->url : asset('img/fruite-item-1.jpg') }}"
                class="img-fluid w-100 rounded-top"
                style="height:200px; object-fit:cover;"
                alt="{{ $product->name }}"
                loading="lazy">
        </a>
    </div>

    {{-- Badge catégorie --}}
    @if($product->category)
    <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
        style="top: 10px; left: 10px; font-size: .8rem;">
        {{ $product->category->name }}
    </div>
    @endif

    {{-- Badge remise --}}
    @if($hasDiscount)
    <div class="text-white bg-danger px-2 py-1 rounded position-absolute"
        style="top: 10px; right: 10px; font-size: .75rem;">
        -{{ $discountPct }}%
    </div>
    @endif

    {{-- Badge rupture stock --}}
    @if($product->stock_quantity <= 0)
    <div class="text-white bg-dark px-2 py-1 rounded position-absolute"
        style="bottom: 10px; left: 10px; font-size: .75rem; opacity: .85;">
        Rupture de stock
    </div>
    @endif

    <div class="p-4 border border-secondary border-top-0 rounded-bottom">
        <h4 class="mb-1">
            <a href="{{ route('shop.show', $product->slug) }}" class="text-dark text-decoration-none">
                {{ $product->name }}
            </a>
        </h4>
        @if($product->short_description)
        <p class="text-truncate mb-2 text-muted small">{{ $product->short_description }}</p>
        @endif

        {{-- Notes --}}
        @if($product->rating_count > 0)
        <div class="d-flex align-items-center gap-1 mb-2">
            @for($i = 1; $i <= 5; $i++)
                <i class="fa fa-star{{ $i <= round($product->rating_avg) ? ' text-secondary' : ' text-muted' }}"
                   style="font-size:.75rem;"></i>
            @endfor
            <small class="text-muted">({{ $product->rating_count }})</small>
        </div>
        @endif

        <div class="d-flex justify-content-between flex-lg-wrap align-items-center gap-2">
            <div>
                <span class="text-dark fs-5 fw-bold">
                    {{ number_format($product->price, 0, ',', ' ') }} FCFA
                </span>
                @if($hasDiscount)
                <span class="text-muted text-decoration-line-through ms-1 small">
                    {{ number_format($product->compare_price, 0, ',', ' ') }} FCFA
                </span>
                @endif
                @if($product->unit)
                <span class="text-muted small"> / {{ $product->unit }}</span>
                @endif
            </div>

            <div class="d-flex gap-1">
                {{-- Ajout au panier --}}
                @if($product->stock_quantity > 0)
                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"
                        class="btn border border-secondary rounded-pill px-3 text-primary btn-sm"
                        title="Ajouter au panier">
                        <i class="fa fa-shopping-bag text-primary"></i>
                    </button>
                </form>
                @endif

                <a href="{{ route('shop.show', $product->slug) }}"
                    class="btn border border-secondary rounded-pill px-3 text-primary btn-sm"
                    title="Voir le produit">
                    <i class="fa fa-eye text-primary"></i>
                </a>
            </div>
        </div>
    </div>
</div>
