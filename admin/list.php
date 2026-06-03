<?php

declare(strict_types=1);

use Esse\Auth;
use EsseNews\NewsRepository;

$flash = null;
if (!empty($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCsrf()) { http_response_code(403); exit; }

    $action = $_POST['_action'] ?? '';
    $id     = (int) ($_POST['news_id'] ?? 0);

    if ($action === 'delete' && $id > 0) {
        NewsRepository::delete($id);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'News-Eintrag gelöscht.'];
        header('Location: /admin/news');
        exit;
    }

    if ($action === 'toggle' && $id > 0) {
        NewsRepository::toggleActive($id);
        header('Location: /admin/news');
        exit;
    }
}

$allNews      = NewsRepository::all();
$pageTitle    = 'News verwalten';
$activeNav    = 'admin.news';
$topbarRight  = '<a href="/admin/news/create" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-lg"></i> Neue News
</a>';

ob_start();
?>

<div class="card">
    <div class="card-header py-2">
        <small class="text-secondary">Alle News</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Datum</th>
                    <th>Überschrift</th>
                    <th>Autor</th>
                    <th>Sichtbarkeit</th>
                    <th>Status</th>
                    <th class="text-end">Aktionen</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($allNews)): ?>
                <tr>
                    <td colspan="7" class="text-center text-secondary py-4">
                        Noch keine News vorhanden.
                    </td>
                </tr>
            <?php else: foreach ($allNews as $row): ?>
                <tr>
                    <td class="text-secondary"><?= (int) $row['id'] ?></td>
                    <td><?= htmlspecialchars(date('d.m.Y', strtotime($row['datum']))) ?></td>
                    <td>
                        <strong><?= htmlspecialchars($row['ueberschrift']) ?></strong><br>
                        <small class="text-secondary">
                            <?= htmlspecialchars(mb_strimwidth(strip_tags($row['kurznews']), 0, 80, '…')) ?>
                        </small>
                    </td>
                    <td><?= htmlspecialchars($row['autor']) ?></td>
                    <td>
                        <?php if ($row['visible']): ?>
                            <span class="badge bg-primary">
                                <i class="bi bi-globe2"></i> Extern
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-lock"></i> Intern
                            </span>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if ($row['active']): ?>
                            <span class="badge bg-success">
                                <i class="bi bi-check-lg"></i> Aktiv
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary">
                                <i class="bi bi-dash"></i> Inaktiv
                            </span>
                        <?php endif ?>
                    </td>
                    <td class="text-end" style="white-space:nowrap">
                        <a href="/admin/news/edit/<?= (int) $row['id'] ?>"
                           class="btn btn-sm btn-outline-secondary" title="Bearbeiten">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="_csrf"   value="<?= Auth::csrfToken() ?>">
                            <input type="hidden" name="_action" value="toggle">
                            <input type="hidden" name="news_id" value="<?= (int) $row['id'] ?>">
                            <button class="btn btn-sm btn-outline-<?= $row['active'] ? 'warning' : 'success' ?>"
                                    title="<?= $row['active'] ? 'Deaktivieren' : 'Aktivieren' ?>">
                                <i class="bi bi-<?= $row['active'] ? 'pause' : 'play' ?>"></i>
                            </button>
                        </form>
                        <form method="post" class="d-inline"
                              onsubmit="return confirm('News wirklich löschen?')">
                            <input type="hidden" name="_csrf"   value="<?= Auth::csrfToken() ?>">
                            <input type="hidden" name="_action" value="delete">
                            <input type="hidden" name="news_id" value="<?= (int) $row['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger" title="Löschen">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require ESSE_ROOT . '/admin/layout.php';
