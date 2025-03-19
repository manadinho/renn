@props([
    'hasHeader'  => true,
    'hasFeature' => true,
    'hasFooter'  => true,
])

<!DOCTYPE html>

<html
    lang="{{ app()->getLocale() }}"
    dir="{{ core()->getCurrentLocale()->direction }}"
>
    <head>

        {!! view_render_event('bagisto.shop.layout.head.before') !!}

        <title>{{ $title ?? '' }}</title>

        <meta charset="UTF-8">

        <meta
            http-equiv="X-UA-Compatible"
            content="IE=edge"
        >
        <meta
            http-equiv="content-language"
            content="{{ app()->getLocale() }}"
        >

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
        >
        <meta
            name="base-url"
            content="{{ url()->to('/') }}"
        >
        <meta
            name="currency"
            content="{{ core()->getCurrentCurrency()->toJson() }}"
        >

        @stack('meta')

        <link
            rel="icon"
            sizes="16x16"
            href="{{ core()->getCurrentChannel()->favicon_url ?? bagisto_asset('images/favicon.ico') }}"
        />

        @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])

        <link
            rel="preload"
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
            as="style"
        >
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        >

        <link
            rel="preload"
            href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap"
            as="style"
        >
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap"
        >

        @stack('styles')

        <style>
            {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
        </style>

        {!! view_render_event('bagisto.shop.layout.head.after') !!}

        <!-- Meta Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '568654015497243');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=568654015497243&ev=PageView&noscript=1"
        /></noscript>
        <!-- End Meta Pixel Code -->


        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-0KTKPP4BKR"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-0KTKPP4BKR');
        </script>
    </head>

    <body>
        {!! view_render_event('bagisto.shop.layout.body.before') !!}

        <a
            href="#main"
            class="skip-to-main-content-link"
        >
            Skip to main content
        </a>

        <div id="app">
            <!-- Flash Message Blade Component -->
            <x-shop::flash-group />

            <!-- Confirm Modal Blade Component -->
            <x-shop::modal.confirm />

            <!-- Page Header Blade Component -->
            @if ($hasHeader)
                <x-shop::layouts.header />
            @endif

            {!! view_render_event('bagisto.shop.layout.content.before') !!}

            <!-- Page Content Blade Component -->
            <main id="main" class="bg-white">
                {{ $slot }}
            </main>

            {!! view_render_event('bagisto.shop.layout.content.after') !!}


            <!-- Page Services Blade Component -->
            @if ($hasFeature)
                <x-shop::layouts.services />
            @endif

            <!-- Page Footer Blade Component -->
            @if ($hasFooter)
                <x-shop::layouts.footer />
            @endif
        </div>

        {!! view_render_event('bagisto.shop.layout.body.after') !!}

        @stack('scripts')

        {!! view_render_event('bagisto.shop.layout.vue-app-mount.before') !!}
        <script>
            /**
             * Load event, the purpose of using the event is to mount the application
             * after all of our `Vue` components which is present in blade file have
             * been registered in the app. No matter what `app.mount()` should be
             * called in the last.
             */
            window.addEventListener("load", function (event) {
                app.mount("#app");
            });
        </script>

        {!! view_render_event('bagisto.shop.layout.vue-app-mount.after') !!}

        <script type="text/javascript">
            {!! core()->getConfigData('general.content.custom_scripts.custom_javascript') !!}
        </script>
    </body>
</html>
