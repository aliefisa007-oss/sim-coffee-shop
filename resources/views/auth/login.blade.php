<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Contact Coffee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'DM Sans', 'Segoe UI', system-ui, sans-serif; }

        body {
            background: #0f1117;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Pattern biji kopi samar di background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            opacity: 0.035;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Cg fill='none' stroke='%23c8a97e' stroke-width='2'%3E%3Cellipse cx='30' cy='30' rx='16' ry='11' transform='rotate(-30 30 30)'/%3E%3Cpath d='M20 30 Q30 24 40 30' transform='rotate(-30 30 30)'/%3E%3Cellipse cx='90' cy='80' rx='16' ry='11' transform='rotate(35 90 80)'/%3E%3Cpath d='M80 80 Q90 74 100 80' transform='rotate(35 90 80)'/%3E%3C/g%3E%3C/svg%3E");
            background-size: 120px 120px;
        }

        /* Glow ambient di belakang card */
        .glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
        }
        .glow-1 {
            width: 420px; height: 420px;
            background: rgba(200,169,126,0.16);
            top: -120px; left: -100px;
        }
        .glow-2 {
            width: 360px; height: 360px;
            background: rgba(91,141,238,0.10);
            bottom: -140px; right: -100px;
        }

        .login-card {
            position: relative;
            z-index: 1;
            background: #161920;
            border: 1px solid #23262f;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.45), 0 1px 3px rgba(0,0,0,0.3);
            animation: cardIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(18px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .brand { color: #c8a97e; font-weight: 700; letter-spacing: 0.1em; }

        .login-card img {
            animation: logoIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
        }
        @keyframes logoIn {
            from { opacity: 0; transform: scale(0.85); }
            to { opacity: 1; transform: scale(1); }
        }

        .form-control {
            background: #1a1d27 !important;
            border: 1px solid #2a2d38 !important;
            color: #e8e6e0 !important;
            border-radius: 8px !important;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-control:focus {
            border-color: #c8a97e !important;
            box-shadow: 0 0 0 3px rgba(200,169,126,0.1) !important;
        }

        .btn-login {
            background: linear-gradient(135deg, #c8a97e, #a87d50);
            border: none;
            color: #1a1208;
            font-weight: 700;
            border-radius: 8px;
            padding: 12px;
            width: 100%;
            font-size: 14px;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(200,169,126,0.25);
            filter: brightness(1.05);
        }
        .btn-login:active {
            transform: translateY(0);
        }

        label { color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; }
    </style>
</head>
<body>
<div class="glow glow-1"></div>
<div class="glow glow-2"></div>

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
