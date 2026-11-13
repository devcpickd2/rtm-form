<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ready Meal | Login</title>

    <style>
        :root {
            --clr-rose: #C97064;
            --clr-rose-deep: #A6484A;
            --clr-blush: #E8B4A8;
            --clr-cream: #F6E8DE;
            --clr-glass: rgba(255, 255, 255, 0.22);
            --clr-glass-strong: rgba(255, 255, 255, 0.34);
            --clr-border: rgba(255, 255, 255, 0.45);
            --clr-text: #4A2A28;
            --clr-text-soft: #7A4A45;
            --clr-text-dim: rgba(74, 42, 40, 0.62);
            --radius-lg: 28px;
            --radius-md: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(160deg, var(--clr-cream) 0%, var(--clr-blush) 55%, var(--clr-rose) 100%);
            position: relative;
            overflow: hidden;
            padding: 24px;
        }

        /* Optional background photo layer — degrades gracefully to pure gradient if missing */
        .bg-photo {
            position: absolute;
            inset: 0;
            background-image: url('{{ asset('images/ready_meal_new2.png') }}');
            background-size: cover;
            background-position: center;
            opacity: 0.50;
            mix-blend-mode: multiply;
            z-index: 0;
        }

        /* Ambient decorative blobs for glassmorphism depth — pure CSS, no assets needed */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            z-index: 0;
            animation: float 11s ease-in-out infinite;
        }

        .blob-1 {
            width: 340px;
            height: 340px;
            background: var(--clr-rose-deep);
            top: -100px;
            left: -110px;
        }

        .blob-2 {
            width: 300px;
            height: 300px;
            background: var(--clr-blush);
            bottom: -100px;
            right: -90px;
            animation-delay: -3s;
        }

        .blob-3 {
            width: 220px;
            height: 220px;
            background: #FFFFFF;
            top: 42%;
            right: 6%;
            opacity: 0.25;
            animation-delay: -5s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) translateX(0);
            }

            50% {
                transform: translateY(-20px) translateX(12px);
            }
        }

        .wrap {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 28px;
            width: 100%;
            max-width: 420px;
        }

        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .brand-mark {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--clr-rose), var(--clr-rose-deep));
            border: 1px solid var(--clr-border);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 6px;
            box-shadow: 0 10px 26px rgba(166, 72, 74, 0.35);
        }

        h1.title {
            font-size: 2.1rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            color: var(--clr-rose-deep);
            text-transform: uppercase;
            text-shadow: 0 2px 10px rgba(255, 255, 255, 0.4);
        }

        .subtitle {
            font-size: 1.2rem;
            color: #FFFFFF;
            font-weight: 500;
            letter-spacing: 0.02em;
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.8);
        }

        /* Glass card */
        .card {
            width: 100%;
            padding: 40px 34px;
            background: var(--clr-glass);
            border: 1px solid var(--clr-border);
            border-radius: var(--radius-lg);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            box-shadow: 0 20px 45px rgba(166, 72, 74, 0.16), inset 0 1px 0 rgba(255, 255, 255, 0.5);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .field {
            position: relative;
        }

        .field svg.icon-left {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            stroke: var(--clr-rose-deep);
            opacity: 0.75;
            pointer-events: none;
        }

        .field input {
            width: 100%;
            padding: 14px 16px 14px 46px;
            background: rgba(255, 255, 255, 0.45);
            border: 1px solid rgba(166, 72, 74, 0.18);
            border-radius: var(--radius-md);
            color: var(--clr-text);
            font-size: 0.95rem;
            font-weight: 500;
            outline: none;
            transition: background 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .field input::placeholder {
            color: var(--clr-text-dim);
        }

        .field input:focus {
            background: rgba(255, 255, 255, 0.65);
            border-color: var(--clr-rose);
            box-shadow: 0 0 0 4px rgba(201, 112, 100, 0.18);
        }

        .field.password input {
            padding-right: 46px;
        }

        .toggle-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
        }

        .toggle-pass svg {
            width: 18px;
            height: 18px;
            stroke: var(--clr-rose-deep);
            opacity: 0.75;
        }

        .row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.8rem;
            color: var(--clr-text-dim);
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .remember input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--clr-rose-deep);
            cursor: pointer;
        }

        .row-between a {
            color: var(--clr-text-soft);
            text-decoration: none;
            font-weight: 500;
        }

        .row-between a:hover {
            color: var(--clr-rose-deep);
            text-decoration: underline;
        }

        button.submit {
            margin-top: 6px;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #FFF5F2;
            background: linear-gradient(120deg, var(--clr-rose), var(--clr-rose-deep));
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(166, 72, 74, 0.32);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        button.submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(166, 72, 74, 0.4);
            filter: brightness(1.05);
        }

        button.submit:active {
            transform: translateY(0);
        }

        /* Basic error styling if Laravel validation errors exist */
        .error-text {
            font-size: 0.78rem;
            color: #A6484A;
            font-weight: 600;
            margin-top: -10px;
        }

        @media (max-width: 480px) {
            h1.title {
                font-size: 1.7rem;
            }

            .card {
                padding: 32px 24px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .blob {
                animation: none;
            }
        }

        .sso-error {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: -10px;
            border-radius: 14px;
            background: rgba(255, 235, 235, 0.88);
            border: 1px solid rgba(166, 72, 74, 0.25);
            color: #A6484A;
            font-size: 0.85rem;
            font-weight: 600;
            text-align: center;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 8px 20px rgba(166, 72, 74, 0.12);
        }
    </style>
</head>

<body>

    <div class="bg-photo"></div>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="wrap">
        <div class="brand">
            <div class="brand-mark">
                <!-- Simple plate/utensil mark, inline SVG (no external icon font needed) -->
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF"
                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"></circle>
                    <circle cx="12" cy="12" r="4.5"></circle>
                </svg>
            </div>
            <h1 class="title">Ready Meal</h1>
            <span class="subtitle">Masuk untuk melanjutkan</span>
        </div>

        @if ($errors->has('sso'))
            <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-600 text-sm font-medium text-center">
            {{ $errors->first('sso') }}
            </div>
        @endif

        <div class="card">

            <form method="POST" action="{{ url('/login') }}" autocomplete="off">
                @csrf

                <div class="field">
                    <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <input type="text" name="username" placeholder="Username" required autofocus>
                </div>
                @error('username')
                    <span class="error-text">{{ $message }}</span>
                @enderror

                <div class="field password">
                    <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="4" y="11" width="16" height="9" rx="2"></rect>
                        <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
                    </svg>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <button type="button" class="toggle-pass" id="togglePass" aria-label="Tampilkan password">
                        <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror

                <div class="row-between">
                    <label class="remember">
                        <input type="checkbox" name="remember">
                        Ingat saya
                    </label>
                    <a href="#">Lupa password?</a>
                </div>

                <button type="submit" class="submit">Login</button>
            </form>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePass');
        const passInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        toggleBtn.addEventListener('click', function() {
            const isHidden = passInput.type === 'password';
            passInput.type = isHidden ? 'text' : 'password';

            eyeIcon.innerHTML = isHidden ?
                '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.6 21.6 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 7 11 7a21.6 21.6 0 0 1-2.94 3.94M1 1l22 22"></path><path d="M14.12 14.12A3 3 0 1 1 9.88 9.88"></path>' :
                '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"></path><circle cx="12" cy="12" r="3"></circle>';
        });
    </script>
</body>

</html>
