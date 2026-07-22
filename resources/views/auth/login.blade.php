<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JustFeast — Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brand:      #FF6B00;
            --brand-dark: #e05a00;
            --bg:         #0d0d0d;
            --surface:    #181818;
            --surface2:   #202020;
            --border:     rgba(255,255,255,0.08);
            --text:       #f0f0f0;
            --muted:      #777;
            --error:      #ff4d4f;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 2rem 1rem 4rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(255,107,0,0.12) 0%, transparent 70%);
            top: -150px; left: -150px;
            pointer-events: none;
            filter: blur(80px);
        }

        .layout {
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 2rem;
            width: 100%;
            max-width: 1000px;
            position: relative;
            z-index: 1;
            align-items: start;
        }

        @media (max-width: 780px) {
            .layout { grid-template-columns: 1fr; }
        }

        /* ── Left: Login Form ─────────────────────────────────────────────── */
        .form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.2rem;
            box-shadow: 0 24px 80px rgba(0,0,0,0.5);
            animation: slideUp 0.4s ease;
            position: sticky;
            top: 2rem;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.8rem;
        }
        .logo-icon {
            width: 38px; height: 38px;
            background: var(--brand);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
        .logo-text { font-size: 1.3rem; font-weight: 800; letter-spacing: -0.5px; }
        .logo-text span { color: var(--brand); }

        h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.3rem; letter-spacing: -0.4px; }
        .subtitle { color: var(--muted); font-size: 0.85rem; margin-bottom: 1.6rem; }

        .alert-error {
            background: rgba(255,77,79,0.1);
            border: 1px solid rgba(255,77,79,0.3);
            color: var(--error);
            border-radius: 10px;
            padding: 0.7rem 0.9rem;
            font-size: 0.83rem;
            margin-bottom: 1.1rem;
        }

        .field { margin-bottom: 1rem; }
        label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.45rem;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: var(--text);
            font-size: 0.92rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(255,107,0,0.15);
        }
        input.is-error { border-color: var(--error); }
        .field-error { color: var(--error); font-size: 0.76rem; margin-top: 0.3rem; }

        .remember {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.3rem;
        }
        .remember input[type="checkbox"] { accent-color: var(--brand); cursor: pointer; }
        .remember label {
            font-size: 0.83rem;
            color: var(--muted);
            text-transform: none;
            letter-spacing: 0;
            cursor: pointer;
            margin: 0;
        }

        .btn-login {
            width: 100%;
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.82rem;
            font-size: 0.93rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
            box-shadow: 0 4px 20px rgba(255,107,0,0.35);
        }
        .btn-login:hover  { background: var(--brand-dark); box-shadow: 0 6px 28px rgba(255,107,0,0.5); }
        .btn-login:active { transform: scale(0.98); }
        .btn-login:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* ── Right: User Directory ────────────────────────────────────────── */
        .directory {
            animation: slideUp 0.5s ease 0.1s both;
        }

        .dir-title {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--muted);
            margin-bottom: 1rem;
            padding-left: 0.2rem;
        }

        .role-group { margin-bottom: 1.4rem; }

        .role-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.55rem;
        }
        .role-pill {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 2px 8px;
            border-radius: 20px;
        }
        .pill-admin  { background: rgba(255,107,0,0.15); color: #FF6B00; border: 1px solid rgba(255,107,0,0.25); }
        .pill-vendor { background: rgba(99,102,241,0.15); color: #818cf8; border: 1px solid rgba(99,102,241,0.25); }
        .pill-runner { background: rgba(34,197,94,0.15);  color: #4ade80; border: 1px solid rgba(34,197,94,0.25); }
        .pill-client { background: rgba(14,165,233,0.15); color: #38bdf8; border: 1px solid rgba(14,165,233,0.25); }

        .user-grid {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .user-card {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s, transform 0.1s;
            text-align: left;
            width: 100%;
            font-family: inherit;
        }
        .user-card:hover {
            background: rgba(255,107,0,0.06);
            border-color: rgba(255,107,0,0.25);
            transform: translateX(3px);
        }
        .user-card:active { transform: translateX(3px) scale(0.99); }

        .user-avatar {
            width: 34px; height: 34px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            flex-shrink: 0;
            color: #fff;
        }
        .avatar-admin  { background: linear-gradient(135deg, #FF6B00, #ff9a3c); }
        .avatar-vendor { background: linear-gradient(135deg, #6366f1, #818cf8); }
        .avatar-runner { background: linear-gradient(135deg, #16a34a, #4ade80); }
        .avatar-client { background: linear-gradient(135deg, #0284c7, #38bdf8); }

        .user-info { flex: 1; min-width: 0; }
        .user-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-email {
            font-size: 0.75rem;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fill-icon {
            color: var(--muted);
            font-size: 0.7rem;
            flex-shrink: 0;
            transition: color 0.15s;
        }
        .user-card:hover .fill-icon { color: var(--brand); }

        .hint {
            text-align: center;
            color: var(--muted);
            font-size: 0.73rem;
            margin-top: 0.6rem;
        }
    </style>
</head>
<body>
    <div class="layout">

        {{-- ── Login Form ──────────────────────────────────────────────────── --}}
        <div class="form-card">
            <div class="logo">
                <div class="logo-icon" style="overflow: hidden; background: white; border: 1px solid var(--border);">
                    <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" style="width: 100%; height: 100%; object-fit: contain; padding: 4px; border-radius: 9px;">
                </div>
                <div class="logo-text">Just<span>Feast</span></div>
            </div>

            <h1>Welcome back</h1>
            <p class="subtitle">Sign in to access your account</p>

            @if ($errors->any())
                <div class="alert-error">⚠️ {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="field">
                    <label for="email">Email address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@justfeast.com"
                        class="{{ $errors->has('email') ? 'is-error' : '' }}"
                        required autofocus
                    >
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>
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

                <button type="submit" class="btn-login" id="loginBtn">Sign In</button>
            </form>
        </div>

        {{-- ── User Directory ───────────────────────────────────────────────── --}}
        <div class="directory">
            <div class="dir-title">👤 Select your account</div>

            @php
                $roleOrder = ['admin', 'vendor', 'runner', 'client'];
                $roleIcons = ['admin' => '⚙️', 'vendor' => '🍽️', 'runner' => '🛵', 'client' => '👤'];
            @endphp

            @foreach ($roleOrder as $role)
                @if ($users->has($role))
                    <div class="role-group">
                        <div class="role-label">
                            <span class="role-pill pill-{{ $role }}">{{ ucfirst($role) }}s</span>
                        </div>
                        <div class="user-grid">
                            @foreach ($users[$role] as $user)
                                <button
                                    class="user-card"
                                    type="button"
                                    onclick="fillLogin('{{ $user->email }}')"
                                    id="user-card-{{ $user->id }}"
                                    title="Sign in as {{ $user->name }}"
                                >
                                    <div class="user-avatar avatar-{{ $role }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="user-info">
                                        <div class="user-name">{{ $user->name }}</div>
                                        <div class="user-email">{{ $user->email }}</div>
                                    </div>
                                    <span class="fill-icon">→</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="hint">🔑 All dev accounts use the password: <strong style="color:#f0f0f0">password</strong></div>
        </div>

    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            localStorage.removeItem('justfeast_admin_user');
            localStorage.removeItem('justfeast_vendor_user');
            localStorage.removeItem('justfeast_runner_user');
            localStorage.removeItem('justfeast_client_user');
        });

        function fillLogin(email) {
            const emailInput    = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            emailInput.value    = email;
            passwordInput.value = 'password';

            // Highlight the filled fields briefly
            [emailInput, passwordInput].forEach(el => {
                el.style.borderColor = '#FF6B00';
                el.style.boxShadow   = '0 0 0 3px rgba(255,107,0,0.2)';
                setTimeout(() => {
                    el.style.borderColor = '';
                    el.style.boxShadow   = '';
                }, 1200);
            });

            document.getElementById('loginBtn').focus();
        }

        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('loginBtn');
            btn.textContent = 'Signing in…';
            btn.disabled    = true;
        });
    </script>
</body>
</html>
