<?php
session_start();
error_reporting(0);

class Auth
{
    public $con = '';

    function __construct($db)
    {
        $this->con = $db;
    }

    public function login($email)
    {
        $query = "SELECT * FROM users WHERE email = '$email'";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function all()
    {
        $query = "SELECT * FROM users";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $list = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = $row;
        }
        return $list;
    }

    public function fetch($id)
    {
        $query = "SELECT * FROM users WHERE id = $id";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $results = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $row;
        }
        return $results;
    }

    public function add($data)
    {
        if (!empty($data) && is_array($data)) {
            $check_user = "SELECT * FROM users WHERE email = '" . $data['email'] . "'";
            $stmt_check = $this->con->prepare($check_user);
            $stmt_check->execute();
            $num = $stmt_check->rowCount();

            if ($num > 0) {
                return false;
            } else {
                foreach ($data as $key => $value) {
                    if (is_null($value) || $value == '')
                        unset($data[$key]);
                }
                $fields  = implode(", ", array_keys($data)); //fields for insert query
                $values   = implode("','", array_values($data)); //values for insert query
                $query = "INSERT INTO users ($fields) values('$values')";
                $stmt = $this->con->prepare($query);
                $stmt->execute();

                return true;
            }
        } else {
            return false;
        }
    }

    public function update($data, $id)
    {
        if (!empty($data) && is_array($data)) {
            $update_query = '';
            $tol_data = count($data);

            foreach ($data as $columns => $values) {
                if ($values == "" || $values == "null") //if value is nul then not add in string
                {
                    $tol_data--;
                } else {
                    if ($tol_data > 1) {
                        $update_query .= "$columns";
                        $update_query .= "=";
                        $update_query .= "'";
                        $update_query .= $values;
                        $update_query .= "',";
                        $tol_data--;
                    } else {
                        $update_query .= "$columns";
                        $update_query .= "=";
                        $update_query .= "'";
                        $update_query .= $values;
                        $update_query .= "'";
                    }
                }
            }
            //	update query
            $query = "Update users set $update_query where id='$id'";
            $stmt = $this->con->prepare($query);
            $stmt->execute();
        } else {
            return false;
        }
    }
}
