<?php

declare(strict_types=1);

/** @var array{id:string,name:string,email:string,role:string,status:string} $user */
/** @var string $roleId */
/** @var array<string, string> $errors */
/** @var array<string, string> $old */
$errors = $errors ?? [];
$old = $old ?? [];
$name = (string) ($old['name'] ?? $user['name']);
$email = (string) ($old['email'] ?? $user['email']);
$status = (string) ($old['status'] ?? $user['status']);
$roleId = (string) ($old['role_id'] ?? $roleId);
?>
<form class="form-card" action="index.php?route=users.update" method="post">
    <input type="hidden" name="id" value="<?= e($user['id']) ?>">
    <label>Nombre
        <input type="text" name="name" required maxlength="120" value="<?= e($name) ?>" autocomplete="name" minlength="3">
    </label>
    <?php if (!empty($errors['name'])): ?>
        <p class="field-error"><?= e($errors['name']) ?></p>
    <?php endif; ?>
    <label>Email
        <input type="email" name="email" required maxlength="190" value="<?= e($email) ?>" autocomplete="email">
    </label>
    <?php if (!empty($errors['email'])): ?>
        <p class="field-error"><?= e($errors['email']) ?></p>
    <?php endif; ?>
    <label>Nueva contraseña <small>Dejar vacío para no cambiar (mín. 8 si la cambias)</small>
        <input type="password" name="password" minlength="8" placeholder="Opcional" autocomplete="new-password">
    </label>
    <?php if (!empty($errors['password'])): ?>
        <p class="field-error"><?= e($errors['password']) ?></p>
    <?php endif; ?>
    <label>Rol
        <select name="role_id">
            <option value="ADMIN" <?= $roleId === 'ADMIN' ? 'selected' : '' ?>>Administrador</option>
            <option value="MEMBER" <?= $roleId === 'MEMBER' ? 'selected' : '' ?>>Miembro</option>
            <option value="REVIEWER" <?= $roleId === 'REVIEWER' ? 'selected' : '' ?>>Revisor</option>
        </select>
    </label>
    <?php if (!empty($errors['role_id'])): ?>
        <p class="field-error"><?= e($errors['role_id']) ?></p>
    <?php endif; ?>
    <label>Estado
        <select name="status">
            <option value="ACTIVE" <?= $status === 'ACTIVE' ? 'selected' : '' ?>>ACTIVE</option>
            <option value="INACTIVE" <?= $status === 'INACTIVE' ? 'selected' : '' ?>>INACTIVE</option>
            <option value="PENDING" <?= $status === 'PENDING' ? 'selected' : '' ?>>PENDING</option>
            <option value="BLOCKED" <?= $status === 'BLOCKED' ? 'selected' : '' ?>>BLOCKED</option>
        </select>
    </label>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a class="btn btn-ghost" href="index.php?route=users.show&amp;id=<?= e($user['id']) ?>">Cancelar</a>
    </div>
</form>
