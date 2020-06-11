<?php

class tools_files {

    static function copy_dir($src, $dst) {
        try {
            $dir = opendir($src);

            if (!$dir)
                throw new Exception('не могу открыть папку ' . $dir);

            if (is_dir($dst)) {
                self::remove_dir($dst);
            } else if (!mkdir($dst, 0755, true))
                throw new Exception('не могу создать папку ' . $dst);

            while (false !== ( $file = readdir($dir))) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if (is_dir($src . '/' . $file)) {

                        if (!self::copy_dir($src . '/' . $file, $dst . '/' . $file))
                            throw new Exception('не могу скопировать папку ' . $dst . '/' . $file);
                    } else {
                        if (!copy($src . '/' . $file, $dst . '/' . $file))
                            throw new Exception('не могу скопировать файл ' . $dst . '/' . $file);
                    }
                }
            }

            closedir($dir);

            return true;
        } catch (Exception $e) {
            errorReport($e, get_defined_vars());
            return false;
        }
    }

    static function remove_dir($dir) {

        if (!is_dir($dir) || is_link($dir))
            return unlink($dir);
        
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..')
                continue;
            if (!tools_files::remove_dir($dir . '/' . $file)) {
                chmod($dir . '/' . $file, 0777);
                if (!tools_files::remove_dir($dir . '/' . $file))
                    return false;
            };
        }
        return rmdir($dir);
    }

}
