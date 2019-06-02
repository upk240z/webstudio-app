<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @yield('head')
</head>
<body>
<div class="container">
    @yield('contents')
</div>

@yield('footer')
</body>
</html>
