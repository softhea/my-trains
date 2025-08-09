<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'My Train Shop')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Your custom styles -->
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>
<body>

  @include('partials.navbar')

  <main class="py-4">
    @yield('content')
  </main>

  @include('partials.footer')

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Your custom scripts -->
  <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>