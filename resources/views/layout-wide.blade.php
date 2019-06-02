<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @yield('head')
</head>
<body>
<div class="container-fluid">
    @component('parts.nav')
    @endcomponent
    @yield('contents')
</div>

@yield('footer')
</body>
</html>
