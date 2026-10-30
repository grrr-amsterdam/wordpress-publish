# WordPress Publish — Claude Code instructies

Dit bestand geldt voor iedereen die Claude Code in deze repo gebruikt. Lees het van begin tot eind voordat je begint.

## Project-context

WordPress-plugin van Norday (voorheen GRRR) waarmee CMS-gebruikers een Next.js-frontend kunnen publiceren vanuit WordPress. De plugin biedt een admin-pagina en een REST-API die een GitHub Actions-workflow (`workflow_dispatch`) triggert; die workflow bouwt en deployt de frontend. Daarnaast wordt een publish automatisch getriggerd op het moment dat een ingepland bericht live gaat. De plugin wordt via Composer (type `wordpress-plugin`) in klantprojecten geïnstalleerd.

- PHP ^8.1, namespace `Grrr\WordpressPublish`, PSR-4 vanuit `src/`.
- Entry point: `wordpress-publish.php` (bevat de plugin-header met het versienummer); `src/Plugin.php` bootstrapt `Api`, `Admin` en `ScheduledPublish`.
- Authenticatie richting GitHub gaat via een GitHub App: `src/JWT.php` ondertekent met de private key (`ext-openssl`), `src/GitHubApi.php` wisselt die in voor een installation token.
- Configuratie komt uitsluitend uit PHP-constanten (`GRRR_WORDPRESS_PUBLISH_*`), gelezen in `src/Config.php`. Voeg nooit echte waarden toe aan de repo; zie `README.md` voor de lijst.
- Views in `src/views/`, statische assets (CSS, JS, icons) in `src/assets/`. `src/views` is uitgesloten van PHPStan.
- Geen package.json-scripts of JS-build: Node is er alleen voor Prettier.
- Versiebeheer: bij een release het versienummer in de plugin-header van `wordpress-publish.php` verhogen én een entry toevoegen aan `CHANGELOG.md`.
- Het textdomain mag pas vanaf de `init`-hook geladen worden (zie CHANGELOG 1.1.2); laad geen vertalingen eerder.

## Tooling en commando's

- Tests: geen testsuite aanwezig.
- Lint / format-check: `pnpm exec prettier --check .`
- Format: `pnpm exec prettier --write .` (formatteert ook PHP via `@prettier/plugin-php`)
- Statische analyse: `composer static-analysis` (PHPStan level 9 met `phpstan-baseline.neon`; voeg geen nieuwe fouten toe aan de baseline, los ze op)
- Installatie: `composer install` en `pnpm install`
- CI (`.github/workflows/ci.yml`) draait exact deze Prettier-check en PHPStan op elke push, plus actionlint op de workflow-bestanden.

## Algemene regels

De Norday-teamstandaarden (coding patterns, git-workflow, spec-driven development), de MCP-servers (Context7, Sentry) en de `/norday-engineering:spec`-skill komen uit de `norday-engineering`-plugin en gelden hier automatisch. Installeer die plugin als dat nog niet is gebeurd: `/plugin marketplace add norday-agency/claude-plugins` en `/plugin install norday-engineering@norday-tools`.
