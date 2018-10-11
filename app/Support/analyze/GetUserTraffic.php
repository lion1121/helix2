<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 07.10.2018
 * Time: 21:51
 */

namespace Support\analyze;
use Support\Db\Db;

trait GetUserTraffic
{


    /**Get tables that were created by user
     * @return array
     */
    public function getUserTraffic()
    {
        $tables = [];
        $db =  Db::getConnection('traffics');
        $query = 'SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema=\'traffics\'';
        $prepare = $db->prepare($query);
        $prepare->execute();
        $result = $prepare->fetchAll(\PDO::FETCH_ASSOC);

        // Get tables that were created by user
        foreach ($result as $table) {
            if (strpos( $table['TABLE_NAME'], $_SESSION['username']) !== false){
                $tables[] = $table['TABLE_NAME'];
            }
        }

        return $tables;

    }

}