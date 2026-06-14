# Changelog — esse-news

## [Unreleased]

### Behoben

- **CSP-Konformität**: Inline `<style>`/`<script>`-Blöcke und ein Inline-`style`-Attribut
  in `admin/form.php` bzw. `admin/list.php` verstießen gegen die CSP (`script-src 'self'`,
  `style-src 'self'`). CSS und JS nach `assets/css/news-admin.css` bzw.
  `assets/js/news-admin.js` ausgelagert, über eine eigene Plugin-Asset-Route ausgeliefert
  und per `$extraHead`/`$extraScriptFiles` eingebunden. Inline-`style="white-space:nowrap"`
  durch Bootstrap-Klasse `text-nowrap` ersetzt.
- Editor-Initialisierung nutzte das veraltete `$extraScripts` (vom Admin-Layout nicht mehr
  unterstützt) — auf `$extraScriptConfig`/`$extraScriptFiles` umgestellt, Konfiguration
  (Editor-Selektor, Upload-URL) läuft jetzt über den JSON-Konfig-Block.

### Hinzugefügt

- "Aus Mediathek einfügen"-Button im Summernote-Editor (`admin/partials/media-picker.php`
  + `media-button.js`, analog zum CMS-eigenen Seiteneditor).

## [0.1.1] — 2026-06-05

### Behoben

- Admin-Templates (`admin/list.php`, `admin/form.php`) fälschlicherweise auf `Esse\Ui::*` migriert —
  der Admin-Bereich läuft auf Bootstrap 5 und kennt keine `esse-*`-Klassen.
  Beide Dateien wieder auf direktes Bootstrap-Markup zurückgestellt.

---

## [0.1.0] — 2026-06-05

### Theme-agnostische UI-Schicht

Komplette Migration aller Frontend- und Admin-Templates von direktem Bootstrap-Markup
auf `Esse\Ui::*`-Methoden. Das Plugin gibt kein Bootstrap-spezifisches HTML mehr aus —
alle Themes, auch solche ohne Bootstrap, rendern die Plugin-Ausgaben korrekt.

**Voraussetzung:** ESSE CMS >= 0.2.0 (enthält `Esse\Ui`, `Esse\PageRenderer` mit Icon-Parameter).

### Geändert

- **Bootstrap → Esse\Ui** — alle Komponenten ersetzt:
  - `<div class="card">` → `Ui::panel()`
  - `<div class="alert alert-*">` → `Ui::alert()`
  - `<span class="badge bg-*">` → `Ui::badge()`
  - `<a class="btn …">` → `Ui::button()`
  - `<div class="row"><div class="col">` → `Ui::grid()` / `Ui::section()`
  - `<table class="table">` → `Ui::table()`
  - `<p class="text-muted text-center">` → `Ui::emptyState()`
  - `<nav><ol class="breadcrumb">` → `Ui::breadcrumb()`
  - `<hr class="mb-*">` → `Ui::divider()`

- **Icon-Pack-Unterstützung** — alle Icon-Referenzen sind pack-agnostisch:
  - Ui-Komponenten-Option `'icon'` enthält nur den Icon-Namen (z. B. `'newspaper'`),
    der Prefix wird vom aktiven Icon-Pack geliefert
  - Direkte `<i class="bi bi-*">`-Tags durch `Ui::icon()` ersetzt
  - `registerPage()` und `PageRenderer::renderFile()` übergeben den Icon-Namen

- **Frontend-Rendering** — `/news`-Route nutzt jetzt `PageRenderer::renderFile()` mit Icon-Parameter;
  `/news/{id}` übergibt `'icon' => 'newspaper'` im `$page`-Array an den `page.render`-Hook

---

## [0.0.1] — 2026-06-01

### Erstveröffentlichung

Initiale Implementierung als Plugin für [ESSE CMS](https://github.com/nfsmw15/esse-cms).

### Features

- News erstellen, bearbeiten, löschen
- News aktiv / inaktiv schalten
- Sichtbarkeit: **Intern** (nur eingeloggte Mitglieder) / **Extern** (öffentlich)
- Frontend-Übersichtsseite (`/news`) mit Teaser-Text
- Frontend-Detailseite (`/news/{id}`)
- Admin-Bereich (`/admin/news`) mit vollständiger CRUD-Verwaltung
- Autor wird automatisch vom eingeloggten User gesetzt, nicht editierbar

### Editor

- **Summernote BS5** WYSIWYG-Editor — identische Initialisierung wie der CMS-eigene Seiten-Editor
- Deutsche Lokalisierung (`summernote-de-DE`)
- Dark-Theme-Anpassung passend zum ESSE Admin-Layout
- Bild-Upload via `/admin/files/upload` (CMS-eigener Upload-Endpoint)

### Integration

- Sidebar-Eintrag via `addAdminNav()`
- Frontend-Seiten via `registerPage()` im CMS angemeldet (Menü-Dropdown, Slug-Konflikt-Erkennung)
- Theme-Integration über `Hooks::fire('page.render', ...)` — kompatibel mit jedem ESSE-Theme
- DB-Migration via `CREATE TABLE IF NOT EXISTS` — läuft automatisch in `boot()`
- Deinstallation löscht die Datenbanktabelle vollständig
