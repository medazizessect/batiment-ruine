<?php
$numDoc = $v['numero_doc'] ?? '';
$dateDoc = !empty($v['date_doc']) ? date('d/m/Y', strtotime($v['date_doc'])) : date('d/m/Y');
$meetingDate = !empty($case['date_reclamation']) ? date('d/m/Y', strtotime($case['date_reclamation'])) : $dateDoc;
$commissionItems = [];
if (!empty($case['commission'])) {
    $commissionItems = array_values(array_filter(array_map('trim', preg_split('/\s*\/\s*/u', (string)$case['commission']))));
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>PDF - <?= htmlspecialchars($label) ?></title>
<style>
*{box-sizing:border-box} body{font-family:'Times New Roman',serif;direction:rtl;padding:15mm;line-height:1.8}
.top{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid #000;padding-bottom:4mm}
.logo img{width:62px;height:62px;object-fit:contain}
.blk{font-size:13px}
.title{text-align:center;margin:6mm 0}
.title h1{font-size:22px;text-decoration:underline;margin:0 0 3mm}
.meta{display:flex;justify-content:space-between;margin-bottom:5mm}
.meta div{font-size:15px}
.p{margin:2mm 0;font-size:15px}
.list{margin:3mm 0 4mm;padding:0 18px 0 0}
.list li{margin:1.5mm 0}
.box{border:1px solid #000;padding:4mm}
.noprint{position:fixed;top:10px;left:10px}
@media print{.noprint{display:none}}
</style>
</head>
<body>
<div class="noprint"><button onclick="window.print()">📄 طباعة / حفظ PDF</button></div>
<div class="top">
  <div class="blk">الجمهورية التونسية<br>وزارة الداخلية<br>بلدية سوسة</div>
  <div class="logo"><img src="Logo_commune_Sousse.svg" alt="logo"></div>
</div>
<div class="title">
    <h1>محضر معاينة بناء متداعي للسقوط</h1>
</div>
<div class="meta">
    <div><b>عدد:</b> <?= htmlspecialchars($numDoc ?: '...') ?></div>
    <div><b>بتاريخ:</b> <?= $dateDoc ?></div>
</div>

<p class="p">عملا بالقانون المتعلق بالبنايات المتداعية للسقوط، وعلى إشعار السيد(ة) المعني، وتحت عدد الضبط:
    <b><?= htmlspecialchars($case['bureau_ordre_id'] ?? '') ?></b> بتاريخ <b><?= $meetingDate ?></b>.</p>

<p class="p">توجهنا نحن الممضون أسفله:</p>
<ul class="list">
    <?php if ($commissionItems): foreach ($commissionItems as $item): ?>
        <li><?= htmlspecialchars($item) ?></li>
    <?php endforeach; else: ?>
        <li>—</li>
    <?php endif; ?>
</ul>

<div class="box">
    <p class="p"><b>العقار الكائن بـ:</b> <?= htmlspecialchars($addressLabel ?? '') ?></p>
    <p class="p"><b>المالك:</b> <?= htmlspecialchars($v['owner_name'] ?? ($case['proprietaire'] ?? '')) ?></p>
    <?php if (!empty($v['occupied_by'])): ?><p class="p"><b>المشغول من طرف:</b> <?= htmlspecialchars($v['occupied_by']) ?></p><?php endif; ?>
    <?php if (!empty($v['confirmation_degree'])): ?><p class="p"><b>درجة التأكيد:</b> <?= htmlspecialchars($v['confirmation_degree']) ?></p><?php endif; ?>
    <?php if (!empty($v['observations'])): ?><p class="p"><b>نتيجة المعاينة والتشخيص الأولي:</b><br><?= nl2br(htmlspecialchars($v['observations'])) ?></p><?php endif; ?>
</div>
</body>
</html>
