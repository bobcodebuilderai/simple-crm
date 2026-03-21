# Simple CRM

Et enkelt, robust og vedlikeholdbart CRM-system bygget med PHP, MySQL og Tailwind CSS.

## Teknologistack

- **PHP** (ren, uten rammeverk)
- **MySQL** med PDO + prepared statements
- **Tailwind CSS** (via CDN)
- **Vanilla JavaScript**

## Funksjoner

### ✅ Implementert
- **Kundedatabase** - Full CRUD med Brønnøysundregistrene (BRReg) API-integrasjon
- **Kontaktpersoner** - Koblet til kunder, med primærkontakt-funksjon
- **Aktivitetslogg** - Loggføring av møter, samtaler, e-poster, etc.
- **Deals/Salg** - Salgsmuligheter med status (ny, pågående, vunnet, tapt)
- **Oppgaver** - Tasks knyttet til deals med forfallsdato og prioritet
- **Globalt søk** - Søk i kunder og kontaktpersoner
- **Dashboard** - Oversikt med statistikk

### 🚧 Kommer (ikke fullført)
- Filvedlegg til aktiviteter
- Prosjektlenker
- E-post/SMS-påminnelser for oppgaver
- Brukerautentisering

## Arkitektur

```
simple-crm/
├── config/              # Konfigurasjonsfiler
├── includes/            # Hjelpefunksjoner og database
├── models/              # Data-modeller (MVC)
├── controllers/         # Kontrollere (MVC)
├── views/               # Views/templates (MVC)
├── public/              # Offentlig tilgjengelige filer
│   └── index.php        # Entry point
├── uploads/             # Opplastede filer
└── sql/                 # Database-schema
```

## Installasjon

### 1. Forutsetninger
- PHP 7.4+
- MySQL 5.7+ eller MariaDB 10.2+
- Apache eller Nginx

### 2. Database-oppsett

```sql
-- Opprett database og bruker
CREATE DATABASE simple_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'crm_user'@'localhost' IDENTIFIED BY 'ditt_passord';
GRANT ALL PRIVILEGES ON simple_crm.* TO 'crm_user'@'localhost';
FLUSH PRIVILEGES;

-- Importer schema
mysql -u crm_user -p simple_crm < sql/schema.sql
```

### 3. Konfigurasjon

Kopier `config/config.example.php` til `config/config.php`:

```bash
cp config/config.example.php config/config.php
```

Rediger `config/config.php` med dine database-innstillinger:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'simple_crm');
define('DB_USER', 'crm_user');
define('DB_PASS', 'ditt_passord');
define('APP_URL', 'http://localhost/simple-crm');
```

### 4. Webserver-konfigurasjon

**Apache (.htaccess):**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

**Nginx:**
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 5. Tillatelser

```bash
chmod 755 uploads/
chmod 755 uploads/attachments/
```

## Bruk

### Tilgang
- Åpne `http://din-server/simple-crm` i nettleseren
- Dashboard viser oversikt over kunder, deals og oppgaver

### BRReg-oppslag
Ved opprettelse av ny kunde kan du skrive organisasjonsnummer (9 siffer) og klikke "Hent data" for automatisk utfylling fra Brønnøysundregistrene.

## Sikkerhet

- ✅ PDO med prepared statements (beskyttelse mot SQL injection)
- ✅ Output escaping (beskyttelse mot XSS)
- ✅ CSRF-tokens på alle skjemaer
- ✅ Input-validering på server-side
- ✅ Filopplasting med type- og størrelsesvalidering

## Database-design

### Tabeller
- `customers` - Kunder
- `contacts` - Kontaktpersoner
- `activities` - Aktivitetslogg
- `attachments` - Filvedlegg
- `projects` - Prosjektlenker (eksterne)
- `deals` - Salgsmuligheter
- `tasks` - Oppgaver
- `users` - Brukere (for fremtidig auth)

## Videreutvikling

### Planlagte funksjoner (v2)
- [ ] Brukerautentisering og roller
- [ ] Filvedlegg til aktiviteter
- [ ] E-postpåminnelser for oppgaver
- [ ] Rapporter og eksport
- [ ] API for integrasjoner

## Lisens

MIT License - Fri bruk og modifisering.

## Utviklet av

Bygget som et læringsprosjekt for enkel, robust PHP-arkitektur uten tunge rammeverk.
