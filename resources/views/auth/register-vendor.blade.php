<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>justFeast — Vendor Registration</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/jm.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/jm.png') }}">
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
            max-width: 520px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* ── Register Form Card ─────────────────────────────────────────── */
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
            justify-content: flex-start;
            margin-bottom: 1.8rem;
        }
        .logo-icon {
            height: 48px;
            padding: 6px 14px;
            background: #FFFFFF;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
            border: 1px solid var(--border);
        }
        .logo-icon img {
            height: 100%;
            width: auto;
            object-fit: contain;
        }

        .badge-vendor {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(163, 29, 29, 0.08);
            color: var(--brand);
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

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
            align-items: flex-start;
            gap: 0.6rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-grid .full-width {
            grid-column: span 2;
        }

        @media (max-width: 580px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-grid .full-width { grid-column: span 1; }
        }

        .field { margin-bottom: 1.1rem; }
        label {
            display: block;
            font-size: 0.73rem;
            font-weight: 800;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.45rem;
        }
        input[type="email"],
        input[type="text"],
        input[type="tel"],
        input[type="password"],
        select {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 0.85rem 1.1rem;
            color: var(--text);
            font-size: 0.92rem;
            font-family: inherit;
            font-weight: 700;
            outline: none;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }
        input:focus, select:focus {
            border-color: var(--brand);
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(163, 29, 29, 0.12);
        }
        input.is-error, select.is-error { border-color: var(--error); }
        .field-error { color: var(--error); font-size: 0.78rem; margin-top: 0.35rem; font-weight: 700; }

        .btn-register {
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
            margin-top: 0.5rem;
        }
        .btn-register:hover  {
            background: linear-gradient(135deg, #841313 0%, #630C0C 100%);
            box-shadow: 0 12px 32px rgba(163, 29, 29, 0.45);
            transform: translateY(-1px);
        }
        .btn-register:active { transform: scale(0.98); }
        .btn-register:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .footer-note {
            text-align: center;
            margin-top: 1.6rem;
            font-size: 0.88rem;
            color: var(--muted);
            font-weight: 600;
        }
        .footer-note a {
            color: var(--brand);
            font-weight: 800;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .footer-note a:hover {
            color: var(--brand-dark);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="layout">

        <div class="form-card">
            <div class="logo">
                <img src="{{ asset('images/logo/jm.png') }}" alt="justFeast Logo" style="height: 52px; width: auto; border-radius: 16px; box-shadow: 0 6px 20px rgba(0,0,0,0.12); border: 1px solid rgba(0,0,0,0.08);">
            </div>

            <div class="badge-vendor">
                <i class="fas fa-store"></i> Vendor Portal Onboarding
            </div>

            <h1>Register your Business</h1>
            <p class="subtitle">Join our event ecosystem and serve customers instantly</p>

            @if ($errors->any())
                <div class="alert-error">
                    <i class="fas fa-circle-exclamation" style="margin-top: 2px;"></i>
                    <div>
                        <strong>Please resolve the following errors:</strong>
                        <ul style="margin-top: 4px; padding-left: 1.2rem; font-size: 0.82rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register.vendor.post') }}" enctype="multipart/form-data" id="registerForm">
                @csrf

                <div class="form-grid">
                    <div class="field">
                        <label for="name">Contact Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="John Doe"
                            class="{{ $errors->has('name') ? 'is-error' : '' }}"
                            required autofocus
                        >
                        @error('name')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="business_name">Business Name</label>
                        <input
                            type="text"
                            id="business_name"
                            name="business_name"
                            value="{{ old('business_name') }}"
                            placeholder="e.g. Gourmet Burger Co."
                            class="{{ $errors->has('business_name') ? 'is-error' : '' }}"
                            required
                        >
                        @error('business_name')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="email">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="vendor@justfeast.co.ke"
                            class="{{ $errors->has('email') ? 'is-error' : '' }}"
                            required
                        >
                        @error('email')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="phone">Phone Number</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="0712345678"
                            class="{{ $errors->has('phone') ? 'is-error' : '' }}"
                        >
                        @error('phone')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field full-width">
                        <label for="event_id">Event Participation</label>
                        <select id="event_id" name="event_id" class="{{ $errors->has('event_id') ? 'is-error' : '' }}">
                            @if(isset($events) && count($events) > 0)
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                        {{ $event->name }} {{ $event->venue ? '('.$event->venue->name.')' : '' }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">Main Concert Event</option>
                            @endif
                        </select>
                        @error('event_id')
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

                    <div class="field">
                        <label for="password_confirmation">Confirm Password</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="••••••••"
                            required
                        >
                    </div>

                    <div class="field full-width">
                        <label for="logo">Business Logo (Optional)</label>
                        <input
                            type="file"
                            id="logo"
                            name="logo"
                            accept="image/*"
                            style="padding: 0.6rem 1rem;"
                        >
                        @error('logo')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn-register" id="registerBtn">
                    <span>Complete Vendor Registration</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </form>

            <div class="footer-note">
                Already registered? <a href="{{ route('login') }}">Sign In to Portal</a>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function () {
            const btn = document.getElementById('registerBtn');
            btn.innerHTML = '<span>Creating Vendor Account...</span> <i class="fas fa-spinner fa-spin text-xs"></i>';
            btn.disabled  = true;
        });
    </script>
</body>
</html>
