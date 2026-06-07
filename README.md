# esse-news

News-System Plugin für [ESSE CMS](https://github.com/nfsmw15/esse-cms).

## Über das Plugin

Fügt ein vollständiges News-System zum ESSE CMS hinzu. Unterstützt öffentliche und interne News,
Frontend-Darstellung mit Theme-Integration, vollständige Admin-Verwaltung und WYSIWYG-Editor mit
Bild-Upload.

Das Plugin gibt **kein Bootstrap-spezifisches Markup** aus. Alle Ausgaben nutzen die `Esse\Ui`-Schicht
und funktionieren mit jedem ESSE-Theme, unabhängig vom eingesetzten CSS-Framework oder Icon-Pack.

## Voraussetzungen

- [ESSE CMS](https://github.com/nfsmw15/esse-cms) >= 0.2.0

## Installation

1. ZIP-Datei herunterladen (Releases) oder aus dem Quellcode erstellen
2. Im ESSE Admin unter **Plugins → ZIP hochladen** installieren
3. Plugin in der Plugin-Liste aktivieren

Die Datenbanktabelle wird beim ersten Aktivieren automatisch angelegt.

### ZIP selbst erstellen

```bash
zip -r esse-news-v0.1.0.zip esse-news/ \
  --exclude "*.git*" --exclude "*/.vscode/*"
```

## Routen

| Route | Beschreibung | Sichtbarkeit |
|-------|-------------|--------------|
| `/news` | News-Übersicht | öffentlich |
| `/news/{id}` | News-Detailseite | öffentlich |
| `/admin/news` | Admin-Übersicht | admin |
| `/admin/news/create` | News erstellen | admin |
| `/admin/news/edit/{id}` | News bearbeiten | admin |

## Features

- News erstellen, bearbeiten, löschen
- News aktiv / inaktiv schalten
- Sichtbarkeit: **Intern** (nur eingeloggte Mitglieder) oder **Extern** (öffentlich)
- Autor wird automatisch vom eingeloggten User gesetzt und ist nicht editierbar
- WYSIWYG-Editor via **Summernote BS5** inkl. Bild-Upload
- Sidebar-Eintrag im Admin-Bereich
- Frontend-Seiten im CMS registriert (Menü-Dropdown, Slug-Konflikt-Erkennung)
- Theme-Integration über `page.render`-Hook und `Esse\Ui`-Komponenten
- Icon-Pack-agnostisch — Icon-Namen werden ohne Prefix übergeben, das aktive Pack liefert den Prefix

## Dateistruktur

```
esse-news/
├── plugin.json           Metadaten (Name, Version, Klasse)
├── Plugin.php            Hauptklasse — boot(), install(), uninstall()
├── NewsRepository.php    Datenbankzugriff (CRUD, Migration)
├── admin/
│   ├── list.php          Admin-Übersicht aller News
│   └── form.php          Admin-Formular (Erstellen & Bearbeiten)
└── frontend/
    ├── list.php          Frontend-Übersichtsseite (/news)
    └── detail.php        Frontend-Detailseite (/news/{id})
```

## Lizenz

AGPL-3.0-or-later — siehe [LICENSE](LICENSE)

Copyright (C) 2026 Andreas P. — [nfsmw15.de](https://nfsmw15.de)
