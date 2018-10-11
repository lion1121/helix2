<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 08.10.2018
 * Time: 10:28
 */

namespace Support\analyze;


use Support\Db\Db;

trait DropTable
{
    /**
     * @param $tableName
     */
    public function dropTable($tableName)
    {
        $db = Db::getConnection('traffics');
        $sql = "DROP TABLE IF EXISTS " . $tableName .";";
        $prepare = $db->prepare($sql);
        $prepare->execute();
    }
}