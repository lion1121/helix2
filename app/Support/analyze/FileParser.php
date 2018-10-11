<?php

namespace Support\analyze;

use Support\Db\Db;

class FileParser
{


    public function createTextParser()
    { 
        return new TextFileParser();
    }

    public function createExcelParser()
    {
        return new XlsFileParser();
    }
}
     