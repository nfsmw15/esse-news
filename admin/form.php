<?php

declare(strict_types=1);

use Esse\Auth;
use EsseNews\NewsRepository;

$newsId = $newsId ?? 0;
$isEdit = $newsId > 0;

$dbRow = null;
if ($isEdit) {
    $dbRow = NewsRepository::find($newsId);
    if (!$dbRow) {
        header('Location: /admin/news');
        exit;
    }
}

$errors = [];
$flash  = null;
if (!empty($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

// Autor ist immer der eingeloggte User — beim Erstellen gesetzt, beim Bearbeiten unveränderlich
$currentAutor = $isEdit
    ? ($dbRow['autor'] ?? '')
    : (Auth::user()['display_name'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCsrf()) { http_response_code(403); exit; }

    $ueberschrift = trim($_POST['ueberschrift'] ?? '');
    $kurznews     = trim($_POST['kurznews']     ?? '');
    $news         = $_POST['news']              ?? '';
    $visible      = (int) ($_POST['visible']    ?? 0);

    if ($ueberschrift === '') $errors[] = 'Überschrift darf nicht leer sein.';
    if ($kurznews     === '') $errors[] = 'Kurzbeschreibung darf nicht leer sein.';
    if ($news         === '') $errors[] = 'Text darf nicht leer sein.';

    if (empty($errors)) {
        $data = [
            'ueberschrift' => mb_substr($ueberschrift, 0, 128),
            'kurznews'     => mb_substr($kurznews,     0, 500),
            'news'         => $news,
            'autor'        => mb_substr($currentAutor, 0, 64),
            'visible'      => $visible ? 1 : 0,
        ];

        if ($isEdit) {
            NewsRepository::update($newsId, $data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'News aktualisiert.'];
            header("Location: /admin/news/edit/{$newsId}");
        } else {
            $data['datum']  = date('Y-m-d');
            $data['zeit']   = date('H:i:s');
            $data['active'] = 1;
            $newId = NewsRepository::create($data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'News erstellt.'];
            header("Location: /admin/news/edit/{$newId}");
        }
        exit;
    }
}

$f = (!empty($errors) && $_SERVER['REQUEST_METHOD'] === 'POST')
    ? array_merge($dbRow ?? [], $_POST)
    : ($dbRow ?? []);

$pageTitle = $isEdit ? 'News bearbeiten' : 'Neue News';
$activeNav = 'admin.news';

ob_start();
?>
<div class="mb-3">
    <a href="/admin/news" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Zur Übersicht
    </a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach ?>
    </ul>
</div>
<?php endif ?>

<form method="post">
    <input type="hidden" name="_csrf" value="<?= Auth::csrfToken() ?>">

    <div class="card mb-4">
        <div class="card-header py-2">
            <small class="text-secondary">
                <?= $isEdit ? 'News bearbeiten' : 'Neue News erstellen' ?>
            </small>
        </div>
        <div class="card-body">

            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label">Überschrift</label>
                    <input name="ueberschrift" type="text" class="form-control"
                           maxlength="128" required
                           value="<?= htmlspecialchars($f['ueberschrift'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Autor</label>
                    <input type="hidden" name="autor" value="<?= htmlspecialchars($currentAutor) ?>">
                    <input type="text" class="form-control" value="<?= htmlspecialchars($currentAutor) ?>" disabled>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Kurzbeschreibung
                    <small class="text-secondary">(Vorschautext in der Übersicht)</small>
                </label>
                <input name="kurznews" type="text" class="form-control"
                       maxlength="500" required
                       value="<?= htmlspecialchars($f['kurznews'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Vollständiger Text</label>
                <textarea name="news" id="news"><?= $f['news'] ?? '' ?></textarea>
            </div>

            <div class="mb-1">
                <label class="form-label">Sichtbarkeit</label>
                <div class="d-flex gap-4 mt-1">
                    <div class="form-check">
                        <input class="form-check-input" type="radio"
                               name="visible" value="0" id="vis-intern"
                               <?= empty($f['visible']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="vis-intern">
                            <i class="bi bi-lock text-warning"></i>
                            <strong>Intern</strong>
                            <small class="text-secondary">— nur für eingeloggte Mitglieder</small>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio"
                               name="visible" value="1" id="vis-extern"
                               <?= !empty($f['visible']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="vis-extern">
                            <i class="bi bi-globe2 text-primary"></i>
                            <strong>Extern</strong>
                            <small class="text-secondary">— öffentlich für alle Besucher</small>
                        </label>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="/admin/news" class="btn btn-outline-secondary">Abbrechen</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-floppy"></i> Speichern
            </button>
        </div>
    </div>
</form>

<?php require ESSE_ROOT . '/admin/partials/media-picker.php'; ?>
<?php
$content = ob_get_clean();

$extraHead = '<link rel="stylesheet" href="/public/vendor/summernote/summernote-bs5.min.css">
<link rel="stylesheet" href="/plugins/esse-news/assets/css/news-admin.css">';

$extraScriptConfig = array_merge($extraScriptConfig ?? [], [
    'news-editor-config' => [
        'selector'  => '#news',
        'uploadUrl' => '/admin/files/upload',
    ],
]);
$extraScriptFiles = array_merge($extraScriptFiles ?? [], [
    '/public/vendor/summernote/jquery.min.js',
    '/public/vendor/summernote/summernote-bs5.min.js',
    '/public/vendor/summernote/summernote-de-DE.min.js',
    '/public/assets/js/media-button.js',
    '/plugins/esse-news/assets/js/news-admin.js',
]);

require ESSE_ROOT . '/admin/layout.php';
