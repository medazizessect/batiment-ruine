<?php
require 'config.php';
requireLogin();
require 'db.php';

$totalCases = (int)$pdo->query("SELECT COUNT(*) FROM batiments")->fetchColumn();
$treatedCases = (int)$pdo->query("SELECT COUNT(DISTINCT batiment_id) FROM documents_officiels WHERE type='step5_decision'")->fetchColumn();
$inProgressCases = (int)$pdo->query("
    SELECT COUNT(DISTINCT d.batiment_id)
    FROM documents_officiels d
    WHERE d.type IN ('step2_pv','step3_expert_request','step4_expert_report')
      AND d.batiment_id NOT IN (SELECT batiment_id FROM documents_officiels WHERE type='step5_decision')
")->fetchColumn();
$newCases = max(0, $totalCases - $treatedCases - $inProgressCases);
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>لوحة الإحصائيات</title>
<style>
*{box-sizing:border-box}body{margin:0;font-family:Segoe UI,Arial;background:#f0f2f5}
header{background:linear-gradient(135deg,#1a3c5e,#2e6da4);color:#fff;padding:16px 22px}
.wrap{padding:18px}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px}
.card{background:#fff;border-radius:12px;padding:18px;box-shadow:0 3px 12px rgba(0,0,0,.08)}
.label{color:#666;font-size:13px}.num{font-size:32px;font-weight:700;margin-top:6px}
</style>
</head>
<body>
<?php include '_menu.php'; ?>
<header><h2 style="margin:0">📊 لوحة الإحصائيات</h2></header>
<div class="wrap">
    <div class="grid">
        <div class="card"><div class="label">إجمالي الملفات</div><div class="num"><?= $totalCases ?></div></div>
        <div class="card"><div class="label">ملفات مكتملة</div><div class="num"><?= $treatedCases ?></div></div>
        <div class="card"><div class="label">ملفات قيد المعالجة</div><div class="num"><?= $inProgressCases ?></div></div>
        <div class="card"><div class="label">ملفات جديدة</div><div class="num"><?= $newCases ?></div></div>
    </div>
</div>
</body>
</html>
