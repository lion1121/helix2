<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 11.10.2018
 * Time: 14:26
 */

namespace Support\analyze;


use Support\Db\Db;

trait GetLonLat
{
    public function getLonLat($table)
    {
        $db = Db::getConnection('traffics');
        $query = "select traffics.{$table}.ta, traffics.{$table}.lac, traffics.{$table}.cellid, traffics.{$table}.position, count(traffics.{$table}.ta), goodata.ukrcell.lon, goodata.ukrcell.lat,goodata.ukrcell.range from traffics.{$table}
                  left join goodata.ukrcell on  traffics.{$table}.lac = goodata.ukrcell.area and traffics.{$table}.cellid = goodata.ukrcell.cell group by traffics.{$table}.ta;";
        $prepare = $db->prepare($query);
        $prepare->execute();
        $result =  $prepare->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }
}