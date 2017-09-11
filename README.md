csv.local
=========

A Symfony project created on September 5, 2017, 6:41 pm.

# Run
download the project and run composer install
write in console: php ./bin/console server:start
put your file to in csv folder
then run the command php ./bin/console csv:import filename_with_extension
## Test
php ./vendor/bin/simple-phpunit

### Что бы я добавил
С помощью стратегии добавил бы возможность импорта разных файлов. Вывод ошибочных кортежей в лог. Графический интерфейс с возможностью загрузки
