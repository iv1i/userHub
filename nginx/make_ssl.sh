# Создайте папку для сертификатов
mkdir -p ssl

# Генерация приватного ключа и самоподписанного сертификата
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout ssl/localhost.key \
    -out ssl/localhost.crt \
    -subj "/C=RU/ST=Moscow/L=Moscow/O=Development/CN=localhost"