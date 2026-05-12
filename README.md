# Dokerizacia 
Spustanie dokeru 
bash
docker compose up --build

overenie
http://localhost:8080


docker compose down
docker compose up --build
Teraz project je spusteny z kontejnerom ked budeme uz robit poriadne spolu z Laravel musime pridat
Dockerfile
'COPY --from=composer:2 /usr/bin/composer /usr/bin/composer'

'RUN composer install --no-dev --optimize-autoloader'