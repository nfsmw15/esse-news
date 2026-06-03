<?php

declare(strict_types=1);

use Esse\Auth;
use Esse\Hooks;
use Esse\Router;
use EsseNews\NewsRepository;

$row = NewsRepository::findPublic($newsId);

if (!$row) {
    Router::abort(404);
    return;
}

$datum = date('d.m.Y', strtotime($row['datum']));
$zeit  = substr($row['zeit'], 0, 5);

ob_start();
?>
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><a href="/news">News</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($row['ueberschrift']) ?></li>
    </ol>
</nav>

<p class="text-secondary mb-3">
    <i class="bi bi-person"></i> <?= htmlspecialchars($row['autor']) ?>
    &nbsp;&middot;&nbsp;
    <i class="bi bi-clock"></i> <?= htmlspecialchars($datum) ?> um <?= htmlspecialchars($zeit) ?> Uhr
    <?php if (Auth::check()): ?>
    &nbsp;&middot;&nbsp;
    <?php if ($row['visible']): ?>
        <span class="badge bg-primary"><i class="bi bi-globe2"></i> Extern</span>
    <?php else: ?>
        <span class="badge bg-warning text-dark"><i class="bi bi-lock"></i> Intern</span>
    <?php endif ?>
    <?php endif ?>
</p>

<hr class="border-secondary mb-4">

<p class="lead mb-4"><?= htmlspecialchars(strip_tags($row['kurznews'])) ?></p>

<div class="news-content">
    <?= $row['news'] ?>
</div>

<hr class="border-secondary mt-4">

<div class="d-flex gap-2">
    <a href="/news" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Zurück zur Übersicht
    </a>
    <?php if (Auth::meetsRole('admin')): ?>
    <a href="/admin/news/edit/<?= (int) $row['id'] ?>" class="btn btn-sm btn-outline-info">
        <i class="bi bi-pencil"></i> Bearbeiten
    </a>
    <?php endif ?>
</div>
<?php
$content = ob_get_clean();
$page    = ['title' => $row['ueberschrift'], 'slug' => 'news'];

if (Hooks::has('page.render')) {
    Hooks::fire('page.render', $page, $content);
} else {
    header('Content-Type: text/html; charset=utf-8');
    echo $content;
}
