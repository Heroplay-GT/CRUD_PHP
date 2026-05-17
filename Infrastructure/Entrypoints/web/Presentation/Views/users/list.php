<?php

declare(strict_types=1);

/** @var list<\QA\Infrastructure\Entrypoints\Web\Controllers\Dto\UserResponse> $users */
?>
<div class="toolbar">
    <a class="btn btn-primary" href="index.php?route=users.create">Crear usuario</a>
</div>
<div class="table-wrap">
<table class="data-table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= e((string) $u->id) ?></td>
            <td><strong><?= e($u->name) ?></strong></td>
            <td><?= e($u->email) ?></td>
            <td><?= e($u->roleLabel) ?></td>
            <td>
                <?php if (($u->status ?? '') === 'ACTIVE'): ?>
                    <span class="badge badge--ok">Activo</span>
                <?php else: ?>
                    <span class="badge badge--off"><?= e($u->status) ?></span>
                <?php endif; ?>
            </td>
            <td class="row-actions">
                <a class="btn btn-ghost" href="index.php?route=users.show&amp;id=<?= e((string) $u->id) ?>">Ver</a>
                <a class="btn btn-ghost" href="index.php?route=users.edit&amp;id=<?= e((string) $u->id) ?>">Editar</a>
                <form action="index.php?route=users.delete" method="post" onsubmit="return confirm('¿Eliminar este usuario?');">
                    <input type="hidden" name="id" value="<?= e((string) $u->id) ?>">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
