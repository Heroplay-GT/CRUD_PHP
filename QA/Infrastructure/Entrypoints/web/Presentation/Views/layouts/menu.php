<?php

declare(strict_types=1);

$authed = isset($_SESSION['auth']['id']);
?>
<nav class="site-nav" aria-label="Principal">
    <a href="index.php?route=home">Inicio</a>
    <?php if ($authed): ?>
        <a href="index.php?route=users.index">Usuarios</a>
        <a href="index.php?route=users.create">Nuevo usuario</a>
        <span class="nav-logout">
            <a class="btn btn-ghost" href="index.php?route=auth.logout">Salir (<?= e((string) ($_SESSION['auth']['name'] ?? '')) ?>)</a>
        </span>
    <?php else: ?>
        <a href="index.php?route=auth.login">Entrar</a>
        <a href="index.php?route=auth.forgot">Olvidé contraseña</a>
    <?php endif; ?>
</nav>
