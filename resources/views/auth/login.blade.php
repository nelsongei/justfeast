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

        /* Bright ambient background glows */
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
            display: grid;
            grid-template-columns: 440px 1fr;
            gap: 2.5rem;
            width: 100%;
            max-width: 1040px;
            position: relative;
            z-index: 1;
            align-items: start;
        }

        @media (max-width: 840px) {
            .layout { grid-template-columns: 1fr; }
        }

        /* ── Left: Login Form Card ─────────────────────────────────────────── */
        .form-card {
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(15, 23, 42, 0.04);
            animation: slideUp 0.45s ease;
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

        /* ── Right: User Directory ────────────────────────────────────────── */
        .directory {
            animation: slideUp 0.5s ease 0.1s both;
        }

        .dir-title {
            font-size: 0.78rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--brand);
            margin-bottom: 1.2rem;
            padding-left: 0.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .role-group { margin-bottom: 1.5rem; }

        .role-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.6rem;
        }
        .role-pill {
            font-size: 0.68rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 4px 12px;
            border-radius: 20px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }
        .pill-admin  { background: #FEF2F2; color: #991B1B; border: 1px solid #FCA5A5; }
        .pill-vendor { background: #FFF8E7; color: #B45309; border: 1px solid #F7E5B2; }
        .pill-runner { background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; }
        .pill-client { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; }

        .user-grid {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .user-card {
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 0.8rem 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.9rem;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: left;
            width: 100%;
            font-family: inherit;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        }
        .user-card:hover {
            background: #FFFDF9;
            border-color: var(--gold);
            transform: translateX(4px);
            box-shadow: 0 10px 30px rgba(255, 194, 68, 0.25);
        }
        .user-card:active { transform: translateX(2px) scale(0.99); }

        .user-avatar {
            width: 40px; height: 40px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.95rem;
            font-weight: 900;
            flex-shrink: 0;
            color: #FFFFFF;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .avatar-admin  { background: linear-gradient(135deg, #A31D1D, #841313); }
        .avatar-vendor { background: linear-gradient(135deg, #FFC244, #E0A325); color: #0F172A; }
        .avatar-runner { background: linear-gradient(135deg, #05A357, #047A43); }
        .avatar-client { background: linear-gradient(135deg, #2563EB, #1D4ED8); }

        .user-info { flex: 1; min-width: 0; }
        .user-name {
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-email {
            font-size: 0.78rem;
            color: var(--muted);
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fill-icon {
            color: #CBD5E1;
            font-size: 0.8rem;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .user-card:hover .fill-icon { color: var(--brand); transform: translateX(3px); }

        .hint {
            text-align: center;
            color: #475569;
            font-size: 0.78rem;
            font-weight: 700;
            margin-top: 1.2rem;
            padding: 0.8rem;
            background: #FFF8E7;
            border: 1px solid #F7E5B2;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(255, 194, 68, 0.1);
        }
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
                    <label for="email">Email Address</label>
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

                <button type="submit" class="btn-login" id="loginBtn">
                    <span>Sign In</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>

        {{-- ── User Directory ───────────────────────────────────────────────── --}}
        <div class="directory">
            <div class="dir-title">
                <i class="fas fa-users-viewfinder text-sm"></i> Select account to log in
            </div>

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
                                    <i class="fas fa-chevron-right fill-icon"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="hint">🔑 All dev accounts use the password: <strong style="color:#B45309; font-weight:800;">password</strong></div>
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
                el.style.borderColor = '#A31D1D';
                el.style.boxShadow   = '0 0 0 4px rgba(163, 29, 29, 0.15)';
                setTimeout(() => {
                    el.style.borderColor = '';
                    el.style.boxShadow   = '';
                }, 1200);
            });

            document.getElementById('loginBtn').focus();
        }

        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<span>Signing in...</span> <i class="fas fa-spinner fa-spin text-xs"></i>';
            btn.disabled  = true;
        });
    </script>
</body>
</html>
