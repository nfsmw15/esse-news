<?php

declare(strict_types=1);

namespace EsseNews;

use Esse\Auth;
use Esse\DB;

class NewsRepository
{
    public static function migrate(): void
    {
        $t = DB::table('news');
        DB::query("CREATE TABLE IF NOT EXISTS `{$t}` (
            `id`           bigint(20)   NOT NULL AUTO_INCREMENT,
            `datum`        date         NOT NULL,
            `zeit`         time         NOT NULL,
            `ueberschrift` varchar(128) NOT NULL,
            `kurznews`     varchar(500) NOT NULL,
            `news`         longtext     NOT NULL,
            `autor`        varchar(64)  NOT NULL,
            `active`       tinyint(1)   NOT NULL DEFAULT 1 COMMENT '0=inaktiv, 1=aktiv',
            `visible`      tinyint(1)   NOT NULL DEFAULT 0 COMMENT '0=intern, 1=extern',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public static function drop(): void
    {
        $t = DB::table('news');
        DB::query("DROP TABLE IF EXISTS `{$t}`");
    }

    public static function all(): array
    {
        $t = DB::table('news');
        return DB::fetchAll("SELECT * FROM `{$t}` ORDER BY datum DESC, zeit DESC");
    }

    public static function listPublic(int $limit = 20, int $offset = 0): array
    {
        $t    = DB::table('news');
        $cond = Auth::check() ? '' : ' AND visible = 1';
        return DB::fetchAll(
            "SELECT * FROM `{$t}` WHERE active = 1{$cond} ORDER BY datum DESC, zeit DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }

    public static function countPublic(): int
    {
        $t    = DB::table('news');
        $cond = Auth::check() ? '' : ' AND visible = 1';
        return (int) DB::value("SELECT COUNT(*) FROM `{$t}` WHERE active = 1{$cond}");
    }

    public static function findPublic(int $id): ?array
    {
        $t    = DB::table('news');
        $cond = Auth::check() ? '' : ' AND visible = 1';
        return DB::fetch("SELECT * FROM `{$t}` WHERE id = ? AND active = 1{$cond}", [$id]);
    }

    public static function find(int $id): ?array
    {
        $t = DB::table('news');
        return DB::fetch("SELECT * FROM `{$t}` WHERE id = ?", [$id]);
    }

    public static function create(array $data): int
    {
        return DB::insert(DB::table('news'), $data);
    }

    public static function update(int $id, array $data): void
    {
        DB::update(DB::table('news'), $data, ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        DB::delete(DB::table('news'), ['id' => $id]);
    }

    public static function toggleActive(int $id): void
    {
        $t = DB::table('news');
        DB::query("UPDATE `{$t}` SET active = 1 - active WHERE id = ?", [$id]);
    }
}
