<?php

declare(strict_types=1);

?>
<?php
/** @var array<string, string> $errors */
/** @var array<string, string> $old */
$errors = $errors ?? [];
$old = $old ?? [];
?>
<form class="form-card" action="index.php?route=auth.authenticate" method="post">
    <label>Email
        <input type="email" name="email" required autocomplete="username" value="<?= e((string) ($old['email'] ?? '')) ?>">
    </label>
    <?php if (!empty($errors['email'])): ?>
        <p class="field-error"><?= e($errors['email']) ?></p>
    <?php endif; ?>
    <label>Contraseña
        <input type="password" name="password" required autocomplete="current-password">
    </label>
    <?php if (!empty($errors['password'])): ?>
        <p class="field-error"><?= e($errors['password']) ?></p>
    <?php endif; ?>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Entrar</button>
        <a class="btn btn-ghost" href="index.php?route=auth.forgot">Olvidé contraseña</a>
    </div>
</form>
