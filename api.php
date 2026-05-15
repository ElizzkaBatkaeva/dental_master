<?php
require 'config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if($action == 'get_services') {
    $stmt = $pdo->query("SELECT id, name, price, duration FROM service WHERE is_active=1");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif($action == 'get_doctors') {
    $stmt = $pdo->query("SELECT id, full_name as name, specialty FROM user WHERE role='doctor' AND is_active=1");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif($action == 'get_free_times') {
    $doctor_id = $_GET['doctor_id'];
    $date = $_GET['date'];
    $stmt = $pdo->prepare("SELECT schedule_json FROM user WHERE id=?");
    $stmt->execute([$doctor_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$doctor || empty($doctor['schedule_json'])) {
        echo json_encode([]);
        exit;
    }
    $schedule = json_decode($doctor['schedule_json'], true);
    if(!is_array($schedule)) $schedule = [];
    $stmt = $pdo->prepare("SELECT appointment_time FROM appointment WHERE doctor_id=? AND appointment_date=? AND status IN ('pending','confirmed')");
    $stmt->execute([$doctor_id, $date]);
    $booked = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $free = array_diff($schedule, $booked);
    echo json_encode(array_values($free));
}
elseif($action == 'create_appointment') {
    if(!isLoggedIn()) { 
        http_response_code(401); 
        echo json_encode(['error'=>'Не авторизован']); 
        exit; 
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $service_id = $data['service_id'];
    $doctor_id = $data['doctor_id'];
    $date = $data['date'];
    $time = $data['time'];
    
    // Проверяем, что время ещё свободно
    $stmt = $pdo->prepare("SELECT schedule_json FROM user WHERE id=?");
    $stmt->execute([$doctor_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    $schedule = json_decode($doctor['schedule_json'], true);
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointment WHERE doctor_id=? AND appointment_date=? AND appointment_time=? AND status IN ('pending','confirmed')");
    $stmt->execute([$doctor_id, $date, $time]);
    $isBooked = $stmt->fetchColumn() > 0;
    
    if($isBooked || !in_array($time, $schedule)) {
        echo json_encode(['error'=>'Это время уже занято']);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT price FROM service WHERE id=?");
    $stmt->execute([$service_id]);
    $price = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("INSERT INTO appointment (patient_id, doctor_id, service_id, appointment_date, appointment_time, total_price, status) VALUES (?,?,?,?,?,?,'pending')");
    $stmt->execute([$_SESSION['user_id'], $doctor_id, $service_id, $date, $time, $price]);
    
    // Получаем телефон клиента
    $userPhone = getUserPhone($pdo, $_SESSION['user_id']);
    
    echo json_encode([
        'success'=>true, 
        'appointment_id'=>$pdo->lastInsertId(),
        'phone'=>$userPhone
    ]);
}
elseif($action == 'cancel_appointment' && $_SERVER['REQUEST_METHOD']=='POST') {
    $id = $_POST['appointment_id'];
    $stmt = $pdo->prepare("UPDATE appointment SET status='cancelled' WHERE id=? AND patient_id=?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $_SESSION['flash'] = ['type'=>'success', 'message'=>'Запись отменена'];
    header('Location: cabinet.php');
}
elseif($action == 'get_services_by_category') {
    $category = $_GET['category'] ?? '';
    $stmt = $pdo->prepare("SELECT id, name, price, duration, description FROM service WHERE category=? AND is_active=1");
    $stmt->execute([$category]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif($action == 'get_all_services') {
    $stmt = $pdo->query("SELECT id, name, price, duration, description, category FROM service WHERE is_active=1");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif($action == 'get_service_details') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT id, name, price, duration, description FROM service WHERE id=?");
    $stmt->execute([$id]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}
elseif($action == 'update_appointment_status' && $_SERVER['REQUEST_METHOD']=='POST') {
    if(!isDoctor() && !isAdmin()) { 
        http_response_code(403); 
        echo json_encode(['error'=>'Доступ запрещён']); 
        exit; 
    }
    $id = $_POST['appointment_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE appointment SET status=? WHERE id=?");
    $stmt->execute([$status, $id]);
    $_SESSION['flash'] = ['type'=>'success', 'message'=>'Статус обновлён'];
    header('Location: cabinet.php');
    exit;
}
elseif($action == 'update_appointment_status' && $_SERVER['REQUEST_METHOD']=='POST') {
    if(!isDoctor() && !isAdmin()) { 
        http_response_code(403); 
        echo json_encode(['error'=>'Доступ запрещён']); 
        exit; 
    }
    $id = $_POST['appointment_id'];
    $status = $_POST['status'];
    
    // Проверяем, что статус допустимый
    $allowedStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if(!in_array($status, $allowedStatuses)) {
        $_SESSION['flash'] = ['type'=>'error', 'message'=>'Недопустимый статус'];
        header('Location: cabinet.php');
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE appointment SET status=? WHERE id=?");
    $stmt->execute([$status, $id]);
    
    $_SESSION['flash'] = ['type'=>'success', 'message'=>'Статус записи обновлён'];
    
    // Возвращаемся на страницу, откуда пришли
    $referer = $_SERVER['HTTP_REFERER'] ?? 'cabinet.php';
    header('Location: ' . $referer);
    exit;
}
else {
    echo json_encode(['error'=>'Unknown action']);
}
?>