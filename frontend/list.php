<?php

declare(strict_types=1);

use Esse\Auth;
use Esse\Ui;
use EsseNews\NewsRepository;

$newsList = NewsRepository::listPublic(20, 0);

if (empty($newsList)) {
    echo Ui::emptyState('Keine Neuigkeiten', 'Aktuell sind keine News vorhanden.', ['icon' => 'newspaper']);
} else {
    foreach ($newsList as $row) {
        $datum = date('d.m.Y', strtotime($row['datum']));
        $zeit  = substr($row['zeit'], 0, 5);
        $url   = '/news/' . (int) $row['id'];

        $badge = '';
        if (Auth::check()) {
            $badge = ($row['visible']
                ? Ui::badge('Extern', 'info')
                : Ui::badge('Intern', 'warning')) . ' ';
        }

        $meta = '<p class="news-meta">'
              . Ui::e($row['autor'])
              . ' &middot; '
              . Ui::e($datum) . ' um ' . Ui::e($zeit) . ' Uhr'
              . '</p>';

        $body = $badge
              . $meta
              . '<p>' . Ui::e(strip_tags($row['kurznews'])) . '</p>';

        echo Ui::section(
            $row['ueberschrift'],
            $body,
            ['action' => Ui::button('Mehr lesen', $url, ['variant' => 'ghost', 'size' => 'sm', 'icon' => 'arrow-right'])]
        );
    }
}
