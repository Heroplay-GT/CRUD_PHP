<?php

declare(strict_types=1);

?>
<?php
/** @var array<string, string> $errors */
/** @var array<string, string> $old */
$errors = $errors ?? [];
$old = $old ?? [];
?>
<form class="form-card" action="index.php?route=users.store" method="post">
    <label>Nombre
        <input type="text" name="name" required maxlength="120" autocomplete="name" minlength="3" value="<?= e((string) ($old['name'] ?? '')) ?>">
    </label>
    <?php if (!empty($errors['name'])): ?>
        <p class="field-error"><?= e($errors['name']) ?></p>
    <?php endif; ?>
    <label>Email
        <input type="email" name="email" required maxlength="190" autocomplete="email" value="<?= e((string) ($old['email'] ?? '')) ?>">
    </label>
    <?php if (!empty($errors['email'])): ?>
        <p class="field-error"><?= e($errors['email']) ?></p>
    <?php endif; ?>
    <label>Contraseña
        <input type="password" name="password" required minlength="8" autocomplete="new-password">
    </label>
    <?php if (!empty($errors['password'])): ?>
        <p class="field-error"><?= e($errors['password']) ?></p>
    <?php endif; ?>
    <label>Rol
        <select name="role_id">
            <?php $role = (string) ($old['role_id'] ?? 'ADMIN'); ?>
            <option value="ADMIN" <?= $role === 'ADMIN' ? 'selected' : '' ?>>Administrador</option>
            <option value="MEMBER" <?= $role === 'MEMBER' ? 'selected' : '' ?>>Miembro</option>
            <option value="REVIEWER" <?= $role === 'REVIEWER' ? 'selected' : '' ?>>Revisor</option>
        </select>
    </label>
    <?php if (!empty($errors['role_id'])): ?>
        <p class="field-error"><?= e($errors['role_id']) ?></p>
    <?php endif; ?>
    <label>Estado
        <select name="status">
            <?php $status = (string) ($old['status'] ?? 'ACTIVE'); ?>
            <option value="ACTIVE" <?= $status === 'ACTIVE' ? 'selected' : '' ?>>ACTIVE</option>
            <option value="INACTIVE" <?= $status === 'INACTIVE' ? 'selected' : '' ?>>INACTIVE</option>
            <option value="PENDING" <?= $status === 'PENDING' ? 'selected' : '' ?>>PENDING</option>
            <option value="BLOCKED" <?= $status === 'BLOCKED' ? 'selected' : '' ?>>BLOCKED</option>
        </select>
    </label>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a class="btn btn-ghost" href="index.php?route=users.index">Cancelar</a>
    </div>
</form>
