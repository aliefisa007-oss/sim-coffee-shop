<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Contact Coffee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #0f1117; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #161920; border: 1px solid #23262f; border-radius: 16px; padding: 40px; width: 100%; max-width: 400px; }
        .brand { color: #c8a97e; font-weight: 700; letter-spacing: 0.1em; }
        .form-control { background: #1a1d27 !important; border: 1px solid #2a2d38 !important; color: #e8e6e0 !important; border-radius: 8px !important; }
        .form-control:focus { border-color: #c8a97e !important; box-shadow: 0 0 0 3px rgba(200,169,126,0.1) !important; }
        .btn-login { background: linear-gradient(135deg, #c8a97e, #a87d50); border: none; color: #1a1208; font-weight: 700; border-radius: 8px; padding: 12px; width: 100%; font-size: 14px; cursor: pointer; }
        label { color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="text-center mb-4">
        <div class="text-center mb-2">
    <img src="{{ asset('images/logo.png') }}"
         alt="Contact Coffee"
         style="width:80px; height:80px; border-radius:50%; object-fit:cover;">
</div>
<div class="brand fs-5">CONTACT COFFEE</div>
        <div style="color:#555; font-size:12px; margin-top:4px;">Sistem Informasi Manajemen</div>
    </div>
    @if($errors->any())
        <div class="alert py-2 px-3 mb-3" style="background:#2a1414; border:1px solid #5a2020; color:#e07c7c; border-radius:8px; font-size:13px;">
            {{ $errors->first() }}
        </div>
    @endif
    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control mt-1" value="{{ old('email') }}" required autofocus placeholder="owner@contactcoffee.com">
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control mt-1" required placeholder="••••••••">
        </div>
        <div class="mb-4 d-flex align-items-center gap-2">
            <input type="checkbox" name="remember" id="remember" class="form-check-input">
            <label for="remember" class="form-check-label" style="font-size:13px; color:#666; text-transform:none;">Ingat saya</label>
        </div>
        <button type="submit" class="btn-login">MASUK</button>
    </form>
</div>
</body>
</html>