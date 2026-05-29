@php
// SEO Head Component — usage: <x-seo title="..." description="..." />
$siteName   = $companyName ?? 'Constructive Cleaning Company LTD';
$baseUrl    = rtrim(config('app.url'), '/');

$rawUrl     = (request()->secure() ? 'https' : 'http').'://'.request()->getHost().request()->getRequestUri();
$currentUrl = $canonical ?? strtok($rawUrl, '?');

$defaultDesc = 'Constructive Cleaning Company LTD — professional land management, debris removal, rural development, development advisory, and licensed international metal trading across Trinidad & Tobago.';
$metaDesc    = $description ?? $defaultDesc;

$pageTitle  = (isset($title) && $title)
    ? $title.' | '.$siteName
    : $siteName.' — Efficient, Constructive, United';

$logoUrl    = $baseUrl.'/images/ccc_ops_logo.png';
$ogImage    = isset($image) && $image
    ? (str_starts_with($image, 'http') ? $image : $baseUrl.'/'.$image)
    : $logoUrl;

$ogType     = $type ?? 'website';
$doNoindex  = $noindex ?? false;

$phone   = \App\Models\Setting::get('company_phone', '');
$email   = \App\Models\Setting::get('company_email', '');
$slogan  = \App\Models\Setting::get('company_slogan', 'Efficiency, Constructiveness & Unity — As Far As The Eyes Can See');

$ldJson = [
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'         => 'LocalBusiness',
            '@id'           => $baseUrl.'/#organization',
            'name'          => $siteName,
            'alternateName' => 'CCC',
            'url'           => $baseUrl,
            'logo'          => ['@type' => 'ImageObject', 'url' => $logoUrl, 'width' => 1254, 'height' => 1254],
            'image'         => $logoUrl,
            'description'   => $defaultDesc,
            'slogan'        => $slogan,
            'address'       => ['@type' => 'PostalAddress', 'addressCountry' => 'TT', 'addressRegion' => 'Trinidad and Tobago'],
            'contactPoint'  => [['@type' => 'ContactPoint', 'telephone' => $phone, 'email' => $email, 'contactType' => 'customer service', 'availableLanguage' => 'English', 'areaServed' => ['TT', 'Caribbean']]],
            'areaServed'    => ['@type' => 'GeoCircle', 'geoMidpoint' => ['@type' => 'GeoCoordinates', 'latitude' => 10.6549, 'longitude' => -61.5019], 'geoRadius' => '500000'],
            'hasOfferCatalog' => [
                '@type'          => 'OfferCatalog',
                'name'           => 'Services',
                'itemListElement' => [
                    ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Development Advisory']],
                    ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Rural Development']],
                    ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Debris Cleaning and Removal']],
                    ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Land Maintenance']],
                    ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Licensed International Metal Trading']],
                ],
            ],
            'sameAs' => [],
        ],
        [
            '@type'           => 'WebSite',
            '@id'             => $baseUrl.'/#website',
            'url'             => $baseUrl,
            'name'            => $siteName,
            'publisher'       => ['@id' => $baseUrl.'/#organization'],
            'potentialAction' => ['@type' => 'SearchAction', 'target' => ['@type' => 'EntryPoint', 'urlTemplate' => $baseUrl.'/services?search={search_term_string}'], 'query-input' => 'required name=search_term_string'],
        ],
        [
            '@type'       => 'WebPage',
            '@id'         => $currentUrl.'#webpage',
            'url'         => $currentUrl,
            'name'        => $pageTitle,
            'isPartOf'    => ['@id' => $baseUrl.'/#website'],
            'about'       => ['@id' => $baseUrl.'/#organization'],
            'description' => Str::limit($metaDesc, 200),
            'inLanguage'  => 'en-TT',
        ],
    ],
];
@endphp

{{-- Primary Meta --}}
<title>{{ $pageTitle }}</title>
<meta name="description"   content="{{ Str::limit($metaDesc, 160) }}" />
<meta name="keywords"      content="land management Trinidad, debris removal, land clearing, rural development, metal trading Trinidad, cleaning company TT, Constructive Cleaning" />
<meta name="author"        content="{{ $siteName }}" />
<meta name="robots"        content="{{ $doNoindex ? 'noindex, nofollow' : 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1' }}" />
<meta name="theme-color"   content="#1d4ed8" />
<link rel="canonical"      href="{{ $currentUrl }}" />

{{-- Favicon --}}
<link rel="icon"             href="{{ asset('images/ccc_ops_logo.png') }}" type="image/png" />
<link rel="shortcut icon"    href="{{ asset('images/ccc_ops_logo.png') }}" type="image/png" />
<link rel="apple-touch-icon" href="{{ asset('images/ccc_ops_logo.png') }}" />
<meta name="msapplication-TileImage" content="{{ asset('images/ccc_ops_logo.png') }}" />
<meta name="msapplication-TileColor" content="#1d4ed8" />

{{-- Open Graph --}}
<meta property="og:type"         content="{{ $ogType }}" />
<meta property="og:site_name"    content="{{ $siteName }}" />
<meta property="og:title"        content="{{ $pageTitle }}" />
<meta property="og:description"  content="{{ Str::limit($metaDesc, 200) }}" />
<meta property="og:url"          content="{{ $currentUrl }}" />
<meta property="og:image"        content="{{ $ogImage }}" />
<meta property="og:image:width"  content="1254" />
<meta property="og:image:height" content="1254" />
<meta property="og:image:alt"    content="{{ $siteName }} logo" />
<meta property="og:locale"       content="en_TT" />

{{-- Twitter / X Cards --}}
<meta name="twitter:card"        content="summary_large_image" />
<meta name="twitter:title"       content="{{ $pageTitle }}" />
<meta name="twitter:description" content="{{ Str::limit($metaDesc, 200) }}" />
<meta name="twitter:image"       content="{{ $ogImage }}" />
<meta name="twitter:image:alt"   content="{{ $siteName }}" />

{{-- Geo --}}
<meta name="geo.region"    content="TT" />
<meta name="geo.placename" content="Trinidad and Tobago" />
<meta name="ICBM"          content="10.6549, -61.5019" />

{{-- JSON-LD Structured Data (built in PHP array above, safe-encoded here) --}}
<script type="application/ld+json">{!! json_encode($ldJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
