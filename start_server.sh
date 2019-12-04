echo "Starting server on port 8080"

sudo systemctl start mariadb

php -S localhost:8080
