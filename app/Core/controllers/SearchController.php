<?php

namespace Core\controllers;

use Complex\Exception;
use Core\Models\Helix;

use DOMDocument;
use Support\search\GetDbs;
use Support\search\GetTableById;
use Support\Twig\TwigView;
use Support\XlsWriter\XlsWriter;


class SearchController
{
    use GetDbs;
    use GetTableById;

    /**
     * Pass available data bases to searchpanel view
     * @return void
     */
    public function index()
    {
        $helixTables = $this->getDbs();
        $curentDate = \date("Y-m-d");
        try {
            $twig = new TwigView('/helix/searchpanel.php.twig', [
                'tables' => $helixTables,
                'date' => $curentDate
            ]);

            echo $twig->render();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    /**
     *Call from AJAX 'ajax/search'
     *Output search result depends on selected table
     */
    public function searchFormHandler()
    {
        $request = $_POST;
        $params = [];
        // Clear empty keys from parameters
        foreach ($request as $key => $value) {
            if ($value === '') {
                continue;
            } else {
                $params[$key] = $value;
            }
        }
        if ($params['tableid'] !== '0') {
            // Table was selected (retrieve from certain table)
            $tableId = $params['tableid'];
        } else {
            // Table wasn't selected (retrieve from all tables)
            $tableId = null;
        }
        unset($params['tableid']);
        $helix = new Helix();
        //If table was selected
        if (!is_null($tableId)) {
            $tableName = $this->getTableById($tableId);
            $results = $helix->dbSelect($tableName[0], $params);
            echo json_encode($results);
        } else {
            $tables = $helix->selectTablesByParams($params);
            $results = $helix->dbSelect($tables, $params);
            echo json_encode($results);
        }
    }

    /**
     *Call from AJAX 'ajax/getTableFields'
     * Retrieve column names which are equals to searchform
     */
    public function getTableFields()
    {
        $request = $_POST;
        $helix = new Helix();
        if ($request['id'] !== '0') {
            $tableName = $helix->getTableFieldsById($request['id']);
            echo json_encode($tableName);
        }
        
    }

    function file_force_download($file) {
           if (file_exists($file)) {
//               header('Content-Description: File Transfer');
//               header('Content-Type: application/octet-stream');
//               header('Content-Disposition: attachment; filename="' . basename($file) . '"');

               header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
               header('Content-Disposition: attachment; filename="' . $file . '"');  //File name extension was wrong
               header("Expires: 0");
               header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
               header("Cache-Control: private",false);
               readfile($file);
               exit();
           }
    }

    public function downloadXlsResult()
    {
        if (isset($_POST['download'])) {
                $writer = new XlsWriter();
                $fileName =  $writer->write();
                $filepath = $writer->fileDirectory . $fileName;
                try{
                    if (file_exists($filepath)){
                        $this->file_force_download($filepath);
                    } else {
                        throw new \Exception('File Not Found!');
                    }
                } catch ( \Exception $e){
                    echo $e->getMessage();
                }
            }



    }

}