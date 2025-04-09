# 🚀 Backend Laravel - Proyecto de Gestión de Tareas

Este es un proyecto backend desarrollado con Laravel para la gestión de tareas. A continuación se detallan los pasos para instalar, configurar y ejecutar el entorno de desarrollo.

---

## 📋 Requisitos Técnicos

- 🐘 **PHP** `>= 8.4.5` (Recomendado)
- 🌐 **Laravel Framework** `12.7.2`
- 📦 **Composer** `>= 2.8.7`
- 🛢 **Base de datos**: MySQL

---

## 🛠 Instalación y Configuración

### 1️⃣ Clonar el repositorio

```bash
git clone https://github.com/ronaldocl1997/backTareas.git
cd backTareas


2️⃣ Instalar dependencias
composer install

3️⃣ Copiar archivo de entorno y configurarlo
cp .env.example .env

Luego editar el archivo .env con tus credenciales de base de datos:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_de_datos
DB_USERNAME=usuario
DB_PASSWORD=contraseña

4️⃣ Generar clave de aplicación
php artisan key:generate

5️⃣ Ejecutar migraciones y seeders
php artisan migrate --seed

📁 Estructura de Carpetas (Resumen)
app/
├── Http/
│   └── Controllers/   # Controladores
├── Models/            # Modelos Eloquent
└── Services/          # Servicios personalizados

database/
├── migrations/        # Migraciones (esquemas de BD)
└── seeders/           # Datos iniciales

🚀 Levantar el proyecto
php artisan serve

🐞 Debugging
Ver logs de errores: storage/logs/laravel.log

📊 Comandos de Optimización
composer dump-autoload
php artisan optimize
php artisan view:cache

📫 Autor
Desarrollado por Ronaldo C.
GitHub: ronaldocl1997

📚 Documentación de la API
Puedes consultar la documentación completa de los endpoints en el siguiente enlace:

👉 https://documenter.getpostman.com/view/15102924/2sB2cX7fkm

