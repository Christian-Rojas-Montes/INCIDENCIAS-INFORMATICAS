# Sistema de Atención de Incidencias Informáticas

Aplicación web desarrollada con Laravel 12, PHP y MySQL para registrar incidencias, asignar técnicos, administrar prioridades y mantener el historial de seguimiento.

## Requisitos

- PHP 8.2 o superior con las extensiones PDO MySQL, Mbstring, OpenSSL, Tokenizer, XML, Ctype, JSON, Fileinfo y BCMath.
- Composer 2.
- MySQL 8 o MariaDB 10.4 o superior (XAMPP incluye MariaDB).

## Puesta en marcha en XAMPP

1. Inicia MySQL desde el panel de control de XAMPP.
2. Crea la base de datos `incidencias_informaticas` en phpMyAdmin o en la consola MySQL:

   ```sql
   CREATE DATABASE incidencias_informaticas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Revisa `.env`. La configuración inicial usa `127.0.0.1:3306`, usuario `root` y contraseña vacía. Actualiza estos datos si tu instalación usa otros valores.
4. Desde la carpeta del proyecto ejecuta:

   ```bash
   composer install
   php artisan key:generate
   php artisan migrate --seed
   php artisan serve
   ```

5. Abre `http://127.0.0.1:8000`.

El seeder carga prioridades y registros de ejemplo para usuarios, técnicos e incidencias. Bootstrap se carga desde CDN.

## Pruebas

Ejecuta la suite automatizada con:

```bash
php artisan test
```

Las pruebas usan SQLite en memoria y ejecutan las migraciones de forma aislada; no modifican los datos de MySQL.

## Módulos

- **Usuarios:** solicitantes y sus incidencias.
- **Técnicos:** especialidades, disponibilidad y asignaciones.
- **Prioridades:** nivel y descripción.
- **Incidencias:** registro, fecha y hora de ocurrencia, lugar, clasificación, asignación y cambios de estado.
- **Seguimiento:** bitácora de comentarios; cada actualización sincroniza el estado y el técnico actual de la incidencia.

Los formularios validan los campos y relaciones. Los registros vinculados a incidencias no se pueden eliminar mientras se usen; al eliminar una incidencia también se elimina su historial.
