<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Connexion') - SIC</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/react-index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/react-app.css') }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 30s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .authContainer {
            max-width: 440px;
            width: 100%;
            position: relative;
            z-index: 1;
        }

        .authCard {
            background: #ffffff;
            border-radius: 20px;
            padding: 48px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }

        .authHeader {
            text-align: center;
            margin-bottom: 32px;
        }

        .authBrand {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            border-radius: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0 auto 20px;
        }

        .authBrand .material-symbols-outlined {
            font-size: 40px;
            color: #ffffff;
        }

        .authTitle {
            font-size: 28px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 8px;
        }

        .authSubtitle {
            font-size: 14px;
            color: #6b7280;
        }

        .formGroup {
            margin-bottom: 20px;
        }

        .formLabel {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .inputGroup {
            position: relative;
        }

        .inputIcon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .inputIcon .material-symbols-outlined {
            font-size: 20px;
        }

        .formInput {
            width: 100%;
            padding: 12px 14px 12px 44px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #111827;
            transition: all 0.2s ease;
            background: #f9fafb;
        }

        .formInput:focus {
            outline: none;
            border-color: #667eea;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .formInput::placeholder {
            color: #9ca3af;
        }

        .formError {
            display: block;
            font-size: 13px;
            color: #ef4444;
            margin-top: 6px;
        }

        .formHelp {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-top: 6px;
        }

        .checkboxGroup {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkboxGroup input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            cursor: pointer;
        }

        .checkboxGroup label {
            font-size: 14px;
            color: #6b7280;
            cursor: pointer;
        }

        .btnPrimary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btnPrimary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btnPrimary:active {
            transform: translateY(0);
        }

        .authFooter {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .authFooter p {
            font-size: 14px;
            color: #6b7280;
        }

        .authFooter a {
            color: #667eea;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s ease;
        }

        .authFooter a:hover {
            color: #764ba2;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alertSuccess {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .alertError {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        @media (max-width: 480px) {
            .authCard {
                padding: 32px 24px;
            }

            .authTitle {
                font-size: 24px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="authContainer">
        <div class="authCard">
            <div class="authHeader">
                <div class="authBrand">
                    <span class="material-symbols-outlined">account_balance</span>
                </div>
                <h1 class="authTitle">@yield('auth-title', 'SIC Comptabilité')</h1>
                <p class="authSubtitle">@yield('auth-subtitle', 'Gestion comptable en partie double')</p>
            </div>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="alert alertSuccess">
                    <span class="material-symbols-outlined" style="font-size: 20px;">check_circle</span>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alertError">
                    <span class="material-symbols-outlined" style="font-size: 20px;">error</span>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alertError">
                    <span class="material-symbols-outlined" style="font-size: 20px;">error</span>
                    <div>
                        <strong>Erreurs :</strong>
                        <ul style="margin: 8px 0 0 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>

</html>
