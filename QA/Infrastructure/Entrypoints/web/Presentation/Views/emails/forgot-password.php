<?php

declare(strict_types=1);

/** @var string $resetUrl */
/** @var string $userName */
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Restablecer contraseña</title></head>
<body class="email-body" style="font-family:system-ui,sans-serif;">
<p>Hola <?= e($userName) ?>,</p>
<p>Recibimos una solicitud para restablecer tu contraseña.</p>
<p><a href="<?= e($resetUrl) ?>">Haz clic aquí para cambiar tu contraseña</a></p>
<p>Si no hiciste esta solicitud, puedes ignorar este correo.</p>
</body>
</html>
