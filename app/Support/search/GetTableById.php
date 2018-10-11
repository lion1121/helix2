<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 23.08.2018
 * Time: 15:12
 */

namespace Support\search;


use PDO;
use Support\Db\Db;

trait GetTableById
{
    public function getTableById($id)
    {
        $db = Db::getConnection('dbs');
        $prepare = $db->prepare('SELECT dbs.table FROM dbs WHERE dbs.id = :tableid');
        $prepare->execute([
            'tableid' => $id
        ]);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}