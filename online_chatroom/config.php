<?php
    //$db = new SQLite3(getcwd().'/databases/chat.db');
    //LOCAL
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $db = new SQLite3(getcwd().'/databases/chat.db');
    //I6
?>