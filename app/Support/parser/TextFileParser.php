<?php


namespace Support\parser;



use Support\Db\Db;


class TextFileParser extends FileParser implements IParse
{
    public function parse($file)
    {
        //Remove first service lines from text file
        $file = array_slice($file, 6);

        foreach ($file as $key => $value) {
            if (strstr(iconv("CP1251", "UTF-8", $value), '======================СТАТИСТИКА======================') !== false) {
                break;
            } else {
                $data[] = iconv("CP1251", "UTF-8", $value);
            }
        }

        foreach ($data as $line) {
            $line = str_replace(array("\n", "\r", "\t"), '|', $line);
            $row[] = array_slice(explode('|', $line), 0, -2);
        }
        return $row;

    }



}