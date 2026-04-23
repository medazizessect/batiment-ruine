<?php
require 'config.php';
requireLogin();
require 'db.php';

$total = (int)$pdo->query("SELECT COUNT(*) FROM batiments")->fetchColumn();
$treated = (int)$pdo->query("SELECT COUNT(DISTINCT batiment_id) FROM documents_officiels WHERE type='step5_decision'")->fetchColumn();
$inProgress = (int)$pdo->query("
    SELECT COUNT(DISTINCT batiment_id) FROM documents_officiels
    WHERE type IN ('step2_pv','step3_expert_request','step4_expert_report')
      AND batiment_id NOT IN (SELECT batiment_id FROM documents_officiels WHERE type='step5_decision')
")->fetchColumn();
$newCases = max(0, $total - $treated - $inProgress);
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>لوحة الإحصائيات</title>
<style>
*{box-sizing:border-box}body{margin:0;font-family:Segoe UI,Arial;background:#f0f2f5;direction:rtl}
.wrap{padding:18px}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px}
.card{background:#fff;border-radius:12px;padding:16px;box-shadow:0 3px 12px rgba(0,0,0,.08)}
.n{font-size:34px;font-weight:800;color:#1a3c5e}.t{color:#666;font-size:14px}
</style>
</head>
<body>
<?php include '_menu.php'; ?>
<div class="wrap">
    <div class="grid">
        <div class="card"><div class="n"><?= $total ?></div><div class="t">إجمالي الملفات</div></div>
        <div class="card"><div class="n"><?= $treated ?></div><div class="t">ملفات معالجة (قرار نهائي)</div></div>
        <div class="card"><div class="n"><?= $inProgress ?></div><div class="t">ملفات قيد المعالجة</div></div>
        <div class="card"><div class="n"><?= $newCases ?></div><div class="t">ملفات جديدة</div></div>
    </div>
</div>
</body>
</html>
