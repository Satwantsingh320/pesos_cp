@if ($products->count())
    <div class="row">
        @foreach ($products as $product)
            <div class="col-lg-4 col-sm-6">
                {{-- //component --}}
                <x-product-card :product="$product" />
            </div>
        @endforeach

        @if ($products->hasPages())
            <div class="d-flex flex-column align-items-center mt-4 gap-2">
                <p class="text-muted small mb-1">
                    {{ __('website.showing') }} {{ $products->firstItem() }}
                    {{ __('website.to') }} {{ $products->lastItem() }}
                    {{ __('website.of') }} {{ $products->total() }}
                    {{ __('website.results') }}
                </p>
                {{ $products->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@else
    <h2 style="text-align: center;">
        {{ __('website.no_products_available') }}
    </h2>
@endif