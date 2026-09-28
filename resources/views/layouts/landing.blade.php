<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', $description ?? 'Career School — Platform karier, portofolio karya, dan jejaring resmi SMK Negeri 1 Cirebon.')">
    <title>@yield('title', $title ?? 'Career School Smkn 1 Cirebon')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logos/logo-career-school.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sora:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Vendor Libraries -->
    <link rel="stylesheet" href="{{ asset('vendor/choices/choices.min.css') }}">
    <script src="{{ asset('vendor/choices/choices.min.js') }}"></script>

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-bg text-text-primary antialiased font-sans selection:bg-brand-primary selection:text-neutral-900 transition-colors duration-150 min-h-screen flex flex-col">

    <!-- Navigation Bar -->
    <x-landing.navbar />

    <!-- Main Dynamic Content -->
    <main class="flex-1">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

    <!-- Footer -->
    <x-landing.footer />

</body>
</html>
