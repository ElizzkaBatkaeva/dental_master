<?php require 'config.php';
if(!isAdmin()) { header('Location: index.php'); exit; }

// Добавление услуги
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['add_service'])) {
    $stmt = $pdo->prepare("INSERT INTO service (name, category, price, duration, description, icon) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$_POST['name'], $_POST['category'], $_POST['price'], $_POST['duration'], $_POST['description'], $_POST['icon']]);
    header('Location: admin_services.php'); exit;
}
// Редактирование услуги
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['edit_service'])) {
    $stmt = $pdo->prepare("UPDATE service SET name=?, category=?, price=?, duration=?, description=?, icon=? WHERE id=?");
    $stmt->execute([$_POST['name'], $_POST['category'], $_POST['price'], $_POST['duration'], $_POST['description'], $_POST['icon'], $_POST['service_id']]);
    header('Location: admin_services.php'); exit;
}
// Удаление
if(isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM service WHERE id=?")->execute([$_GET['delete']]);
    header('Location: admin_services.php'); exit;
}

include 'header.php';
$services = $pdo->query("SELECT * FROM service")->fetchAll();
?>

<div class="container">
    <h1>Управление услугами</h1>
    
    <!-- Форма добавления услуги с подписями -->
    <form method="POST" style="background:white; padding:24px; border-radius:32px; margin-bottom:30px;">
        <h3>Добавить новую услугу</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:16px;">
            <div><label><strong>Название *</strong></label><input type="text" name="name" required></div>
            <div><label><strong>Категория *</strong></label>
                <select name="category" required>
                    <option value="diagnostics">Диагностика</option><option value="therapy">Лечение</option>
                    <option value="surgery">Хирургия</option><option value="implants">Имплантация</option>
                    <option value="prosthetics">Протезирование</option><option value="hygiene">Гигиена</option>
                </select>
            </div>
            <div><label><strong>Цена (₽) *</strong></label><input type="number" name="price" required></div>
            <div><label><strong>Длительность (мин) *</strong></label><input type="number" name="duration" required></div>
            <div><label><strong>Иконка (fa-xxx)</strong></label><input type="text" name="icon" placeholder="fa-tooth"></div>
            <div style="grid-column:span 2;"><label><strong>Описание</strong></label><textarea name="description" rows="2" style="width:100%;"></textarea></div>
        </div>
        <button type="submit" name="add_service" class="btn-primary" style="margin-top:20px;">Добавить услугу</button>
    </form>

    <!-- Таблица услуг с кнопкой редактирования -->
    <div style="overflow-x:auto;">
        <table style="width:100%; background:white; border-radius:24px; border-collapse:collapse;">
            <thead style="background:#0F766E; color:white;"><tr><th>Название</th><th>Категория</th><th>Цена</th><th>Длит.</th><th>Действия</th></tr></thead>
            <tbody>
            <?php foreach($services as $s): ?>
                <tr>
                    <td style="padding:12px;"><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= $s['category'] ?></td>
                    <td><?= number_format($s['price'],0,'',' ') ?> ₽</td>
                    <td><?= $s['duration'] ?> мин</td>
                    <td>
                        <button class="btn-outline" style="padding:4px 12px;" onclick="openEditService(<?= htmlspecialchars(json_encode($s)) ?>)">Ред.</button>
                        <a href="?delete=<?= $s['id'] ?>" onclick="return confirm('Удалить?')" style="color:#e11d48; margin-left:10px;">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Модальное окно редактирования услуги -->
<div id="editServiceModal" class="modal">
    <div class="modal-content" style="max-width:600px;">
        <span class="close" onclick="closeEditServiceModal()">&times;</span>
        <h2>Редактирование услуги</h2>
        <form method="POST">
            <input type="hidden" name="service_id" id="edit_service_id">
            <div><label><strong>Название</strong></label><input type="text" name="name" id="edit_service_name" required></div>
            <div><label><strong>Категория</strong></label>
                <select name="category" id="edit_service_category">
                    <option value="diagnostics">Диагностика</option><option value="therapy">Лечение</option>
                    <option value="surgery">Хирургия</option><option value="implants">Имплантация</option>
                    <option value="prosthetics">Протезирование</option><option value="hygiene">Гигиена</option>
                </select>
            </div>
            <div><label><strong>Цена (₽)</strong></label><input type="number" name="price" id="edit_service_price" required></div>
            <div><label><strong>Длительность (мин)</strong></label><input type="number" name="duration" id="edit_service_duration" required></div>
            <div><label><strong>Иконка (fa-xxx)</strong></label><input type="text" name="icon" id="edit_service_icon"></div>
            <div><label><strong>Описание</strong></label><textarea name="description" id="edit_service_description" rows="3"></textarea></div>
            <button type="submit" name="edit_service" class="btn-primary" style="margin-top:15px;">Сохранить</button>
        </form>
    </div>
</div>

<script>
function openEditService(service) {
    document.getElementById('edit_service_id').value = service.id;
    document.getElementById('edit_service_name').value = service.name;
    document.getElementById('edit_service_category').value = service.category;
    document.getElementById('edit_service_price').value = service.price;
    document.getElementById('edit_service_duration').value = service.duration;
    document.getElementById('edit_service_icon').value = service.icon || '';
    document.getElementById('edit_service_description').value = service.description || '';
    document.getElementById('editServiceModal').style.display = 'block';
}
function closeEditServiceModal() {
    document.getElementById('editServiceModal').style.display = 'none';
}
window.onclick = function(e) { if(e.target == document.getElementById('editServiceModal')) closeEditServiceModal(); }
</script>

<?php include 'footer.php'; ?>