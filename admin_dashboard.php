<?php require 'config.php';
if(!isAdmin()) { header('Location: index.php'); exit; }
include 'header.php';
$stats = [];
$stats['total_patients'] = $pdo->query("SELECT COUNT(*) FROM user WHERE role='patient'")->fetchColumn();
$stats['total_doctors'] = $pdo->query("SELECT COUNT(*) FROM user WHERE role='doctor'")->fetchColumn();
$stats['total_services'] = $pdo->query("SELECT COUNT(*) FROM service")->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointment WHERE appointment_date=?");
$stmt->execute([date('Y-m-d')]);
$stats['today_appointments'] = $stmt->fetchColumn();

$stats['pending'] = $pdo->query("SELECT COUNT(*) FROM appointment WHERE status='pending'")->fetchColumn();
?>
<div class="container">
    <div style="background:linear-gradient(135deg,#0F766E,#1BAF8C); color:white; padding:40px; border-radius:32px;"><h1>Админ-панель</h1></div>
    <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:20px; margin:32px 0;">
        <div class="stat-card">Пациентов: <?= $stats['total_patients'] ?></div>
        <div class="stat-card">Врачей: <?= $stats['total_doctors'] ?></div>
        <div class="stat-card">Услуг: <?= $stats['total_services'] ?></div>
        <div class="stat-card">Записей сегодня: <?= $stats['today_appointments'] ?></div>
        <div class="stat-card">Ожидают: <?= $stats['pending'] ?></div>
    </div>
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:24px;">
        <a href="admin_services.php" class="btn-primary">Управление услугами</a>
        <a href="admin_doctors.php" class="btn-primary">Управление врачами</a>
        <a href="admin_appointments.php" class="btn-primary">Все записи</a>
    </div>
</div>
<style>.stat-card{background:white; padding:20px; border-radius:24px; text-align:center; font-weight:bold; box-shadow:0 2px 6px rgba(0,0,0,0.05);}</style>
<?php include 'footer.php'; ?>