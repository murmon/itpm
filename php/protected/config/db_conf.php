<?php
if (IS_PRODUCTION) {    // CONFIG DB CONNECT HERE =)
    return array(
        'connectionString' => 'mysql:host=feoktist.mysql.ukraine.com.ua;dbname=feoktist_itp',
        'username' => 'feoktist_itp',
        'password' => 'zaygvh9k',
    );
} else {
    return array(
        'connectionString' => 'mysql:host=localhost;dbname=itpm',
        'username' => 'root',

        'enableProfiling' => true,
        'enableParamLogging' => true
    );
}