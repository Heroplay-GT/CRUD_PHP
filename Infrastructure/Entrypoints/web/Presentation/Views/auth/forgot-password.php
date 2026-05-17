<?php

declare(strict_types=1);

?>
<?php
/** @var array<string, string> $errors */
/** @var array<string, string> $old */
$errors = $errors ?? [];
$old = $old ?? [];
?>
<div class="prose forgot-help">
    <p><strong>Recuperación de contraseña</strong></p>
    <p>Si el correo existe, se genera una clave temporal y verás la confirmación. Por seguridad, el mensaje es el mismo aunque el correo no esté registrado.</p>
</div>
<form class="form-card" action="index.php?route=auth.forgot.send" method="post">
    <label>Email
        <input type="email" name="email" required autocomplete="email" value="<?= e((string) ($old['email'] ?? '')) ?>">
    </label>
    <?php if (!empty($errors['email'])): ?>
        <p class="field-error"><?= e($errors['email']) ?></p>
    <?php endif; ?>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enviar</button>
        <a class="btn btn-ghost" href="index.php?route=auth.login">Volver al login</a>
    </div>
</form>
