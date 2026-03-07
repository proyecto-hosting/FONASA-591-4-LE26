{ pkgs, ... }: {
  # Canal estable de paquetes de Nix
  channel = "stable-23.11"; 

  # Paquetes del sistema requeridos para nuestra arquitectura
  packages = [
    pkgs.php82
    pkgs.php82Packages.composer
    pkgs.nodejs_20
    pkgs.postgresql # Cliente psql para conectarnos a Cloud SQL
    pkgs.google-cloud-sdk # Herramienta gcloud para interactuar con tu proyecto en GCP
  ];

  # Variables de entorno globales para el workspace
  env = {};

  idx = {
    # Extensiones de VS Code clave para tu productividad en solitario
    extensions = [
      "bmewburn.vscode-intelephense-client" # El mejor autocompletado para PHP
      "bradlc.vscode-tailwindcss" # Fundamental para maquetar rápido las vistas
      "amiralizadeh9480.laravel-extra-intellisense" # Autocompletado de rutas y vistas Laravel
      "mtxr.sqltools" # Para explorar la base de datos sin salir del editor
      "mtxr.sqltools-driver-pg" # Driver de Postgres para SQLTools
    ];

    # Scripts automatizados al crear o arrancar el entorno
    workspace = {
      onCreate = {
        # Esto se ejecuta la primera vez que clonas el repo en IDX
        setup-laravel = "composer install && npm install && cp .env.example .env && php artisan key:generate";
      };
      onStart = {
        # Esto asegura que el SDK de Google Cloud esté listo para autenticarte
        gcp-auth-reminder = "echo 'Recuerda ejecutar: gcloud auth login'";
      };
    };

    # Configuración del servidor de pruebas integrado en IDX
    previews = {
      enable = true;
      previews = {
        web = {
          # Levanta el servidor local de Laravel exponiendo el puerto correcto
          command = ["php" "artisan" "serve" "--port" "$PORT" "--host" "0.0.0.0"];
          manager = "web";
        };
      };
    };
  };
}