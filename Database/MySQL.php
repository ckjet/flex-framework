<?php

namespace Hypilon\Database;

use Hypilon\Config\Config;
use PDO;
use PDOException;

class MySQL
{
    /** @var PDO $pdo */
    private $pdo;

    public function __construct()
    {
        $host = Config::get('db_host', '');
        $name = Config::get('db_name', '');
        $user = Config::get('db_user', '');
        $password = Config::get('db_password', '');
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        $dsn = "mysql:host={$host};dbname={$name};charset=utf8";
        try {
            $this->pdo = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            $this->getError($e->getMessage(), __FILE__, __LINE__);
        }
    }

    public function fetch($query, $params = [])
    {
        $row = $this->pdo->prepare($query);
        $row->execute($params);
        return $row->fetch();
    }

    public function fetchAll($query, $params = [])
    {
        $row = $this->pdo->prepare($query);
        $row->execute($params);
        return $row->fetchAll();
    }

    public function execute($query, $params = [])
    {
        $row = $this->pdo->prepare($query);
        $row->execute($params);
        return $this->pdo->lastInsertId();
    }

    public function count($query)
    {
        $row = $this->pdo->prepare($query);
        $row->execute();
        return (int)$row->rowCount();
    }

    private function getError($sql, $file = '', $line = '')
    {
        $host = Config::get('db_host', '');
        $name = Config::get('db_name', '');
        $user = Config::get('db_user', '');
        $password = Config::get('db_password', '');
        $out = "Date: " . date("[G:i:s] j.m.Y O") . "\n";
        $out .= "\n";
        $out .= "MySQL:\n";
        if ($this->pdo) {
            $out .= "Query:                 " . $sql . "\n";
            $out .= "mysql_errInfo():         " . print_R($this->pdo->errorInfo(), true) . "\n";
            $out .= "mysql_errCode():         " . $this->pdo->errorCode() . "\n";
            $out .= "mysql_server_info():   " . $this->pdo->server_info . "\n";
        } else {
            $out .= "Error:                 " . $sql . "\n";
        }
        $out .= "\n";
        $out .= "Script:\n";
        $out .= "SCRIPT_FILELINE:       " . $file . ":" . $line . "\n";
        $out .= "SCRIPT_FILENAME:       " . $_SERVER["SCRIPT_FILENAME"] . "\n";
        $out .= "\n";
        $out .= "Request:\n";
        $out .= "REQUEST_URI:           " . $_SERVER["REQUEST_URI"] . "\n";
        $out .= "QUERY_STRING:          " . $_SERVER["QUERY_STRING"] . "\n";
        if (isset($_SERVER['HTTP_REFERER'])) {
            $out .= "HTTP_REFERER:          " . $_SERVER["HTTP_REFERER"] . "\n";
        }
        $out .= "HTTP_HOST:             " . $_SERVER["HTTP_HOST"] . "\n";
        $out .= "REQUEST_METHOD:        " . $_SERVER["REQUEST_METHOD"] . "\n";
        $out .= "\n";
        $out .= "Server:\n";
        $out .= "SERVER_SOFTWARE:       " . $_SERVER["SERVER_SOFTWARE"] . "\n";
        $out .= "SERVER_PORT:           " . $_SERVER["SERVER_PORT"] . "\n";
        $out .= "SERVER_NAME:           " . $_SERVER["SERVER_NAME"] . "\n";
        $out .= "\n";
        $out .= "User:\n";
        $out .= "HTTP_USER_AGENT:       " . $_SERVER["HTTP_USER_AGENT"] . "\n";
        $out .= "REMOTE_ADDR:           " . $_SERVER["REMOTE_ADDR"] . "\n";
        $out .= "REMOTE_PORT:           " . $_SERVER["REMOTE_PORT"] . "\n";
        $headers = "From: " . $_SERVER["HTTP_HOST"] . " <1248783@gmail.com>" . "\n"
            . "X-Mailer: PHP/" . phpversion() . "\n"
            . "X-Priority: 3\n"
            . "Content-Type: text/plain; charset=utf-8\n";
        $mail = $out;
        $mail .= "db_host:           " . $host . "\n";
        $mail .= "db_database:           " . $name . "\n";
        $mail .= "db_user:           " . $user . "\n";
        $mail .= "db_password:           " . $password . "\n";
        //@mail('1248783@gmail.com', "MySQL Error!", $mail, $headers);
        if (Config::get('debug_mode')) {
            exit("<pre>\n\nMySQL Error!\n\n" . $out . "</pre>");
        }
        die();
    }
}