<?php

class tools_photo {
    
    static function upload($object,$id) {
        $config = Zend_Registry::get('cnf');

        $allowed_ext = array("gif","jpg","jpeg");

        if (isset($_FILES["photo"]["tmp_name"]) && is_uploaded_file($_FILES["photo"]["tmp_name"])) {

            $object = $object."_photo";

            $receive_dir = $config->$object->dir.$id;

            if (!is_dir($receive_dir)) mkdir($receive_dir,0777,true);

            /************** REMOVE OLD FILES ******************/
            $oldfiles=scandir($receive_dir);

            foreach ($oldfiles as $oldfile) if ($oldfile!="." && $oldfile!="..") {
                $ename = explode_filename($oldfile);
                $ename["ext"] = mb_strtolower($ename["ext"]);
                if (in_array($ename["ext"],$allowed_ext)) unlink("$receive_dir/$oldfile");
            }
            /*****************************************************/

            $ename = explode_filename($_FILES["photo"]["name"]);

            $tmp_filename = "temp.{$ename['ext']}";

            move_uploaded_file($_FILES["photo"]["tmp_name"],"$receive_dir/$tmp_filename");

            foreach (explode(",","big,medium,small,micro") as $size) {

                $filename = mb_strtolower("{$size}_{$id}.{$ename['ext']}");

                self::create_thumb(
                    $config->$object->$size->width,
                    $config->$object->$size->height,
                    "$receive_dir/$tmp_filename",
                    "$receive_dir/$filename",
                    $config->$object->$size->quality,
                    $config->$object->$size->cut,
                    $config->$object->$size->border,
                    $config->$object->$size->up_cut
                );
            }

            unlink("$receive_dir/$tmp_filename");

        }

    }
    
    
    
    /**
     * @desc �?зменяет размер изображения с сохранением пропорций.
     * @param type $max_w  Ширина, создаваемого изображения
     * @param type $max_h Высота, создаваемого изображения
     * @param type $img_in   Полный путь до исходного изображения
     * @param type $img_out   Полный путь до выходного изображения
     * @param type $quality   качество JPEG  0-100
     * @param type $cut   Подрезать ли края если исходная пропорция не совпадает с конечной
     * @param type $border    Вставлять ли  Рамку, путь до файла с рамкой указан в самом начале
     * @param type $crop_data    -   Массив данных для указания координат обрезания изображения в %, точка [ x ] [ y ] (точка старта, левый верхний угол)   [w] [h] - ширина и высота в %
     * @param type $error  -  текст ошибки в случае неудачи
     * @param type $force_size - принудительно делать превью указанного в $max_w, $max_h размера
     * @return boolean
     * @throws Exception
     */
    static function create_thumb($max_w, $max_h, $img_in, $img_out, $quality=80, $cut=false, $border=false, $crop_data=false, &$error = "", $force_size=false) {

        $border_im = $_SERVER["DOCUMENT_ROOT"]."/public/images/border.png";
        $error = "";

        try {

            if (!file_exists($img_in))
                throw new Exception('Нет исходного файла изображения');

            $img_prop = array();

            list($img_prop["width"],$img_prop["height"],$img_prop["type"],$img_prop["web_str"])=getimagesize($img_in);

            if (!$img_prop["width"] || !$img_prop["height"])
                throw new Exception('Не смогли получить размеры изображения '.$img_in);
            
            $src_img_prop = $img_prop;

            $cut_x=0;
            $cut_y=0;

            if ($force_size) {
                $new_size["width"] = $max_w;
                $new_size["height"] = $max_h;
            }else {
                if ($img_prop["width"]>$max_w || $img_prop["height"]>$max_h) {
                    $need_prop=$max_w/$max_h;

                    $orig_prop=$img_prop["width"]/$img_prop["height"];

                    $new_size = array();

                    $new_size["width"] = $max_w;
                    $new_size["height"] = $max_h;

                    if (!$cut) {
                        if ($need_prop > $orig_prop)
                            $new_size["width"] = $max_h * $orig_prop;
                        else $new_size["height"] = $max_w / $orig_prop;
                    }else {
                        if ($need_prop > $orig_prop) {
                                $need_height = $img_prop["width"] / $need_prop;
                                $razn = $img_prop["height"] - $need_height;
                                $cut_y = round($razn / 2);
                                $img_prop["height"]=$img_prop["height"]-$razn;
                        }else {
                                $need_width = $img_prop["height"] * $need_prop;
                                $razn = $img_prop["width"] - $need_width;
                                $cut_x = round($razn / 2);
                                $img_prop["width"]=$img_prop["width"]-$razn;
                        };
                    };

                }else {
                    $new_size["width"] = $img_prop["width"];
                    $new_size["height"]=$img_prop["height"];
                };
            }

            $new_image=imagecreatetruecolor($new_size["width"],$new_size["height"]);

            switch ($img_prop["type"]) {
                case 1:
                    $old_image=imagecreatefromgif($img_in);
                    break;
                case 2:
                    $old_image=imagecreatefromjpeg($img_in);
                    break;
                case 3:
                    $old_image=imagecreatefrompng($img_in);
                    imagesavealpha($new_image, true);
                    $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
                    imagecolortransparent($new_image, $color);
                    imagefill($new_image, 0, 0, $color);                    
                    break;

                default:
                    $old_image =  imagecreatefromstring(file_get_contents($img_in));
                    break;
            };

            if (!$old_image) throw new Exception('Загруженный файл не является корректным изображением '.$img_in);

            if ($cut && is_array($crop_data)) {

                $src_x = round($src_img_prop['width']/100*$crop_data['x']);
                $src_y = round($src_img_prop['height']/100*$crop_data['y']);
                
                if ($new_size["width"]<$new_size["height"]) {
                    $src_w = round($src_img_prop['width']/100*$crop_data['w']);
                    //$src_h = round($src_img_prop['height']/100*$crop_data['h']);
                    $src_h = $src_w * ($new_size["width"]/$new_size["height"]);
                }else {
                    
                    $src_h = round($src_img_prop['height']/100*$crop_data['h']);
                    
                    $src_w = $src_h * ($new_size["width"]/$new_size["height"]);
                    
                }

                if (!imagecopyresampled(
                        $new_image, //$dst_image
                        $old_image, //$src_image
                        0, //$dst_x
                        0, //$dst_y
                        $src_x, //$src_x
                        $src_y, //$src_y
                        $new_size["width"], //$dst_w
                        $new_size["height"], //$dst_h
                        $src_w, //$src_w
                        $src_h //$src_h
                    )) throw new Exception('Невозможно привести изображение к корректным размерам, попробуйте загрузить другое изображение');

            }else {
                if (!imagecopyresampled(
                        $new_image,
                        $old_image,
                        0,
                        0,
                        $cut_x,
                        $cut_y,
                        $new_size["width"],
                        $new_size["height"],
                        $img_prop["width"],
                        $img_prop["height"]
                    ))
                        throw new Exception('Невозможно привести изображение к корректным размерам, попробуйте загрузить другое изображение');

            }

            if ($border) {
                $border_data = getimagesize($border_im);
                $border_res = imagecreatefrompng($border_im);

                if (!imagecopyresampled($new_image,$border_res,0,0,0,0,$new_size["width"],$new_size["height"],$border_data[0],$border_data[1]))
                    throw new Exception('Невозможно привести изображение к корректным размерам, попробуйте загрузить другое изображение');
            }

            $tmp_arr = explode('.',$img_out);

            $out_ext = mb_strtolower(array_pop($tmp_arr));


            switch (true) {
                case ($out_ext=='gif'):
                    imagegif($new_image,$img_out);
                    break;

                case (in_array($out_ext,array('jpg','jpeg'))):
                    imagejpeg($new_image,$img_out,$quality);
                    break;

                case ($out_ext=='png'):
                    $result = imagepng($new_image,$img_out);
                    break;

                default:
                    throw new Exception('Неизвестный формат изображения! '.$img_out);
                    break;
            }



            return true;
        }catch (Exception $e) {
            $error = $e->getMessage();
            return false;
        }
    }
    
    
    
    static function getPhoto($size="medium",$object="student",$id=0,$gender="U") {
        $config = Zend_Registry::get('cnf');
        $object = $object."_photo";

        if (!isset($config->$object)) {
            errorReport('При попытке получить фото произошла ошибка: была произведена попытка обратиться к несуществующему свойству конфигурации '.$config->$object,get_defined_vars());
            return '';
        }

        $photo_dir = "{$config->$object->dir}$id";
        $files = glob("$photo_dir/{$size}_{$id}.*");
        $photo = (count($files)>0 && $files[0]!="") ? str_replace($config->path->root,"/",$files[0]) : false;

        if ($photo===false) {
            $photo = ($config->$object->$size->nophoto instanceof Zend_Config)
                ? $config->$object->$size->nophoto->$gender
                : $config->$object->$size->nophoto;
        }

        return $photo;
    }


    static function delPhoto($object="student",$id=0) {
        $config = Zend_Registry::get('cnf');
        $object = $object."_photo";

        if (!isset($config->$object)) {
            errorReport('При попытке удалить фото произошла ошибка: была произведена попытка обратиться к несуществующему свойству конфигурации '.$config->$object,get_defined_vars());
            return '';
        }

        $photo_dir = "{$config->$object->dir}/$id";
        $files = glob("$photo_dir/*.*");
        if (sizeof($files)>0) foreach ($files as $file) unlink($file);
        rmdir($photo_dir);
    }
    
}
