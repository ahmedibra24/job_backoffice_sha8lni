<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'job-board-backoffice') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="flex">

        {{--! Sidebar  --}}
        @include('layouts.navigation')

        {{--! Main Content  --}}
        <div class="flex-1 min-h-screen bg-gray-100">
            {{--! Page Heading --}}
            @isset($header)
                <header class=" flex items-center py-2 pr-16 justify-between  bg-white shadow w-full">
                    <div class=" py-4 px-4">
                        {{ $header }}
                    </div>
                    @if (Auth::user()->role=="admin")
                        <div class="text-sm font-bold text-gray-600">
                            <p >{{ Auth::user()->name ?? 'user Name' }}</p>
                        </div>  
                    @else                  
                        <div class=" flex items-center">
                            <x-company-logo :logoUri="Auth::user()->companies->logoUri" :logoName="Auth::user()->companies->logoName" :companyName="Auth::user()->companies->name" class="w-8 h-8" />
                            <div class="text-xs text-gray-600">
                                <p >{{ Auth::user()->name ?? 'user Name' }}</p>
                                <p>{{ Auth::user()->companies->name ?? 'company Name' }}</p>
                            </div>
                        </div>
                    @endif
                    
                </header>
            @endisset

            {{--! Page Content --}}
            <main>
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>