# CLAUDE.md — YoguiFit (prodaw2627mma)

Contexto del proyecto para Claude Code. Se carga automáticamente al iniciar cada sesión.

## Normas de trabajo

- **Responder siempre en español.**
- **Nunca crear ni publicar Artifacts** (ni páginas en claude.ai ni documentos en conectores) de este proyecto. Cualquier documento, plan, informe o explicación que haya que conservar se escribe como fichero `.md` dentro del repositorio (en la raíz o en `docs/`), para que solo pueda leerse desde aquí.
- No hacer commit ni push sin que se pida. Al hacerlo, mensajes de commit en español.
- No mostrar en la conversación valores de `.env` (contraseña de Neon, `APP_KEY`); si hay que inspeccionarlos, enmascararlos.

## Qué es

Web de **YoguiFit**, centro de terapia deportiva y masaje Thai Fusión. Proyecto de clase (DAW 2026-27). Permite ver servicios y tarifas, reservar una cita mediante un cuestionario y, con cuenta de usuario, consultar "Mis citas".

## Stack

- **Laravel 13** (requiere **PHP ≥ 8.4**: varios paquetes de Symfony 8 lo exigen) con **Laravel Breeze** (Blade) para la autenticación.
- **Vite 8 + Tailwind** para los assets del área de usuario (`layouts/app.blade.php`). Las páginas públicas usan `layouts/yoguifit.blade.php` con estilos propios y Bootstrap Icons desde CDN.
- **PostgreSQL en Neon** (región `eu-west-2`, Londres) como base de datos, en local y en producción.
- **Vercel** para el despliegue, con el runtime comunitario `vercel-php@0.9.0` (PHP 8.5).

## Estructura funcional

| Ruta | Controlador | Notas |
|---|---|---|
| `/` | `HomeController@index` | Portada |
| `/servicios` | `ServicioController@index` | Lista de servicios |
| `/tarifas` | `TarifaController@index` | Servicios agrupados por `tipo`: `cursos`, `masajes`, `yoga` |
| `/reservar` (GET/POST) | `CuestionarioController` | Crea una `Cita` en estado `pendiente`; acepta `?servicio_id=` |
| `/mis-citas` | `CitaController@index` | Requiere login |
| `/contacto`, `/donde-estamos`, `/acerca-de`, `/aviso-legal`, `/politica-privacidad`, `/terminos-condiciones` | `HomeController` | Páginas informativas (`resources/views/info/`) |
| `/login`, `/register`, `/profile`… | Breeze | `routes/auth.php` |

Modelos:
- `Servicio`: `tipo` (enum cursos/masajes/yoga), `titulo`, `descripcion`, `imagen`, `duracion`, `precio` (string), `orden`. Datos iniciales en `ServicioSeeder` (18 servicios).
- `Cita`: `user_id` (nullable), `servicio_id` (nullable), `nombre`, `telefono`, `sexo`, `respuesta_2`, `respuesta_3`, `estado` (enum pendiente/confirmada/cancelada), `notas`.
- `User`: el estándar de Breeze.

Sesiones, caché y colas usan el driver `database` (tablas en Neon).

## Entorno local

- PHP es el de **XAMPP**: `/usr/bin/php` → `/opt/lampp-8.4/php/bin/php` (PHP 8.4.26). `/opt/lampp` es un enlace a `/opt/lampp-8.4` y `/opt/lampp/htdocs` es el mismo directorio que `/opt/htdocs`.
- Ese PHP **no venía con PostgreSQL**. Se compilaron `pdo_pgsql` y `pgsql` con `phpize` a partir del código fuente de PHP 8.4.26 y se activaron en `/opt/lampp-8.4/php/etc/conf.d/20-pgsql.ini`. `apt install php8.4-pgsql` no sirve (ese PHP no es de apt). Si se actualiza XAMPP/PHP, hay que recompilarlas (requiere `libpq-dev` y `autoconf`).
- Node vía nvm (v23). Si `npm run build` falla con "Cannot find native binding", ejecutar `npm ci`.
- Arrancar: `php artisan serve`. Migraciones: `php artisan migrate` (van directamente contra Neon; no hay base de datos local).
- El `.env` **no está en el repo**. Plantilla en `.env.example`. Necesita `APP_KEY`, `DB_CONNECTION=pgsql` y `DATABASE_URL` (la cadena de conexión de Neon con el pooler). Si se cambia de equipo hay que copiarlo a mano.

## Despliegue en Vercel

- URL: https://prodaw2627mma.vercel.app — repo `mariomascu/prodaw2627mma`, rama `main`. **Cada push a `main` despliega automáticamente**; "Redeploy" en Vercel reutiliza el mismo commit (solo sirve para aplicar cambios de variables de entorno).
- `vercel.json`: todas las peticiones van a `api/index.php` (que incluye `public/index.php`); los estáticos de `public/` se sirven directamente. `/`, `/index.php` y `/.htaccess` se fuerzan a la función para que no se descarguen como ficheros. Región `lhr1`, junto a Neon.
- El sistema de ficheros de Vercel es de solo lectura: vistas compiladas y cachés de bootstrap van a `/tmp` (variables en `vercel.json`), logs a `stderr` en una sola línea (`LOG_STDERR_FORMATTER`) para que el panel de Vercel no corte el mensaje.
- Variables en el panel de Vercel: `APP_KEY` (la misma que la del `.env` local), `DB_CONNECTION=pgsql`, `APP_ENV=production`. `DATABASE_URL` y demás las inyecta la integración de Neon.
- `public/build` (assets compilados de Vite) **está versionado** a propósito: así Laravel encuentra el `manifest.json` dentro de la función. Tras cambiar CSS/JS, ejecutar `npm run build` y hacer commit de `public/build`.
- `vendor/` y `node_modules/` no están en el repo; Vercel instala las dependencias.
- `bootstrap/app.php` confía en todos los proxies (`trustProxies(at: '*')`) para generar URLs `https` detrás de Vercel.
- Para leer errores de producción: pestaña **Logs** del proyecto en Vercel, o `npx vercel logs prodaw2627mma.vercel.app`.

## Particularidades de Neon (no tocar sin entenderlas)

En `config/database.php`, conexión `pgsql`:

1. **`PDO::ATTR_EMULATE_PREPARES => true`**: el pooler de Neon (PgBouncer) aborta las transacciones con sentencias preparadas nativas (`current transaction is aborted`). Sin esto fallan las migraciones y cualquier escritura dentro de una transacción.
2. **Endpoint en la contraseña cuando no hay SNI**: el `libpq` del runtime de Vercel es anterior a la 14 y no envía SNI, así que Neon responde "Endpoint ID is not specified". Si el host es `*.neon.tech` y `PGSQL_LIBPQ_VERSION < 14`, la configuración descompone `DATABASE_URL` y usa la contraseña `endpoint=<id>$<contraseña>`. Con `libpq` ≥ 14 (local) se usa la URL tal cual, porque ese formato rompe la autenticación SCRAM.
3. La conexión lee `DB_URL` y, si no existe, `DATABASE_URL`.

La base de datos de Neon también contiene el esquema `neon_auth` (Neon Auth); no pertenece a la app y no debe tocarse.
