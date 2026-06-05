<?php

declare(strict_types=1);

use Esse\Auth;
use Esse\Ui;
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

$allNews     = NewsRepository::all();
$pageTitle   = 'News verwalten';
$activeNav   = 'admin.news';
$topbarRight = Ui::button('Neue News', '/admin/news/create', ['size' => 'sm', 'icon' => 'plus-lg']);

ob_start();

if (empty($allNews)) {
    echo Ui::emptyState('Keine News vorhanden', 'Noch keine News-Einträge erstellt.', [
        'icon'   => 'newspaper',
        'action' => Ui::button('Neue News', '/admin/news/create', ['icon' => 'plus-lg']),
    ]);
} else {
    $headers = ['#', 'Datum', 'Überschrift', 'Autor', 'Sichtbarkeit', 'Status', 'Aktionen'];
    $rows    = [];

    foreach ($allNews as $row) {
        $id = (int) $row['id'];

        $visiBadge   = $row['visible']
            ? Ui::badge('Extern',  'info')
            : Ui::badge('Intern',  'warning');

        $activeBadge = $row['active']
            ? Ui::badge('Aktiv',   'success')
            : Ui::badge('Inaktiv', 'default');

        $editBtn = Ui::button('', '/admin/news/edit/' . $id, [
            'variant' => 'ghost',
            'size'    => 'sm',
            'icon'    => 'pencil',
            'attr'    => ['title' => 'Bearbeiten'],
        ]);

        $toggleBtn = Ui::button('', '/admin/news', [
            'variant' => 'ghost',
            'size'    => 'sm',
            'method'  => 'post',
            'icon'    => $row['active'] ? 'pause' : 'play',
            'attr'    => ['title' => $row['active'] ? 'Deaktivieren' : 'Aktivieren'],
            'hidden'  => '<input type="hidden" name="_action" value="toggle">'
                       . '<input type="hidden" name="news_id" value="' . $id . '">',
        ]);

        $deleteBtn = Ui::button('', '/admin/news', [
            'variant' => 'danger',
            'size'    => 'sm',
            'method'  => 'post',
            'icon'    => 'trash',
            'attr'    => ['title' => 'Löschen', 'onclick' => "return confirm('News wirklich löschen?')"],
            'hidden'  => '<input type="hidden" name="_action" value="delete">'
                       . '<input type="hidden" name="news_id" value="' . $id . '">',
        ]);

        $titleCell = '<strong>' . Ui::e($row['ueberschrift']) . '</strong><br>'
                   . '<small>' . Ui::e(mb_strimwidth(strip_tags($row['kurznews']), 0, 80, '…')) . '</small>';

        $rows[] = [
            Ui::e((string) $id),
            Ui::e(date('d.m.Y', strtotime($row['datum']))),
            $titleCell,
            Ui::e($row['autor']),
            $visiBadge,
            $activeBadge,
            '<div style="white-space:nowrap">' . $editBtn . $toggleBtn . $deleteBtn . '</div>',
        ];
    }

    echo Ui::panel('Alle News', Ui::table($headers, $rows));
}

$content = ob_get_clean();
require ESSE_ROOT . '/admin/layout.php';
