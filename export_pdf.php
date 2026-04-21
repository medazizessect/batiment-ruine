<?php
require 'config.php';
requireLogin();
require 'db.php';

$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT b.* FROM batiments b
        WHERE b.bureau_ordre_id LIKE :s
           OR b.proprietaire LIKE :s
           OR b.lieu LIKE :s
           OR EXISTS (
               SELECT 1
               FROM documents_officiels d
               LEFT JOIN adresses a ON a.id = d.address_id
               WHERE d.batiment_id = b.id AND a.libelle LIKE :s
           )
        ORDER BY b.id DESC
    ");
    $stmt->execute([':s' => "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM batiments ORDER BY id DESC");
}
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>تقرير البنايات المتداعية</title>
<style>
body{font-family:'Times New Roman',serif;direction:rtl;margin:12mm}
h2{margin:0 0 10px;text-align:center}
table{width:100%;border-collapse:collapse;font-size:12px}
th,td{border:1px solid #ddd;padding:6px;vertical-align:top}
th{background:#f2f2f2}
@media print{.noprint{display:none}}
</style>
</head>
<body>
<div class="noprint" style="margin-bottom:10px"><button onclick="window.print()">🖨️ طباعة / حفظ PDF</button></div>
<h2>تقرير البنايات المتداعية</h2>
<table>
    <tr><th>#</th><th>ID bureau d'ordre</th><th>التاريخ</th><th>المالك</th><th>ملاحظات</th></tr>
    <?php foreach($rows as $i => $r): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($r['bureau_ordre_id'] ?? '') ?></td>
            <td><?= !empty($r['date_reclamation']) ? date('d/m/Y', strtotime($r['date_reclamation'])) : '' ?></td>
            <td><?= htmlspecialchars($r['proprietaire'] ?? '') ?></td>
            <td><?= nl2br(htmlspecialchars($r['observations'] ?? '')) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
