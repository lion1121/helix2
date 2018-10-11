<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 08.10.2018
 * Time: 15:45
 */

namespace Support\analyze;


use Support\Db\Db;

trait GetStatistic
{


    public function getStatistic($table)
    {
        $db = Db::getConnection('traffics');
        $query = "select {$table}.ta, {$table}.tb, {$table}.`type`, SUM({$table}.durability) as 'duration', count({$table}.tb) as 'count' from {$table} where {$table}.tb in (select {$table}.tb from {$table}) group by  ta, `type`  ;";
        $prepare = $db->prepare($query);
        $prepare->execute();
//        $result = ;
        $stat_array = [];
        $stat_array['statistic'] = array();

        $result = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }
}