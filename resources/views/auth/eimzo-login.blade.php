<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OneID - Yagona identifikatsiya tizimi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: #003399;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-text {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .header-btn {
            background: transparent;
            border: 1.5px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .header-btn:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.5);
        }

        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
        }

        .card-header {
            padding: 2rem;
            text-align: center;
            border-bottom: 1px solid #e8ecf1;
        }

        .card-header h4 {
            color: #1a1a1a;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .card-body {
            padding: 2rem;
        }

        .tab-container {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            padding: 0.25rem;
            background: #f5f7fa;
            border-radius: 10px;
        }

        .tab-btn {
            flex: 1;
            padding: 0.75rem;
            border: none;
            background: transparent;
            color: #666;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .tab-btn.active {
            background: white;
            color: #003399;
            box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            color: #1a1a1a;
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-select {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            font-size: 0.95rem;
            color: #333;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .form-select:focus {
            outline: none;
            border-color: #003399;
            box-shadow: 0 0 0 3px rgba(0,51,153,0.1);
        }

        .btn-primary {
            width: 100%;
            padding: 0.875rem 1.5rem;
            background: #003399;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary:hover:not(:disabled) {
            background: #002266;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,51,153,0.2);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-usb {
            width: 100%;
            padding: 0.875rem 1.5rem;
            background: transparent;
            color: #666;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .btn-usb:hover {
            border-color: #003399;
            color: #003399;
            background: rgba(0,51,153,0.02);
        }

        .usb-icon {
            width: 28px;
            height: 28px;
        }

        .divider {
            border: 0;
            border-top: 1px solid #e8ecf1;
            margin: 1.5rem 0;
        }

        .footer-text {
            text-align: center;
            color: #666;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .btn-register {
            display: inline-block;
            color: #003399;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .btn-register:hover {
            text-decoration: underline;
        }

        .status-message {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .status-loading {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .help-text {
            text-align: center;
            color: #999;
            font-size: 0.85rem;
            margin-top: 1rem;
        }

        .help-text a {
            color: #003399;
            text-decoration: none;
        }

        .help-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .main-container {
                padding: 1rem;
            }
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo-container">
            <div class="logo-text">OneID</div>
        </div>
        <div class="header-actions">
            <button class="header-btn">
                <i class="ri-question-line"></i> Yordam
            </button>
        </div>
    </header>

    <main class="main-container">
        <div class="login-card">
            <div class="card-header">
                <h4>Tizimga yo'naltirish</h4>
            </div>
            <div class="card-body">
                <div class="tab-container">
                    <button class="tab-btn" onclick="switchTab('login')">Login</button>
                    <button class="tab-btn" onclick="switchTab('mobile')">Mobile-ID</button>
                    <button class="tab-btn active" onclick="switchTab('eri')">ERI</button>
                    <button class="tab-btn" onclick="switchTab('qr')">QR-kod</button>
                </div>

                <div id="eimzo-status"></div>
                <div id="eimzo-message"></div>
                <div id="eimzo-progress"></div>

                <div id="eri-content" class="tab-content active">
                    <div class="form-group">
                        <label for="eimzo-keys" class="form-label">ERI ni tanlang</label>
                        <select id="eimzo-keys" class="form-select" onchange="showKeyDetails()">
                            <option value="">Yuklanmoqda...</option>
                        </select>
                    </div>

                    <div id="key-details" style="display: none; background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem;">
                        <div style="font-weight: 600; margin-bottom: 0.5rem; color: #003399;">Tanlangan kalit ma'lumotlari:</div>
                        <div id="key-info" style="color: #666;"></div>
                    </div>

                    <button type="button" class="btn-primary" id="login-btn" onclick="eimzoLogin()" disabled>
                        Kirish
                    </button>

                    <button type="button" class="btn-usb" id="usb-btn" onclick="loadUSBKeys()">
                        <svg class="usb-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M38.701 24.355H36.512L36.045 26.62H38.555C38.788 26.62 40.1 26.453 40.1 25.353C40.1 24.266 38.701 24.355 38.701 24.355ZM39.521 20.339H37.371L36.997 22.135H39.334C39.522 22.135 40.447 21.989 40.447 21.129C40.447 20.271 39.521 20.339 39.521 20.339ZM44.064 23.109C44.064 23.109 45.5 22.366 45.5 20.016C45.5 16.301 41.123 16.5 41.123 16.5H38.258L38.932 13.5H17.961C8.617 13.5 5.803 20.274 5.803 20.274L5.736 20.5H2.547L1.5 26.5H4.87L4.871 26.643C4.871 26.643 4.586 33.5 15.334 33.5H34.376L35.055 30.5H39.444C44.173 30.5 45.035 27.146 45.035 25.6C45.032 23.838 44.064 23.109 44.064 23.109Z" fill="#44444bba"/>
                            <path d="M14.022 29.5C8.716 29.5 8.716 25.876 8.784 25.514C9.37459 22.8414 9.97093 20.17 10.573 17.5H14.413L13.055 23.854C13.055 23.854 12.084 26.582 14.306 26.582C16.387 26.582 16.642 24.047 16.642 24.047L18.107 17.504H21.946L20.364 24.483C20.365 24.48 20.258 29.5 14.022 29.5ZM26.098 29.521C23.424 29.521 21.14 28.259 21.242 25.381H24.68C24.68 25.957 24.766 27.008 26.313 27.008C26.94 27.008 28.001 26.742 28.001 25.875C28.001 24.244 22.404 25.09 22.404 21.305C22.404 19.242 24.303 17.52 27.393 17.52C32.369 17.52 32.006 21.269 32.006 21.269H28.637C28.637 20.225 27.973 20.065 27.174 20.065C26.374 20.065 25.802 20.408 25.802 21.009C25.802 22.48 31.436 21.465 31.436 25.54C31.436 27.305 30.012 29.521 26.098 29.521Z" fill="white"/>
                        </svg>
                        USB token orqali kirish
                    </button>
                </div>

                <hr class="divider">

                <div class="footer-text">
                    <p style="margin-bottom: 0.5rem;">Tizimda akkauntingiz yo'qmi?</p>
                    <a href="#" class="btn-register">Ro'yxatdan o'tish</a>
                </div>

                <div class="help-text">
                    <p>* Tizimga kirish orqali siz shaxsiy ma'lumotlaringizni ushbu tizimga uzatishga rozilik bildirasiz.</p>
                    <p style="margin-top: 0.75rem;">E-IMZO dasturi o'rnatilgan bo'lishi kerak<br>
                    <a href="https://e-imzo.uz/main/downloads/" target="_blank">E-IMZO yuklab olish</a></p>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('js/e-imzo.js') }}"></script>
    <script src="{{ asset('js/e-imzo-client.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        function switchTab(tabName) {
            const tabs = document.querySelectorAll('.tab-btn');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            event.target.classList.add('active');

            if (tabName === 'eri') {
                document.getElementById('eri-content').classList.add('active');
            }
        }

        function loadUSBKeys() {
            AppLoad();
        }

        function showKeyDetails() {
            var combo = document.getElementById('eimzo-keys');
            var detailsDiv = document.getElementById('key-details');
            var infoDiv = document.getElementById('key-info');

            if (!combo || !combo.value || combo.value === '') {
                detailsDiv.style.display = 'none';
                return;
            }

            var option = combo.options[combo.selectedIndex];
            var vo = JSON.parse(option.getAttribute('data-vo'));

            var html = '<div style="line-height: 1.6;">';
            html += '<div><strong>Ism:</strong> ' + (vo.CN || 'N/A') + '</div>';

            if (vo.PINFL) {
                html += '<div><strong>PINFL:</strong> ' + vo.PINFL + '</div>';
            }

            if (vo.TIN || vo.UID) {
                html += '<div><strong>INN:</strong> ' + (vo.TIN || vo.UID) + '</div>';
            }

            if (vo.O) {
                html += '<div><strong>Tashkilot:</strong> ' + vo.O + '</div>';
            }

            if (vo.T) {
                html += '<div><strong>Lavozim:</strong> ' + vo.T + '</div>';
            }

            if (vo.serialNumber) {
                html += '<div><strong>Serial:</strong> ' + vo.serialNumber + '</div>';
            }

            if (vo.type) {
                html += '<div><strong>Turi:</strong> ' + vo.type.toUpperCase() + '</div>';
            }

            if (vo.validTo) {
                var validToDate = new Date(vo.validTo);
                html += '<div><strong>Amal qilish muddati:</strong> ' + validToDate.toLocaleDateString('uz-UZ') + '</div>';
            }

            if (vo.expired) {
                html += '<div style="color: #dc3545; font-weight: 600; margin-top: 0.5rem;">⚠️ Bu kalitning muddati tugagan</div>';
            }

            html += '<div style="margin-top: 0.75rem; padding: 0.5rem; background: #fff3cd; border-radius: 4px; font-size: 0.85rem;"><strong>Ogohlantirish:</strong> Bu ma\'lumotlar tanlangan kalitdan olingan. Agar login qilgandan so\'ng boshqa ma\'lumotlar ko\'rsatilsa, PFX faylda muammo bor.</div>';
            html += '</div>';

            infoDiv.innerHTML = html;
            detailsDiv.style.display = 'block';
        }
    </script>
</body>
</html>
