<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', config('app.name', 'Laravel App'))</title>

    <meta name="keywords"
        content="@yield('keywords', 'Iqbolshoh_dev, Iqbolshoh_777, ' . config('app.name', 'Laravel App'))">
    <meta name="description" content="@yield('description', 'Iqbolshoh - Full-Stack Developer from Samarkand!')">
    <meta name="author" content="Iqbolshoh_dev">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container mt-5">
        @yield('content')
    </div>
</body>

</html>