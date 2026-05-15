<?php 
require 'config.php';
if(!isAdmin()) { header('Location: index.php'); exit; }

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE appointment SET status=? WHERE id=?");
    $stmt->execute([$_POST['status'], $_POST['appointment_id']]);
    
    // Устанавливаем flash сообщение
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Статус записи обновлен'];
    header('Location: admin_appointments.php'); 
    exit;
}

include 'header.php';

// Маппинг статусов на русский
$statuses = [
    'pending' => 'Новая',
    'confirmed' => 'Подтверждена',
    'completed' => 'Завершена',
    'cancelled' => 'Отменена'
];

// Получаем все записи
$appointments = $pdo->query("
    SELECT a.*, u1.full_name as patient_name, u2.full_name as doctor_name, s.name as service_name 
    FROM appointment a 
    JOIN user u1 ON a.patient_id=u1.id 
    JOIN user u2 ON a.doctor_id=u2.id 
    JOIN service s ON a.service_id=s.id 
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
")->fetchAll();
?>

<div class="container">
    <h1><i class="fas fa-calendar-check"></i> Все записи</h1>
    
    <?php if(isset($_SESSION['flash'])): ?>
        <div class="flash-message flash-<?= $_SESSION['flash']['type'] ?>">
            <i class="fas fa-<?= $_SESSION['flash']['type'] == 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Дата/Время</th>
                    <th>Пациент</th>
                    <th>Врач</th>
                    <th>Услуга</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($appointments as $a): ?>
                <tr>
                    <td data-label="Дата/Время">
                        <strong><?= date('d.m.Y', strtotime($a['appointment_date'])) ?></strong><br>
                        <small><?= $a['appointment_time'] ?></small>
                    </td>
                    <td data-label="Пациент"><?= htmlspecialchars($a['patient_name']) ?></td>
                    <td data-label="Врач"><?= htmlspecialchars($a['doctor_name']) ?></td>
                    <td data-label="Услуга"><?= htmlspecialchars($a['service_name']) ?></td>
                    <td data-label="Сумма"><strong><?= number_format($a['total_price'],0,'',' ') ?> ₽</strong></td>
                    <td data-label="Статус">
                        <span class="status-badge status-<?= $a['status'] ?>">
                            <?= $statuses[$a['status']] ?>
                        </span>
                    </td>
                    <td data-label="Действие">
                        <form method="POST" class="status-form" onsubmit="return confirm('Изменить статус записи?')">
                            <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                            <input type="hidden" name="update_status" value="1">
                            <select name="status" class="status-select">
                                <option value="pending" <?= $a['status']=='pending' ? 'selected' : '' ?>>Новая</option>
                                <option value="confirmed" <?= $a['status']=='confirmed' ? 'selected' : '' ?>>Подтверждена</option>
                                <option value="completed" <?= $a['status']=='completed' ? 'selected' : '' ?>>Завершена</option>
                                <option value="cancelled" <?= $a['status']=='cancelled' ? 'selected' : '' ?>>Отменена</option>
                            </select>
                            <button type="submit" class="btn-update">
                                <i class="fas fa-save"></i> Сохранить
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    font-size: 2rem;
    margin-bottom: 30px;
    color: #1e3a3f;
}

.flash-message {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    animation: slideIn 0.3s ease;
}

.flash-success {
    background: #d1fae5;
    color: #065f46;
    border-left: 4px solid #059669;
}

.flash-error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #dc2626;
}

.flash-message i {
    margin-right: 10px;
    font-size: 1.2rem;
}

@keyframes slideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.admin-table {
    width: 100%;
    background: white;
    border-radius: 20px;
    border-collapse: collapse;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.admin-table thead {
    background: linear-gradient(135deg, #0F766E, #0d5c56);
    color: white;
}

.admin-table th {
    padding: 16px 15px;
    font-weight: 600;
    font-size: 0.95rem;
}

.admin-table td {
    padding: 16px 15px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
}

.admin-table tr:hover {
    background: #f8fafc;
}

.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-pending { background: #fef3c7; color: #b45309; }
.status-confirmed { background: #d1fae5; color: #065f46; }
.status-completed { background: #e0e7ff; color: #3730a3; }
.status-cancelled { background: #fee2e2; color: #b91c1c; }

.status-select {
    padding: 8px 12px;
    border-radius: 30px;
    border: 1px solid #cbd5e1;
    background: white;
    font-size: 0.9rem;
    cursor: pointer;
    margin-right: 8px;
}

.btn-update {
    background: #0F766E;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-update:hover {
    background: #0a5c55;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(15,118,110,0.3);
}

.status-form {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

/* Адаптив для мобильных */
@media (max-width: 768px) {
    .admin-table thead {
        display: none;
    }
    
    .admin-table tbody tr {
        display: block;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 15px;
    }
    
    .admin-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border: none;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .admin-table td:last-child {
        border-bottom: none;
    }
    
    .admin-table td:before {
        content: attr(data-label);
        font-weight: bold;
        color: #0F766E;
        margin-right: 15px;
    }
    
    .status-form {
        justify-content: flex-end;
    }
}
</style>

<?php include 'footer.php'; ?>