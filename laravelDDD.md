# Curso Laravel DDD
+ [Repositorio Comando Estructura + Boiler Plate](https://github.com/JJRuizDeveloper/Laravel-DDD-Boilerplate)
+ [DDD vs Hexagonal](https://onedrive.live.com/?redeem=aHR0cHM6Ly8xZHJ2Lm1zL2IvYy85QjUwRUMzRUU1Mzg0MzA2L0VVdXRONjVFeHp4R250UVJ4NFpicjNVQlBzZXFBTjFqZERaaHZXTlFtbVNoNlE%5FZT1mQU9QSGc&cid=9B50EC3EE5384306&id=9B50EC3EE5384306%21sae37ad4bc744463c9ed411c7865baf75&parId=root&o=OneUp)


## Crear un entorno de trabajo para Laravel 12
:::tip Nota
Tendremos un entorno con:
+ PHP (versión compatible con Laravel 12)
+ Composer
+ Node.js (para compilar assets)
+ MySQL (puerto 3308)
+ Nginx o Apache (puerto 8020)
+ Volúmenes persistentes para que los cambios no se pierdan
:::

1. Crear un directorio para el proyecto:
    ```bash
    mkdir laravel12-docker
    cd laravel12-docker
    ```
2. Crear un Dockerfile personalizado para el contenedor PHP que incluya Composer y las extensiones necesarias:
    ```bash
    mkdir -p docker/php
    ```
3. Crea un archivo Dockerfile en **laravel12-docker/docker/php**:
    ```Dockerfile
    FROM php:8.3-fpm

    # Instalar dependencias del sistema
    RUN apt-get update && apt-get install -y \
        git \
        curl \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        zip \
        unzip

    # Instalar Node.js y npm
    RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
        && apt-get install -y nodejs

    # Instalar extensiones PHP necesarias para Laravel
    RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

    # Instalar Composer globalmente
    COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

    # Establecer directorio de trabajo
    WORKDIR /var/www

    # Exponer puerto 9000 y ejecutar php-fpm
    EXPOSE 9000
    CMD ["php-fpm"]
    ```
2. Crear el **docker-compose.yml**:
```yml title="docker-compose.yml"
services:
  app:
    build:
      context: ./docker/php
    container_name: laravel12_app
    working_dir: /var/www
    volumes:
      - .:/var/www
    networks:
      - laravel_network
  webserver:
    image: nginx:latest
    container_name: laravel12_webserver
    ports:
      - "8020:80"
    volumes:
      - .:/var/www
      - ./nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app
    networks:
      - laravel_network
  db:
    image: mysql:8
    container_name: laravel12_db
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: laravel
      MYSQL_USER: laravel
      MYSQL_PASSWORD: secret
    ports:
      - "3308:3306"
    volumes:
      - db_data:/var/lib/mysql
    networks:
      - laravel_network
networks:
  laravel_network:
    driver: bridge
volumes:
  db_data:
```
3. Crear carpeta **nginx**:
    ```bash
    mkdir nginx
    ```
4. Crear archivo **default.conf**:
    ```bash
    sudo nano nginx/default.conf
    ```
    + Pegar el siguiente contenido:
        ```conf
        server {
            listen 80;
            server_name localhost;
            root /var/www/public;
            index index.php index.html index.htm;
            
            location / {
                try_files $uri $uri/ /index.php?$query_string;
            }

            location ~ \.php$ {
                include fastcgi_params;
                fastcgi_pass app:9000;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            }

            location ~ /\.ht {
                deny all;
            }
        }

        ```
        + Guarda con CTRL+X, luego Y, y presiona Enter.
5. Levantar el entorno:
    ```bash
    docker-compose up -d --build
    ```
6. Ingresar al contenedor de PHP en donde Composer ya está instalado:
    ```bash
    docker-compose exec -it app bash
    ```
7. Dentro del contenedor de PHP instalar el instalador global de Laravel:
    ```bash
    composer global require laravel/installer
    ```
    + Agregar ruta al PATH:
        ```bash
        echo 'export PATH="$PATH:$HOME/.composer/vendor/bin"' >> ~/.bashrc
        source ~/.bashrc
        ```
8. Dar permiso de lectura y escritura en el proyecto **laravel12-docker**:
    ```bash
    whoami  # para obtener el usuario
    sudo chown -R pbazo:pbazo /home/pbazo/www/laravel12-docker  # en donde pbazo es el usuario obtenido en el paso anterior | Verificar ruta en tu equipo
    ```

## Creación de un primer proyecto de Laravel 12
1. En **laravel12-docker/nginx/default.conf** indicar la ruta del proyecto:
    ```conf
    root /var/www/ddd-boilerplate/public;
    ```
2. Reiniciar contenedor nginx:
    ```bash
    docker-compose restart webserver
    ```
3. Ingresar al contenedor de PHP:
    ```bash
    docker-compose exec -it app bash
    ```
4. Crear proyecto Laravel:
    ```bash
    laravel new ddd-boilerplate
    ```
    + Which starter kit would you like to install?: None
    + Which database will your application use?: SQLite
    + Would you like to run npm install and npm run build?: Yes
6. Ejecutar dentro del contenedro para habilitar API:
    ```bash
    php artisan api:install     # Opción 1
    php artisan install:api     # Opción 2
    ```
    :::tip Nota
    En caso de que el camando artisan api no se ejecute, realizar la incorporación manualmente siguiendo los siguientes pasos:
    1. Crea manualmente el archivo de rutas API:
        ```bash
        touch routes/api.php
        ```
    2. Modificar archivo **bootstrap/app.php** para registrar el archivo de rutas API:
        ```php title="bootstrap/app.php"
        ->withRouting(
            web: __DIR__.'/../routes/web.php',
            api: __DIR__.'/../routes/api.php', // Agregar esta línea
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
        )        
        ```
    3. En el archivo **routes/api.php** recién escribir:
        ```php title="routes/api.php"
        <?php

        use Illuminate\Http\Request;
        use Illuminate\Support\Facades\Route;

        Route::get('/user', function (Request $request) {
            return $request->user();
        })->middleware('auth:sanctum');
        ```
    :::
5. (En caso de ser necesario) Dar permisos de lectura y escritura al proyecto dentro del contenedor de PHP:
    ```bash
    chown -R www-data:www-data /var/www/ddd-boilerplate
    chmod -R 775 /var/www/ddd-boilerplate
    ```    
6. Ver proyecto en:
    ```
    http://localhost:8020
    ```

## Crear estructura del proyecto para adecuarlo a DDD
1. Crear carpeta **ddd-boilerplate/src**.
2. Modificar **ddd-boilerplate/composer.json**:
    ```json
    {
        // ...
        "autoload": {
            "psr-4": {
                // ...
                "Src\\": "src/",
                // ...
            }
        // ...
    },
    }
    ```
3. Crear las siguientes carpetas para los **Bounded Context**:
    + 📁 ddd-boilerplate/src/admin
    + 📁 ddd-boilerplate/src/platform
    + 📁 ddd-boilerplate/src/landing
4. Crear las carpetas de los modulos de los **Bounded Context**:
    + 📁 ddd-boilerplate/src/admin/course
    + 📁 ddd-boilerplate/src/admin/user
    + 📁 ddd-boilerplate/src/platform/course
    + 📁 ddd-boilerplate/src/platform/user
    + 📁 ddd-boilerplate/src/platform/diploma
5. Crear las carpetas de capas de aplicación, dominio e infraestructura de los modulos de los **Bounded Context**:
    + 📁 ddd-boilerplate/src/platform/course/application
    + 📁 ddd-boilerplate/src/platform/course/domain
    + 📁 ddd-boilerplate/src/platform/course/infrastructure
    + 📁 ddd-boilerplate/src/platform/user/application
    + 📁 ddd-boilerplate/src/platform/user/domain
    + 📁 ddd-boilerplate/src/platform/user/infrastructure
    + 📁 ddd-boilerplate/src/platform/diploma/application
    + 📁 ddd-boilerplate/src/platform/diploma/domain
    + 📁 ddd-boilerplate/src/platform/diploma/infrastructure
5. Crear las carpetas de diferenciación en las capas de aplicación, dominio e infraestructura de los modulos de los **Bounded Context**:
    + 📁 ddd-boilerplate/src/platform/user/infrastructure/routes
    + 📁 ddd-boilerplate/src/platform/user/infrastructure/repository
    + 📁 ddd-boilerplate/src/platform/user/infrastructure/observers
    + 📁 ddd-boilerplate/src/platform/diploma/domain/entity
    + 📁 ddd-boilerplate/src/platform/diploma/domain/valueObjects

## Crear la estructura de capas de aplicación
1. Modificar **ddd-boilerplate/app/Models/User.php**:
    ```php title="ddd-boilerplate/app/Models/User.php"
    // ...
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Laravel\Sanctum\HasApiToken;    /* Agregar esta línea */
    // ...
    ```
2. Crear comando **ddd-boilerplate/app/Console/Commands/DDDStructure.php**:
    ```php title="ddd-boilerplate/app/Console/Commands/DDDStructure.php"
    <?php

    namespace App\Console\Commands;

    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\File;

    // @author: Juanjo Ruiz (Github: jjruizdeveloper | youtube: @gogodev | discord: juanjo.ruiz)

    class DDDStructure extends Command
    {
        /**
        * The name and signature of the console command.
        *
        * @var string
        */
        protected $signature = 'make:ddd {context : The bounded context, such as admin, lms or job_request} {entity : The entity to create the DDD structure, books for example}';

        /**
        * The console command description.
        *
        * @var string
        */
        protected $description = 'Creates DDD folder structure for the given entity';

        /**
        * Execute the console command.
        *
        * @return int
        */
        public function handle()
        {
            $uri = base_path('src/'. $this->argument('context') .'/'. $this->argument('entity'));
            $this->info('Creating structure...');

            File::makeDirectory($uri . '/domain', 0755, true, true);
            $this->info($uri . '/domain');

            File::makeDirectory($uri . '/domain/entities', 0755, true, true);
            $this->info($uri . '/domain/entities');

            File::makeDirectory($uri . '/domain/value_objects', 0755, true, true);
            $this->info($uri . '/domain/value_objects');

            File::makeDirectory($uri . '/domain/contracts', 0755, true, true);
            $this->info($uri . '/domain/contracts');

            File::makeDirectory($uri . '/application', 0755, true, true);
            $this->info($uri . '/application');

            File::makeDirectory($uri . '/infrastructure', 0755, true, true);
            $this->info($uri . '/infrastructure');

            File::makeDirectory($uri . '/infrastructure/controllers', 0755, true, true);
            $this->info($uri . '/infrastructure/controllers');

            File::makeDirectory($uri . '/infrastructure/routes', 0755, true, true);
            $this->info($uri . '/infrastructure/routes');

            File::makeDirectory($uri . '/infrastructure/validators', 0755, true, true);
            $this->info($uri . '/infrastructure/validators');

            File::makeDirectory($uri . '/infrastructure/repositories', 0755, true, true);
            $this->info($uri . '/infrastructure/repositories');

            File::makeDirectory($uri . '/infrastructure/listeners', 0755, true, true);
            $this->info($uri . '/infrastructure/listeners');

            File::makeDirectory($uri . '/infrastructure/events', 0755, true, true);
            $this->info($uri . '/infrastructure/events');

            // api.php
            $content = "<?php\n\n//use Src\\".$this->argument('context')."\\".$this->argument('entity')."\\infrastructure\controllers\ExampleGETController;\n\n// Simpele route example\n// Route::get('/', [ExampleGETController::class, 'index']);\n\n//Authenticathed route example\n// Route::middleware(['auth:sanctum','activitylog'])->get('/', [ExampleGETController::class, 'index']);";
            File::put($uri . '/infrastructure/routes/api.php', $content);
            $this->info('Routes entry point added in ' . $uri . 'infrastructure/routes/api.php' );

            // local api.php added to main api.php
            $content = "\nRoute::prefix('" . $this->argument('context') . "_" .$this->argument('entity') . "')->group(base_path('src/". $this->argument('context') . "/" .$this->argument('entity') ."/infrastructure/routes/api.php'));\n";
            File::append(base_path('routes/api.php'), $content);
            $this->info('Module routes linked in main routes directory.');

            // ExampleGETController.php
            $content = "<?php\n\nnamespace Src\\" . $this->argument('context')."\\".$this->argument('entity')."\\infrastructure\\controllers;\n\nuse App\\Http\\Controllers\\Controller;\n\nfinal class ExampleGETController extends Controller { \n\n public function index() { \n // TODO: DDD Controller content here \n }\n}";
            File::put($uri.'/infrastructure/controllers/ExampleGETController.php', $content);
            $this->info('Example controller added');

            // ExampleValidatorRequest.php
            $content = "<?php\n\nnamespace Src\\".$this->argument('context')."\\".$this->argument('entity')."\\infrastructure\\validators;\n\nuse Illuminate\Foundation\Http\FormRequest;\n\nclass ExampleValidatorRequest extends FormRequest\n{\npublic function authorize()\n{\nreturn true;\n}\n\npublic function rules()\n{\nreturn [\n'field' => 'nullable|max:255'\n];\n}\n\n}";
            File::put($uri.'/infrastructure/validators/ExampleValidatorRequest.php', $content);
            $this->info('Example validation request added');

            $this->info('Structure ' . $this->argument('entity') . ' DDD successfully created.');

            return Command::SUCCESS;
        }
    }
    ```
3. Ejecutar:
    ```bash
    php artisan make:ddd platform purchase
    ```
    + Cambios realizados:
        + Creación de la siguiete estructura de carpetas:
            + ddd-boilerplate/src/platform/purchase
                + 📁 application
                + 📁 domain
                    + 📁 contracts
                    + 📁 entities
                    + 📁 value_objects
                + 📁 infrastructure
                    + 📁 controllers
                        + 📄 ExampleGETController.php
                    + 📁 events
                    + 📁 listeners
                    + 📁 repositories
                    + 📁 routes
                        + 📄 api.php
                    + 📁 validators
                        + 📄 ExampleValidatorRequest.php
        + **ddd-boilerplate/routes/api.php** (modificación):
            ```php
            // ...
            Route::prefix('platform_purchase')->group(base_path('src/platform/purchase/infrastructure/routes/api.php'));
            ```
        + **ddd-boilerplate/src/platform/purchase/infrastructure/controllers/ExampleGETController.php** (creación):
            ```php
            <?php

            namespace Src\platform\purchase\infrastructure\controllers;

            use App\Http\Controllers\Controller;

            final class ExampleGETController extends Controller {
                public function index() { 
                    // TODO: DDD Controller content here 
                }
            }
            ```
        + **ddd-boilerplate/src/platform/purchase/infrastructure/routes/api.php** (creación):
            ```php
            <?php

            //use Src\platform\purchase\infrastructure\controllers\ExampleGETController;

            // Simpele route example
            // Route::get('/', [ExampleGETController::class, 'index']);

            //Authenticathed route example
            // Route::middleware(['auth:sanctum','activitylog'])->get('/', [ExampleGETController::class, 'index']);
            ```
        + **ddd-boilerplate/src/platform/purchase/infrastructure/validators/ExampleValidatorRequest.php** (creación):
            ```php
            <?php

            namespace Src\platform\purchase\infrastructure\validators;

            use Illuminate\Foundation\Http\FormRequest;

            class ExampleValidatorRequest extends FormRequest
            {
                public function authorize() {
                    return true;
                }

                public function rules() {
                    return [
                        'field' => 'nullable|max:255'
                    ];
                }
            }            
            ```
