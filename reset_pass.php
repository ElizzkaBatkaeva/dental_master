<?php
require 'config.php';

// Хеш для пароля "doctor123"
$new_password_hash = password_hash('doctor123', PASSWORD_DEFAULT);

echo "Новый хеш для пароля 'doctor123': " . $new_password_hash . "<br><br>";

// Обновляем пароли для всех докторов
$stmt = $pdo->prepare("UPDATE user SET password_hash = ? WHERE role = 'doctor'");
$stmt->execute([$new_password_hash]);

$count = $stmt->rowCount();
echo "Обновлено докторов: " . $count . "<br><br>";

// Проверяем обновленных докторов
$stmt = $pdo->prepare("SELECT id, email, full_name, role FROM user WHERE role = 'doctor'");
$stmt->execute();
$doctors = $stmt->fetchAll();

echo "Список докторов с новым паролем 'doctor123':<br>";
echo "<ul>";
foreach($doctors as $doctor) {
    echo "<li>" . htmlspecialchars($doctor['email']) . " - " . htmlspecialchars($doctor['full_name']) . "</li>";
}
echo "</ul>";

echo "<br><a href='login.php'>Перейти на страницу входа</a>";
?>