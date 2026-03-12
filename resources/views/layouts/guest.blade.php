@props(['title' => ''])

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name') }} | {{ $title ?? '' }}</title>
    <!-- Google Font (SAMA DENGAN DASHBOARD) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS -->
{{-- <link rel="stylesheet" href="{{ asset(path: 'assets/css/custom.css') }}"> --}}
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}?v={{ time() }}">


</head>

<body class="auth-page">

<div class="auth-wrapper">
    <div class="auth-card">

        {{ $slot }}

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
window.addEventListener("DOMContentLoaded", function () {
    setTimeout(function () {
        window.dispatchEvent(new Event("resize"));
    }, 60);
});
</script>


</body>
</html>
