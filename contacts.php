<?php require 'config.php'; ?>
<?php include 'header.php'; ?>
<div class="container">
    <h1 style="font-size:2.5rem; margin:48px 0 32px;">Контакты</h1>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:48px;">
        <div style="background:white; border-radius:32px; padding:40px;">
            <i class="fas fa-map-marker-alt" style="font-size:2rem; color:#0F766E;"></i><h3>Адрес</h3><p>г. Пенза, ул. Московская, 78</p>
            <i class="fas fa-phone" style="font-size:2rem; color:#0F766E; margin-top:24px;"></i><h3>Телефон</h3><p>+7 (8412) 55-88-99</p><br><p>+7 (8412) 34-56-78 </p><br><p>+7 (8412) 23-45-67</p> 
            <i class="fas fa-envelope" style="font-size:2rem; color:#0F766E; margin-top:24px;"></i><h3>Email</h3><p>info@dentalmaster.ru</p>
            <i class="fas fa-clock" style="font-size:2rem; color:#0F766E; margin-top:24px;"></i><h3>Часы работы</h3><p>Пн-Пт 9:00–20:00, Сб 10:00–16:00, Вс по записи</p>
        </div>
        <div style="background:white; border-radius:32px; padding:40px;">
            <i class="fas fa-id-card" style="font-size:2rem; color:#0F766E;"></i><h3>Лицензия</h3><p>№ ЛО-58-01-003215 от 15.03.2024</p>
            <i class="fas fa-building" style="font-size:2rem; color:#0F766E; margin-top:24px;"></i><h3>Реквизиты</h3><p>ООО "Дентал Мастер"<br>ИНН 5836991234</p>
        </div>
    </div>

    <!-- НОВЫЙ БЛОК: Законы и права -->
    <div style="background:white; border-radius:32px; padding:40px; margin-top:48px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <i class="fas fa-gavel" style="font-size: 2rem; color:#0F766E;"></i>
            <h3 style="margin-top: 12px;">Ваши права и законодательство</h3>
        </div>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px,1fr)); gap:24px;">
            <div>
                <i class="fas fa-balance-scale" style="color:#0F766E; margin-right:8px;"></i>
                <a href="https://www.consultant.ru/document/cons_doc_LAW_39570/" target="_blank" rel="noopener noreferrer">Закон о защите прав потребителей</a>
                <p style="font-size:0.9rem; color:#4b5563; margin-top:8px;">Ст. 10 — информация об услугах, ст. 29 — право на отказ от лечения</p>
            </div>
            <div>
                <i class="fas fa-user-md" style="color:#0F766E; margin-right:8px;"></i>
                <a href="https://www.consultant.ru/document/cons_doc_LAW_121895/" target="_blank" rel="noopener noreferrer">Федеральный закон №323-ФЗ "Об основах охраны здоровья"</a>
                <p style="font-size:0.9rem; color:#4b5563; margin-top:8px;">Информированное добровольное согласие, врачебная тайна</p>
            </div>
            <div>
                <i class="fas fa-lock" style="color:#0F766E; margin-right:8px;"></i>
                <a href="https://www.consultant.ru/document/cons_doc_LAW_61801/" target="_blank" rel="noopener noreferrer">Закон о персональных данных (152-ФЗ)</a>
                <p style="font-size:0.9rem; color:#4b5563; margin-top:8px;">Конфиденциальность ваших данных в клинике</p>
            </div>
            <div>
                <i class="fas fa-file-signature" style="color:#0F766E; margin-right:8px;"></i>
                <a href="https://www.consultant.ru/document/cons_doc_LAW_424313/" target="_blank" rel="noopener noreferrer">Правила предоставления мед. услуг (Постановление №1107)</a>
                <p style="font-size:0.9rem; color:#4b5563; margin-top:8px;">Договор, порядок оплаты и жалобы</p>
            </div>
        </div>
        <p style="margin-top: 32px; padding-top: 16px; border-top: 1px solid #e5e7eb; text-align: center; font-size:0.85rem; color:#6b7280;">
            <i class="fas fa-shield-alt"></i> Все права пациентов защищены в соответствии с законодательством РФ.
        </p>
    </div>
</div>
<?php include 'footer.php'; ?>