@inject ('reviewHelper', 'Webkul\Product\Helpers\Review')
@inject ('productViewHelper', 'Webkul\Product\Helpers\View')

@php
    $avgRatings = $reviewHelper->getAverageRating($product);

    $percentageRatings = $reviewHelper->getPercentageRating($product);

    $customAttributeValues = $productViewHelper->getAdditionalData($product);

    $attributeData = collect($customAttributeValues)->filter(fn ($item) => ! empty($item['value']));
@endphp

<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="{{ trim($product->meta_description) != "" ? $product->meta_description : \Illuminate\Support\Str::limit(strip_tags($product->description), 120, '') }}"/>

    <meta name="keywords" content="{{ $product->meta_keywords }}"/>

    @if (core()->getConfigData('catalog.rich_snippets.products.enable'))
        <script type="application/ld+json">
            {!! app('Webkul\Product\Helpers\SEO')->getProductJsonLd($product) !!}
        </script>
    @endif

    <?php $productBaseImage = product_image()->getProductBaseImage($product); ?>

    <meta name="twitter:card" content="summary_large_image" />

    <meta name="twitter:title" content="{{ $product->name }}" />

    <meta name="twitter:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />

    <meta name="twitter:image:alt" content="" />

    <meta name="twitter:image" content="{{ $productBaseImage['medium_image_url'] }}" />

    <meta property="og:type" content="og:product" />

    <meta property="og:title" content="{{ $product->name }}" />

    <meta property="og:image" content="{{ $productBaseImage['medium_image_url'] }}" />

    <meta property="og:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />

    <meta property="og:url" content="{{ route('shop.product_or_category.index', $product->url_key) }}" />
@endPush

<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{ trim($product->meta_title) != "" ? $product->meta_title : $product->name }}
    </x-slot>

    {!! view_render_event('bagisto.shop.products.view.before', ['product' => $product]) !!}

    <!-- Breadcrumbs -->
    @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        <div class="flex justify-center max-lg:hidden">
            <x-shop::breadcrumbs
                name="product"
                :entity="$product"
            />
        </div>
    @endif

    <!-- Product Information Vue Component -->
    <v-product>
        <x-shop::shimmer.products.view />
    </v-product>

    <!-- Information Section -->
    <div class="1180:mt-20">
        <div class="max-1180:hidden">
            <x-shop::tabs
                position="center"
                ref="productTabs"
            >
                <!-- Description Tab -->
                {!! view_render_event('bagisto.shop.products.view.description.before', ['product' => $product]) !!}

                <x-shop::tabs.item
                    id="descritpion-tab"
                    class="container mt-[60px] !p-0"
                    :title="trans('shop::app.products.view.description')"
                    :is-selected="true"
                >
                    <div class="container mt-[60px] max-1180:px-5">
                        <p class="text-lg text-zinc-500 max-1180:text-sm">
                            {!! $product->description !!}
                        </p>
                    </div>
                </x-shop::tabs.item>

                {!! view_render_event('bagisto.shop.products.view.description.after', ['product' => $product]) !!}

                <!-- Additional Information Tab -->
                @if(count($attributeData))
                    <x-shop::tabs.item
                        id="information-tab"
                        class="container mt-[60px] !p-0"
                        :title="trans('shop::app.products.view.additional-information')"
                        :is-selected="false"
                    >
                        <div class="container mt-[60px] max-1180:px-5">
                            <div class="mt-8 grid max-w-max grid-cols-[auto_1fr] gap-4">
                                @foreach ($customAttributeValues as $customAttributeValue)
                                    @if (! empty($customAttributeValue['value']))
                                        <div class="grid">
                                            <p class="text-base text-black">
                                                {!! $customAttributeValue['label'] !!}
                                            </p>
                                        </div>

                                        @if ($customAttributeValue['type'] == 'file')
                                            <a 
                                                href="{{ Storage::url($product[$customAttributeValue['code']]) }}" 
                                                download="{{ $customAttributeValue['label'] }}"
                                            >
                                                <span class="icon-download text-2xl"></span>
                                            </a>
                                        @elseif ($customAttributeValue['type'] == 'image')
                                            <a 
                                                href="{{ Storage::url($product[$customAttributeValue['code']]) }}" 
                                                download="{{ $customAttributeValue['label'] }}"
                                            >
                                                <img 
                                                    class="h-5 min-h-5 w-5 min-w-5" 
                                                    src="{{ Storage::url($customAttributeValue['value']) }}" 
                                                />
                                            </a>
                                        @else
                                            <div class="grid">
                                                <p class="text-base text-zinc-500">
                                                    {!! $customAttributeValue['value'] !!}
                                                </p>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </x-shop::tabs.item>
                @endif

                <!-- Reviews Tab -->
                <x-shop::tabs.item
                    id="review-tab"
                    class="container mt-[60px] !p-0"
                    :title="trans('shop::app.products.view.review')"
                    :is-selected="false"
                >
                    @include('shop::products.view.reviews')
                </x-shop::tabs.item>
            </x-shop::tabs>
        </div>
    </div>

    <!-- Information Section -->
    <div class="container mt-6 grid gap-3 !p-0 max-1180:px-5 1180:hidden">
        <!-- Description Accordion -->
        <x-shop::accordion
            class="max-md:border-none"
            :is-active="true"
        >
            <x-slot:header class="bg-gray-100 max-md:!py-3 max-sm:!py-2">
                <p class="text-base font-medium 1180:hidden">
                    @lang('shop::app.products.view.description')
                </p>
            </x-slot>

            <x-slot:content class="max-sm:px-0">
                <div class="mb-5 text-lg text-zinc-500 max-1180:text-sm max-md:mb-1 max-md:px-4">
                    {!! $product->description !!}
                </div>
            </x-slot>
        </x-shop::accordion>

        <!-- Additional Information Accordion -->
        @if (count($attributeData))
            <x-shop::accordion
                class="max-md:border-none"
                :is-active="false"
            >
                <x-slot:header class="bg-gray-100 max-md:!py-3 max-sm:!py-2">
                    <p class="text-base font-medium 1180:hidden">
                        @lang('shop::app.products.view.additional-information')
                    </p>
                </x-slot>

                <x-slot:content class="max-sm:px-0">
                    <div class="container max-1180:px-5">
                        <div class="grid max-w-max grid-cols-[auto_1fr] gap-4 text-lg text-zinc-500 max-1180:text-sm">
                            @foreach ($customAttributeValues as $customAttributeValue)
                                @if (! empty($customAttributeValue['value']))
                                    <div class="grid">
                                        <p class="text-base text-black">
                                            {{ $customAttributeValue['label'] }}
                                        </p>
                                    </div>

                                    @if ($customAttributeValue['type'] == 'file')
                                        <a
                                            href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                            download="{{ $customAttributeValue['label'] }}"
                                        >
                                            <span class="icon-download text-2xl"></span>
                                        </a>
                                    @elseif ($customAttributeValue['type'] == 'image')
                                        <a
                                            href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                            download="{{ $customAttributeValue['label'] }}"
                                        >
                                            <img 
                                                class="h-5 min-h-5 w-5 min-w-5" 
                                                src="{{ Storage::url($customAttributeValue['value']) }}"
                                                alt="Product Image"
                                            />
                                        </a>
                                    @else
                                        <div class="grid">
                                            <p class="text-base text-zinc-500">
                                                {{ $customAttributeValue['value'] ?? '-' }}
                                            </p>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                </x-slot>
            </x-shop::accordion>
        @endif

        <!-- Reviews Accordion -->
        <x-shop::accordion
            class="max-md:border-none"
            :is-active="false"
        >
            <x-slot:header
                class="bg-gray-100 max-md:!py-3 max-sm:!py-2"
                id="review-accordian-button"
            >
                <p class="text-base font-medium">
                    @lang('shop::app.products.view.review')
                </p>
            </x-slot>

            <x-slot:content>
                @include('shop::products.view.reviews')
            </x-slot>
        </x-shop::accordion>
    </div>

    <!-- Featured Products -->
    <x-shop::products.carousel
        :title="trans('shop::app.products.view.related-product-title')"
        :src="route('shop.api.products.related.index', ['id' => $product->id])"
    />

    <!-- Upsell Products -->
    <x-shop::products.carousel
        :title="trans('shop::app.products.view.up-sell-title')"
        :src="route('shop.api.products.up-sell.index', ['id' => $product->id])"
    />

    {!! view_render_event('bagisto.shop.products.view.after', ['product' => $product]) !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-product-template"
        >
            <x-shop::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form
                    ref="formData"
                    @submit="handleSubmit($event, addToCart)"
                >
                    <input
                        type="hidden"
                        name="product_id"
                        value="{{ $product->id }}"
                    >

                    <input
                        type="hidden"
                        name="is_buy_now"
                        v-model="is_buy_now"
                    >

                    <div class="container px-[60px] max-1180:px-0">
                        <div class="mt-12 flex gap-9 max-1180:flex-wrap max-lg:mt-0 max-sm:gap-y-4">
                            <!-- Gallery Blade Inclusion -->
                            @include('shop::products.view.gallery')

                            <!-- Details -->
                            <div class="relative max-w-[590px] max-1180:w-full max-1180:max-w-full max-1180:px-5 max-sm:px-4">
                                {!! view_render_event('bagisto.shop.products.name.before', ['product' => $product]) !!}

                                <div class="flex justify-between gap-4">
                                    <h1 class="text-3xl font-medium max-sm:text-xl">
                                        {{ $product->name }}
                                    </h1>

                                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                        <div
                                            class="flex max-h-[46px] min-h-[46px] min-w-[46px] cursor-pointer items-center justify-center rounded-full border bg-white text-2xl transition-all hover:opacity-[0.8] max-sm:max-h-7 max-sm:min-h-7 max-sm:min-w-7 max-sm:text-base"
                                            role="button"
                                            aria-label="@lang('shop::app.products.view.add-to-wishlist')"
                                            tabindex="0"
                                            :class="isWishlist ? 'icon-heart-fill text-red-600' : 'icon-heart'"
                                            @click="addToWishlist"
                                        >
                                        </div>
                                    @endif
                                </div>

                                {!! view_render_event('bagisto.shop.products.name.after', ['product' => $product]) !!}

                                <!-- Rating -->
                                {!! view_render_event('bagisto.shop.products.rating.before', ['product' => $product]) !!}

                                @if ($totalRatings = $reviewHelper->getTotalFeedback($product))
                                    <!-- Scroll To Reviews Section and Activate Reviews Tab -->
                                    <div
                                        class="mt-1 w-max cursor-pointer max-sm:mt-1.5"
                                        role="button"
                                        tabindex="0"
                                        @click="scrollToReview"
                                    >
                                        <x-shop::products.ratings
                                            class="transition-all hover:border-gray-400 max-sm:px-3 max-sm:py-1"
                                            :average="$avgRatings"
                                            :total="$totalRatings"
                                            ::rating="true"
                                        />
                                    </div>
                                @endif

                                {!! view_render_event('bagisto.shop.products.rating.after', ['product' => $product]) !!}

                                <!-- Pricing -->
                                {!! view_render_event('bagisto.shop.products.price.before', ['product' => $product]) !!}

                                <p class="mt-[22px] flex items-center gap-2.5 text-2xl !font-medium max-sm:mt-2 max-sm:gap-x-2.5 max-sm:gap-y-0 max-sm:text-lg">
                                    {!! $product->getTypeInstance()->getPriceHtml() !!}
                                </p>

                                @if (\Webkul\Tax\Facades\Tax::isInclusiveTaxProductPrices())
                                    <span class="text-sm font-normal text-zinc-500 max-sm:text-xs">
                                        (@lang('shop::app.products.view.tax-inclusive'))
                                    </span>
                                @endif

                                @if (count($product->getTypeInstance()->getCustomerGroupPricingOffers()))
                                    <div class="mt-2.5 grid gap-1.5">
                                        @foreach ($product->getTypeInstance()->getCustomerGroupPricingOffers() as $offer)
                                            <p class="text-zinc-500 [&>*]:text-black">
                                                {!! $offer !!}
                                            </p>
                                        @endforeach
                                    </div>
                                @endif

                                {!! view_render_event('bagisto.shop.products.price.after', ['product' => $product]) !!}

                                {!! view_render_event('bagisto.shop.products.short_description.before', ['product' => $product]) !!}

                                <p class="mt-6 text-lg text-zinc-500 max-sm:mt-1.5 max-sm:text-sm">
                                    {!! $product->short_description !!}
                                </p>

                                {!! view_render_event('bagisto.shop.products.short_description.after', ['product' => $product]) !!}

                                @include('shop::products.view.types.configurable')

                                @include('shop::products.view.types.grouped')

                                @include('shop::products.view.types.bundle')

                                @include('shop::products.view.types.downloadable')


                                <!-- Product Actions and Qunatity Box -->
                                <div class="mt-8 flex max-w-[470px] gap-4 max-sm:mt-4">

                                    {!! view_render_event('bagisto.shop.products.view.quantity.before', ['product' => $product]) !!}

                                    @if ($product->getTypeInstance()->showQuantityBox())
                                        <x-shop::quantity-changer
                                            name="quantity"
                                            value="1"
                                            class="gap-x-4 rounded-xl px-7 py-4 max-md:py-3 max-sm:gap-x-5 max-sm:rounded-lg max-sm:px-4 max-sm:py-1.5"
                                        />
                                    @endif

                                    {!! view_render_event('bagisto.shop.products.view.quantity.after', ['product' => $product]) !!}

                                    @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                                        <!-- Add To Cart Button -->
                                        {!! view_render_event('bagisto.shop.products.view.add_to_cart.before', ['product' => $product]) !!}

                                        <x-shop::button
                                            type="submit"
                                            class="secondary-button w-full max-w-full max-md:py-3 max-sm:rounded-lg max-sm:py-1.5"
                                            button-type="secondary-button"
                                            :loading="false"
                                            :title="trans('shop::app.products.view.add-to-cart')"
                                            :disabled="! $product->isSaleable(1)"
                                            ::loading="isStoring.addToCart"
                                        />

                                        {!! view_render_event('bagisto.shop.products.view.add_to_cart.after', ['product' => $product]) !!}
                                    @endif
                                    @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page') && $product->isSaleable(1))
                                        <span
                                            class="cursor-pointer p-2.5 text-2xl max-sm:hidden"
                                            role="button"
                                            style="padding: 0px;"
                                            tabindex="0"
                                            @click="orderOnWhatsApp()"
                                        >
                                            <svg width="50" height="50" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" rx="20" fill="url(#paint0_linear_2569_5464)"></rect><path d="M7 33L8.83631 26.3234C7.70317 24.3691 7.10776 22.1537 7.10885 19.8819C7.11212 12.7796 12.9193 7 20.0544 7C23.517 7.00108 26.7672 8.34333 29.212 10.7787C31.6557 13.214 33.0011 16.451 33 19.8938C32.9967 26.9973 27.1896 32.7768 20.0544 32.7768C17.8883 32.7758 15.7537 32.2352 13.863 31.2082L7 33ZM14.1809 28.8758C16.0052 29.9537 17.7468 30.5993 20.0501 30.6004C25.9802 30.6004 30.811 25.7969 30.8143 19.8917C30.8165 13.9745 26.0085 9.1775 20.0588 9.17533C14.1243 9.17533 9.29674 13.9788 9.29457 19.883C9.29348 22.2934 10.0032 24.0982 11.1951 25.9865L10.1077 29.9385L14.1809 28.8758ZM26.5756 22.9564C26.4951 22.8221 26.2796 22.7419 25.9552 22.5805C25.6319 22.4191 24.0416 21.6402 23.7444 21.5329C23.4484 21.4257 23.2329 21.3715 23.0162 21.6943C22.8007 22.0161 22.1803 22.7419 21.992 22.9564C21.8036 23.1709 21.6142 23.198 21.291 23.0366C20.9677 22.8752 19.9249 22.5361 18.6894 21.4387C17.7283 20.585 17.0785 19.5309 16.8901 19.2081C16.7018 18.8863 16.8706 18.7119 17.0316 18.5516C17.1775 18.4075 17.3549 18.1757 17.5171 17.9872C17.6815 17.8008 17.7348 17.6665 17.8437 17.4509C17.9514 17.2364 17.8981 17.0479 17.8165 16.8865C17.7348 16.7262 17.0883 15.1413 16.8194 14.4967C16.556 13.8694 16.2893 13.9539 16.0912 13.9442L15.4707 13.9333C15.2552 13.9333 14.9047 14.0135 14.6086 14.3363C14.3126 14.6592 13.4766 15.437 13.4766 17.0219C13.4766 18.6068 14.6359 20.1376 14.797 20.3521C14.9591 20.5666 17.0774 23.8188 20.3222 25.213C21.0939 25.5445 21.697 25.7428 22.1661 25.8912C22.9411 26.136 23.6465 26.1013 24.2038 26.019C24.8253 25.9269 26.1174 25.2401 26.3873 24.4883C26.6573 23.7353 26.6573 23.0907 26.5756 22.9564Z" fill="white"></path><defs><linearGradient id="paint0_linear_2569_5464" x1="19.5928" y1="2.40043" x2="19.7955" y2="36.5829" gradientUnits="userSpaceOnUse"><stop stop-color="#57D163"></stop><stop offset="1" stop-color="#23B33A"></stop></linearGradient></defs></svg>
                                        </span>
                                    @endif
                                </div>
                                
                                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page') && $product->isSaleable(1))
                                    <span
                                        class="cursor-pointer p-2.5 text-2xl sm:hidden"
                                        role="button"
                                        style="padding: 0px;top: 6px;position: inherit;"
                                        tabindex="0"
                                        @click="orderOnWhatsApp()"
                                    >
                                        <svg style="background: #00cd3b;border-radius: 24px;padding: 1px" width="50" height="50" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" rx="20" fill="url(#paint0_linear_2569_5464)"></rect><path d="M7 33L8.83631 26.3234C7.70317 24.3691 7.10776 22.1537 7.10885 19.8819C7.11212 12.7796 12.9193 7 20.0544 7C23.517 7.00108 26.7672 8.34333 29.212 10.7787C31.6557 13.214 33.0011 16.451 33 19.8938C32.9967 26.9973 27.1896 32.7768 20.0544 32.7768C17.8883 32.7758 15.7537 32.2352 13.863 31.2082L7 33ZM14.1809 28.8758C16.0052 29.9537 17.7468 30.5993 20.0501 30.6004C25.9802 30.6004 30.811 25.7969 30.8143 19.8917C30.8165 13.9745 26.0085 9.1775 20.0588 9.17533C14.1243 9.17533 9.29674 13.9788 9.29457 19.883C9.29348 22.2934 10.0032 24.0982 11.1951 25.9865L10.1077 29.9385L14.1809 28.8758ZM26.5756 22.9564C26.4951 22.8221 26.2796 22.7419 25.9552 22.5805C25.6319 22.4191 24.0416 21.6402 23.7444 21.5329C23.4484 21.4257 23.2329 21.3715 23.0162 21.6943C22.8007 22.0161 22.1803 22.7419 21.992 22.9564C21.8036 23.1709 21.6142 23.198 21.291 23.0366C20.9677 22.8752 19.9249 22.5361 18.6894 21.4387C17.7283 20.585 17.0785 19.5309 16.8901 19.2081C16.7018 18.8863 16.8706 18.7119 17.0316 18.5516C17.1775 18.4075 17.3549 18.1757 17.5171 17.9872C17.6815 17.8008 17.7348 17.6665 17.8437 17.4509C17.9514 17.2364 17.8981 17.0479 17.8165 16.8865C17.7348 16.7262 17.0883 15.1413 16.8194 14.4967C16.556 13.8694 16.2893 13.9539 16.0912 13.9442L15.4707 13.9333C15.2552 13.9333 14.9047 14.0135 14.6086 14.3363C14.3126 14.6592 13.4766 15.437 13.4766 17.0219C13.4766 18.6068 14.6359 20.1376 14.797 20.3521C14.9591 20.5666 17.0774 23.8188 20.3222 25.213C21.0939 25.5445 21.697 25.7428 22.1661 25.8912C22.9411 26.136 23.6465 26.1013 24.2038 26.019C24.8253 25.9269 26.1174 25.2401 26.3873 24.4883C26.6573 23.7353 26.6573 23.0907 26.5756 22.9564Z" fill="white"></path><defs><linearGradient id="paint0_linear_2569_5464" x1="19.5928" y1="2.40043" x2="19.7955" y2="36.5829" gradientUnits="userSpaceOnUse"><stop stop-color="#57D163"></stop><stop offset="1" stop-color="#23B33A"></stop></linearGradient></defs></svg>
                                    </span>
                                @endif

                                <!-- Buy Now Button -->
                                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                                    {!! view_render_event('bagisto.shop.products.view.buy_now.before', ['product' => $product]) !!}

                                    @if (core()->getConfigData('catalog.products.storefront.buy_now_button_display'))
                                        <x-shop::button
                                            type="submit"
                                            class="primary-button mt-5 w-full max-w-[470px] max-md:py-3 max-sm:mt-3 max-sm:rounded-lg max-sm:py-1.5"
                                            button-type="primary-button"
                                            :title="trans('shop::app.products.view.buy-now')"
                                            :disabled="! $product->isSaleable(1)"
                                            ::loading="isStoring.buyNow"
                                            @click="is_buy_now=1;"
                                        />
                                    @endif

                                    {!! view_render_event('bagisto.shop.products.view.buy_now.after', ['product' => $product]) !!}
                                @endif

                                {!! view_render_event('bagisto.shop.products.view.additional_actions.before', ['product' => $product]) !!}

                                <!-- Share Buttons -->
                                <div class="mt-10 flex gap-9 max-md:mt-4 max-md:flex-wrap max-sm:justify-center max-sm:gap-3">
                                    {{-- {!! view_render_event('bagisto.shop.products.view.compare.before', ['product' => $product]) !!}

                                    <div
                                        class="flex cursor-pointer items-center justify-center gap-2.5 max-sm:gap-1.5 max-sm:text-base"
                                        role="button"
                                        tabindex="0"
                                        @click="is_buy_now=0; addToCompare({{ $product->id }})"
                                    >
                                        @if (core()->getConfigData('catalog.products.settings.compare_option'))
                                            <span
                                                class="icon-compare text-2xl"
                                                role="presentation"
                                            ></span>

                                            @lang('shop::app.products.view.compare')
                                        @endif
                                    </div>

                                    {!! view_render_event('bagisto.shop.products.view.compare.after', ['product' => $product]) !!} --}}
                                </div>

                                {!! view_render_event('bagisto.shop.products.view.additional_actions.after', ['product' => $product]) !!}
                            </div>
                        </div>
                    </div>
                </form>
            </x-shop::form>
        </script>

        <script type="module">
            app.component('v-product', {
                template: '#v-product-template',

                data() {
                    return {
                        isWishlist: Boolean("{{ (boolean) auth()->guard()->user()?->wishlist_items->where('channel_id', core()->getCurrentChannel()->id)->where('product_id', $product->id)->count() }}"),

                        isCustomer: '{{ auth()->guard('customer')->check() }}',

                        is_buy_now: 0,

                        isStoring: {
                            addToCart: false,

                            buyNow: false,
                        },
                    }
                },

                methods: {
                    addToCart(params) {
                        const operation = this.is_buy_now ? 'buyNow' : 'addToCart';

                        this.isStoring[operation] = true;

                        let formData = new FormData(this.$refs.formData);

                        this.ensureQuantity(formData);

                        this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            })
                            .then(response => {
                                if (response.data.message) {
                                    this.$emitter.emit('update-mini-cart', response.data.data);

                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                    if (response.data.redirect) {
                                        window.location.href= response.data.redirect;
                                    }
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                                }

                                this.isStoring[operation] = false;
                            })
                            .catch(error => {
                                this.isStoring[operation] = false;

                                this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.message });
                            });
                    },

                    orderOnWhatsApp() {
                        const url = window.location.href;
                        window.open(`https://wa.me/923006934360?text=I%20want%20to%20order%20this%20product:%20${encodeURIComponent(url)}`, '_blank');
                    },

                    addToWishlist() {
                        if (this.isCustomer) {
                            this.$axios.post('{{ route('shop.api.customers.account.wishlist.store') }}', {
                                    product_id: "{{ $product->id }}"
                                })
                                .then(response => {
                                    this.isWishlist = ! this.isWishlist;

                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                                })
                                .catch(error => {});
                        } else {
                            window.location.href = "{{ route('shop.customer.session.index')}}";
                        }
                    },

                    addToCompare(productId) {
                        /**
                         * This will handle for customers.
                         */
                        if (this.isCustomer) {
                            this.$axios.post('{{ route("shop.api.compare.store") }}', {
                                    'product_id': productId
                                })
                                .then(response => {
                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                                })
                                .catch(error => {
                                    if ([400, 422].includes(error.response.status)) {
                                        this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.data.message });

                                        return;
                                    }

                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message});
                                });

                            return;
                        }

                        /**
                         * This will handle for guests.
                         */
                        let existingItems = this.getStorageValue(this.getCompareItemsStorageKey()) ?? [];

                        if (existingItems.length) {
                            if (! existingItems.includes(productId)) {
                                existingItems.push(productId);

                                this.setStorageValue(this.getCompareItemsStorageKey(), existingItems);

                                this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.products.view.add-to-compare')" });
                            } else {
                                this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('shop::app.products.view.already-in-compare')" });
                            }
                        } else {
                            this.setStorageValue(this.getCompareItemsStorageKey(), [productId]);

                            this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.products.view.add-to-compare')" });
                        }
                    },

                    getCompareItemsStorageKey() {
                        return 'compare_items';
                    },

                    setStorageValue(key, value) {
                        localStorage.setItem(key, JSON.stringify(value));
                    },

                    getStorageValue(key) {
                        let value = localStorage.getItem(key);

                        if (value) {
                            value = JSON.parse(value);
                        }

                        return value;
                    },

                    scrollToReview() {
                        let accordianElement = document.querySelector('#review-accordian-button');

                        if (accordianElement) {
                            accordianElement.click();

                            accordianElement.scrollIntoView({
                                behavior: 'smooth'
                            });
                        }
                        
                        let tabElement = document.querySelector('#review-tab-button');

                        if (tabElement) {
                            tabElement.click();

                            tabElement.scrollIntoView({
                                behavior: 'smooth'
                            });
                        }
                    },

                    ensureQuantity(formData) {
                        if (! formData.has('quantity')) {
                            formData.append('quantity', 1);
                        }
                    },
                },
            });
        </script>
    @endPushOnce
</x-shop::layouts>
