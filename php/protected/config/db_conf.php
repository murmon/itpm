<?php
if (IS_PRODUCTION) {    // CONFIG DB CONNECT HERE =)
    return array(
        'connectionString' => 'mysql:host=127.5.177.2;dbname=itpm',
        'username' => 'adminabt1wmM',
        'password' => 'T7Z78LpcJEiH',
    );
} else {
    return array(
        'connectionString' => 'mysql:host=localhost;dbname=itpm',
        'username' => 'root',

        'enableProfiling' => true,
        'enableParamLogging' => true
    );
}