<?php
namespace my_framework;
class Model{
    protected $table;
    protected static $pdo = null;
    protected $dbname;
    protected $host;
    protected $user;
    protected $password;

    function __construct(){
        $params = func_get_args();
        $this->table = strtolower(explode('\\', get_class($this))[1]);
        if(empty($params)){
            try{
                if (self::$pdo === null) {
                    $config = $this->loadConfig();
                    extract($config);
                    $pdo = new \PDO('mysql:dbname='. $dbname .';host='.$host, $user,$password);
                    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
                    self::$pdo = $pdo;
                }    
            }
            catch(PDOException $e){
                echo $e->getMessage();
            }
        }
        else{
            if (self::$pdo === null) {
                try{
                    $pdo = new \PDO('mysql:dbname='. $params[0]['dbname'] .';host='.$params[0]['host'], $params[0]['user'], $params[0]['password']);
                    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
                    self::$pdo = $pdo;
                }
                catch(PDOException $e){
                    echo $e->getMessage();
                }
            }
        }
    }

    function getDbname(){
        return $this->dbname;
    }

    function setDbname($name){
        $this->dbname = $name;
    }

    function getHost(){
        return $this->host;
    }

    function setHost($host){
        $this->host = $host;
    }

    function getUser(){
        return $this->user;
    }

    function setUser($user){
        $this->user = $user;
    }

    function setPassword($password){
        $this->password = $password;
    }

    function getPassword(){
        return $this->password;
    }

    function findOne($query, $params){
        $query = $this::$pdo->prepare($query);
        $query->execute($params);
        $result = $query->fetch();
        if(is_object($result)){
            return true;
        }
        else{
            return false;
        }
    }

    function getAll($table){
        $query = $this::$pdo->prepare('SELECT * FROM ' . $table);
        $query->execute();
        if(is_object($query)){
            while($row = $query->fetch()){
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    function loadConfig(){
        if(file_exists(ROOT . 'app/config.ini')){
            $config = parse_ini_file(ROOT . 'app/config.ini');
        }
        return $config;
    }

    function create($params){
        if(!is_array($params)){
            return false;
        }
        $i = 0;
        $query = 'INSERT INTO ' . $this->table . ' (';
        foreach ($params as $key => $value) {
            $i++;
            if($i != count($params)){
                $query .= $key . ', ';
            }
            else{
                $query .= $key . ') ';
            }
        }
        $i = 0;
        $query .= 'VALUES (';
        foreach ($params as $key => $value) {
            $i++;
            if($i != count($params)){
                $query .= ':' . $key . ', ';
            }
            else{
                $query .= ':' . $key . ')';
            }
        }
        $result = $this::$pdo->prepare($query);
        return $result->execute($params);
    }

    function read($params){
        if(!is_array($params)){
            return false;
        }
        $query = 'SELECT * FROM ' . $this->table . ' WHERE ' . $params[0] . ' =:' . $params[0];
        $result = $this::$pdo->prepare($query);
        $result->execute([$params[0] => $params[1]]);
        if(is_object($result)){
            while($row = $result->fetch()){
                $read[] = $row;
            }
            return $read;
        }
    }

    function update($params, $condition){
        $i = 0;

        if(!is_array($params)){
            return false;
        }
        $query = 'UPDATE ' . $this->table . ' SET ';
        foreach($params as $key => $value){
            $i++;
            if($i != count($params)){
                $query .= $key . '=:' . $key .', ';
            }
            else{
                $query .= $key . '=:' . $key .' ';
            }
        }
        $query .= ' WHERE ' . $condition[0] . '=:' . $condition[0] . '2';
        $params[$condition[0] . '2'] = $condition[1];
        $action = $this::$pdo->prepare($query);
        try{
            $action->execute($params);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }
    }

    function delete($params){
        $i = 0;

        if(!is_array($params)){
            return false;
        }
        $query = 'DELETE FROM ' . $this->table . ' WHERE ';
        foreach ($params as $key => $value) {
            $i++;
            if($i != count($params)){
                $query .= $key . '=:' . $key . ' AND ';
            }
            else{
                $query .= $key . '=:' . $key;
            }
        }
        $action = $this::$pdo->prepare($query);
        try{
            $action->execute($params);
        }
        catch(\PDOException $e){
            return $e->getMessage();
        }
        return true;
    }
}
?>