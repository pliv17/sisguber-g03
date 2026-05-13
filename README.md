# Sistema de Abastecimiento y Almacén

Scaffolding base (PHP 8.2 · MySQL 10.4 · Bootstrap 5.3 · jQuery 3.x)
sin framework, con arquitectura MVC ligera y front controller único.

---

## Requisitos

| Componente | Versión mínima |
|---|---|
| PHP | 8.2 |
| MySQL / MariaDB | 10.4 |
| Apache | 2.4 (con `mod_rewrite`) |
| Composer | 2.x |

> El proyecto está diseñado para XAMPP 8.2.12, pero funciona en cualquier
> entorno con los requisitos anteriores.

---

## Instalación rápida

```bash
# 1. Clonar / descomprimir el proyecto
cd /ruta/a/htdocs       # o donde apunte tu document root

# 2. Instalar dependencias PHP
composer install

# 3. Configurar entorno
cp .env.example .env
# Edita .env con tus datos de base de datos y APP_URL

# 4. Crear la base de datos (si aún no existe)
mysql -u root -p -e "CREATE DATABASE abastecimiento CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Importar dump existente (cuando lo tengas)
# mysql -u root -p abastecimiento < dump_legacy.sql
```

### Servidor de desarrollo rápido (sin Apache)

```bash
php -S localhost:8080 -t public
```

Abre `http://localhost:8080` en el navegador.

### Con Apache (XAMPP)

El **document root** del virtual host debe apuntar a `public/`:

```apache
<VirtualHost *:80>
    ServerName abastecimiento.local
    DocumentRoot "C:/xampp/htdocs/abastecimiento/public"
    <Directory "C:/xampp/htdocs/abastecimiento/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Asegúrate de que `mod_rewrite` esté habilitado en `httpd.conf`.

---

## Variables de entorno (`.env`)

| Variable | Descripción | Ejemplo |
|---|---|---|
| `APP_ENV` | Entorno: `development` / `production` | `development` |
| `APP_DEBUG` | Mostrar errores detallados | `true` |
| `APP_URL` | URL base sin barra final | `http://localhost:8080` |
| `DB_HOST` | Host de MySQL | `127.0.0.1` |
| `DB_PORT` | Puerto de MySQL | `3306` |
| `DB_NAME` | Nombre de la base de datos | `abastecimiento` |
| `DB_USER` | Usuario de MySQL | `root` |
| `DB_PASS` | Contraseña de MySQL | *(vacío en local)* |
| `APP_TIMEZONE` | Zona horaria PHP | `America/Lima` |
| `SESSION_NAME` | Nombre de la cookie de sesión | `abastecimiento_session` |

**Nunca subas `.env` al repositorio.** Solo `.env.example`.

---

## Estructura de carpetas

```
abastecimiento/
├── bootstrap.php               ← Arranque: autoload, .env, errores
├── composer.json               ← PSR-4 (App\ → src/) + phpdotenv
├── phpcs.xml                   ← Reglas PSR-12 para el equipo
│
├── public/                     ← ⚠️  ÚNICO directorio expuesto al cliente
│   ├── index.php               ← Front Controller (todo entra aquí)
│   ├── .htaccess               ← Rewrite rules → index.php
│   ├── css/
│   │   └── app.css             ← Estilos propios (Bootstrap ya en CDN)
│   ├── js/
│   │   └── app.js              ← jQuery + helpers Ajax
│   └── img/                    ← Imágenes estáticas
│
├── routes/
│   └── web.php                 ← Registro de todas las rutas
│
├── src/                        ← Código PHP de la aplicación (PSR-4 App\)
│   ├── Core/
│   │   ├── Database.php        ← Conexión PDO centralizada (Singleton)
│   │   ├── Router.php          ← Enrutador ligero
│   │   ├── Request.php         ← Encapsula $_GET/$_POST/$_SERVER
│   │   ├── Response.php        ← json(), redirect(), view()
│   │   ├── Session.php         ← Sesión segura (httponly, samesite)
│   │   └── helpers.php         ← e(), asset(), url(), dd()
│   ├── Controllers/            ← Solo orquestación HTTP
│   ├── Services/               ← Lógica de negocio
│   └── Repositories/           ← Consultas SQL (solo PDO, sin HTML)
│
├── views/
│   ├── layouts/
│   │   └── main.php            ← Layout HTML5 con navbar Bootstrap
│   ├── home/
│   │   └── index.php           ← Vista de bienvenida
│   └── errors/
│       └── error.php           ← Vista de errores 404/500
│
├── logs/                       ← Logs de la app (gitignoreado)
├── config/                     ← Configs adicionales futuras
├── .env.example                ← Plantilla de variables de entorno
└── .gitignore
```

---

## Rutas disponibles

| Método | URI | Descripción |
|---|---|---|
| GET | `/` | Página de bienvenida con panel de estado |
| GET | `/health` | JSON con estado del sistema y BD |
| GET | `/api/ping` | JSON para prueba Ajax (`app.js`) |

---

## Cómo añadir un módulo nuevo (ejemplo: Maestros → Almacenes)

### Paso 1 — Repository (acceso a datos)

Crea `src/Repositories/AlmacenesRepository.php`:

```php
<?php
namespace App\Repositories;

class AlmacenesRepository extends BaseRepository
{
    protected string $table = 'almacenes';

    public function findAll(): array
    {
        return $this->pdo
            ->query("SELECT * FROM {$this->table} ORDER BY nombre")
            ->fetchAll();
    }

    public function insert(array $data): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO {$this->table} (nombre, activo) VALUES (:nombre, :activo)"
        );
        $stmt->execute([':nombre' => $data['nombre'], ':activo' => 1]);
        return (int) $this->pdo->lastInsertId();
    }
}
```

### Paso 2 — Service (lógica de negocio)

Crea `src/Services/AlmacenesService.php`:

```php
<?php
namespace App\Services;

use App\Repositories\AlmacenesRepository;

class AlmacenesService
{
    public function __construct(
        private AlmacenesRepository $repo = new AlmacenesRepository()
    ) {}

    public function listar(): array { return $this->repo->findAll(); }

    public function guardar(array $datos): int
    {
        if (empty($datos['nombre'])) {
            throw new \InvalidArgumentException('El nombre es requerido.');
        }
        return $this->repo->insert($datos);
    }
}
```

### Paso 3 — Controller (orquestación HTTP)

Crea `src/Controllers/Maestros/AlmacenesController.php`:

```php
<?php
namespace App\Controllers\Maestros;

use App\Core\{Request, Response};
use App\Services\AlmacenesService;

class AlmacenesController
{
    public function __construct(
        private AlmacenesService $service = new AlmacenesService()
    ) {}

    public function index(Request $request): void
    {
        $almacenes = $this->service->listar();
        Response::view('layouts.main', [
            'pageTitle'   => 'Almacenes',
            'contentView' => 'maestros/almacenes/index',
            'almacenes'   => $almacenes,
        ]);
    }
}
```

### Paso 4 — Vista

Crea `views/maestros/almacenes/index.php` con la tabla HTML.

### Paso 5 — Rutas

En `routes/web.php`:

```php
use App\Controllers\Maestros\AlmacenesController;

$router->get('/maestros/almacenes', [AlmacenesController::class, 'index']);
$router->post('/maestros/almacenes/guardar', [AlmacenesController::class, 'guardar']);
```

---

## Estándares de código (PSR-12)

```bash
# Instalar PHP CodeSniffer
composer require --dev squizlabs/php_codesniffer

# Verificar
./vendor/bin/phpcs

# Corregir automáticamente
./vendor/bin/phpcbf
```

---

## Seguridad — recordatorio rápido

- **XSS**: Usa siempre `e($variable)` en vistas, nunca `echo $variable` directo.
- **SQL Injection**: Siempre `prepare()` + `execute([':param' => $valor])`.
- **CSRF**: Implementar token en formularios POST (próximo paso).
- **Sesiones**: `Session::start()` ya configura `httponly` y `SameSite=Strict`.
- **Producción**: Pon `APP_DEBUG=false` y `APP_ENV=production` en `.env`.

---

## Importar base de datos legacy

```bash
# Importar dump existente
mysql -u root -p abastecimiento < /ruta/al/dump_legacy.sql

# Verificar tablas importadas
mysql -u root -p -e "USE abastecimiento; SHOW TABLES;"
```

---

## Equipo de desarrollo

> Actualiza esta sección con los datos del equipo.

- **Stack**: PHP 8.2, MySQL 10.4, Bootstrap 5.3, jQuery 3.7
- **Arquitectura**: MVC ligero, Front Controller, PSR-4
- **Estándar**: PSR-12
