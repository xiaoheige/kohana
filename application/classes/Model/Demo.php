<?php defined('SYSPATH') or die('No direct script access.');

class Model_Demo extends Model_Database
{
    public function add($data)
    {
        $sql = 'insert into table (name, age) values(:name, :age)';
        $params = array(
            ':name' => $data['name'],
            ':age'  => $data['age'],
        );
        $query = DB::query(Database::INSERT, $sql);
        $query -> parameters($params);
        list($id, $affect) = $query->execute();
        return $id;
    }

    public function get($page = 1, $page_size = 20, $$id)
    {
        $offset = ($page - 1) * $page_size;
        $sql = "select id, name, age from table where id=:id order by id desc limit {$offset}, {$page_size}";
        $query = DB::query(Database::SELECT, $sql);
        $query->parameters(array(
            ':id'   => $id,
        ));
        return $query->execute()->as_array();
    }

    public function update($id, $age)
    {
        $sql = 'update table set age=:age where id=:id';
        $query = DB::query(Database::UPDATE, $sql);
        $query -> parameters(array(
            ':age'  => $age,
        ));
        return $query->execute();
    }

}

