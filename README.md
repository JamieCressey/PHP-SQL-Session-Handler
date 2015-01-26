#Deprecated, please use [PHP-MySQL-Session-Handler](https://github.com/jayc89/PHP-MySQL-Session-Handler)


PHP SQL Session Handler
=========

SQLSessionHandler is a PHP MySQL Session Handler written for use with [PHP-MySQL-PDO-Database-Class](https://github.com/jayc89/php-mysql-pdo-database-class)

Installation
--------------

```sh
composer install jayc89/php-sql-session-handler
```

Configuration
--------------

```sql
CREATE TABLE `session_handler` (
    `id` varchar(255) NOT NULL,
    `data` mediumtext NOT NULL,
    `timestamp` int(255) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

```sh
require 'vendor/autoload.php';
$db = new Db();
$session = new SQLSessionHandler();
$session->setDbConnection($db);

$session->setDbTable('session_handler');
session_set_save_handler(array($session, 'open'),
                         array($session, 'close'),
                         array($session, 'read'),
                         array($session, 'write'),
                         array($session, 'destroy'),
                         array($session, 'gc'));

register_shutdown_function('session_write_close');
session_start();

```


License
----

MIT


**Free Software, Hell Yeah!**


Credits
-----------

sprainr - https://github.com/sprain/PHP-MySQL-Session-Handler

