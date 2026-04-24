<?php

declare(strict_types=1);

?>
<?php
/** @var array<string, string> $errors */
/** @var array<string, string> $old */
/** @var string $token */
/** @var string $email */
$errors = $errors ?? [];
$old = $old ?? [];
?>
<div class="prose forgot-help">
    <p><strong>Restablecer contraseña</strong></p>
    <p>Ingresa una contraseña nueva para tu cuenta.</p>
</div>
<form class="form-card" action="index.php?route=auth.reset.update" method="post">
    <input type="hidden" name="token" value="<?= e($token) ?>">
    <label>Email
        <input type="email" name="email" value="<?= e((string) ($old['email'] ?? $email ?? '')) ?>" readonly>
    </label>
    <label>Nueva contraseña
        <input type="password" name="password" required minlength="8" autocomplete="new-password">
    </label>
    <?php if (!empty($errors['password'])): ?>
        <p class="field-error"><?= e($errors['password']) ?></p>
    <?php endif; ?>
    <label>Confirmar contraseña
        <input type="password" name="password_confirm" required minlength="8" autocomplete="new-password">
    </label>
    <?php if (!empty($errors['password_confirm'])): ?>
        <p class="field-error"><?= e($errors['password_confirm']) ?></p>
    <?php endif; ?>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
        <a class="btn btn-ghost" href="index.php?route=auth.login">Volver al login</a>
    </div>
</form>
