<?php
error_reporting(0);
ini_set('display_errors', 0);
require 'db.php';

$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT b.* FROM batiments b
        WHERE b.numero_rapport LIKE :s
           OR b.lieu LIKE :s
           OR b.proprietaire LIKE :s
           OR b.observations LIKE :s
           OR b.bureau_ordre_id LIKE :s
           OR EXISTS (
               SELECT 1
               FROM documents_officiels d
               LEFT JOIN adresses a ON a.id = d.address_id
               WHERE d.batiment_id = b.id AND a.libelle LIKE :s
           )
        ORDER BY b.id ASC
    ");
    $stmt->execute([':s' => "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM batiments ORDER BY id ASC");
}
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>تقرير البنايات</title>
<style>
body{font-family:Arial,sans-serif;direction:rtl;margin:20px}
h2{margin:0 0 8px}
table{width:100%;border-collapse:collapse;font-size:12px}
th,td{border:1px solid #bbb;padding:6px;vertical-align:top}
th{background:#f0f0f0}
.noprint{margin-bottom:10px}
@media print {.noprint{display:none}}
</style>
</head>
<body>
<div class="noprint"><button onclick="window.print()">🖨️ طباعة / حفظ PDF</button></div>
<h2>تقرير البنايات المتاحة</h2>
<div style="margin:0 0 10px;font-size:12px">تاريخ: <?= date('d/m/Y H:i') ?><?= $search !== '' ? ' | بحث: ' . htmlspecialchars($search) : '' ?></div>
<table>
    <tr><th>#</th><th>ID bureau d'ordre</th><th>المكان</th><th>المالك</th><th>تاريخ</th><th>ملاحظات</th></tr>
    <?php if (!$rows): ?>
        <tr><td colspan="6" style="text-align:center">لا توجد بيانات</td></tr>
    <?php endif; ?>
    <?php foreach ($rows as $i => $r): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($r['bureau_ordre_id'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['lieu'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['proprietaire'] ?? '') ?></td>
            <td><?= !empty($r['date_rapport']) ? date('d/m/Y', strtotime($r['date_rapport'])) : '' ?></td>
            <td><?= htmlspecialchars($r['observations'] ?? '') ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
