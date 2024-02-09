# fp_pro

## Pasos para la subida al repositorio

1. Crear el repositorio en GitHub SIN README Y SIN .gitignore
   - Por ejemplo el repositorio está aquí: https://github.com/imnyima/fp_pro
2. Al crear el proyecto Symfony, se generan ambos archivos y el directorio .git. Por consola:
```console
cd /var/www/html/fp_pro
sudo rm -r .git
sudo git init
sudo git branch -m main
sudo remote add origin https://github.com/imnyima/fp_pro.git
sudo git status
sudo git add .
sudo git commit -m "Mensaje"
sudo git push -u origin main
```