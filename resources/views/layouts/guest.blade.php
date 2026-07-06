<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Track Rail') }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --rail-blue: #1d4ed8;
            --rail-blue-dark: #1e3a8a;
            --rail-green: #15803d;
            --rail-green-dark: #14532d;
            --rail-purple: #7c3aed;
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .btn-rail-blue {
            background-color: var(--rail-blue);
            border-color: var(--rail-blue);
            color: white;
        }
        .btn-rail-blue:hover {
            background-color: var(--rail-blue-dark);
            border-color: var(--rail-blue-dark);
            color: white;
        }
        .btn-rail-green {
            background-color: var(--rail-green);
            border-color: var(--rail-green);
            color: white;
        }
        .btn-rail-green:hover {
            background-color: var(--rail-green-dark);
            border-color: var(--rail-green-dark);
            color: white;
        }
        .btn-rail-purple {
            background-color: var(--rail-purple);
            border-color: var(--rail-purple);
            color: white;
        }
        .btn-rail-purple:hover {
            background-color: #6d28d9;
            border-color: #6d28d9;
            color: white;
        }
        .form-control-rail {
            background-color: #f3f4f6;
            border: none;
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            font-size: 1rem;
        }
        .form-control-rail:focus {
            background-color: #ffffff;
            box-shadow: 0 0 0 0.2rem rgba(29, 78, 216, 0.15);
            border-color: var(--rail-blue);
        }
        .role-btn {
            border-width: 2px;
            border-radius: 0.75rem;
            padding: 0.75rem;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .role-btn.active-passenger {
            border-color: var(--rail-blue);
            background-color: #eff6ff;
            color: var(--rail-blue);
        }
        .role-btn.active-station {
            border-color: var(--rail-green);
            background-color: #f0fdf4;
            color: var(--rail-green);
        }
        .logo-circle {
            width: 80px;
            height: 80px;
            background: var(--rail-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(29, 78, 216, 0.3);
            margin: 0 auto;
        }
        .logo-circle.green {
            background: var(--rail-green);
            box-shadow: 0 4px 15px rgba(21, 128, 61, 0.3);
        }
        .logo-circle.purple {
            background: var(--rail-purple);
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
        }
        .auth-card-wrapper {
            width: 100%;
            max-width: 420px;
            margin: 2rem auto;
        }
        .auth-card {
            background: white;
            border: none;
            border-radius: 1.25rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            padding: 2.5rem;
        }
    </style>
</head>
<body>
    <div class="min-vh-100 d-flex align-items-center justify-content-center py-4">
        <div class="auth-card-wrapper">
            {{ $slot }}
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>