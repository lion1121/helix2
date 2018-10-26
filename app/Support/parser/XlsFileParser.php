<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 10.10.2018
 * Time: 11:48
 */

namespace Support\parser;


use Support\parser\IParse;

class XlsFileParser extends FileParser implements IParse
{
    public function parse($file)
    {
        // TODO: Implement parse() method.
//        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
//        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xls");
        $fileName = explode('\\', $file);
        $fileName = $fileName[count($fileName) - 1];
        $fileName = explode('.', $fileName);
        $fileName = $fileName[0];
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly(["TB_" . $fileName]);
        $spreadsheet = $reader->load($file);
        $sheetData = $spreadsheet->getSheetByName("TB_" . $fileName)->toArray(null, false, true, false);
        $data = array_slice($sheetData, 2);

        foreach ($data as $row) {
            if($row[10] === null){
                continue;
            } else {
                $dataClear[] = $row;
            }
        }
        return $dataClear;
    }
}