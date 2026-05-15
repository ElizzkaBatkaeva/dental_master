<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Дентал Мастер</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Oswald:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: #f5fbf9;
            color: #1a2e3b;
        }

        :root {
            --primary: #0F766E;
            --primary-dark: #0a5c55;
            --primary-light: #00ffc8;
            --secondary: #37FFA2;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 32px;
        }

        h1,
        h2,
        h3,
        h4,
        .logo-text,
        .hero-title {
            font-family: 'Oswald', sans-serif;
            letter-spacing: 1px;
        }

        /* ===== Глобальные стили для форм ===== */
        input,
        select,
        textarea {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #cbd5e1;
            border-radius: 28px;
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
            transition: 0.2s;
            background: white;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1e3a3f;
        }

        .form-group {
            margin-bottom: 20px;
        }

        /* ===== Шапка ===== */
        .header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(18, 255, 235, 0.3);
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
            flex-wrap: wrap;
            gap: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #0F766E, #1BAF8C);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Десктопное меню */
        .nav-links {
            display: flex;
            gap: 36px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            text-decoration: none;
            font-weight: 600;
            color: #244c5c;
            transition: 0.2s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        /* Кнопка гамбургера (скрыта на десктопе) */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            background: none;
            border: none;
            padding: 10px;
            z-index: 1001;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: var(--primary);
            margin: 3px 0;
            transition: 0.3s;
            border-radius: 2px;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* Мобильное меню */
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 80%;
            max-width: 350px;
            height: 100vh;
            background: white;
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.1);
            z-index: 999;
            transition: 0.3s ease-in-out;
            padding: 80px 30px 30px;
            overflow-y: auto;
        }

        .mobile-menu.active {
            right: 0;
        }

        .mobile-menu a {
            display: block;
            text-decoration: none;
            padding: 15px 0;
            font-weight: 600;
            color: #244c5c;
            border-bottom: 1px solid #e2e8f0;
            transition: 0.2s;
        }

        .mobile-menu a:hover {
            color: var(--primary);
            padding-left: 10px;
        }

        .mobile-menu .btn-outline {
            display: inline-block;
            margin-top: 10px;
            text-align: center;
        }

        .mobile-menu .user-name {
            display: block;
            padding: 10px 0;
            color: var(--primary);
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Оверлей */
        .menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            display: none;
        }

        .menu-overlay.active {
            display: block;
        }

        .btn-outline {
            border: 1.5px solid var(--primary-light);
            background: transparent;
            padding: 8px 24px;
            border-radius: 44px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            color: var(--primary);
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline:hover {
            background: var(--primary-light);
        }

        .btn-primary {
            background: linear-gradient(105deg, #0F766E, #1BAF8C);
            border: none;
            padding: 12px 34px;
            border-radius: 44px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: 0.2s;
            display: inline-block;
            text-decoration: none;
            text-align: center;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 118, 110, 0.3);
        }

        /* ===== Фильтр-чипсы ===== */
        .filter-chip {
            background: white;
            padding: 8px 26px;
            border-radius: 50px;
            text-decoration: none;
            color: #0F766E;
            border: 1px solid #cce0d9;
            transition: 0.2s;
            display: inline-block;
        }

        .filter-chip.active,
        .filter-chip:hover {
            background: #0F766E;
            color: white;
            border-color: #0F766E;
        }

        /* ===== Слайдер ===== */
        .slider-container {
            position: relative;
            max-width: 100%;
            margin: 40px auto;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.1);
        }

        .slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .slide {
            min-width: 100%;
            box-sizing: border-box;
        }

        .slide img {
            width: 100%;
            height: 450px;
            object-fit: cover;
            display: block;
        }

        .prev,
        .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 24px;
            padding: 12px 18px;
            border: none;
            cursor: pointer;
            border-radius: 50%;
            transition: 0.2s;
            z-index: 10;
        }

        .prev:hover,
        .next:hover {
            background: var(--primary);
        }

        .prev {
            left: 20px;
        }

        .next {
            right: 20px;
        }

        .dots {
            text-align: center;
            position: absolute;
            bottom: 20px;
            width: 100%;
            z-index: 10;
        }

        .dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            background: #bbb;
            border-radius: 50%;
            margin: 0 5px;
            cursor: pointer;
            transition: 0.2s;
        }

        .dot.active {
            background: var(--primary-light);
        }

        /* ===== Две колонки с фото ===== */
        .two-col-img {
            display: flex;
            gap: 40px;
            align-items: center;
            margin: 60px 0;
        }

        .two-col-img .col {
            flex: 1;
        }

        .two-col-img img {
            width: 100%;
            border-radius: 32px;
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.1);
        }

        /* ===== Футер ===== */
        .footer {
            background: #072b26;
            color: #ccf0e8;
            padding: 56px 0 32px;
            margin-top: 70px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer h4 {
            color: var(--primary-light);
            margin-bottom: 16px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 32px;
            border-top: 1px solid rgba(18, 255, 235, 0.2);
        }

        .flash-messages {
            position: fixed;
            top: 90px;
            right: 20px;
            z-index: 1100;
        }

        .flash {
            padding: 12px 24px;
            border-radius: 12px;
            margin-bottom: 10px;
            animation: slideIn 0.3s ease;
        }

        .flash.success {
            background: #10b981;
            color: white;
        }

        .flash.error {
            background: #ef4444;
            color: white;
        }

        .flash.info {
            background: #3b82f6;
            color: white;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 30px;
            border-radius: 32px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        /* Табы в кабинете */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .tab-btn.active {
            background: #0F766E;
            color: white;
        }

        /* ===== АДАПТИВНЫЕ СТИЛИ ДЛЯ ВСЕХ СТРАНИЦ ===== */
        @media (max-width: 1024px) {
            .container {
                padding: 0 24px;
            }

            .hero-title {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 16px;
            }

            .nav {
                flex-direction: row;
                justify-content: space-between;
                text-align: center;
                gap: 12px;
            }

            /* Скрываем десктопное меню */
            .nav-links {
                display: none;
            }

            /* Показываем гамбургер */
            .hamburger {
                display: flex;
            }

            .logo-text {
                font-size: 1.4rem;
            }

            .logo-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }

            .btn-primary,
            .btn-outline {
                padding: 8px 20px;
                font-size: 0.9rem;
            }

            h1 {
                font-size: 1.8rem !important;
            }

            h2 {
                font-size: 1.5rem !important;
            }

            .stat-card {
                padding: 16px;
                font-size: 0.9rem;
            }

            /* Сетки администратора в один столбец */
            .admin-stats,
            .stats-grid,
            [style*="grid-template-columns:repeat(5,1fr)"] {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }

            [style*="display:grid"]:not(.two-col-img):not(.footer-grid) {
                grid-template-columns: 1fr !important;
            }

            /* Таблицы с горизонтальной прокруткой */
            table,
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            table td,
            table th {
                white-space: nowrap;
                padding: 10px 8px !important;
                font-size: 0.85rem;
            }

            /* Модальные окна во всю ширину */
            .modal-content {
                width: 95%;
                margin: 20% auto;
                padding: 20px;
                max-width: none;
            }

            /* Формы и инпуты */
            input,
            select,
            textarea,
            button {
                font-size: 16px !important;
                /* предотвращает зум на iOS */
            }

            /* Слайдер */
            .slide img {
                height: 250px;
            }

            .prev,
            .next {
                padding: 8px 12px;
                font-size: 18px;
            }

            /* Две колонки с фото */
            .two-col-img {
                flex-direction: column;
                gap: 24px;
                margin: 40px 0;
            }

            /* Flash сообщения */
            .flash-messages {
                top: 80px;
                right: 10px;
                left: 10px;
            }

            .flash {
                font-size: 0.85rem;
                text-align: center;
            }

            /* Карточки врачей, услуг */
            .category-card,
            .doctor-card,
            [style*="grid-template-columns:repeat(auto-fill,minmax(320px,1fr))"] {
                grid-template-columns: 1fr !important;
            }

            /* Футер */
            .footer-grid {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 24px;
            }

            /* Админ панель - ссылки */
            .admin-links,
            [style*="display:grid; grid-template-columns:repeat(3,1fr)"] {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 12px;
            }

            .logo-text {
                font-size: 1.2rem;
            }

            .hero-title {
                font-size: 1.6rem;
            }

            .btn-primary,
            .btn-outline {
                padding: 6px 16px;
                font-size: 0.8rem;
            }

            .modal-content {
                padding: 16px;
            }

            .modal-content h2 {
                font-size: 1.3rem;
            }

            .mobile-menu {
                width: 85%;
                padding: 70px 20px 20px;
            }
        }

        .logo img {
            width: 240px;
            height: auto;
            object-fit: contain;
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .logo img {
                width: 180px;
                /* Уменьшаем логотип на планшетах */
            }
        }

        @media (max-width: 480px) {
            .logo img {
                width: 150px;
                /* Еще меньше на телефонах */
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="container">
            <div class="nav">
                <div class="logo" onclick="location.href='index.php'">
                    <div class="logo-icon">
                        <img src="img/logo.svg" alt="Логотип Дентал Мастер"
                            style="width: 240px; height: auto; object-fit: contain;">
                    </div>
                </div>

                <!-- Десктопное меню -->
                <div class="nav-links">
                    <a href="index.php">Главная</a>
                    <a href="services.php">Услуги</a>
                    <a href="doctors.php">Врачи</a>
                    <a href="contacts.php">Контакты</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="cabinet.php">Личный кабинет</a>
                        <span style="color:var(--primary);"><?= htmlspecialchars($_SESSION['full_name']) ?></span>
                        <a href="logout.php">Выйти</a>
                    <?php else: ?>
                        <a href="login.php">Вход</a>
                        <a href="register.php" class="btn-outline">Регистрация</a>
                    <?php endif; ?>
                </div>

                <!-- Кнопка гамбургера для мобильных -->
                <button class="hamburger" id="hamburgerBtn">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Мобильное меню -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="index.php">Главная</a>
        <a href="services.php">Услуги</a>
        <a href="doctors.php">Врачи</a>
        <a href="contacts.php">Контакты</a>
        <?php if (isLoggedIn()): ?>
            <span class="user-name"><?= htmlspecialchars($_SESSION['full_name']) ?></span>
            <a href="cabinet.php">Личный кабинет</a>
            <a href="logout.php">Выйти</a>
        <?php else: ?>
            <a href="login.php">Вход</a>
            <a href="register.php" class="btn-outline" style="text-align: center;">Регистрация</a>
        <?php endif; ?>
    </div>

    <!-- Оверлей -->
    <div class="menu-overlay" id="menuOverlay"></div>

    <script>
        // Гамбургер меню
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const menuOverlay = document.getElementById('menuOverlay');

        function toggleMenu() {
            hamburgerBtn.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            menuOverlay.classList.toggle('active');

            // Блокируем скролл body когда меню открыто
            if (mobileMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        function closeMenu() {
            hamburgerBtn.classList.remove('active');
            mobileMenu.classList.remove('active');
            menuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        hamburgerBtn.addEventListener('click', toggleMenu);
        menuOverlay.addEventListener('click', closeMenu);

        // Закрываем меню при клике на ссылку
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', closeMenu);
        });

        // Закрываем меню при изменении размера окна (если стало десктопом)
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768 && mobileMenu.classList.contains('active')) {
                closeMenu();
            }
        });
    </script>

    <div class="flash-messages">
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="flash <?= $_SESSION['flash']['type'] ?>"><?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
    </div>
    <main>