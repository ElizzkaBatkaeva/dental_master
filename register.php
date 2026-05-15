<?php require 'config.php';
if(isLoggedIn()) { header('Location: cabinet.php'); exit; }
if($_SERVER['REQUEST_METHOD']=='POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $age = (int)$_POST['age'];
    $pass = $_POST['password'];
    $pass2 = $_POST['password2'];
    $agree = isset($_POST['agree']) ? 1 : 0;
    
    // Валидация
    if($pass != $pass2) { $_SESSION['flash']=['type'=>'error','message'=>'Пароли не совпадают']; header('Location: register.php'); exit; }
    if($age < 18) { $_SESSION['flash']=['type'=>'error','message'=>'Вам должно быть 18 лет или больше']; header('Location: register.php'); exit; }
    if(!$agree) { $_SESSION['flash']=['type'=>'error','message'=>'Необходимо согласие с условиями']; header('Location: register.php'); exit; }
    
    $stmt = $pdo->prepare("SELECT id FROM user WHERE email=?");
    $stmt->execute([$email]);
    if($stmt->fetch()) { $_SESSION['flash']=['type'=>'error','message'=>'Email уже занят']; header('Location: register.php'); exit; }
    
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO user (email, password_hash, full_name, phone, gender, age, role, agree_terms) VALUES (?,?,?,?,?,?,'patient',?)");
    $stmt->execute([$email, $hash, $full_name, $phone, $gender, $age, $agree]);
    
    $id = $pdo->lastInsertId();
    $_SESSION['user_id'] = $id;
    $_SESSION['full_name'] = $full_name;
    $_SESSION['role'] = 'patient';
    $_SESSION['flash'] = ['type'=>'success', 'message'=>'Регистрация успешна!'];
    header('Location: cabinet.php');
    exit;
}
include 'header.php'; ?>
<div class="container" style="max-width:600px; margin:60px auto;">
    <div style="background:white; border-radius:32px; padding:48px; box-shadow:0 20px 35px rgba(0,0,0,0.05);">
        <h1 style="text-align:center; margin-bottom:24px;">Регистрация</h1>
        <form method="POST">
            <div class="form-group">
                <label>ФИО *</label>
                <input type="text" name="full_name" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Телефон *</label>
                <input type="tel" name="phone" required>
            </div>
            
            <div class="form-group">
                <label>Пол *</label>
                <div style="display: flex; gap: 20px; margin-top: 8px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                        <input type="radio" name="gender" value="male" required> Мужской
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                        <input type="radio" name="gender" value="female" required> Женский
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Возраст (от 18 лет) *</label>
                <input type="number" name="age" min="18" max="120" required>
            </div>
            
            <div class="form-group">
                <label>Пароль *</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Подтверждение пароля *</label>
                <input type="password" name="password2" required>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: flex-start; gap: 10px; font-weight: normal; cursor: pointer;">
                    <input type="checkbox" name="agree" required style="width: auto; margin-top: 3px;">
                    <span>Я принимаю <a href="terms.php" target="_blank">Пользовательское соглашение</a> и <a href="privacy.php" target="_blank">Политику конфиденциальности</a></span>
                </label>
            </div>
            
            <button type="submit" class="btn-primary" style="width:100%;">Зарегистрироваться</button>
        </form>
        <p style="text-align:center; margin-top:24px;">Уже есть аккаунт? <a href="login.php" style="color:#0F766E;">Войти</a></p>
    </div>
</div>
<?php include 'footer.php'; ?>