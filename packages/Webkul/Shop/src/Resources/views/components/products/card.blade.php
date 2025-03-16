<v-product-card
    {{ $attributes }}
    :product="product"
>
</v-product-card>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-card-template"
    >
        <!-- Grid Card -->
        <div
            class="1180:transtion-all group w-full rounded-md 1180:relative 1180:grid 1180:content-start 1180:overflow-hidden 1180:duration-300 1180:hover:shadow-[0_5px_10px_rgba(0,0,0,0.1)]"
            v-if="mode != 'list'"
        >
            <div class="relative max-h-[300px] max-w-[291px] overflow-hidden max-md:max-h-60 max-md:max-w-full max-md:rounded-lg max-sm:max-h-[200px] max-sm:max-w-full">
                {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

                <!-- Product Image -->
                <a
                    :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`"
                    :aria-label="product.name + ' '"
                >
                    <x-shop::media.images.lazy
                        class="after:content-[' '] relative bg-zinc-100 transition-all duration-300 after:block after:pb-[calc(100%+9px)] group-hover:scale-105"
                        ::src="product.base_image.medium_image_url"
                        ::key="product.id"
                        ::index="product.id"
                        width="291"
                        height="300"
                        ::alt="product.name"
                    />
                </a>

                {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}
                
                <!-- Product Ratings -->
                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}

                @if (core()->getConfigData('catalog.products.review.summary') == 'star_counts')
                    <x-shop::products.ratings
                        class="absolute bottom-1.5 items-center !border-white bg-white/80 !px-2 !py-1 text-xs max-sm:!px-1.5 max-sm:!py-0.5 ltr:left-1.5 rtl:right-1.5"
                        ::average="product.ratings.average"
                        ::total="product.ratings.total"
                        ::rating="false"
                        v-if="product.ratings.total"
                    />
                @else
                    <x-shop::products.ratings
                        class="absolute bottom-1.5 items-center !border-white bg-white/80 !px-2 !py-1 text-xs max-sm:!px-1.5 max-sm:!py-0.5 ltr:left-1.5 rtl:right-1.5"
                        ::average="product.ratings.average"
                        ::total="product.reviews.total"
                        ::rating="false"
                        v-if="product.reviews.total"
                    />
                @endif

                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}

                <div class="action-items bg-black">
                    <!-- Product Sale Badge -->
                    <p
                        class="absolute top-1.5 inline-block rounded-[44px] bg-red-600 px-2.5 text-sm text-white max-sm:rounded-l-none max-sm:rounded-r-xl max-sm:px-2 max-sm:py-0.5 max-sm:text-xs ltr:left-1.5 max-sm:ltr:left-0 rtl:right-5 max-sm:rtl:right-0"
                        v-if="product.on_sale"
                    >
                        @lang('shop::app.components.products.card.sale')
                    </p>

                    <!-- Product New Badge -->
                    <p
                        class="absolute top-1.5 inline-block rounded-[44px] bg-navyBlue px-2.5 text-sm text-white max-sm:rounded-l-none max-sm:rounded-r-xl max-sm:px-2 max-sm:py-0.5 max-sm:text-xs ltr:left-1.5 max-sm:ltr:left-0 rtl:right-1.5 max-sm:rtl:right-0"
                        v-else-if="product.is_new"
                    >
                        @lang('shop::app.components.products.card.new')
                    </p>

                    <div class="opacity-0 transition-all duration-300 group-hover:bottom-0 group-hover:opacity-100 max-lg:opacity-100 max-sm:opacity-100">

                        {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}

                        @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                            <span
                                class="absolute top-2.5 flex h-6 w-6 items-center justify-center rounded-full border border-zinc-200 bg-white text-lg md:hidden ltr:right-1.5 rtl:left-1.5"
                                role="button"
                                aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                                tabindex="0"
                                :class="product.is_wishlist ? 'icon-heart-fill text-red-500' : 'icon-heart'"
                                @click="addToWishlist()"
                            >
                            </span>
                        @endif

                        {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

                        @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                            {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}
                            <span
                                class="icon-cart top-10 absolute flex h-6 w-6 items-center justify-center rounded-full border border-zinc-200 bg-white text-lg sm:hidden ltr:right-1.5 rtl:left-1.5"
                                role="button"
                                :disabled="! product.is_saleable || isAddingToCart"
                                tabindex="0"
                                @click="addToCart()"
                            >
                            </span>
                            {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}
                        @endif
                        @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                            <span
                                class="absolute flex h-6 w-6 items-center justify-center rounded-full border border-zinc-200 bg-white text-lg sm:hidden ltr:right-1.5 rtl:left-1.5"
                                role="button"
                                style="top: 4.3rem;"
                                :disabled="! product.is_saleable || isAddingToCart"
                                tabindex="0"
                                @click="orderOnWhatsApp(`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`)"
                            >
                                @include('social_share::icons.whatsapp')
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Product Information Section -->
            <div class="-mt-9 grid max-w-[291px] translate-y-9 content-start gap-2.5 bg-white p-2.5 transition-transform duration-300 ease-out group-hover:-translate-y-0 group-hover:rounded-t-lg max-md:relative max-md:mt-0 max-md:translate-y-0 max-md:gap-0 max-md:px-0 max-md:py-1.5 max-sm:min-w-[170px] max-sm:max-w-[192px]">

                {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}
                    
                <p class="text-base font-medium max-md:mb-1.5 max-md:max-w-56 max-md:whitespace-break-spaces max-md:leading-6 max-sm:max-w-[192px] max-sm:text-sm max-sm:leading-4">
                    @{{ product.name }}
                </p>

                {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

                <!-- Pricing -->
                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

                <div
                    class="flex items-center gap-2.5 text-lg font-semibold max-sm:text-sm max-sm:leading-6"
                    v-html="product.price_html"
                >
                </div>

                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

                <!-- Product Actions Section -->
                <div class="action-items flex items-center justify-between opacity-0 transition-all duration-300 ease-in-out group-hover:opacity-100 max-md:hidden">
                    @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                        {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}

                        <button
                            class="secondary-button w-full max-w-full p-2.5 text-sm font-medium max-sm:rounded-xl max-sm:p-2"
                            :disabled="! product.is_saleable || isAddingToCart"
                            @click="addToCart()"
                        >
                            @lang('shop::app.components.products.card.add-to-cart')
                        </button>

                        {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}
                    @endif
                    
                    {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}

                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                        <span
                            class="cursor-pointer p-2.5 text-2xl max-sm:hidden"
                            role="button"
                            aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                            tabindex="0"
                            :class="product.is_wishlist ? 'icon-heart-fill text-red-600' : 'icon-heart'"
                            @click="addToWishlist()"
                        >
                        </span>
                    @endif

                    {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

                    {!! view_render_event('bagisto.shop.components.products.card.compare_option.before') !!}

                    @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                        <span
                            class="cursor-pointer p-2.5 text-2xl max-sm:hidden"
                            role="button"
                            style="top: 4.3rem;"
                            :disabled="! product.is_saleable || isAddingToCart"
                            tabindex="0"
                            @click="orderOnWhatsApp(`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`)"
                        >
                            <svg style="background: #00cd3b;border-radius: 20px;padding: 1px" width="30" height="30" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" rx="20" fill="url(#paint0_linear_2569_5464)"></rect><path d="M7 33L8.83631 26.3234C7.70317 24.3691 7.10776 22.1537 7.10885 19.8819C7.11212 12.7796 12.9193 7 20.0544 7C23.517 7.00108 26.7672 8.34333 29.212 10.7787C31.6557 13.214 33.0011 16.451 33 19.8938C32.9967 26.9973 27.1896 32.7768 20.0544 32.7768C17.8883 32.7758 15.7537 32.2352 13.863 31.2082L7 33ZM14.1809 28.8758C16.0052 29.9537 17.7468 30.5993 20.0501 30.6004C25.9802 30.6004 30.811 25.7969 30.8143 19.8917C30.8165 13.9745 26.0085 9.1775 20.0588 9.17533C14.1243 9.17533 9.29674 13.9788 9.29457 19.883C9.29348 22.2934 10.0032 24.0982 11.1951 25.9865L10.1077 29.9385L14.1809 28.8758ZM26.5756 22.9564C26.4951 22.8221 26.2796 22.7419 25.9552 22.5805C25.6319 22.4191 24.0416 21.6402 23.7444 21.5329C23.4484 21.4257 23.2329 21.3715 23.0162 21.6943C22.8007 22.0161 22.1803 22.7419 21.992 22.9564C21.8036 23.1709 21.6142 23.198 21.291 23.0366C20.9677 22.8752 19.9249 22.5361 18.6894 21.4387C17.7283 20.585 17.0785 19.5309 16.8901 19.2081C16.7018 18.8863 16.8706 18.7119 17.0316 18.5516C17.1775 18.4075 17.3549 18.1757 17.5171 17.9872C17.6815 17.8008 17.7348 17.6665 17.8437 17.4509C17.9514 17.2364 17.8981 17.0479 17.8165 16.8865C17.7348 16.7262 17.0883 15.1413 16.8194 14.4967C16.556 13.8694 16.2893 13.9539 16.0912 13.9442L15.4707 13.9333C15.2552 13.9333 14.9047 14.0135 14.6086 14.3363C14.3126 14.6592 13.4766 15.437 13.4766 17.0219C13.4766 18.6068 14.6359 20.1376 14.797 20.3521C14.9591 20.5666 17.0774 23.8188 20.3222 25.213C21.0939 25.5445 21.697 25.7428 22.1661 25.8912C22.9411 26.136 23.6465 26.1013 24.2038 26.019C24.8253 25.9269 26.1174 25.2401 26.3873 24.4883C26.6573 23.7353 26.6573 23.0907 26.5756 22.9564Z" fill="white"></path><defs><linearGradient id="paint0_linear_2569_5464" x1="19.5928" y1="2.40043" x2="19.7955" y2="36.5829" gradientUnits="userSpaceOnUse"><stop stop-color="#57D163"></stop><stop offset="1" stop-color="#23B33A"></stop></linearGradient></defs></svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- List Card -->
        <div
            class="relative flex max-w-max grid-cols-2 gap-4 overflow-hidden rounded max-sm:flex-wrap"
            v-else
        >
            <div class="group relative max-h-[258px] max-w-[250px] overflow-hidden"> 

                {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

                <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`">
                    <x-shop::media.images.lazy
                        class="after:content-[' '] relative min-w-[250px] bg-zinc-100 transition-all duration-300 after:block after:pb-[calc(100%+9px)] group-hover:scale-105"
                        ::src="product.base_image.medium_image_url"
                        ::key="product.id"
                        ::index="product.id"
                        width="291"
                        height="300"
                        ::alt="product.name"
                    />
                </a>

                {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

                <div class="action-items bg-black">
                    <p
                        class="absolute top-5 inline-block rounded-[44px] bg-red-500 px-2.5 text-sm text-white ltr:left-5 max-sm:ltr:left-2 rtl:right-5"
                        v-if="product.on_sale"
                    >
                        @lang('shop::app.components.products.card.sale')
                    </p>

                    <p
                        class="absolute top-5 inline-block rounded-[44px] bg-navyBlue px-2.5 text-sm text-white ltr:left-5 max-sm:ltr:left-2 rtl:right-5"
                        v-else-if="product.is_new"
                    >
                        @lang('shop::app.components.products.card.new')
                    </p>

                    <div class="opacity-0 transition-all duration-300 group-hover:bottom-0 group-hover:opacity-100 max-sm:opacity-100">

                        {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}

                        @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                            <span 
                                class="absolute top-5 flex h-[30px] w-[30px] cursor-pointer items-center justify-center rounded-md bg-white text-2xl ltr:right-5 rtl:left-5"
                                role="button"
                                aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                                tabindex="0"
                                :class="product.is_wishlist ? 'icon-heart-fill text-red-600' : 'icon-heart'"
                                @click="addToWishlist()"
                            >
                            </span>
                        @endif

                        {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

                        {!! view_render_event('bagisto.shop.components.products.card.compare_option.before') !!}

                        @if (core()->getConfigData('catalog.products.settings.compare_option'))
                            <span
                                class="icon-compare absolute top-16 flex h-[30px] w-[30px] cursor-pointer items-center justify-center rounded-md bg-white text-2xl ltr:right-5 rtl:left-5"
                                role="button"
                                aria-label="@lang('shop::app.components.products.card.add-to-compare')"
                                tabindex="0"
                                @click="addToCompare(product.id)"
                            >
                            </span>
                        @endif

                        {!! view_render_event('bagisto.shop.components.products.card.compare_option.after') !!}
                    </div>
                </div>
            </div>

            <div class="grid content-start gap-4">

                {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}

                <p class="text-base">
                    @{{ product.name }}
                </p>

                {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

                <div
                    class="flex gap-2.5 text-lg font-semibold"
                    v-html="product.price_html"
                >
                </div>

                {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

                <!-- Needs to implement that in future -->
                <div class="flex hidden gap-4">
                    <span class="block h-[30px] w-[30px] rounded-full bg-[#B5DCB4]">
                    </span>

                    <span class="block h-[30px] w-[30px] rounded-full bg-zinc-500">
                    </span>
                </div>

                {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}

                <p class="text-sm text-zinc-500">
                    <template  v-if="! product.ratings.total">
                        <p class="text-sm text-zinc-500">
                            @lang('shop::app.components.products.card.review-description')
                        </p>
                    </template>

                    <template v-else>
                        @if (core()->getConfigData('catalog.products.review.summary') == 'star_counts')
                            <x-shop::products.ratings
                                ::average="product.ratings.average"
                                ::total="product.ratings.total"
                                ::rating="false"
                            />
                        @else
                            <x-shop::products.ratings
                                ::average="product.ratings.average"
                                ::total="product.reviews.total"
                                ::rating="false"
                            />
                        @endif
                    </template>
                </p>

                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}

                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))

                    {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}

                    <x-shop::button
                        class="primary-button whitespace-nowrap px-8 py-2.5"
                        :title="trans('shop::app.components.products.card.add-to-cart')"
                        ::loading="isAddingToCart"
                        ::disabled="! product.is_saleable || isAddingToCart"
                        @click="addToCart()"
                    />

                    {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}

                @endif
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-product-card', {
            template: '#v-product-card-template',

            props: ['mode', 'product'],

            data() {
                return {
                    isCustomer: '{{ auth()->guard('customer')->check() }}',

                    isAddingToCart: false,
                }
            },

            methods: {
                addToWishlist() {
                    if (this.isCustomer) {
                        this.$axios.post(`{{ route('shop.api.customers.account.wishlist.store') }}`, {
                                product_id: this.product.id
                            })
                            .then(response => {
                                this.product.is_wishlist = ! this.product.is_wishlist;

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
                    let items = this.getStorageValue() ?? [];

                    if (items.length) {
                        if (! items.includes(productId)) {
                            items.push(productId);

                            localStorage.setItem('compare_items', JSON.stringify(items));

                            this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });
                        } else {
                            this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('shop::app.components.products.card.already-in-compare')" });
                        }
                    } else {
                        localStorage.setItem('compare_items', JSON.stringify([productId]));

                        this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.components.products.card.add-to-compare-success')" });

                    }
                },

                getStorageValue(key) {
                    let value = localStorage.getItem('compare_items');

                    if (! value) {
                        return [];
                    }

                    return JSON.parse(value);
                },

                orderOnWhatsApp(url) {
                    window.open(`https://wa.me/923006934360?text=I%20want%20to%20order%20this%20product:%20${encodeURIComponent(url)}`, '_blank');
                },

                addToCart() {
                    this.isAddingToCart = true;

                    this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', {
                            'quantity': 1,
                            'product_id': this.product.id,
                        })
                        .then(response => {
                            if (response.data.message) {
                                this.$emitter.emit('update-mini-cart', response.data.data );

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            } else {
                                this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                            }

                            this.isAddingToCart = false;
                        })
                        .catch(error => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                            if (error.response.data.redirect_uri) {
                                window.location.href = error.response.data.redirect_uri;
                            }
                            
                            this.isAddingToCart = false;
                        });
                },
            },
        });
    </script>
@endpushOnce