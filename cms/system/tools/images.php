<?php

class tools_images {
    
    /*
     * @desc $params
     * @param width string
     * @param height string
     */
    
    static function thumb($params=[], &$error='') {
        
        $max_w = @$params['width'];
        $max_h = @$params['height'];
        
        $img_in = @$params['src'];
        $img_out = @$params['dst'];
        
        $quality = @$params['quality'];
        if (!$quality) $quality = 80;
        
        $cut = @$params['cut'];
        
        $border = @$params['border'];
        $crop_data = @$params['crop_data'];
        $force_size = @$params['force_size'];
        
        return tools_photo::create_thumb(
                $max_w, 
                $max_h, 
                $img_in, 
                $img_out, 
                $quality, 
                $cut, 
                $border, 
                $crop_data, 
                $error, 
                $force_size
        );
        
        
    }




    static function preview($path, $size="130x130", $watermark=false, $cut=true, $cutData = false) {
        
        if ($cutData ) {
            $crop_data = [];
            list($crop_data['x'],$crop_data['y'],$crop_data['w'],$crop_data['h']) = explode(',',$cutData);
        }else $crop_data = false;
        
        $path_s = $path;
        $path = explode('/',$path);
        $filename = array_pop($path);
        $id = array_pop($path);
        $root = Zend_Registry::get('cnf')->path->root;
        
        $dir = $root."public/thumb/$id";
        
        if (!is_dir($dir))
            mkdir($dir,0777,true);
        
        $thumb_im = $dir.'/'.$size.'_'.md5($path_s.$size.($watermark?'1':'0').($cut?'1':'0').print_r($cutData,1)).'_'.$filename;
        
        if (!file_exists($thumb_im)) {
            
            list($width, $height) = explode("x",$size);
            try {
                self::thumb([
                    'src'       => $root.mb_substr($path_s,1),
                    'dst'       => $thumb_im,
                    'width'     => $width,
                    'height'    => $height,
                    'cut'       => $cut,
                    'crop_data' => $crop_data
                ]);

                if ($watermark)
                    self::placeWatermark($thumb_im);
            }catch (Exception $e) {
                $thumb_im.='(err)';
            }
        }
        
        return str_replace($root, '/', $thumb_im);
                
    }
    
    
    static function placeWatermark($file, $size='') {
        
        $stamp = imagecreatefrompng(Zend_Registry::get('cnf')->path->root.'public/watermark.png');
        
        $img_prop = array();

        list($img_prop["width"],$img_prop["height"],$img_prop["type"],$img_prop["web_str"])=getimagesize($file);

        
        $new_image=imagecreatetruecolor($img_prop["width"],$img_prop["height"]);

        switch ($img_prop["type"]) {
            case 1:
                $im=imagecreatefromgif($file);
                break;
            case 2:
                $im=imagecreatefromjpeg($file);
                break;
            case 3:
                $im=imagecreatefrompng($file);
                imagesavealpha($new_image, true);
                $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
                imagecolortransparent($new_image, $color);
                imagefill($new_image, 0, 0, $color);                    
                break;

            default:
                $im =  imagecreatefromstring(file_get_contents($file));
                break;
        };

        if (!$im) throw new Exception('����������� ���� �� �������� ���������� ������������ '.$file);
        

        // ��������� ����� ��� ������ � ��������� ������/������ ������
        $marge_right = 0;
        $marge_bottom = 0;
        $sx = imagesx($stamp);
        $sy = imagesy($stamp);

        // ����������� ����������� ������ �� ���������� � ������� �������� ����
        // � ������ ���������� ��� ������� ���������������� ������. 
        imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

        switch ($img_prop["type"]) {
            case 1:
                imagegif($im, $file);
                break;
            case 2:
                imagejpeg($im, $file, 87);
                break;
            case 3:
                imagepng($im, $file);
                break;
        };
        
    }


    
}
