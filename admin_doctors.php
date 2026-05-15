<?php require 'config.php';
if(!isAdmin()) { header('Location: index.php'); exit; }

// Создаём папку для сертификатов
$certsDir = 'uploads/certs/';
if(!is_dir($certsDir)) mkdir($certsDir, 0777, true);

// Добавление врача
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['add_doctor'])) {
    $photoName = null;
    if(isset($_FILES['photo']) && $_FILES['photo']['error']==0) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photoName = time().'.'.strtolower($ext);
        move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/'.$photoName);
    }
    $stmt = $pdo->prepare("INSERT INTO user (email, password_hash, full_name, phone, specialty, experience, bio, photo, role) VALUES (?,?,?,?,?,?,?,?,'doctor')");
    $stmt->execute([$_POST['email'], password_hash($_POST['password'],PASSWORD_DEFAULT), $_POST['full_name'], $_POST['phone'], $_POST['specialty'], $_POST['experience'], $_POST['bio'], $photoName]);
    
    $doctorId = $pdo->lastInsertId();
    // Загрузка сертификатов
    if(isset($_FILES['certificates']) && !empty($_FILES['certificates']['name'][0])) {
        $certs = [];
        foreach($_FILES['certificates']['tmp_name'] as $key => $tmp_name) {
            if($_FILES['certificates']['error'][$key] == 0 && !empty($tmp_name)) {
                $origName = basename($_FILES['certificates']['name'][$key]);
                $newName = time().'_'.$key.'_'.preg_replace('/[^a-zA-Z0-9\._-]/', '', $origName);
                if(move_uploaded_file($tmp_name, $certsDir.$newName)) {
                    $certs[] = $newName;
                }
            }
        }
        if(!empty($certs)) {
            $pdo->prepare("UPDATE user SET certificates=? WHERE id=?")->execute([json_encode($certs), $doctorId]);
        }
    }
    header('Location: admin_doctors.php'); exit;
}

// Редактирование врача
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['edit_doctor'])) {
    $id = $_POST['doctor_id'];
    $photoName = $_POST['existing_photo'];
    if(isset($_FILES['photo']) && $_FILES['photo']['error']==0) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photoName = time().'.'.strtolower($ext);
        move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/'.$photoName);
    }
    // Обновляем основные данные
    $stmt = $pdo->prepare("UPDATE user SET email=?, full_name=?, phone=?, specialty=?, experience=?, bio=?, photo=? WHERE id=?");
    $stmt->execute([$_POST['email'], $_POST['full_name'], $_POST['phone'], $_POST['specialty'], $_POST['experience'], $_POST['bio'], $photoName, $id]);
    
    // Загрузка новых сертификатов
    if(isset($_FILES['certificates']) && !empty($_FILES['certificates']['name'][0])) {
        $newCerts = [];
        foreach($_FILES['certificates']['tmp_name'] as $key => $tmp_name) {
            if($_FILES['certificates']['error'][$key] == 0 && !empty($tmp_name)) {
                $origName = basename($_FILES['certificates']['name'][$key]);
                $newName = time().'_'.$key.'_'.preg_replace('/[^a-zA-Z0-9\._-]/', '', $origName);
                if(move_uploaded_file($tmp_name, $certsDir.$newName)) {
                    $newCerts[] = $newName;
                }
            }
        }
        if(!empty($newCerts)) {
            $existing = $pdo->prepare("SELECT certificates FROM user WHERE id=?");
            $existing->execute([$id]);
            $oldCerts = json_decode($existing->fetchColumn(), true) ?: [];
            $allCerts = array_merge($oldCerts, $newCerts);
            $pdo->prepare("UPDATE user SET certificates=? WHERE id=?")->execute([json_encode($allCerts), $id]);
        }
    }
    // Удаление отмеченных сертификатов
    if(isset($_POST['delete_certs']) && is_array($_POST['delete_certs'])) {
        $existing = $pdo->prepare("SELECT certificates FROM user WHERE id=?");
        $existing->execute([$id]);
        $certs = json_decode($existing->fetchColumn(), true) ?: [];
        foreach($_POST['delete_certs'] as $certToDelete) {
            $key = array_search($certToDelete, $certs);
            if($key !== false) {
                unset($certs[$key]);
                @unlink($certsDir.$certToDelete);
            }
        }
        $pdo->prepare("UPDATE user SET certificates=? WHERE id=?")->execute([json_encode(array_values($certs)), $id]);
    }
    header('Location: admin_doctors.php'); exit;
}

// Удаление врача
if(isset($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT certificates FROM user WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    $certs = json_decode($stmt->fetchColumn(), true) ?: [];
    foreach($certs as $cert) @unlink($certsDir.$cert);
    $pdo->prepare("DELETE FROM user WHERE id=?")->execute([$_GET['delete']]);
    header('Location: admin_doctors.php'); exit;
}

include 'header.php';
$doctors = $pdo->query("SELECT * FROM user WHERE role='doctor'")->fetchAll();
?>

<div class="container">
    <h1>Управление врачами</h1>
    
    <!-- ФОРМА ДОБАВЛЕНИЯ ВРАЧА (с полем для сертификатов) -->
    <form method="POST" enctype="multipart/form-data" style="background:white; padding:24px; border-radius:32px; margin-bottom:40px;">
        <h3>Добавить нового врача</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px;">
            <div><label><strong>ФИО *</strong></label><input type="text" name="full_name" required></div>
            <div><label><strong>Email *</strong></label><input type="email" name="email" required></div>
            <div><label><strong>Телефон</strong></label><input type="text" name="phone"></div>
            <div><label><strong>Специализация</strong></label><input type="text" name="specialty"></div>
            <div><label><strong>Опыт (лет)</strong></label><input type="number" name="experience"></div>
            <div><label><strong>Пароль *</strong></label><input type="password" name="password" required></div>
            <div><label><strong>Фото</strong></label><input type="file" name="photo" accept="image/*"></div>
            <div style="grid-column:span 2;">
                <label><strong>Сертификаты (можно выбрать несколько файлов: JPG, PNG, PDF)</strong></label>
                <input type="file" name="certificates[]" multiple accept="image/*,application/pdf">
            </div>
            <div style="grid-column:span 2;"><label><strong>Биография</strong></label><textarea name="bio" rows="3" style="width:100%;"></textarea></div>
        </div>
        <button type="submit" name="add_doctor" class="btn-primary" style="margin-top:20px;">Добавить врача</button>
    </form>

    <!-- СПИСОК ВРАЧЕЙ -->
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:24px;">
        <?php foreach($doctors as $d): 
            $photo = !empty($d['photo']) && file_exists('uploads/'.$d['photo']) ? 'uploads/'.$d['photo'] : 'https://randomuser.me/api/portraits/men/1.jpg';
            $certs = json_decode($d['certificates'], true) ?: [];
        ?>
            <div style="background:white; border-radius:28px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
                <div style="display:flex; gap:20px; align-items:center;">
                    <img src="<?= $photo ?>" style="width:80px; height:80px; object-fit:cover; border-radius:50%;">
                    <div>
                        <h3 style="margin:0;"><?= htmlspecialchars($d['full_name']) ?></h3>
                        <p style="margin:5px 0;"><?= htmlspecialchars($d['specialty']) ?></p>
                        <button class="btn-outline" style="padding:6px 16px; font-size:0.9rem;" onclick='openEditDoctor(<?= json_encode($d) ?>, <?= json_encode($certs) ?>)'>Редактировать</button>
                        <a href="?delete=<?= $d['id'] ?>" onclick="return confirm('Удалить врача?')" style="color:#e11d48; margin-left:10px;">Удалить</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- МОДАЛЬНОЕ ОКНО РЕДАКТИРОВАНИЯ (с полем для сертификатов и отображением текущих) -->
<div id="editDoctorModal" class="modal">
    <div class="modal-content" style="max-width:700px;">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Редактирование врача</h2>
        <form method="POST" enctype="multipart/form-data" id="editDoctorForm">
            <input type="hidden" name="doctor_id" id="edit_doctor_id">
            <input type="hidden" name="existing_photo" id="edit_existing_photo">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div><label><strong>ФИО</strong></label><input type="text" name="full_name" id="edit_full_name" required></div>
                <div><label><strong>Email</strong></label><input type="email" name="email" id="edit_email" required></div>
                <div><label><strong>Телефон</strong></label><input type="text" name="phone" id="edit_phone"></div>
                <div><label><strong>Специализация</strong></label><input type="text" name="specialty" id="edit_specialty"></div>
                <div><label><strong>Опыт (лет)</strong></label><input type="number" name="experience" id="edit_experience"></div>
                <div><label><strong>Новое фото</strong></label><input type="file" name="photo" accept="image/*"></div>
                <div style="grid-column:span 2;"><label><strong>Биография</strong></label><textarea name="bio" id="edit_bio" rows="3" style="width:100%;"></textarea></div>
                <div style="grid-column:span 2;">
                    <label><strong>Добавить новые сертификаты (можно несколько)</strong></label>
                    <input type="file" name="certificates[]" multiple accept="image/*,application/pdf">
                </div>
                <div style="grid-column:span 2;">
                    <label><strong>Текущие сертификаты (отметьте для удаления)</strong></label>
                    <div id="currentCerts" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:10px;"></div>
                </div>
            </div>
            <div style="display:flex; gap:15px; margin-top:20px;">
                <button type="submit" name="edit_doctor" class="btn-primary">Сохранить изменения</button>
                <button type="button" class="btn-outline" onclick="closeEditModal()">Отмена</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditDoctor(doctor, certs) {
    document.getElementById('edit_doctor_id').value = doctor.id;
    document.getElementById('edit_full_name').value = doctor.full_name;
    document.getElementById('edit_email').value = doctor.email;
    document.getElementById('edit_phone').value = doctor.phone || '';
    document.getElementById('edit_specialty').value = doctor.specialty || '';
    document.getElementById('edit_experience').value = doctor.experience || '';
    document.getElementById('edit_bio').value = doctor.bio || '';
    document.getElementById('edit_existing_photo').value = doctor.photo || '';
    
    let container = document.getElementById('currentCerts');
    if(certs.length) {
        let html = '';
        certs.forEach(cert => {
            let ext = cert.split('.').pop().toLowerCase();
            let preview = '';
            if(ext === 'pdf') {
                preview = `<a href="uploads/certs/${cert}" target="_blank">📄 ${cert}</a>`;
            } else {
                preview = `<img src="uploads/certs/${cert}" style="height:60px; border-radius:8px;">`;
            }
            html += `<div style="border:1px solid #ddd; padding:6px; border-radius:12px; text-align:center;">
                        ${preview}<br>
                        <label><input type="checkbox" name="delete_certs[]" value="${cert}"> Удалить</label>
                    </div>`;
        });
        container.innerHTML = html;
    } else {
        container.innerHTML = '<p>Нет загруженных сертификатов</p>';
    }
    document.getElementById('editDoctorModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editDoctorModal').style.display = 'none';
}
window.onclick = function(e) { if(e.target == document.getElementById('editDoctorModal')) closeEditModal(); }
</script>

<?php include 'footer.php'; ?>