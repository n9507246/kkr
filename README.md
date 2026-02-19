
ssh admin-local@192.168.1.248 && cd ~/kkr
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'

sshfs admin-local@192.168.1.248:/home/admin-local/kkr ~/kkr


#-------------------------------------------
Установка докер для sail

curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
newgrp docker
docker --version
docker run hello-world

#-----------------------------------------
ssh генерация ключа

ssh-keygen -t ed25519 -C "email@example.com"
cat ~/.ssh/id_ed25519.pub



#-----------------------------------------------
установка проекта laravel sail

cp .env.example .env

#ОБЯЗАТЕЛЬНО МЕНЯЕМ ФАЙЛ ОКРУЖЕНИЯ ИНАЧЕ КОНТЕЙНЕРЫ ЗАПУСТЯТСЯ НЕ ПРАВИЛЬНО

#Установите зависимости Composer (без локального PHP)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev


