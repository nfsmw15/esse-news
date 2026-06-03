<?php

declare(strict_types=1);

use Esse\Auth;
use Esse\Hooks;
use EsseNews\NewsRepository;

$newsList = NewsRepository::listPublic(20, 0);

ob_start();
?>
<?php if (empty($newsList)): ?>
    <p class="text-secondary">Aktuell sind keine News vorhanden.</p>
<?php else: foreach ($newsList as $row):
    $datum = date('d.m.Y', strtotime($row['datum']));
    $zeit  = substr($row['zeit'], 0, 5);
?>
    <article class="mb-5">
        <?php if (Auth::check()): ?>
            <?php if ($row['visible']): ?>
                <span class="badge bg-primary mb-1"><i class="bi bi-globe2"></i> Extern</span>
            <?php else: ?>
                <span class="badge bg-warning text-dark mb-1"><i class="bi bi-lock"></i> Intern</span>
            <?php endif ?>
        <?php endif ?>

        <h2 class="h4 mt-1">
            <a href="/news/<?= (int) $row['id'] ?>" class="text-decoration-none">
                <?= htmlspecialchars($row['ueberschrift']) ?>
            </a>
        </h2>
        <p class="text-secondary small mb-2">
            <i class="bi bi-person"></i> <?= htmlspecialchars($row['autor']) ?>
            &nbsp;&middot;&nbsp;
            <i class="bi bi-clock"></i> <?= htmlspecialchars($datum) ?> um <?= htmlspecialchars($zeit) ?> Uhr
        </p>
        <p class="mb-3"><?= htmlspecialchars(strip_tags($row['kurznews'])) ?></p>
        <a href="/news/<?= (int) $row['id'] ?>" class="btn btn-sm btn-outline-primary">
            Mehr lesen <i class="bi bi-arrow-right"></i>
        </a>
        <hr class="border-secondary mt-4">
    </article>
<?php endforeach; endif; ?>
<?php
$content = ob_get_clean();
$page    = ['title' => 'News', 'slug' => 'news'];

if (Hooks::has('page.render')) {
    Hooks::fire('page.render', $page, $content);
} else {
    header('Content-Type: text/html; charset=utf-8');
    echo $content;
}
