<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>justFeast — Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brand:      #A31D1D;
            --brand-dark: #841313;
            --gold:       #FFC244;
            --bg:         #FFFDF9;
            --surface:    #FFFFFF;
            --surface2:   #F8FAFC;
            --border:     #E2E8F0;
            --text:       #0F172A;
            --muted:      #64748B;
            --error:      #EF4444;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1rem 4rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient background glows */
        body::before {
            content: '';
            position: fixed;
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(255, 194, 68, 0.22) 0%, rgba(163, 29, 29, 0.07) 45%, transparent 70%);
            top: -200px; left: -180px;
            pointer-events: none;
            filter: blur(80px);
        }

        body::after {
            content: '';
            position: fixed;
            width: 650px; height: 650px;
            background: radial-gradient(circle, rgba(163, 29, 29, 0.08) 0%, rgba(255, 194, 68, 0.15) 50%, transparent 70%);
            bottom: -180px; right: -180px;
            pointer-events: none;
            filter: blur(80px);
        }

        .layout {
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* ── Login Form Card ─────────────────────────────────────────── */
        .form-card {
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(15, 23, 42, 0.04);
            animation: slideUp 0.45s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 2rem;
        }
        .logo-icon {
            width: 46px; height: 46px;
            background: #FFFFFF;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            padding: 5px;
            box-shadow: 0 8px 20px rgba(163, 29, 29, 0.12);
            border: 1px solid var(--border);
        }
        .logo-icon img {
            width: 100%; height: 100%;
            object-fit: contain;
        }
        .logo-text { font-size: 1.55rem; font-weight: 900; letter-spacing: -0.5px; color: var(--text); }
        .logo-text span { color: var(--gold); }

        h1 { font-size: 1.7rem; font-weight: 900; margin-bottom: 0.35rem; letter-spacing: -0.4px; color: var(--text); }
        .subtitle { color: var(--muted); font-size: 0.88rem; margin-bottom: 1.8rem; font-weight: 600; }

        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FCA5A5;
            color: #991B1B;
            border-radius: 14px;
            padding: 0.85rem 1.1rem;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .field { margin-bottom: 1.2rem; }
        label {
            display: block;
            font-size: 0.73rem;
            font-weight: 800;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.5rem;
        }
        input[type="email"],
        input[type="text"],
        input[type="password"] {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 0.85rem 1.1rem;
            color: var(--text);
            font-size: 0.95rem;
            font-family: inherit;
            font-weight: 700;
            outline: none;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }
        input:focus {
            border-color: var(--brand);
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(163, 29, 29, 0.12);
        }
        input.is-error { border-color: var(--error); }
        .field-error { color: var(--error); font-size: 0.78rem; margin-top: 0.35rem; font-weight: 700; }

        .remember {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.6rem;
        }
        .remember input[type="checkbox"] { accent-color: var(--brand); cursor: pointer; width: 16px; height: 16px; }
        .remember label {
            font-size: 0.85rem;
            color: var(--muted);
            text-transform: none;
            letter-spacing: 0;
            cursor: pointer;
            margin: 0;
            font-weight: 700;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #A31D1D 0%, #841313 100%);
            color: #FFFFFF;
            border: 1px solid rgba(255, 194, 68, 0.3);
            border-radius: 14px;
            padding: 0.95rem;
            font-size: 0.98rem;
            font-weight: 900;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 24px rgba(163, 29, 29, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
        }
        .btn-login:hover  {
            background: linear-gradient(135deg, #841313 0%, #630C0C 100%);
            box-shadow: 0 12px 32px rgba(163, 29, 29, 0.45);
            transform: translateY(-1px);
        }
        .btn-login:active { transform: scale(0.98); }
        .btn-login:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
    </style>
</head>
<body>
    <div class="layout">

        {{-- ── Login Form ──────────────────────────────────────────────────── --}}
        <div class="form-card">
            <div class="logo">
                <div class="logo-icon">
                    <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo">
                </div>
                <div class="logo-text">just<span>Feast</span></div>
            </div>

            <h1>Welcome back</h1>
            <p class="subtitle">Sign in to access your account portal</p>

            @if ($errors->any())
                <div class="alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="field">
                    <label for="email">Email or Phone Number</label>
                    <input
                        type="text"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@justfeast.com or 07XXXXXXXX"
                        class="{{ $errors->has('email') ? 'is-error' : '' }}"
                        required autofocus
                    >
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password or OTP Code</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        class="{{ $errors->has('password') ? 'is-error' : '' }}"
                        required
                    >
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Keep me signed in</label>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <span>Sign In</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>

    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<span>Signing in...</span> <i class="fas fa-spinner fa-spin text-xs"></i>';
            btn.disabled  = true;
        });
    </script>
</body>
</html>
