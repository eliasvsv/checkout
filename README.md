Clonar el proyecto

```bash
  git clone https://link-to-project
```

Ir al directorio del proyecto

```bash
  cd checkout
```

Instalar dependencias

```bash
  composer install
```

Configurar la app

```bash
  agregar la la configuracion de Paypal al archivo .env
```
Modificar el archivo de configuracion database.php  y dar acceso a una base de datos en blanco.

Ejecutar las migraciones
```bash
  php artisan migrate
  php artisan module:migrate // seleccionar opcion All
  php artisan db:seed
```

Ejecutar el proyecto
```bash
  php artisan serve
```

