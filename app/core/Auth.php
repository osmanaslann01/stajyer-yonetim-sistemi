<?php

class Auth
{

    public static function check()
    {
        return isset($_SESSION['kullanici']);
    }


    public static function user()
    {
        return $_SESSION['kullanici'] ?? null;
    }


    public static function logout()
    {
        session_destroy();

        header(
            "Location: index.php?url=login"
        );

        exit;
    }

}