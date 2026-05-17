<?php

declare(strict_types=1);

?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'QA CRUD') ?></title>
    <link rel="stylesheet" href="assets/app.css">
</head>
<body>
<header class="site-header">
    <div class="site-header__inner">
        <div class="site-logo" aria-hidden="true">◆</div>
        <div>
            <h1 class="site-header__title"><?= e($title ?? 'QA') ?></h1>
            <p class="site-header__subtitle">CRUD usuarios · Arquitectura hexagonal (demo)</p>
        </div>
    </div>
</header>
<main class="site-main">
<div class="surface">
