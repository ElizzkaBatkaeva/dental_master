<?php require 'config.php'; ?>
<?php include 'header.php'; ?>

<div class="container">
    <h1 style="font-size:2.5rem; margin:48px 0 32px;">Наши врачи</h1>
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:32px;">
        <?php
        $stmt = $pdo->query("SELECT * FROM user WHERE role='doctor' AND is_active=1 ORDER BY experience DESC");
        while($doc = $stmt->fetch(PDO::FETCH_ASSOC)):
            $photo = !empty($doc['photo']) && file_exists('uploads/'.$doc['photo']) ? 'uploads/'.$doc['photo'] : 'https://randomuser.me/api/portraits/men/1.jpg';
            $certs = json_decode($doc['certificates'], true) ?: [];
        ?>
            <div style="background:white; border-radius:32px; padding:28px; text-align:center; cursor:pointer; box-shadow:0 12px 30px rgba(0,0,0,0.05); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'" onclick='showDoctorModal(<?= json_encode($doc) ?>, <?= json_encode($certs) ?>)'>
                <img src="<?= $photo ?>" alt="<?= htmlspecialchars($doc['full_name']) ?>" style="width:100%; height:220px; object-fit:cover; border-radius:24px;">
                <h3 style="margin:16px 0 8px;"><?= htmlspecialchars($doc['full_name']) ?></h3>
                <p style="color:#0F766E; font-weight:600;"><?= htmlspecialchars($doc['specialty']) ?></p>
                <p><i class="fas fa-star" style="color:#f59e0b;"></i> Стаж <?= $doc['experience'] ?> лет</p>
                <button class="btn-primary" style="margin-top:16px;">Подробнее</button>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<div id="doctorModal" class="modal">
    <div class="modal-content" style="max-width:700px; border-radius:28px; padding:0; overflow:hidden;">
        <span class="close" onclick="closeModal()" style="position:absolute; right:20px; top:15px; font-size:28px; cursor:pointer; z-index:10;">&times;</span>
        
        <img id="modalPhoto" style="width:100%; max-height:350px; object-fit:cover; border-bottom:1px solid #eee;">
        
        <div style="padding:24px 30px 30px;">
            <h2 id="modalName" style="margin:0 0 8px;"></h2>
            <p id="modalSpec" style="color:#0F766E; font-weight:500; margin-bottom:16px;"></p>
            
            <div id="modalBio" style="background:#f9fafb; padding:20px; border-radius:20px; margin:20px 0; line-height:1.5; font-size:15px;"></div>
            
            <div id="modalCerts" style="margin:20px 0 10px; border-top:2px solid #e2e8f0; padding-top:20px;"></div>
            
            
        </div>
    </div>
</div>

<script>
function showDoctorModal(doctor, certs) {
    document.getElementById('modalName').innerText = doctor.full_name;
    document.getElementById('modalSpec').innerHTML = '<i class="fas fa-user-md"></i> ' + doctor.specialty;
    
    // Улучшенное форматирование биографии
    let bioText = doctor.bio || 'Опытный специалист, ждёт вас на приём.';
    // Если биография не содержит маркированных списков, преобразуем её в структурированный вид
    if (!bioText.includes('•') && !bioText.includes('<li>')) {
        bioText = bioText.replace(/\. /g, '.<br>');
        bioText = '<i class="fas fa-graduation-cap" style="color:#0F766E; width:24px;"></i> ' + bioText;
    }
    // Добавим иконки к основным разделам, если их нет
    if (bioText.indexOf('образование') > -1 || bioText.indexOf('Образование') > -1) {
        bioText = bioText.replace(/(Образование|образование)/g, '<i class="fas fa-university"></i> $1');
    }
    if (bioText.indexOf('курс') > -1 || bioText.indexOf('Курс') > -1) {
        bioText = bioText.replace(/(Курсы|курсы)/g, '<i class="fas fa-certificate"></i> $1');
    }
    if (bioText.indexOf('опыт') > -1 || bioText.indexOf('Опыт') > -1) {
        bioText = bioText.replace(/(Опыт|опыт)/g, '<i class="fas fa-chart-line"></i> $1');
    }
    
    document.getElementById('modalBio').innerHTML = bioText;
    
    let photoPath = doctor.photo ? 'uploads/'+doctor.photo : 'https://randomuser.me/api/portraits/men/1.jpg';
    document.getElementById('modalPhoto').src = photoPath;
    
    let certsHtml = '<div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;"><i class="fas fa-award" style="font-size:22px; color:#f59e0b;"></i><strong style="font-size:18px;">Сертификаты и дипломы</strong></div><div style="display:flex; flex-wrap:wrap; gap:15px;">';
    if(certs.length) {
        certs.forEach(cert => {
            let ext = cert.split('.').pop().toLowerCase();
            let filePath = 'uploads/certs/' + cert;
            if(ext === 'pdf') {
                certsHtml += `<div style="text-align:center; background:#f1f5f9; padding:10px; border-radius:16px; width:100px;"><a href="${filePath}" target="_blank"><i class="fas fa-file-pdf" style="font-size:40px; color:#e11d48;"></i><br><small>${cert.length>20 ? cert.substr(0,18)+'…' : cert}</small></a></div>`;
            } else {
                certsHtml += `<div style="text-align:center;"><a href="${filePath}" target="_blank"><img src="${filePath}" style="height:90px; width:auto; border-radius:12px; box-shadow:0 4px 8px rgba(0,0,0,0.1);"></a><br><small>${cert}</small></div>`;
            }
        });
    } else {
        certsHtml += '<p style="color:#64748b;">Нет загруженных сертификатов</p>';
    }
    certsHtml += '</div>';
    document.getElementById('modalCerts').innerHTML = certsHtml;
    
    document.getElementById('doctorModal').style.display = 'block';
}

function closeModal() { document.getElementById('doctorModal').style.display = 'none'; }
window.onclick = function(e) { if(e.target == document.getElementById('doctorModal')) closeModal(); }
</script>

<?php include 'footer.php'; ?>