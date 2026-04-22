<?php
error_reporting(0);
ini_set('display_errors', 0);

$host     = 'localhost';
$dbname   = 'batiments_ruine';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $hasMembres = $pdo->query("SHOW TABLES LIKE 'membres'")->fetchColumn();
    if ($hasMembres) {
        $hasStepPermissions = $pdo->query("SHOW COLUMNS FROM membres LIKE 'step_permissions'")->fetchColumn();
        if (!$hasStepPermissions) {
            $pdo->exec("ALTER TABLE membres ADD COLUMN step_permissions TEXT NULL AFTER role");
        }

        $adminPwdStmt = $pdo->prepare("SELECT id, password FROM membres WHERE username='admin' LIMIT 1");
        $adminPwdStmt->execute();
        $adminRow = $adminPwdStmt->fetch(PDO::FETCH_ASSOC);
        if ($adminRow) {
            $stored = (string)($adminRow['password'] ?? '');
            $needsUpdate = ($stored === 'admin123' || password_verify('admin123', $stored));
            if ($needsUpdate) {
                $pdo->prepare("UPDATE membres SET password=? WHERE id=?")
                    ->execute([password_hash('admin1912', PASSWORD_DEFAULT), (int)$adminRow['id']]);
            }
        }
    }

    $hasCommissionMembers = $pdo->query("SHOW TABLES LIKE 'commission_members'")->fetchColumn();
    if (!$hasCommissionMembers) {
        $pdo->exec("
            CREATE TABLE commission_members (
                id INT AUTO_INCREMENT PRIMARY KEY,
                titre VARCHAR(120) NOT NULL,
                nom VARCHAR(120) NOT NULL,
                ordre INT NOT NULL DEFAULT 0,
                actif TINYINT(1) DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");
        $pdo->exec("
            INSERT INTO commission_members (titre, nom, ordre, actif) VALUES
            ('رئيس اللجنة', 'ممثل البلدية', 1, 1),
            ('عضو', 'ممثل الحماية المدنية', 2, 1),
            ('عضو', 'ممثل الشرطة البلدية', 3, 1)
        ");
    }

    $hasCommissionMembersColumn = $pdo->query("SHOW COLUMNS FROM documents_officiels LIKE 'commission_members'")->fetchColumn();
    if (!$hasCommissionMembersColumn) {
        $pdo->exec("ALTER TABLE documents_officiels ADD COLUMN commission_members TEXT NULL AFTER confirmation_degree");
    }
} catch (PDOException $e) {
    die("
    <div style='font-family:Arial;padding:30px;text-align:center'>
        <h2 style='color:red'>❌ خطأ في الاتصال بقاعدة البيانات</h2>
        <p style='color:#666;margin:10px 0'>" . $e->getMessage() . "</p>
        <a href='init_db.php'
           style='background:#1a3c5e;color:white;padding:10px 20px;
                  border-radius:6px;text-decoration:none'>
            🔧 إنشاء قاعدة البيانات
        </a>
    </div>");
}
?>
