<?php

namespace Core\controllers;

use Core\models\Analyze;
use Support\analyze\CreateTable;
use Support\analyze\DropTable;
use Support\parser\FileParser;
use Support\analyze\GetLonLat;
use Support\analyze\GetStatistic;
use Support\analyze\GetUserTraffic;
use Support\analyze\InsertData;
use Support\analyze\LoadTempFile;
use Support\parser\XlsFileParser;
use Support\Twig\TwigView;
use Support\XlsWriter\XlsWriter;

class AnalyzerController
{
    use GetUserTraffic, CreateTable, DropTable, InsertData, GetStatistic, LoadTempFile, GetLonLat;


    protected $username;
    protected $tableName;
    protected $MAX_TABLE_COUNT;
    protected $fileExtension;
    protected $tempDirPath;

    public function __construct()
    {
        $this->username = $_SESSION['username'];
        $this->MAX_TABLE_COUNT = 3;
        $this->tempDirPath = ROOT . DIRECTORY_SEPARATOR . 'public/' . 'files/' . 'uploads/' . 'excel';
    }

    public function loadTraffic()
    {
        $tables = $this->getUserTraffic();

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post' && $_FILES['traffic']['name'] !== '' && count($tables) < $this->MAX_TABLE_COUNT) {
            $this->fileExtension = explode('.', $_FILES['traffic']['name'])[1];

            if ($this->fileExtension === 'txt') {
                $contents = file($_FILES['traffic']['tmp_name']);
                $this->tableName = $this->username . '_' . explode('.', $_FILES['traffic']['name'])[0];
                $factory = new FileParser();
                $parser = $factory->createTextParser();
                $data = $parser->parse($contents);
                $this->dropTable($this->tableName);
                $this->createTable($this->tableName);
                $this->insertTrafficToTable($data, $this->tableName);
            } elseif ($this->fileExtension === 'xlsx' || $this->fileExtension === 'xls') {
                $this->tableName = $this->username . '_' . explode('.', $_FILES['traffic']['name'])[0];
                $file_tmp = $_FILES['traffic']['tmp_name'];
                $file_name = $this->load($file_tmp, $this->tempDirPath);
                $factory = new FileParser();
                $parser = $factory->createExcelParser();
                $data = $parser->parse($this->tempDirPath . DIRECTORY_SEPARATOR . $file_name);
                $this->dropTable($this->tableName);
                $this->createTable($this->tableName);
                $this->insertTrafficToTable($data, $this->tableName);
            }
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            $contents = "";
        }


        try {
            $twig = new TwigView('/helix/trafficanalyze.twig', [
                'tables' => !is_null($tables) ? $tables : ''
            ]);

            echo $twig->render();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }

    }

    /**
     *
     */
    public function deleteTrafficTable()
    {
        $tableName = htmlspecialchars($_POST['trafficTableName']);
        $this->dropTable($tableName);
    }

    public function analyze()
    {
        $statistic = $this->getStatistic($_POST['analyze']);

        echo json_encode($statistic);
    }

    public function renderAllConnection()
    {
        $table = htmlspecialchars($_POST['getallconnections']);
        $renderAllConnection = $this->getLonLat($table);
        echo json_encode($renderAllConnection);
    }

    public function getResultFromTrafficAnalyze()
    {
        $trafficTable = htmlspecialchars($_POST['getResultTraffic']);
        $result = new Analyze();
        $result = $result->getResult($trafficTable);
        echo json_encode($result);
    }




}