<?php

namespace Support\search;


use PDO;
use Support\Db\Db;

trait GetDbs
{
    /**
     * @return array
     */
    public function getDbs()
    {
        $db = Db::getConnection('dbs');
        $prepare = $db->prepare('
            SELECT * FROM dbs.dbs
        ');
        $prepare->execute();
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}