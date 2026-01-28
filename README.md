# symfony 8-2503

## Stack technique
- Base de données mariadb
- Un serveur web Apache 2.4
- PHP 8.4
- composer pour PHP 




test pdo connection fichier index.php:
# $dsn ='mysql:host=db;dbname=db_symfony;port=3306;charset=utf8mb4;
# $pdo = new PDO($dsn, 'user_symfony', 'xxx'); 

installer symfony dans contenair
# docker exec -it apache2 bash
# voir: https://github.com/ARFP/DWWM_2503/blob/develop/symfony/README.md