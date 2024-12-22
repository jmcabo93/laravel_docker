# Despliegue de API Laravel 8 con Docker

## Requisitos previos

Tener instalados:

- Docker
- Docker Compose

## Estructura del proyecto

```
project-root/
├── docker-compose.yml
├── Dockerfile
├── src/            # Código de la aplicación Laravel
├── .env            # Variables de entorno para Docker
├── nginx/
│   └── default.conf # Configuración de nginx
```

### Archivo `.env`

```
# Nombres de contenedores
APP_CONTAINER_NAME=laravel-app
NGINX_CONTAINER_NAME=nginx-server
DB_CONTAINER_NAME=mysql-db

# Puertos
NGINX_PORT=80
DB_PORT=3306

# La configuración de la base de datos en docker-compos.yml debe coincidir con la del archivo .env de Laravel
MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=laravel
MYSQL_USER=admin
MYSQL_PASSWORD=123456
```

## Pasos para desplegar

### 1. Clonar el repositorio

```bash
git clone https://github.com/jmcabo93/laravel_docker.git
cd project-root
```

### 2. Construir e iniciar los contenedores

Ejecuta el comando 

```bash
docker-compose up --build
```

### 3. Acceder a la aplicación

```
http://localhost
```

### 4. Laravel Breeze

Se utilizó **Laravel Breeze** para generar todo lo relacionado con el proceso de inicio de sesión y registro de usuarios.

### 5. Ejecutar comandos de Laravel

Para crear la base de datos ejecuta:
```bash
docker exec -it laravel-app php artisan migrate
```
Para poblar la base de datos con categorías y productos ejecuta:
```bash
docker exec -it laravel-app php artisan db:seed
```
Para cargar desde una API externa (https://fakestoreapi.com/products) productos ejecuta:
```bash
docker exec -it laravel-app php artisan cargar:productos
```

### 6. Documentación de la API usando Swagger

Puedes visualizar todos los endpoints disponibles y comprobar su funcionamiento en la url:

```
http://localhost/api/documentation
```

### 7. Seguridad

La API ha sido desarrollada utilizando **Laravel Sanctum** para la creación de tokens de autenticación. Todas las rutas están protegidas mediante **JWT (JSON Web Token)**, y es necesario incluir la directiva `Bearer <token>` en el encabezado de las solicitudes para acceder a los endpoints protegidos.

### 8. Detener los contenedores

Para detener los contenedores en ejecución, usa:

```bash
docker-compose down
```
