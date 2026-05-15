<?php
require 'config.php';
if(!isLoggedIn()) { header('Location: login.php'); exit; }
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Маппинг статусов на русский
$statuses = [
    'pending' => 'Новая',
    'confirmed' => 'Подтверждена',
    'completed' => 'Завершена',
    'cancelled' => 'Отменена'
];

if($role == 'admin') { 
    header('Location: admin_dashboard.php'); 
    exit; 
}
elseif($role == 'doctor') {
    include 'header.php';
    
    // Предполагается, что у врача есть номер кабинета в БД (поле cabinet_number)
    // Если нет, можно добавить или временно указать статическое значение
    $stmt = $pdo->prepare("SELECT cabinet_number FROM user WHERE id = ?");
    $stmt->execute([$user_id]);
    $doctor = $stmt->fetch();
    $cabinet_number = $doctor['cabinet_number'] ?? 'не указан';
    
    $today = date('Y-m-d');
    
    // Записи на сегодня
    $stmt = $pdo->prepare("SELECT a.*, u.full_name as patient_name, s.name as service_name 
        FROM appointment a 
        JOIN user u ON a.patient_id = u.id 
        JOIN service s ON a.service_id = s.id 
        WHERE a.doctor_id = ? AND a.appointment_date = ? 
        ORDER BY a.appointment_time");
    $stmt->execute([$user_id, $today]);
    $todayApps = $stmt->fetchAll();
    
    // Будущие записи
    $stmt = $pdo->prepare("SELECT a.*, u.full_name as patient_name, s.name as service_name 
        FROM appointment a 
        JOIN user u ON a.patient_id = u.id 
        JOIN service s ON a.service_id = s.id 
        WHERE a.doctor_id = ? AND a.appointment_date > ? 
        ORDER BY a.appointment_date, a.appointment_time LIMIT 10");
    $stmt->execute([$user_id, $today]);
    $futureApps = $stmt->fetchAll();
    ?>
    <div class="container">
        <h1><i class="fas fa-user-md"></i> Кабинет врача</h1>
        <p>Добро пожаловать, <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong></p>
        
        <!-- Информация о кабинете врача -->
        <div class="cabinet-info">
            <i class="fas fa-door-open"></i> Ваш кабинет: <strong><?= htmlspecialchars($cabinet_number) ?></strong>
        </div>
        
        <h2><i class="fas fa-calendar-day"></i> Записи на сегодня</h2>
        <?php if(count($todayApps) > 0): ?>
            <div class="cards-grid">
                <?php foreach($todayApps as $app): ?>
                    <div class="appointment-card">
                        <div class="appointment-time"><i class="far fa-clock"></i> <?= $app['appointment_time'] ?></div>
                        <div class="appointment-patient"><i class="fas fa-user"></i> Пациент: <?= htmlspecialchars($app['patient_name']) ?></div>
                        <div class="appointment-service"><i class="fas fa-tooth"></i> Услуга: <?= htmlspecialchars($app['service_name']) ?></div>
                        <div class="appointment-cabinet"><i class="fas fa-door-open"></i> Кабинет: <?= htmlspecialchars($cabinet_number) ?></div>
                        <div class="appointment-status"><i class="fas fa-tag"></i> Статус: <strong class="status-badge status-<?= $app['status'] ?>"><?= $statuses[$app['status']] ?></strong></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p><i class="fas fa-check-circle"></i> На сегодня записей нет.</p>
        <?php endif; ?>
        
        <h2><i class="fas fa-calendar-week"></i> Ближайшие записи</h2>
        <?php if(count($futureApps) > 0): ?>
            <div class="cards-grid">
                <?php foreach($futureApps as $app): ?>
                    <div class="appointment-card future-card">
                        <div class="appointment-date">
                            <i class="far fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($app['appointment_date'])) ?> 
                            <i class="far fa-clock"></i> <?= $app['appointment_time'] ?>
                        </div>
                        <div><i class="fas fa-user"></i> Пациент: <?= htmlspecialchars($app['patient_name']) ?></div>
                        <div><i class="fas fa-tooth"></i> Услуга: <?= htmlspecialchars($app['service_name']) ?></div>
                        <div><i class="fas fa-door-open"></i> Кабинет: <?= htmlspecialchars($cabinet_number) ?></div>
                        <div><i class="fas fa-tag"></i> Статус: <strong class="status-badge status-<?= $app['status'] ?>"><?= $statuses[$app['status']] ?></strong></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p><i class="fas fa-inbox"></i> Нет будущих записей.</p>
        <?php endif; ?>
    </div>
    
    <style>
        .cabinet-info {
            background: #e2f0ed;
            padding: 12px 20px;
            border-radius: 40px;
            display: inline-block;
            margin-bottom: 24px;
            font-size: 1rem;
            color: #0F766E;
        }
        .cabinet-info i {
            margin-right: 8px;
        }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .appointment-card {
            background: white;
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: 0.2s;
            border-left: 5px solid #0F766E;
        }
        .appointment-card:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .future-card {
            border-left-color: #3b82f6;
        }
        .appointment-time, .appointment-date {
            font-size: 1.1rem;
            font-weight: bold;
            color: #0F766E;
            margin-bottom: 12px;
        }
        .future-card .appointment-date {
            color: #3b82f6;
        }
        .appointment-card div {
            margin-bottom: 8px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.8rem;
        }
        .status-pending { background: #fef3c7; color: #b45309; }
        .status-confirmed { background: #d1fae5; color: #065f46; }
        .status-completed { background: #e0e7ff; color: #3730a3; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }
        
        @media (max-width: 700px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <?php include 'footer.php';
} 
else {
    // ========== ПАЦИЕНТ ==========
    include 'header.php';
    $stmt = $pdo->prepare("SELECT a.*, u.full_name as doctor_name, s.name as service_name 
        FROM appointment a 
        JOIN user u ON a.doctor_id=u.id 
        JOIN service s ON a.service_id=s.id 
        WHERE a.patient_id=? 
        ORDER BY a.appointment_date DESC");
    $stmt->execute([$user_id]);
    $appointments = $stmt->fetchAll();
    
    $active = array_filter($appointments, function($a) {
        return in_array($a['status'], ['pending', 'confirmed']);
    });
    $history = array_filter($appointments, function($a) {
        return in_array($a['status'], ['completed', 'cancelled']);
    });
    ?>
    <div class="container">
        <h1><i class="fas fa-user-circle"></i> Личный кабинет</h1>
        <div class="tab-buttons">
            <button class="tab-btn active" data-tab="active"><i class="fas fa-check-circle"></i> Активные записи</button>
            <button class="tab-btn" data-tab="history"><i class="fas fa-history"></i> История</button>
        </div>
        
        <div id="activeTab" class="tab-content active">
            <?php if(count($active) > 0): ?>
                <?php foreach($active as $a): ?>
                    <div class="appointment-card patient-card">
                        <div class="appointment-date"><i class="far fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($a['appointment_date'])) ?> <i class="far fa-clock"></i> <?= $a['appointment_time'] ?></div>
                        <div><i class="fas fa-user-md"></i> Врач: <?= htmlspecialchars($a['doctor_name']) ?></div>
                        <div><i class="fas fa-tooth"></i> Услуга: <?= htmlspecialchars($a['service_name']) ?></div>
                        <div><i class="fas fa-ruble-sign"></i> Сумма: <?= number_format($a['total_price'],0,'',' ') ?> ₽</div>
                        <div><i class="fas fa-tag"></i> Статус: <strong class="status-badge status-<?= $a['status'] ?>"><?= $statuses[$a['status']] ?></strong></div>
                        <form method="POST" action="api.php?action=cancel_appointment" class="cancel-form">
                            <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                            <button type="submit" class="btn-cancel" onclick="return confirm('Вы действительно хотите отменить запись?')"><i class="fas fa-times-circle"></i> Отменить запись</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state"><i class="fas fa-calendar-times"></i> У вас нет активных записей. <a href="services.php">Записаться сейчас</a></div>
            <?php endif; ?>
        </div>
        
        <div id="historyTab" class="tab-content">
            <?php if(count($history) > 0): ?>
                <?php foreach($history as $a): ?>
                    <div class="appointment-card history-card">
                        <div><i class="far fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($a['appointment_date'])) ?> <i class="far fa-clock"></i> <?= $a['appointment_time'] ?></div>
                        <div><i class="fas fa-tooth"></i> <?= htmlspecialchars($a['service_name']) ?> — <?= $statuses[$a['status']] ?></div>
                        <div><i class="fas fa-user-md"></i> Врач: <?= htmlspecialchars($a['doctor_name']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state"><i class="fas fa-folder-open"></i> История записей пуста.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
        .tab-buttons {
            display: flex;
            gap: 16px;
            margin: 32px 0 24px;
            flex-wrap: wrap;
        }
        .tab-btn {
            background: white;
            border: 1px solid #cbd5e1;
            padding: 12px 28px;
            border-radius: 48px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            color: #1e3a3f;
        }
        .tab-btn i {
            margin-right: 8px;
        }
        .tab-btn.active {
            background: #0F766E;
            border-color: #0F766E;
            color: white;
            box-shadow: 0 4px 10px rgba(15,118,110,0.2);
        }
        .tab-btn:hover:not(.active) {
            background: #e2f0ed;
            border-color: #0F766E;
        }
        .appointment-card {
            background: white;
            border-radius: 28px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.05);
            transition: 0.2s;
        }
        .patient-card {
            border-left: 5px solid #0F766E;
        }
        .history-card {
            opacity: 0.85;
            background: #fafafa;
        }
        .appointment-date {
            font-weight: bold;
            font-size: 1.1rem;
            color: #0F766E;
            margin-bottom: 12px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.85rem;
        }
        .status-pending { background: #fef3c7; color: #b45309; }
        .status-confirmed { background: #d1fae5; color: #065f46; }
        .status-completed { background: #e0e7ff; color: #3730a3; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }
        
        .btn-cancel {
            background: #fff0f0;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 10px 20px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 16px;
            display: inline-block;
        }
        .btn-cancel i {
            margin-right: 6px;
        }
        .btn-cancel:hover {
            background: #fee2e2;
            border-color: #f87171;
            transform: translateY(-2px);
        }
        .cancel-form {
            margin-top: 8px;
        }
        .empty-state {
            background: white;
            border-radius: 28px;
            padding: 40px;
            text-align: center;
            color: #5c6b7a;
        }
        .empty-state a {
            color: #0F766E;
            font-weight: bold;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .tab-btn {
                flex: 1;
                text-align: center;
                padding: 10px 16px;
                font-size: 0.9rem;
            }
            .appointment-card {
                padding: 18px;
            }
        }
    </style>
    
    <script>
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
                document.getElementById(tabId + 'Tab').classList.add('active');
            });
        });
    </script>
    <?php include 'footer.php';
}
?>