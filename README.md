# DockerDAW

Entorno base con Docker para ejecutar una aplicación PHP detrás de Nginx y con MySQL como base de datos.

## Qué incluye

- Nginx como servidor web
- PHP 8.2 FPM sobre Alpine
- MySQL 8.0
- Volúmenes montados para desarrollo local
- Página inicial en PHP con un dashboard simple de estado

## Estructura del proyecto

```text
.
├── docker-compose.yml
├── dockerfile
├── nginx/
│   └── conf.d/
│       └── default.conf
└── src/
    └── index.php
```

## Servicios

### nginx

- Imagen: `nginx:alpine`
- Puerto expuesto: `80`
- Sirve el contenido desde `src/`
- Usa la configuración definida en `nginx/conf.d/default.conf`

### php

- Construido desde `dockerfile`
- Basado en `php:8.2-fpm-alpine`
- Incluye `composer`, `git`, `nodejs`, `npm` y `mysql-client`
- Extensiones instaladas: `pdo_mysql` y `opcache`

### mysql

- Imagen: `mysql:8.0`
- Puerto expuesto: `3306`
- Volumen persistente: `db_data`

## Cómo levantar el proyecto

1. Abre una terminal en la raíz del proyecto.
2. Construye e inicia los contenedores:

```bash
docker compose up --build
```

3. Abre en el navegador:

```text
http://localhost
```

## Comandos útiles

Iniciar en segundo plano:

```bash
docker compose up -d --build
```

Detener los servicios:

```bash
docker compose down
```

Detener y eliminar volúmenes:

```bash
docker compose down -v
```

Ver logs:

```bash
docker compose logs -f
```

## Configuración de base de datos

Actualmente hay una diferencia entre la configuración de MySQL y los valores por defecto usados por `src/index.php`.

El contenedor MySQL arranca con:

- `MYSQL_ROOT_PASSWORD=your_root_password`
- `MYSQL_DATABASE=your_database_name`

Pero la aplicación PHP intenta conectarse por defecto con:

- `DB_HOST=localhost`
- `DB_PORT=3306`
- `DB_NAME=app_db`
- `DB_USER=root`
- `DB_PASSWORD=`

Eso significa que el dashboard probablemente mostrará la base de datos como desconectada hasta que unifiques la configuración.

### Opción recomendada

Configura variables de entorno en el servicio `php` dentro de `docker-compose.yml` para que coincidan con MySQL:

```yaml
environment:
  DB_HOST: mysql
  DB_PORT: 3306
  DB_NAME: your_database_name
  DB_USER: root
  DB_PASSWORD: your_root_password
```

Después reinicia los contenedores:

```bash
docker compose down
docker compose up --build
```

## Configuración de Nginx

La configuración actual hace lo siguiente:

- Usa `/var/www/html` como raíz del sitio
- Sirve `index.php` e `index.html`
- Redirige solicitudes al front controller cuando el archivo no existe
- Envía los archivos PHP a `php:9000`

## Notas

- En `docker-compose.yml` el build referencia `Dockerfile`, mientras que el archivo del proyecto se llama `dockerfile`.
- En macOS normalmente esto no genera problemas si el sistema de archivos no distingue mayúsculas y minúsculas.
- En entornos Linux o sistemas case-sensitive puede ser necesario renombrar el archivo a `Dockerfile` o ajustar el nombre en `docker-compose.yml`.

## Próximos pasos sugeridos

- Añadir variables de entorno reales para la aplicación
- Crear un archivo `.env` para centralizar configuración
- Añadir una aplicación PHP más completa dentro de `src/`
- Incorporar scripts de desarrollo y pruebas
