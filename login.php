<?php require 'config.php';
if(isLoggedIn()) { header('Location: cabinet.php'); exit; }
if($_SERVER['REQUEST_METHOD']=='POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if($user && password_verify($pass, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['flash'] = ['type'=>'success', 'message'=>'Добро пожаловать!'];
        header('Location: cabinet.php');
        exit;
    } else {
        $_SESSION['flash'] = ['type'=>'error', 'message'=>'Неверный email или пароль'];
        header('Location: login.php');
        exit;
    }
}
include 'header.php'; ?>
<div class="container" style="max-width:500px; margin:60px auto;">
    <div style="background:white; border-radius:32px; padding:48px;">
        <h1 style="text-align:center;">Вход</h1>
        <form method="POST">
            <div style="margin-bottom:16px;"><label>Email</label><input type="email" name="email" required style="width:100%; padding:14px; border-radius:16px;"></div>
            <div style="margin-bottom:24px;"><label>Пароль</label><input type="password" name="password" required style="width:100%; padding:14px; border-radius:16px;"></div>
            <button type="submit" class="btn-primary" style="width:100%;">Войти</button>
        </form>
        <p style="text-align:center; margin-top:24px;">Нет аккаунта? <a href="register.php" style="color:#0F766E;">Регистрация</a></p>
    </div>
</div>
<?php include 'footer.php'; ?>