<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @component('parts.head')
        @slot('title')
            @yield('title')
        @endslot
    @endcomponent
    @yield('head')
</head>
<body>
<div class="container">
    @yield('nav')
    @yield('contents')
</div>
@include('parts.footer')
@yield('script')
</body>
</html>
