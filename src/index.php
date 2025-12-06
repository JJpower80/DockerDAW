<?php
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_port = getenv('DB_PORT') ?: 3306;
$db_name = getenv('DB_NAME') ?: 'app_db';
$db_user = getenv('DB_USER') ?: 'root';
$db_password = getenv('DB_PASSWORD') ?: '';

$php_version = phpversion();
$server_software = $_SERVER['SERVER_SOFTWARE'] ?? 'N/A';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

$db_status = 'Desconectado';
$db_error = '';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;port=$db_port;dbname=$db_name",
        $db_user,
        $db_password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $db_status = 'Conectado';
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

$extensions = [
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'mysqli' => extension_loaded('mysqli'),
    'curl' => extension_loaded('curl'),
    'json' => extension_loaded('json'),
    'opcache' => extension_loaded('opcache'),
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard PHP - Docker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            background: #667eea;
            color: white;
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: bold;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #555;
            font-weight: 600;
        }
        .info-value {
            color: #333;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status.connected {
            background: #d4edda;
            color: #155724;
        }
        .status.disconnected {
            background: #f8d7da;
            color: #721c24;
        }
        .extensions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }
        .extension {
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }
        .extension.loaded {
            background: #d4edda;
            color: #155724;
        }
        .extension.not-loaded {
            background: #f8d7da;
            color: #721c24;
        }
        .error {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 12px;
            border-radius: 5px;
            margin-top: 10px;
            color: #856404;
            font-size: 12px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐳 PHP en Docker</h1>
        <p class="subtitle">Dashboard de estado de la aplicación</p>

        <!-- Información del Servidor -->
        <div class="section">
            <div class="section-title">📊 Información del Servidor</div>
            <div class="info-item">
                <span class="info-label">Host:</span>
                <span class="info-value"><?php echo htmlspecialchars($host); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Versión PHP:</span>
                <span class="info-value"><?php echo $php_version; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Servidor Web:</span>
                <span class="info-value"><?php echo htmlspecialchars($server_software); ?></span>
            </div>
        </div>

        <!-- Estado de la Base de Datos -->
        <div class="section">
            <div class="section-title">🗄️ Base de Datos</div>
            <div class="info-item">
                <span class="info-label">Host:</span>
                <span class="info-value"><?php echo htmlspecialchars($db_host); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Puerto:</span>
                <span class="info-value"><?php echo $db_port; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Base de Datos:</span>
                <span class="info-value"><?php echo htmlspecialchars($db_name); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Estado:</span>
                <span class="info-value">
                    <span class="status <?php echo $db_status === 'Conectado' ? 'connected' : 'disconnected'; ?>">
                        <?php echo $db_status; ?>
                    </span>
                </span>
            </div>
            <?php if ($db_error): ?>
                <div class="error">
                    <strong>Error:</strong> <?php echo htmlspecialchars($db_error); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Extensiones PHP -->
        <div class="section">
            <div class="section-title">🔧 Extensiones PHP</div>
            <div class="extensions">
                <?php foreach ($extensions as $ext => $loaded): ?>
                    <div class="extension <?php echo $loaded ? 'loaded' : 'not-loaded'; ?>">
                        <?php echo $ext; ?>
                        <br>
                        <?php echo $loaded ? '✓ Cargada' : '✗ No cargada'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="footer">
            <p>Aplicación ejecutándose en contenedor Docker</p>
            <p><?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
    </div>
</body>
</html>
