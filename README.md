<p align="center">
    <img src="https://ugb.edu.sv/wp-content/uploads/2023/06/UGB_LOGOTIPO_HORIZONTAL.png" width="650">
 </p>
 
# Docente
**Wiliam Alexis Montes Girón**

# Integrantes del equipo:

**Fredys Alejandro Hernandez Robles – SMSS060924**

**David Alfonso Alvarenga Bonilla – SMSS016424**

**Daniel Antonio Orellana Zelaya – SMSS086223** 

**Danery Alfredo Ascencio Saravia – SMSS057624**

**Samuel Alejandro Batres Caceres - SMSS057424**

**Andrea Yamileth Rodríguez Hernández – SMSS073124**


# QuickBite Express

**QuickBite Express es un sistema web de pedidos para restaurante desarrollado con Laravel, Blade, Tailwind CSS, JavaScript y MySQL. Su propósito es digitalizar y optimizar el proceso de atención y gestión de pedidos en restaurantes pequeños y medianos, permitiendo que los clientes consulten un menú digital, exploren productos organizados por categorías, agreguen artículos a un carrito de compras y realicen pedidos de forma más rápida y ordenada.**

**El sistema incorpora una interfaz moderna y adaptable a distintos dispositivos, con navegación intuitiva y componentes visuales diseñados para mejorar la experiencia del usuario. Además, contempla funcionalidades administrativas para la gestión de productos, categorías, usuarios y pedidos, apoyándose en una base de datos que permite almacenar y administrar la información necesaria para el funcionamiento de la aplicación.**

**QuickBite busca ofrecer una solución tecnológica accesible que mejore la experiencia de los clientes y facilite la administración interna de los pedidos dentro de un restaurante mediante una plataforma web moderna y escalable.**



# Instalación del Proyecto

*Requisitos Previos*

*Antes de ejecutar el proyecto, asegúrese de tener instalados los siguientes componentes:*


*PHP 8.2 o superior*

*Composer*

*Node.js y npm*

*MySQL Server*

*Git*

*Clonar el Repositorio*

**git clone https://github.com/davidbonilla1626-bot/proyecto-progra.git**

*cd restaurante1-app*

# Instalación de Dependencias

Instalar las dependencias de PHP:

*composer install*

Instalar las dependencias de JavaScript:

*npm install*

Configuración del Entorno

*Crear una copia del archivo de configuración:*

*cp .env.example .env*

*Generar la clave de la aplicación:*

*php artisan key:generate*

# Configurar los parámetros de conexión a la base de datos en el archivo .env:*

*DB_CONNECTION=mysql*

*DB_HOST=127.0.0.1*

*DB_PORT=3306*

*DB_DATABASE=restaurante1_db*

*DB_USERNAME=usuario*

*DB_PASSWORD=contraseña*

# Creación de la Base de Datos

Crear una base de datos vacía en MySQL o phpmyadmin con el nombre:

*CREATE DATABASE restaurante1_db;*

Ejecución de Migraciones y Seeders

Ejecutar las migraciones para crear la estructura de la base de datos:

*php artisan migrate*

Cargar los datos iniciales del sistema:

*php artisan db:seed*

*O bien:*

*php artisan migrate:fresh --seed*x    

# Almacenamiento de Archivos

Crear el enlace simbólico para el almacenamiento de imágenes:

*php artisan storage:link*

# Ejecución del Proyecto

Iniciar el servidor de Laravel:

*php artisan serve*

Compilar los recursos del frontend:

*npm run dev*

Finalmente, acceder al sistema desde:

*http://127.0.0.1:8000*

# Gestor de Base de Datos Utilizado:

*El proyecto utiliza MySQL como Sistema Gestor de Base de Datos (SGBD). MySQL fue seleccionado por su estabilidad, facilidad de integración con Laravel mediante Eloquent ORM, soporte para relaciones mediante llaves foráneas y amplio uso en aplicaciones web empresariales.*

*La base de datos almacena la información relacionada con usuarios, categorías de productos, productos, pedidos, detalles de pedidos y demás entidades necesarias para el funcionamiento del sistema de gestión del restaurante.*


<p align="center">
    <img src="https://raw.githubusercontent.com/davidbonilla1626-bot/proyecto-progra/main/restaurante1-app/public/images/quickbite1.png" width="150">
  </p>
