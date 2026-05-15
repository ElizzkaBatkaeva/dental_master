<?php 
require 'config.php'; 

// Убедимся, что телефон пользователя есть в сессии
if(isLoggedIn() && !isset($_SESSION['user_phone'])) {
    $_SESSION['user_phone'] = getUserPhone($pdo, $_SESSION['user_id']);
}

include 'header.php'; 
?>

<div class="container">
    <h1 style="font-size:2.5rem; margin:48px 0 32px;">Наши услуги</h1>
    <p style="text-align:center; margin-bottom:40px;">Выберите направление, чтобы посмотреть полный прайс-лист и записаться</p>

    <!-- Карточки категорий с фото -->
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(380px,1fr)); gap:32px; margin-bottom:60px;">
        <?php
        $categories = [
            ['diagnostics', 'Диагностика', 'img/диагностика.png', 'Комплексное обследование, 3D-снимок, консультация'],
            ['therapy', 'Лечение', 'img/лечение.png', 'Лечение кариеса, пульпита, эстетическая реставрация'],
            ['surgery', 'Хирургия', 'img/хирургия.png', 'Удаление зубов, кист, резекция верхушки корня'],
            ['implants', 'Имплантация', 'img/имплантация.png', 'Установка имплантов под ключ'],
            ['prosthetics', 'Протезирование', 'img/протезирование.png', 'Коронки, мосты, виниры, бюгельные протезы'],
            ['hygiene', 'Гигиена', 'img/чистка.png', 'Профессиональная чистка, AirFlow, отбеливание']
        ];
        foreach($categories as $cat):
            $image = file_exists($cat[2]) ? $cat[2] : 'https://placehold.co/600x400?text='.urlencode($cat[1]);
        ?>
            <div class="category-card" data-category="<?= $cat[0] ?>" style="background:white; border-radius:20px; overflow:hidden; box-shadow:0 12px 30px rgba(7, 122, 94, 0.4); cursor:pointer; transition:0.3s;">
                <img src="<?= $image ?>" alt="<?= $cat[1] ?>" style="width:100%; height:220px; object-fit:cover;">
                <div style="padding:24px;">
                    <h3><?= $cat[1] ?></h3>
                    <p style="color:#6b7280;"><?= $cat[3] ?></p>
                    <button class="btn-primary" style="margin-top:12px; width:100%;">Выбрать услугу</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Модальное окно для выбора услуги и записи -->
<div id="serviceModal" class="modal">
    <div class="modal-content" style="max-width:700px;">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle" style="margin-bottom:20px;">Услуги категории</h2>
        <div id="servicesList" style="max-height:400px; overflow-y:auto; margin-bottom:20px;">
            <!-- Сюда загрузится список услуг -->
        </div>
        <div id="bookingWidgetModal" style="display:none; margin-top:20px; border-top:1px solid #ddd; padding-top:20px;">
            <h3>Запись на приём</h3>
            <div id="wizardContentModal"></div>
            <div id="wizardActionsModal" style="display:flex; justify-content:space-between; margin-top:20px;"></div>
        </div>
    </div>
</div>

<script>
let selectedServiceId = null, selectedDoctorId = null, selectedTime = null, selectedDate = new Date().toISOString().slice(0,10);
let step = 1, doctors = [];
let currentServiceDetails = null; // храним выбранную услугу

async function fetchDoctors() { 
    let r = await fetch('api.php?action=get_doctors'); 
    doctors = await r.json(); 
}

async function getFreeTimes(doctorId, date) { 
    let r = await fetch(`api.php?action=get_free_times&doctor_id=${doctorId}&date=${date}`); 
    return await r.json(); 
}

async function fetchServicesByCategory(category) {
    let res = await fetch(`api.php?action=get_services_by_category&category=${category}`);
    return await res.json();
}

async function fetchServiceDetails(serviceId) {
    let res = await fetch(`api.php?action=get_service_details&id=${serviceId}`);
    return await res.json();
}

function showCategory(category, categoryName) {
    document.getElementById('modalTitle').innerText = categoryName;
    fetchServicesByCategory(category).then(servicesList => {
        let container = document.getElementById('servicesList');
        if(servicesList.length === 0) {
            container.innerHTML = '<p>Нет услуг в этой категории</p>';
            return;
        }
        container.innerHTML = servicesList.map(s => `
            <div class="service-item" data-id="${s.id}" style="background:#f9f9f9; border-radius:24px; padding:16px; margin-bottom:12px; cursor:pointer; transition:0.2s;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div><strong>${escapeHtml(s.name)}</strong><br>${escapeHtml(s.description || '')}</div>
                    <div style="font-size:1.4rem; font-weight:800; color:#0F766E;">${Number(s.price).toLocaleString()} ₽</div>
                </div>
                <div style="margin-top:10px;"><i class="far fa-clock"></i> ${s.duration} мин</div>
            </div>
        `).join('');
        document.querySelectorAll('.service-item').forEach(el => {
            el.onclick = () => {
                selectedServiceId = parseInt(el.dataset.id);
                startBooking();
            };
        });
        document.getElementById('bookingWidgetModal').style.display = 'none';
        document.getElementById('servicesList').style.display = 'block';
    });
}

async function startBooking() {
    // Получаем детали услуги
    currentServiceDetails = await fetchServiceDetails(selectedServiceId);
    if(!currentServiceDetails) {
        alert('Ошибка загрузки услуги');
        return;
    }
    // Скрываем список услуг, показываем виджет
    document.getElementById('servicesList').style.display = 'none';
    document.getElementById('bookingWidgetModal').style.display = 'block';
    step = 2;
    renderWizardModal();
}

function renderWizardModal() {
    let cont = document.getElementById('wizardContentModal');
    let acts = document.getElementById('wizardActionsModal');
    if(step === 2) {
        cont.innerHTML = `<select id="doctorSelectModal" style="width:100%; padding:14px; border-radius:40px; margin-bottom:14px;">
                            ${doctors.map(d=>`<option value="${d.id}">${escapeHtml(d.name)} - ${escapeHtml(d.specialty)}</option>`).join('')}
                          </select>
                          <input type="date" id="datePickModal" value="${selectedDate}" min="${new Date().toISOString().slice(0,10)}" style="width:100%; padding:12px; border-radius:40px;">
                          <div id="timeContainerModal" style="display:flex; flex-wrap:wrap; gap:12px; margin-top:20px;"></div>`;
        acts.innerHTML = `<button class="btn-outline" onclick="closeModal()">Отмена</button>
                          <button class="btn-primary" onclick="selectDoctorAndTimeModal()">Далее</button>`;
        document.getElementById('doctorSelectModal').onchange = () => refreshTimesModal();
        document.getElementById('datePickModal').onchange = () => refreshTimesModal();
        refreshTimesModal();
    } else if(step === 3) {
        cont.innerHTML = `<div style="background:#eef9f5; padding:24px; border-radius:24px;">
            <p><strong>Услуга:</strong> ${escapeHtml(currentServiceDetails.name)} — ${Number(currentServiceDetails.price).toLocaleString()} ₽</p>
            <p><strong>Врач:</strong> ${escapeHtml(doctors.find(d=>d.id==selectedDoctorId)?.name || '')}</p>
            <p><strong>Дата/время:</strong> ${selectedDate} ${selectedTime}</p>
            <p><strong>Сумма:</strong> ${Number(currentServiceDetails.price).toLocaleString()} ₽</p>
        </div>`;
        acts.innerHTML = `<button class="btn-outline" onclick="step=2;renderWizardModal()">Назад</button>
                          <button class="btn-primary" onclick="confirmBookingModal()">Подтвердить</button>`;
    }
}

async function refreshTimesModal() {
    let docId = document.getElementById('doctorSelectModal').value;
    let date = document.getElementById('datePickModal').value;
    selectedDate = date;
    let times = await getFreeTimes(docId, date);
    let container = document.getElementById('timeContainerModal');
    if(times.length === 0) {
        container.innerHTML = '<p style="color:red;">Нет свободного времени на эту дату</p>';
        selectedTime = null;
    } else {
        container.innerHTML = times.map(t=>`<div class="time-slot" data-time="${t}" style="background:#f0f4f9; padding:10px 24px; border-radius:50px; cursor:pointer;">${t}</div>`).join('');
        document.querySelectorAll('#timeContainerModal .time-slot').forEach(el=>{
            el.onclick = function() {
                document.querySelectorAll('#timeContainerModal .time-slot').forEach(c=>c.style.background='#f0f4f9');
                this.style.background = '#12FFEB';
                selectedTime = this.dataset.time;
            };
        });
    }
}

function selectDoctorAndTimeModal() {
    if(!selectedTime) {
        alert('Выберите время');
        return;
    }
    selectedDoctorId = parseInt(document.getElementById('doctorSelectModal').value);
    step = 3;
    renderWizardModal();
}

async function confirmBookingModal() {
    <?php if(!isLoggedIn()): ?>
        alert('Пожалуйста, войдите в систему');
        window.location.href='login.php';
        return;
    <?php endif; ?>
    
    // Показываем индикатор загрузки
    let confirmBtn = document.querySelector('#wizardActionsModal .btn-primary');
    let originalText = confirmBtn ? confirmBtn.innerText : 'Подтвердить';
    if(confirmBtn) {
        confirmBtn.innerText = 'Отправка...';
        confirmBtn.disabled = true;
    }
    
    let res = await fetch('api.php?action=create_appointment', {
        method:'POST', 
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({
            service_id:selectedServiceId, 
            doctor_id:selectedDoctorId, 
            date:selectedDate, 
            time:selectedTime
        })
    });
    
    let data = await res.json();
    
    // Восстанавливаем кнопку
    if(confirmBtn) {
        confirmBtn.innerText = originalText;
        confirmBtn.disabled = false;
    }
    
    if(data.success) {
        // Получаем телефон из ответа сервера
        let userPhone = data.phone || '';
        
        // Показываем красивое сообщение с телефоном
        showSuccessMessageWithPhone(userPhone);
        
        // Сбрасываем переменные
        selectedServiceId = null;
        selectedDoctorId = null;
        selectedTime = null;
        step = 2;
    } else {
        alert('Ошибка: ' + (data.error || 'Неизвестная ошибка'));
    }
}

function showSuccessMessageWithPhone(phone) {
    // Скрываем виджет записи
    document.getElementById('bookingWidgetModal').style.display = 'none';
    
    // Получаем контейнер для списка услуг
    let container = document.getElementById('servicesList');
    container.style.display = 'block';
    
    // Отображаем сообщение об успехе с телефоном
    container.innerHTML = `
        <div style="background: linear-gradient(135deg, #0F766E 0%, #0d5c56 100%); 
                    border-radius: 24px; 
                    padding: 32px 24px; 
                    text-align: center;
                    color: white;
                    margin: 20px 0;
                    animation: fadeInUp 0.5s ease;">
            <div style="font-size: 64px; margin-bottom: 16px;">✅</div>
            <h3 style="color: white; margin-bottom: 16px; font-size: 24px;">Запись создана!</h3>
            <p style="margin-bottom: 12px; font-size: 18px; opacity: 0.95;">
                Скоро с вами свяжется менеджер по телефону
            </p>
            <p style="font-size: 28px; font-weight: bold; margin: 16px 0 8px; letter-spacing: 1px; background: rgba(255,255,255,0.2); padding: 12px 20px; border-radius: 60px; display: inline-block;">
                ${phone ? formatPhoneNumber(phone) : 'указанному при регистрации'}
            </p>
            <p style="font-size: 14px; margin-top: 20px; opacity: 0.8;">
                для подтверждения записи
            </p>
            <button onclick="closeModalAndReset()" style="background: white; 
                       color: #0F766E; 
                       border: none; 
                       padding: 12px 32px; 
                       border-radius: 40px; 
                       font-size: 16px; 
                       font-weight: bold;
                       margin-top: 24px;
                       cursor: pointer;
                       transition: transform 0.2s;
                       box-shadow: 0 4px 12px rgba(0,0,0,0.1);"
                    onmouseover="this.style.transform='scale(1.05)'"
                    onmouseout="this.style.transform='scale(1)'">
                Закрыть
            </button>
        </div>
    `;
}

function formatPhoneNumber(phone) {
    // Форматирование телефона (поддержка разных форматов)
    let cleaned = phone.replace(/\D/g, '');
    
    // Российские номера (11 цифр, начинаются с 7 или 8)
    if (cleaned.length === 11 && (cleaned[0] === '7' || cleaned[0] === '8')) {
        let number = cleaned[0] === '8' ? '7' + cleaned.slice(1) : cleaned;
        return number.replace(/(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})/, '+$1 $2 $3-$4-$5');
    }
    // 10 цифр (без кода страны)
    else if (cleaned.length === 10) {
        return cleaned.replace(/(\d{3})(\d{3})(\d{2})(\d{2})/, '+7 $1 $2-$3-$4');
    }
    
    return phone;
}

function closeModalAndReset() {
    closeModal();
    
    // Небольшая задержка перед сбросом для плавности
    setTimeout(() => {
        // Сбрасываем интерфейс
        let servicesContainer = document.getElementById('servicesList');
        if(servicesContainer) {
            servicesContainer.style.display = 'block';
            servicesContainer.innerHTML = '<div style="text-align:center; padding:40px;">Загрузка услуг...</div>';
        }
        
        let bookingWidget = document.getElementById('bookingWidgetModal');
        if(bookingWidget) {
            bookingWidget.style.display = 'none';
        }
        
        // Сбрасываем переменные
        selectedServiceId = null;
        selectedDoctorId = null;
        selectedTime = null;
        step = 2;
        
        // Перезагружаем текущую категорию, если нужно (опционально)
        // Для этого нужно сохранить последнюю открытую категорию
    }, 100);
}

function closeModal() {
    let modal = document.getElementById('serviceModal');
    if(modal) {
        modal.style.display = 'none';
    }
}

function escapeHtml(str) {
    if(!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if(m === '&') return '&amp;';
        if(m === '<') return '&lt;';
        if(m === '>') return '&gt;';
        return m;
    });
}

// Навешиваем обработчики на карточки категорий
document.querySelectorAll('.category-card').forEach(card => {
    card.onclick = (e) => {
        if(e.target.tagName === 'BUTTON') e.stopPropagation();
        let category = card.dataset.category;
        let title = card.querySelector('h3').innerText;
        openModal(category, title);
    };
});

function openModal(category, categoryName) {
    let modal = document.getElementById('serviceModal');
    if(modal) {
        modal.style.display = 'block';
        showCategory(category, categoryName);
    }
}

// Предзагружаем врачей
fetchDoctors();
</script>

<style>
.category-card:hover { transform: translateY(-6px); box-shadow: 0 20px 35px rgba(0,0,0,0.1); }
.service-item:hover { background:#e6f4f0 !important; }
.time-slot:hover { background:#12FFEB !important; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 30px;
    border-radius: 28px;
    width: 90%;
    max-width: 700px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.close {
    float: right;
    font-size: 32px;
    font-weight: bold;
    cursor: pointer;
    color: #aaa;
    transition: color 0.2s;
}

.close:hover {
    color: #0F766E;
}
</style>

<?php include 'footer.php'; ?>