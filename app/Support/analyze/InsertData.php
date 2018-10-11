<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 08.10.2018
 * Time: 10:30
 */

namespace Support\analyze;


use Support\Db\Db;

trait InsertData
{
    public function insertTrafficToTable($data, $tableName)
    {
        $db = Db::getConnection('traffics');
        foreach ($data as $row) {
            $sql = "INSERT INTO $tableName  VALUES (null, '$row[0]','$row[1]','$row[2]','$row[3]','$row[4]','$row[5]','$row[6]','$row[7]','$row[8]','$row[9]','$row[10]');";
            $prepare = $db->prepare($sql);
            try {
                $prepare->execute();
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }
    }
}