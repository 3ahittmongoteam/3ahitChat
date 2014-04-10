<?php
error_reporting(E_ALL);
define('CHAT_NAME', 'CHAT_NAME');
define('SOCKET_ADDRESS', '192.168.7.69');
define('SOCKET_PORT', '17033');
define('DB_ADDRESS', '127.0.0.1');
define('DB_PORT', 'DB_PORT');
define('DB_USER', 'DB_USER');
define('DB_PASSWORD', 'DB_PASSWORD');

//Anzahl der Benutzer die insgesamt am Server sein dürfen
define('MAX_CLIENT_GLOBAL', '100');

//Anzhahl der Benutzer die maximal in einem neuen Channel sein dürfen
define('MAX_CLIENT_CHANNEL_DEFAULT', '100');

//Größte Anhal an Zeichen, die gesendet werden darf
define('MAX_SIZE', '200');
define('ALLOW_GUEST', FALSE);
define('GLOBAL_SALT', 'adad');
define('SOCKET_MASTER_PASSWORT', 'sha1HashOfPwd');
?>