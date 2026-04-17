<?php

declare(strict_types=1);

/** @var \QA\Infrastructure\Entrypoints\Web\Controllers\Dto\UserResponse $user */
?>
<dl class="detail-list">
    <dt>ID</dt>
    <dd><?= e((string) $user->id) ?></dd>
    <dt>Nombre</dt>
    <dd><?= e($user->name) ?></dd>
    <dt>Email</dt>
    <dd><?= e($user->email) ?></dd>
    <dt>Rol</dt>
    <dd><?= e($user->roleLabel) ?></dd>
    <dt>Estado</dt>
    <dd>
        <?php if (($user->status ?? '') === 'ACTIVE'): ?>
            <span class="badge badge--ok">Activo</span>
        <?php else: ?>
            <span class="badge badge--off"><?= e($user->status) ?></span>
        <?php endif; ?>
    </dd>
</dl>
<p class="row-actions">
    <a class="btn btn-primary" href="index.php?route=users.edit&amp;id=<?= e((string) $user->id) ?>">Editar</a>
    <a class="btn btn-ghost" href="index.php?route=users.index">Volver al listado</a>
</p>
