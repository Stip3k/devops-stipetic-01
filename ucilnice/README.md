# Sistem za rezervacijo učilnic

Spletna aplikacija je gostovana in dosegljiva na povezavi http://st.stipek.si/

## Testni uporabniški račun

Za testiranje aplikacije lahko uporabite naslednji račun:

```
Email: admin@example.com
Geslo: spltenetehnologije
```

## Sistemske zahteve

- PHP >= 8.1
- MySQL >= 5.7 ali MariaDB >= 10.3
- Composer
- Node.js & NPM (za frontend assets)
- Apache/Nginx server

## Okoljske spremenljivke

Vse okoljske spremenljivke so vključene v .env znotraj korenskega direktorija, tam so vrednsoti glede povezav v DB, splošne aplikacijske,...

```bash
cp .env.example .env
```

```
DB_CONNECTION=mysql
DB_HOST=x.x.x.x
DB_PORT=3306
DB_DATABASE=ucilnice
DB_USERNAME=ucilnica
DB_PASSWORD=ucilnica
```

## Dodatni moduli in paketi

Aplikacija uporablja naslednje dodatne pakete:

- **shayanys/laravel-reserve** - za sistem rezervacij
- **laravel/ui** - za Bootstrap auth poglede
- **FullCalendar** - za koledarski prikaz (CDN)
- **Font Awesome** - za ikone (CDN)
- **Bootstrap 5** - CSS framework (CDN)

## Ukazi v primeru nepravilnega nalagnja

```bash
composer dump-autoload
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```