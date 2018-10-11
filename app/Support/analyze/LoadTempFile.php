<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 10.10.2018
 * Time: 12:49
 */

namespace Support\analyze;


trait LoadTempFile
{
    public function load($tmp_file_name, $temp_path)
    {
        try {
            // basename() может предотвратить атаку на файловую систему;
            // может быть целесообразным дополнительно проверить имя файла
            $name = basename($_FILES["traffic"]["name"]);
            if (!move_uploaded_file($tmp_file_name, "$temp_path/$name")) {
                throw new \Exception('Файл не загружен!');
            };
            return $name;

        } catch (\Exception $e) {
            echo  ' Ошибка загрузки файла ' . $e->getMessage();
        }

    }
}