<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; background: #f7f7f7; color: #111; }
        .container { max-width: 1100px; margin: 0 auto; background: #fff; padding: 24px; border-radius: 8px; }
        .row { display: flex; gap: 12px; align-items: center; }
        .between { justify-content: space-between; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; vertical-align: top; }
        th { background: #fafafa; }
        .btn { display: inline-block; padding: 8px 12px; border: 1px solid #bbb; border-radius: 6px; background: #fff; text-decoration: none; color: #111; cursor: pointer; }
        .btn-primary { background: #111; color: #fff; border-color: #111; }
        .btn-danger { background: #c62828; color: #fff; border-color: #c62828; }
        .form-group { margin-bottom: 12px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; }
        .alert { padding: 10px 12px; border-radius: 6px; margin-bottom: 16px; }
        .alert-success { background: #e7f8ed; border: 1px solid #b6e3c4; }
        .error { color: #c62828; font-size: 13px; margin-top: 4px; }
    </style>
</head>
<body>
<div class="container">
    @auth('admin')
        <div class="row between" style="margin-bottom: 12px;">
            <div>Logged in as: <strong>{{ auth('admin')->user()->name }}</strong></div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn">Logout</button>
            </form>
        </div>
    @endauth

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @yield('content')
</div>
</body>
</html>
