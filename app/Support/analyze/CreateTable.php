<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 08.10.2018
 * Time: 10:28
 */

namespace Support\analyze;


use Support\Db\Db;

trait CreateTable
{
    /**
     * @param $tableName
     * @return mixed
     */
    public function createTable($tableName)
    {
        // Delete table if exists
        $db = Db::getConnection('traffics');
        $sql = "CREATE TABLE " . $tableName . "  LIKE traffics.traffic;";
        $prepare = $db->prepare($sql);
        try {
            $prepare->execute();
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
        return $tableName;
    }
}