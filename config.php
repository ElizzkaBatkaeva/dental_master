<?php
session_start();
define('DB_HOST', 'localhost');
define('DB_NAME', 'dental_master');
define('DB_USER', 'root');
define('DB_PASS', 'root');

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS ".DB_NAME);
    $pdo->exec("USE ".DB_NAME);
} catch(PDOException $e) {
    die("Ошибка подключения: ".$e->getMessage());
}

// Функция для проверки и инициализации БД
function initDatabase($pdo) {
    $tables = $pdo->query("SHOW TABLES")->fetchAll();
    if(count($tables) == 0) {
        $sql = "
        CREATE TABLE `user` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `email` VARCHAR(100) UNIQUE NOT NULL,
            `password_hash` VARCHAR(200) NOT NULL,
            `full_name` VARCHAR(150) NOT NULL,
            `phone` VARCHAR(20) NOT NULL,
            `role` VARCHAR(20) DEFAULT 'patient',
            `specialty` VARCHAR(200) DEFAULT NULL,
            `experience` INT DEFAULT NULL,
            `bio` TEXT DEFAULT NULL,
            `schedule_json` TEXT,
            `photo` VARCHAR(255) DEFAULT NULL,
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE `service` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(200) NOT NULL,
            `category` VARCHAR(50) NOT NULL,
            `price` INT NOT NULL,
            `duration` INT DEFAULT 30,
            `description` TEXT,
            `icon` VARCHAR(50) DEFAULT 'fa-tooth',
            `is_active` TINYINT(1) DEFAULT 1
        );
        
        CREATE TABLE `appointment` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `patient_id` INT NOT NULL,
            `doctor_id` INT NOT NULL,
            `service_id` INT NOT NULL,
            `appointment_date` DATE NOT NULL,
            `appointment_time` VARCHAR(10) NOT NULL,
            `status` VARCHAR(20) DEFAULT 'pending',
            `total_price` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`patient_id`) REFERENCES `user`(`id`),
            FOREIGN KEY (`doctor_id`) REFERENCES `user`(`id`),
            FOREIGN KEY (`service_id`) REFERENCES `service`(`id`)
        );
        ";
        $pdo->exec($sql);
        
        // Добавление администратора
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO `user` (email, password_hash, full_name, phone, role) VALUES (?, ?, ?, ?, ?)")
            ->execute(['admin@dentalmaster.ru', $adminPass, 'Администратор', '+7 (8412) 55-88-99', 'admin']);
        
        // Добавление врачей
        $doctors = [
            ['vetrova@dentalmaster.ru', 'Анна Сергеевна Ветрова', 'Имплантолог, стаж 14 лет', 14, '["09:00","10:00","11:00","12:00","14:00","15:00","16:00"]', 'doctor1.jpg'],
            ['orlov@dentalmaster.ru', 'Дмитрий Павлович Орлов', 'Хирург, стаж 11 лет', 11, '["10:00","11:00","12:00","13:00","15:00","16:00","17:00"]', 'doctor2.jpg'],
            ['morozova@dentalmaster.ru', 'Елена Викторовна Морозова', 'Терапевт, стаж 9 лет', 9, '["09:30","10:30","11:30","13:30","14:30","15:30"]', 'doctor3.jpg']
        ];
        $stmt = $pdo->prepare("INSERT INTO `user` (email, password_hash, full_name, phone, specialty, experience, schedule_json, photo, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'doctor')");
        foreach($doctors as $d) {
            $stmt->execute([$d[0], password_hash('doctor123', PASSWORD_DEFAULT), $d[1], '+7 (8412) 000-000', $d[2], $d[3], $d[4], $d[5]]);
        }
        
        // Добавление услуг
        $services = [
            ['Осмотр и консультация', 'diagnostics', 850, 30, 'fa-stethoscope', 'Полный осмотр, консультация врача, составление плана лечения'],
            ['Лечение кариеса', 'therapy', 3500, 60, 'fa-tooth', 'Эстетическая реставрация зубов'],
            ['Профессиональная чистка', 'hygiene', 2900, 45, 'fa-spray-can-sparkles', 'Ультразвуковая чистка, AirFlow'],
            ['Удаление зуба', 'surgery', 5200, 50, 'fa-syringe', 'Атравматичное удаление любой сложности'],
            ['Имплантация зуба', 'implants', 45000, 90, 'fa-microscope', 'Имплантация под ключ'],
            ['Коронка из циркония', 'prosthetics', 28000, 120, 'fa-crown', 'Цельнокерамическая коронка']
        ];
        $stmt = $pdo->prepare("INSERT INTO `service` (name, category, price, duration, icon, description) VALUES (?, ?, ?, ?, ?, ?)");
        foreach($services as $s) {
            $stmt->execute($s);
        }
    }
}

initDatabase($pdo);

// --- ФИКСАЦИЯ ПАРОЛЕЙ (чтобы работали всегда) ---
function fixPasswords($pdo) {
    // Админ
    $admin = $pdo->prepare("SELECT id FROM user WHERE email = 'admin@dentalmaster.ru'");
    $admin->execute();
    if($admin->fetch()) {
        $pdo->prepare("UPDATE user SET password_hash = ? WHERE email = 'admin@dentalmaster.ru'")
            ->execute([password_hash('admin123', PASSWORD_DEFAULT)]);
    }
    // Врачи
    $doctors = ['vetrova@dentalmaster.ru', 'orlov@dentalmaster.ru', 'morozova@dentalmaster.ru'];
    $hash = password_hash('doctor123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE user SET password_hash = ? WHERE email = ?");
    foreach($doctors as $email) {
        $stmt->execute([$hash, $email]);
    }
}
fixPasswords($pdo);

// Функции аутентификации
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
function isDoctor() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'doctor';
}
function isPatient() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'patient';
}

// Функция для получения телефона пользователя по ID
function getUserPhone($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT phone FROM user WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}

// В конце функции initDatabase() или отдельно после неё:
function updateDatabaseStructure($pdo) {
    // Добавляем колонку certificates, если её нет
    $stmt = $pdo->query("SHOW COLUMNS FROM user LIKE 'certificates'");
    if($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE user ADD COLUMN certificates TEXT NULL DEFAULT NULL");
    }
}
updateDatabaseStructure($pdo);
?>