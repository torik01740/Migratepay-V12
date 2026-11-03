<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MigratePay | Control Tower Admin</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body style="margin: 0; padding: 0; background-color: #0A1128; font-family: 'Inter', sans-serif;">
    <script>
        window.AUTH_USER = @json($authUser ?? null);
        window.APP_CONFIG = {
            portal: 'admin',
            csrfToken: '{{ csrf_token() }}',
            baseUrl: '{{ url('/') }}'
        };
    </script>
    <div id="app" data-portal="admin"></div>
</body>
</html>

