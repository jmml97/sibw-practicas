<?php
// Referencias:
// https://webdeasy.de/en/flexible-php-7-mysqli-database-class-download/?ref=morioh.com
// https://johnmorrisonline.com/simple-php-class-prepared-statements-mysqli/
class Database
{

    private $host, $database, $username, $password, $connection;
    private $port = 3306;

    public function __construct($host, $username, $password, $database, $port = 3306, $autoconnect = true)
    {
        $this->host = $host;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;

        if ($autoconnect) {
            $this->open();
        }
    }

    public function open()
    {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
    }

    public function close()
    {
        $this->connection->close();
    }

    public function query($query)
    {
        return $this->connection->query($query);
    }

    public function insert($query)
    {
        if ($this->connection->query($query) === true) {
            return true;
        } else {
            throw new Exception("Error in the insert query: '{$query}'. '{$this->connection->error}'");
        }
    }

    public function delete($query)
    {
        if ($this->connection->query($query) === true) {
            return true;
        } else {
            throw new Exception("Error in the delete query: '{$query}'. '{$this->connection->error}'");
        }
    }

    public function insert_id()
    {
        return $this->connection->insert_id;
    }

    public function update($query)
    {
        if ($this->connection->query($query) === true) {
            return true;
        } else {
            throw new Exception("Error in the update query: '{$query}'. '{$this->connection->error}'");
        }
    }

    public function escape($string)
    {
        return $this->connection->escape_string($string);
    }

    public function fetchAll($query)
    {
        $result = $this->query($query);

        while ($row = $result->fetch_object()) {
            $results[] = $row;
        }

        return $results;
    }

    public function fetchOne($query)
    {
        $result = $this->query($query);

        return $result->fetch_object();
    }

    public function isEmpty($query)
    {
        $result = $this->query($query);
        return ($result->num_rows === 0);
    }
}
