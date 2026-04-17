<?php

declare(strict_types=1);

/** @var string $temporaryPassword */
/** @var string $userName */
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Contraseña temporal</title></head>
<body class="email-body" style="font-family:system-ui,sans-serif;">
<p>Hola <?= e($userName) ?>,</p>
<p>Tu contraseña temporal es: <strong><?= e($temporaryPassword) ?></strong></p>
<p>Cámbiala al iniciar sesión. (En producción esto iría vía <code>SendEmailPort</code> / SMTP o MailHog.)</p>
</body>
</html>
