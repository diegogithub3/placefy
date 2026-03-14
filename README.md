# Proyecto Placefy

## Descripción

Este proyecto es una aplicación web construida con HTML, CSS, JavaScript, y PHP, que interactúa con una base de datos MySQL. El proyecto se ejecuta en un entorno local utilizando XAMPP. El código incluye archivos front-end (HTML, CSS, JS) y archivos back-end (PHP), junto con consultas para interactuar con la base de datos MySQL.

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado lo siguiente:

- [XAMPP](https://www.apachefriends.org/download.html)
- Un editor de código como [Visual Studio Code](https://code.visualstudio.com/)

## Instrucciones de Instalación

1. **Descargar el proyecto:**

   Descarga el archivo `.zip` del repositorio y descomprímelo en tu equipo local.

2. **Colocar el proyecto en la carpeta de XAMPP:**

   Mueve la carpeta descomprimida a la ruta `xampp/htdocs/` en tu máquina local.

3. **Configurar la base de datos:**

   - Inicia XAMPP y enciende los módulos **Apache** y **MySQL**.
   - Abre el navegador y ve a `http://localhost/phpmyadmin`.
   - Crea una nueva base de datos con el nombre `placefy_db`.
   - Importa el archivo SQL proporcionado en la carpeta `/sql` del proyecto para crear las tablas y datos necesarios. Para hacerlo:
     - Haz clic en la base de datos que creaste.
     - Ve a la pestaña **Importar**.
     - Selecciona el archivo SQL de la carpeta `/sql` y haz clic en **Continuar**.

4. **Configurar las credenciales de la base de datos:**

   - Abre el archivo PHP de configuración de la base de datos (generalmente en `/config` o en el archivo principal de conexión PHP).
   - Asegúrate de que las credenciales de la base de datos coincidan con las de tu servidor local en XAMPP:

     ```php
     $host = 'localhost';
     $user = 'root'; 
     $password = ''; 
     $dbname = 'placefy_db';
     ```

5. **Ejecutar la aplicación:**

   - Abre tu navegador web y ve a `http://localhost/placefy-js/views/index.html` para acceder a la aplicación.

## Estructura del Proyecto

- **/css**: Archivos de estilos CSS.
- **/js**: Archivos JavaScript.
- **/php**: Archivos PHP que manejan la lógica del servidor.
- **/sql**: Archivo de base de datos MySQL para importar.
- **/config**: Archivo de configuración de la base de datos.

## Notas Adicionales

- Asegúrate de que los módulos de **Apache** y **MySQL** en XAMPP estén activados cada vez que quieras ejecutar la aplicación.
- Si necesitas modificar la base de datos o las credenciales, recuerda actualizar el archivo de configuración de la conexión en el proyecto.
