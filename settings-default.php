<?php
error_reporting(E_ALL);
define('CHAT_NAME', 'CHAT_NAME');
define('SOCKET_ADDRESS', 'SOCKET_ADDRESS');
define('SOCKET_PORT', 'SOCKET_PORT');
define('DB_ADDRESS', 'DB_ADDRESS');
define('DB_PORT', 'DB_PORT');
define('DB_USER', 'DB_USER');
define('DB_PASSWORD', 'DB_PASSWORD');

//Anzahl der Benutzer die insgesamt am Server sein dürfen
define('MAX_CLIENT_GLOBAL', 'MAX_CLIENT');

//Anzhahl der Benutzer die maximal in einem neuen Channel sein dürfen
define('MAX_CLIENT_CHANNEL_DEFAULT', '123');

//Größte Anhal an Zeichen, die gesendet werden darf
define('MAX_SIZE', 'MAX_SIZE');
define('ALLOW_GUEST', 'ALLOW_GUEST');
define('GLOBAL_SALT', '');
define('SOCKET_MASTER_PASSWORT', 'sha1HashOfPwd');
?>