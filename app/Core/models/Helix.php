<?php

namespace Core\models;


use PDO;
use Support\Db\Db;

class Helix
{

    /**Get tables from DB where columns are equals to request parameters
     * @param $params
     * @return array
     */
    public function selectTablesByParams($params)
    {
        $db = Db::getConnection('helix');
        $dbs = [];

        foreach ($params as $key => $value) {
            $prepare = $db->prepare('
               SELECT DISTINCT table_name FROM information_schema.columns
                WHERE table_schema=\'helix\' AND column_name LIKE :column;
            ');

            $prepare->execute([
                'column' => '%' . $key . '%'
            ]);
            $dbs[$key] = $prepare->fetchAll(PDO::FETCH_NAMED);
        }


        $paramsCount = count($params);
        $tableCount = 0;
        $tables = [];

        foreach ($dbs as $item) {
            $arrs[] = array_column($item, 'table_name');
        }
        foreach ($arrs as $item) {
            foreach ($item as $value) {
                $tables[] = $value;
            }
        }
        $uniqueTables = array_unique($tables);
        foreach ($uniqueTables as $uniqtable) {
            foreach ($arrs as $arr) {
                if (in_array($uniqtable, $arr)) $tableCount++;
            }
            if ($tableCount === $paramsCount) {
                $ready[] = $uniqtable;
            }
            $tableCount = 0;
        }
        return $ready;
    }

    /**
     * Retrieve data from DB (query constructor)
     * @param null $tables
     * @param null $parameters
     * @return array
     */
    public function dbSelect($tables = null, $parameters = null): array
    {
        $db = Db::getConnection('helix');
        $result = [];
        foreach ($tables as $table) {
            $sql = "SELECT * FROM `{$table}` WHERE id IS NOT NULL ";
            foreach ($parameters as $parameter => $value) {
                $sql .= "AND $parameter = '$value' ";
            }
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result[$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $result;
    }


    /**
     * Get table fields from all tables where column values are equal to serachform
     * @param $id
     * @return array
     */
    public function getTableFieldsById($id)
    {
        if ($id !== '0') {
            $db = Db::getConnection('dbs');
            $prepare = $db->prepare('SELECT dbs.table FROM dbs WHERE id = :id');
            $prepare->execute([
                'id' => $id
            ]);
            $tableName = $prepare->fetchAll(PDO::FETCH_ASSOC);
            $db = Db::getConnection();
            $prepare = $db->prepare("
          SELECT COLUMN_NAME FROM information_schema.columns 
          WHERE table_schema='helix' AND table_name = :tname;");
            $prepare->execute([
                'tname' => $tableName[0]['table']
            ]);
            $fields = $prepare->fetchAll(PDO::FETCH_OBJ);
            return $fields;
        }

    }


}