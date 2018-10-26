<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 18.10.2018
 * Time: 11:40
 */

namespace Support\XlsWriter;


use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class XlsWriter implements IWrite
{
    protected $fileName;
    public $fileDirectory;

    public function __construct()
    {
        $this->fileName = 'result_';
        $this->fileDirectory = ROOT . '/public/files/output/';
    }


    public function write()
    {
        $this->fileName = $this->fileName . \date("Y-m-d");
        $spreadsheet = new Spreadsheet();  /*----Spreadsheet object-----*/
        $Excel_writer = new Xls($spreadsheet);  /*----- Excel (Xls) Object*/
        $spreadsheet->setActiveSheetIndex(0);
        $activeSheet = $spreadsheet->getActiveSheet();
        $activeSheet->setCellValue('A1', 'New file content')->getStyle('A1')->getFont()->setBold(true);
        $Excel_writer->save(ROOT . DIRECTORY_SEPARATOR . 'public/files/output/' . $this->fileName . '.xls');
    }


}