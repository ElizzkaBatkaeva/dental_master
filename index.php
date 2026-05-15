<?php require 'config.php'; ?>
<?php include 'header.php'; ?>

<div class="container">
    <!-- Hero блок (оставляем как есть) -->
<div style="position:relative; width: 100vw; left: 50%; transform: translateX(-50%); min-height:600px; overflow:hidden; background: linear-gradient(105deg, rgba(10,53,46,0.85), rgba(15,118,110,0.85)), url('img/hero-bg.jpg') center/cover no-repeat;">
    <div style="position:relative; z-index:2; max-width:680px; margin:0 auto; padding:80px 20px; color:white;">
        <!-- здесь всё содержимое без изменений -->
        <div style="background:rgba(18,255,235,0.2); backdrop-filter:blur(6px); display:inline-block; padding:5px 16px; border-radius:60px; font-size:0.7rem; font-weight:700; margin-bottom:24px;">
            <i class="fas fa-shield-alt"></i> ЛИЦЕНЗИРОВАННАЯ КЛИНИКА В ПЕНЗЕ
        </div>
        <h1 style="font-size:3.4rem; font-weight:800; margin-bottom:20px;">Дентал Мастер — мастера своего дела</h1>
        <p>Инновационная стоматология, проверенная временем. Европейские стандарты, честные цены и забота о каждом пациенте.</p>
        <div style="margin:32px 0; display:flex; gap:16px; flex-wrap:wrap;">
            <a href="services.php" class="btn-primary"><i class="fas fa-calendar-check"></i> Записаться онлайн</a>
            <a href="#about" class="btn-outline" style="background:transparent; border:1.5px solid #12FFEB; color:white;">О клинике</a>
        </div>
        <div style="display:flex; gap:42px; margin-top:32px; flex-wrap:wrap;">
            <div><span style="font-size:1.8rem; font-weight:800; color:#12FFEB;">6 700+</span><br>пациентов</div>
           
            <div><span style="font-size:1.8rem; font-weight:800; color:#12FFEB;">11 лет</span><br>средний стаж</div>
        </div>
    </div>
</div>

    <!-- Блок "О клинике" -->
    <div id="about" style="margin: 60px 0;">
        <h2 style="font-size:2.3rem; color:#0F766E; text-align:center;">О клинике «Дентал Мастер»</h2>
        <div style="max-width: 800px; margin: 30px auto; text-align:center; font-size:1.1rem; line-height:1.5;">
            <p>Мы работаем с 2012 года и за это время помогли более 5000 пациентам обрести здоровую и красивую улыбку. 
            Используем только проверенные материалы и современное оборудование — 3D-томограф, операционные микроскопы, лазеры. 
            Каждый врач регулярно повышает квалификацию в ведущих европейских центрах.</p>
            <p style="margin-top: 20px;">Наша миссия — сделать стоматологию комфортной, безболезненной и доступной. 
            Прозрачные цены, гарантия на все виды работ, рассрочка на лечение.</p>
        </div>
    </div>

    <!-- Две колонки с фото (слева/справа) -->
    <div class="two-col-img">
        <div class="col">
            <img src="img/Gemini_Generated_Image_rsy6tdrsy6tdrsy6.png" alt="Современный кабинет" onerror="this.src='https://placehold.co/600x400?text=Фото+кабинета'">
        </div>
        <div class="col">
            <h3 style="color:#0F766E;">Современное оборудование</h3>
            <p>Цифровой томограф, внутриротовые камеры, лазерный аппарат для безболезненного лечения. 
            Диагностика с точностью до 0.1 мм.</p>
            <p>Стерилизационная система класса «А» — абсолютная безопасность.</p>
        </div>
    </div>
    <div class="two-col-img" style="flex-direction: row-reverse;">
        <div class="col">
            <img src="img/Gemini_Generated_Image_vy5g2vy5g2vy5g2v.png" alt="Уютная зона ожидания" onerror="this.src='https://placehold.co/600x400?text=Фото+зоны+ожидания'">
        </div>
        <div class="col">
            <h3 style="color:#0F766E;">Комфорт и забота</h3>
            <p>Уютная зона ожидания с чаем/кофе, детский уголок, музыкальное сопровождение во время лечения. 
            Для пациентов с дентофобией — седация (лечение во сне).</p>
        </div>
    </div>

    <!-- Преимущества (усиленный блок) -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:28px; margin:60px 0;">
        <div style="padding:28px; background:#fafdfb; border-radius:28px; border-left:5px solid #12FFEB;">
            <i class="fas fa-microscope" style="font-size:2rem; color:#0F766E;"></i>
            <h4 style="margin:12px 0;">Диагностика без ошибок</h4>
            <p>3D-томография, микроскоп, компьютерное моделирование улыбки.</p>
        </div>
        <div style="padding:28px; background:#fafdfb; border-radius:28px; border-left:5px solid #12FFEB;">
            <i class="fas fa-coins" style="font-size:2rem; color:#0F766E;"></i>
            <h4 style="margin:12px 0;">Цены без сюрпризов</h4>
            <p>Полная смета до лечения, фиксированная стоимость, рассрочка 0%.</p>
        </div>
        <div style="padding:28px; background:#fafdfb; border-radius:28px; border-left:5px solid #12FFEB;">
            <i class="fas fa-user-shield" style="font-size:2rem; color:#0F766E;"></i>
            <h4 style="margin:12px 0;">Гарантия до 10 лет</h4>
            <p>На импланты — пожизненная гарантия, на коронки и пломбы — до 10 лет.</p>
        </div>
        <div style="padding:28px; background:#fafdfb; border-radius:28px; border-left:5px solid #12FFEB;">
            <i class="fas fa-clock" style="font-size:2rem; color:#0F766E;"></i>
            <h4 style="margin:12px 0;">Удобные часы работы</h4>
            <p>Пн-Пт 9:00-20:00, Сб 10:00-16:00. Возможен приём в воскресенье.</p>
        </div>
    </div>

    <!-- Слайдер интерьера клиники -->
    <div style="margin: 60px 0 40px;">
        <h2 style="font-size:2rem; color:#0F766E; text-align:center; margin-bottom:24px;">Фотографии клиники</h2>
        <div class="slider-container" id="clinicSlider">
            <div class="slides" id="slides">
                <div class="slide"><img src="img/1 (3).png" alt="Интерьер 1" onerror="this.src='https://placehold.co/1200x450?text=Интерьер+1'"></div>
                <div class="slide"><img src="img/1 (2).png" alt="Интерьер 2" onerror="this.src='https://placehold.co/1200x450?text=Интерьер+2'"></div>
                <div class="slide"><img src="img/1 (1).png" alt="Интерьер 3" onerror="this.src='https://placehold.co/1200x450?text=Интерьер+3'"></div>
            </div>
            <button class="prev" id="prevSlide">&#10094;</button>
            <button class="next" id="nextSlide">&#10095;</button>
            <div class="dots" id="dots"></div>
        </div>
    </div>
</div>

<script>
// Слайдер
let slideIndex = 0;
const slides = document.querySelectorAll('#slides .slide');
const dotsContainer = document.getElementById('dots');
let dots = [];

function showSlide(index) {
    if(index >= slides.length) slideIndex = 0;
    if(index < 0) slideIndex = slides.length-1;
    document.getElementById('slides').style.transform = `translateX(-${slideIndex * 100}%)`;
    dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === slideIndex);
    });
}

function createDots() {
    slides.forEach((_, i) => {
        const dot = document.createElement('span');
        dot.classList.add('dot');
        if(i === 0) dot.classList.add('active');
        dot.addEventListener('click', () => { slideIndex = i; showSlide(slideIndex); });
        dotsContainer.appendChild(dot);
        dots.push(dot);
    });
}
if(slides.length) {
    createDots();
    document.getElementById('prevSlide').addEventListener('click', () => { slideIndex--; showSlide(slideIndex); });
    document.getElementById('nextSlide').addEventListener('click', () => { slideIndex++; showSlide(slideIndex); });
    setInterval(() => { slideIndex++; showSlide(slideIndex); }, 5000);
}
</script>

<?php include 'footer.php'; ?>