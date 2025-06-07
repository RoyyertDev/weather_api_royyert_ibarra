# WeatherAPI - API REST Laravel Senior

API para gestión de usuarios y consulta de datos climáticos usando WeatherAPI.  
Desarrollada por Royyert Ibarra como prueba técnica Senior Laravel.

---

## Tecnologías utilizadas

-   Laravel 12
-   Sanctum 4.1 para autenticación con tokens API
-   HTTP Client de Laravel para consumir WeatherAPI
-   MariaDB
-   PHPUnit para pruebas unitarias y de integración

---

## Funcionalidades

-   Registro y autenticación de usuarios mediante tokens con Laravel Sanctum.
-   Consulta del clima:
    -   Temperatura.
    -   Estado del tiempo.
    -   Viento.
    -   Humedad.
    -   Hora local de cualquier ciudad.
-   Almacenamiento del historial de búsquedas por usuario.
-   Gestión de ciudades favoritas: marcar y listar favoritas.
-   Seguridad con middleware de autenticación y manejo básico de errores.
-   Optimización con caching para consultas frecuentes (10 minutos).

---

## Instalación

1. Clonar el repositorio:
    ```bash
    git clone https://github.com/RoyyertDev/weather_api_royyert_ibarra.git
    ```
2. Acceder a la carpeta del proyecto:
    ```bash
    cd tu_repositorio/tu_proyecto
    ```
3. Instalar dependencias:
    ```bash
    composer install
    ```
4. Copiar el archivo `.env.example` a `.env`
    ```bash
    cp .env.example .env
    ```
5. Modificar los valores de la base de datos y la API de WeatherAPI <WEATHER_API_KEY>.
    ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=weather_api_royyert_ibarra
    DB_USERNAME=root
    DB_PASSWORD=
    WEATHER_API_KEY=la_clave_api_a_utilizar
    ```
6. Generar la clave de aplicación:
    ```bash
    php artisan key:generate
    ```
7. Ejecutar la migración de la base de datos en versiones actuales 10+ te permite crear la base de datos sin tener que crearla manualmente antes de ejecutar las migraciones.
    ```bash
    php artisan migrate
    ```
8. Ejecutar el servidor local:
    ```bash
    php artisan serve
    ```

### Endpoints Disponibles

| Método | Ruta                         | Descripción                             | Autenticación |
| ------ | ---------------------------- | --------------------------------------- | ------------- |
| GET    | `/api/login`                 | Página de bienvenida y explicación      | No            |
| POST   | `/api/register`              | Registrar nuevo usuario                 | No            |
| POST   | `/api/login`                 | Login y obtención de token              | No            |
| POST   | `/api/logout`                | Cerrar sesión (invalidar token)         | Sí            |
| POST   | `/api/weather/`              | Consultar clima por ciudad              | Sí            |
| GET    | `/api/weather/city/{id}`     | Consultar ciudad buscada                | Sí            |
| GET    | `/api/weather/history`       | Consultar historial de búsquedas        | Sí            |
| GET    | `/api/weather/favorites`     | Listar ciudades favoritas               | Sí            |
| PATCH  | `/api/weather/favorite/{id}` | Marcar o desmarcar ciudad como favorita | Sí            |

---

## Ejemplo de uso

### 1. Registro de usuario

**POST** `/api/register`
**Body (JSON):**

```json
{
    "name": "Tu nombre",
    "email": "correo@ejemplo.com",
    "password": "******",
    "password_confirmation": "******"
}
```

**Respuesta Exitosa (Guardar token):**

```json
{
    "status": "success",
    "user": [
        "id": 1,
        "name": "Tu nombre",
        "email": "correo@ejemplo.com"
    ],
    "token": "tu_token_de_accesso",
}
```

### 2. Inicio de sesión

**POST** `/api/login`
**Body (JSON):**

```json
{
    "email": "correo@ejemplo.com",
    "password": "******"
}
```

**Respuesta Exitosa (Guardar token):**

```json
{
    "status": "success",
    "user": [
        "id": 1,
        "name": "Tu nombre",
        "email": "correo@ejemplo.com"
    ],
    "token": "tu_token_de_accesso",
}
```

### 3. Consultar clima

**POST** `/api/weather/`
**Headers:**

```
Authorization: Bearer tu_token_de_accesso
```

**Body (JSON):**

```json
{
    "body": "London"
}
```

**Respuesta Exitosa:**

```json
{
    "status": "success",
    "weather": {
        "temperature_c": 12.2,
        "condition": "Light rain shower",
        "wind_mph": 3.6,
        "humidity": 94,
        "localtime": "2025-06-07 07:15"
    },
    "saved": {
        "user_id": 3,
        "city": "London",
        "weather_data": {
            "temperature_c": 12.2,
            "condition": "Light rain shower",
            "wind_mph": 3.6,
            "humidity": 94,
            "localtime": "2025-06-07 07:15"
        },
        "is_favorite": false,
        "updated_at": "2025-06-07 06:17:18",
        "created_at": "2025-06-07 06:17:18",
        "id": 8
    }
}
```

### 4. Consultar ciudad buscada por ID

**GET** `/api/weather/city/{id}`
**Headers:**

```
Authorization: Bearer tu_token_de_accesso
```

**Respuesta Exitosa:**

```json
{
    "status": "success",
    "ciudad": {
        "id": 6,
        "user_id": 3,
        "city": "London",
        "weather_data": {
            "temperature_c": 10.3,
            "condition": "Light rain",
            "wind_mph": 2.2,
            "humidity": 94,
            "localtime": "2025-06-07 05:56"
        },
        "is_favorite": false,
        "created_at": "2025-06-07 04:58:01",
        "updated_at": "2025-06-07 04:58:01"
    }
}
```

### 5. Consultar historial de búsquedas

**GET** `/api/weather/history`
**Headers:**

```
Authorization: Bearer tu_token_de_accesso
```

**Respuesta Exitosa:**

```json
{
    "status": "success",
    "history": [
        {
            "id": 7,
            "user_id": 3,
            "city": "London",
            "weather_data": {
                "temperature_c": 11.1,
                "condition": "Light rain",
                "wind_mph": 5.4,
                "humidity": 100,
                "localtime": "2025-06-07 06:07"
            },
            "is_favorite": false,
            "created_at": "2025-06-07 05:05:37",
            "updated_at": "2025-06-07 05:05:37"
        },
        {
            "id": 8,
            "user_id": 3,
            "city": "London",
            "weather_data": {
                "temperature_c": 12.2,
                "condition": "Light rain shower",
                "wind_mph": 3.6,
                "humidity": 94,
                "localtime": "2025-06-07 07:15"
            },
            "is_favorite": false,
            "created_at": "2025-06-07 06:17:18",
            "updated_at": "2025-06-07 06:17:18"
        }
    ]
}
```

### 6. Consultar favoritos

**GET** `/api/weather/favorites`
**Headers:**

```
Authorization: Bearer tu_token_de_accesso
```

**Respuesta Exitosa:**

```json
{
    "status": "success",
    "favorites": [
        {
            "id": 7,
            "user_id": 3,
            "city": "London",
            "weather_data": {
                "temperature_c": 11.1,
                "condition": "Light rain",
                "wind_mph": 5.4,
                "humidity": 100,
                "localtime": "2025-06-07 06:07"
            },
            "is_favorite": true,
            "created_at": "2025-06-07 05:05:37",
            "updated_at": "2025-06-07 05:05:37"
        },
        {
            "id": 8,
            "user_id": 3,
            "city": "London",
            "weather_data": {
                "temperature_c": 12.2,
                "condition": "Light rain shower",
                "wind_mph": 3.6,
                "humidity": 94,
                "localtime": "2025-06-07 07:15"
            },
            "is_favorite": true,
            "created_at": "2025-06-07 06:17:18",
            "updated_at": "2025-06-07 06:17:18"
        }
    ]
}
```

### 7. Marcar ciudad como favorita

**PATCH** `/api/weather/favorite/{id}`
**Headers:**

```
Authorization: Bearer tu_token_de_accesso
```

**Respuesta Exitosa:**

```json
{
    "status": "success",
    "message": "Ciudad marcada como favorita.",
    "query": {
        "id": 6,
        "user_id": 3,
        "city": "London",
        "weather_data": {
            "temperature_c": 10.3,
            "condition": "Light rain",
            "wind_mph": 2.2,
            "humidity": 94,
            "localtime": "2025-06-07 05:56"
        },
        "is_favorite": true,
        "created_at": "2025-06-07 04:58:01",
        "updated_at": "2025-06-07 06:24:33"
    }
}
```
