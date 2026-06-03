# Changelog — esse-news

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
