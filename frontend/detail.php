<?php

declare(strict_types=1);

use Esse\Auth;
use Esse\Hooks;
use Esse\Router;
use Esse\Ui;
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
<?= Ui::breadcrumb([
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'News', 'url' => '/news'],
    ['label' => $row['ueberschrift']],
]) ?>

<p class="news-meta">
    <?= Ui::e($row['autor']) ?>
    &nbsp;&middot;&nbsp;
    <?= Ui::e($datum) ?> um <?= Ui::e($zeit) ?> Uhr
    <?php if (Auth::check()): ?>
    &nbsp;&middot;&nbsp;
    <?= $row['visible'] ? Ui::badge('Extern', 'info') : Ui::badge('Intern', 'warning') ?>
    <?php endif ?>
</p>

<?= Ui::divider() ?>

<p class="news-lead"><?= Ui::e(strip_tags($row['kurznews'])) ?></p>

<div class="news-content">
    <?= $row['news'] ?>
</div>

<?= Ui::divider() ?>

<div class="news-actions">
    <?= Ui::button('Zurück zur Übersicht', '/news', ['variant' => 'ghost', 'size' => 'sm', 'icon' => 'arrow-left']) ?>
    <?php if (Auth::meetsRole('admin')): ?>
    <?= Ui::button('Bearbeiten', '/admin/news/edit/' . (int) $row['id'], ['variant' => 'ghost', 'size' => 'sm', 'icon' => 'pencil']) ?>
    <?php endif ?>
</div>
<?php
$content = ob_get_clean();
$page    = ['title' => $row['ueberschrift'], 'slug' => 'news', 'icon' => 'newspaper'];

if (Hooks::has('page.render')) {
    Hooks::fire('page.render', $page, $content);
} else {
    header('Content-Type: text/html; charset=utf-8');
    echo $content;
}
