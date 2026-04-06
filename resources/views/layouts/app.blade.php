<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
</head>

<body class="bg-light">
    @include('partials.navbar')
    <main class="container py-4">
        @yield('content')
    </main>
    @include('partials.footer')
</body>

</html>