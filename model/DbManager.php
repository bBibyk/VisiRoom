<?php

const HOST = '127.0.0.1';
const PORT = '3306';
const DBNAME = 'visiboost';
const CHARSET = 'utf8';
const LOGIN = 'root';
const MDP = '';

class DbManager
{
    private static ?\PDO $cnx = null;

    public static function getConnexion()
    {
        return new PDO('mysql:host='. HOST.';port='.PORT.';dbname='.DBNAME.';charset='.CHARSET, LOGIN, MDP);
    }
}