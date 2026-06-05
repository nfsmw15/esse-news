<?php

declare(strict_types=1);

namespace EsseNews;

use Esse\PageRenderer;
use Esse\Router;

require_once __DIR__ . '/NewsRepository.php';

class Plugin extends \Esse\Plugin
{
    public function boot(): void
    {
        NewsRepository::migrate();

        $this->addAdminNav('News', '/admin/news', 'bi-newspaper', 'admin.news');

        $this->registerPage('/news',       'News',         'newspaper');
        $this->registerPage('/news/{id}',  'News-Detail',  'newspaper');

        $base = $this->basePath();

        // Frontend
        Router::get('/news', fn() => PageRenderer::renderFile("{$base}/frontend/list.php", 'News', 'public', 'newspaper'),
            ['name' => 'news.list', 'auth' => 'public']);

        Router::get('/news/{id}', function (string $id) use ($base) {
            $newsId = (int) $id;
            require "{$base}/frontend/detail.php";
        }, ['name' => 'news.detail', 'auth' => 'public']);

        // Admin
        Router::get('/admin/news', fn() => require "{$base}/admin/list.php",
            ['name' => 'admin.news', 'auth' => 'admin']);

        Router::post('/admin/news', fn() => require "{$base}/admin/list.php",
            ['name' => 'admin.news.post', 'auth' => 'admin']);

        Router::get('/admin/news/create', fn() => require "{$base}/admin/form.php",
            ['name' => 'admin.news.create', 'auth' => 'admin']);

        Router::post('/admin/news/create', fn() => require "{$base}/admin/form.php",
            ['name' => 'admin.news.create.post', 'auth' => 'admin']);

        Router::get('/admin/news/edit/{id}', function (string $id) use ($base) {
            $newsId = (int) $id;
            require "{$base}/admin/form.php";
        }, ['name' => 'admin.news.edit', 'auth' => 'admin']);

        Router::post('/admin/news/edit/{id}', function (string $id) use ($base) {
            $newsId = (int) $id;
            require "{$base}/admin/form.php";
        }, ['name' => 'admin.news.edit.post', 'auth' => 'admin']);
    }

    public function install(): void
    {
        // DB-Migration läuft in boot() — hier ggf. Seed-Daten einfügen
    }

    public function uninstall(): void
    {
        NewsRepository::drop();
    }
}
