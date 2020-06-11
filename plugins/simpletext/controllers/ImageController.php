<?php

/**
 * Description of ImageController
 *
 * @author chenzya
 */
class Simpletext_ImageController extends SiteBaseController {

    function initModel() {
        $this->model = new simpleImageModel();
    }

    function editAction() {

        setlocale(LC_NUMERIC, 'C');

        $this->needAdminRights();

        $this->useAjaxView();

        $path_data = explode('/', $this->params['im']);

        $image = array_pop($path_data);

        $filename = explode_filename($image);

        $full_version = implode("/", $path_data) . '/full_' . $filename['name'] . '.' . $filename['ext'];

        $im = getimagesize($this->config->path->root . $this->params['im']);

        $this->view->prop = $im[0] / $im[1];

        $this->view->w = $im[0];
        $this->view->h = $im[1];

        $this->view->href = $this->params['href'];

        $im_data = $this->model->getData($this->params['im']);

        $full_im = '';

        switch (true) {

            case ($im_data['custom_full_version']):
                $full_im = $im_data['custom_full_version'];
                break;

            case (file_exists($this->config->path->root . $full_version)):
                $full_im = $full_version;
                break;

            default:
                $full_im = $this->params['im'];
                break;
        }

        $this->view->full_version = $full_im;

        $this->view->data = $this->model->getData($this->params['im']);

        $this->view->ext = mb_strtolower($filename['ext']);

        $this->renderTplToContent('image_edit_dialog.tpl');
    }

    function uploadAction() {
        $this->useAjaxView();

        $this->needAdminRights();

        try {
            $error = '';
            $im = '';

            if (is_uploaded_file($_FILES['image']['tmp_name'])) {

                $tmp_dir = $this->config->path->root . 'temp';

                if (!is_dir($tmp_dir))
                    if (!mkdir($tmp_dir, 0777, true))
                        throw new Exception('не могу создать временную папку');

                $old_file_ext = mb_strtolower(array_pop(explode('.', $this->params['old_im'])));

                $name = explode_filename($_FILES['image']['name']);

                $name['ext'] = mb_strtolower($name['ext']);
/*
                if ($name['ext'] != $old_file_ext)
                    throw new Exception('Некорректный формат изображения! �?зображения должно быть формата ' . $old_file_ext);
*/                
                $old_im_size = getimagesize($this->config->path->root . $this->params['old_im']);

                $new_im_size = getimagesize($_FILES['image']['tmp_name']);
/*                    
                if ($old_file_ext == 'png') {
                    if ($old_im_size[0] <> $new_im_size[0] || $old_im_size[1] <> $new_im_size[1])
                        throw new Exception("При обновлении изображений типа PNG, размер загружаемого изображения должен в точности совпадать с исходным ({$old_im_size[0]}px X {$old_im_size[1]}px)");
                }
*/

                $filename = gen_filename() . '.' . $name['ext'];
                
                $im = '/temp/' . $filename;
                
                if ($old_im_size[0] && $old_im_size[1]) {
/*                    
                    if ((int)$this->params['max_width']<$old_im_size[0]) {
                        $old_im_size[0] = $this->params['max_width'];
                        $old_im_size[1] = floor($this->params['max_width']/($new_im_size[0]/$new_im_size[1]));
                    }
 * 
 */
                }else {
                    
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $tmp_dir . '/' . $filename))
                        throw new Exception('Ошибка загрузки файла');
                }
                
                if ($this->params['max_width']) {
                    if ($new_im_size[0]>$this->params['max_width']) {
                        $w = $new_im_size[0];
                        $h = $new_im_size[1];
                        
                        $old_im_size[0] = $this->params['max_width'];
                        $old_im_size[1] = floor($this->params['max_width']/($w/$h));
                        
                    }else $old_im_size = $new_im_size;
                }
                
                
                $cut = ($this->params['cut']) ? true : false;
                
                
                move_uploaded_file($_FILES['image']['tmp_name'], $tmp_dir . '/source_' . $filename);
                
                
                if (!tools_images::thumb([
                    'width'     => $old_im_size[0],
                    'height'    => $old_im_size[1],
                    'src'       => $tmp_dir . '/source_' . $filename,
                    'dst'       => $tmp_dir . '/' . $filename,
                    'quality'   => 90,
                    'cut'       => $cut
                    
                ],$error)) throw new Exception($error);
                
                
            }else $error = "Нет загруженного файла";
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $this->view->content = Zend_Json::encode(array(
            'error' => $error,
            'im'    => $im,
            'width' => $old_im_size[0],
            'height' => $old_im_size[1]
        ));
    }

    function saveAction() {
        $this->needAdminRights();

        $this->useAjaxView();

        try {

            if ($this->params['crop_data']['w'] || $this->params['new_im'] != '') {

                $size = getimagesize($this->config->path->root . $this->params['image']);

                $out = $this->config->path->root . $this->params['image'];

                if ($this->params['new_im']) {

                    $arr = explode("/",$this->params['new_im']);
                    $arr[sizeof($arr)-1] = 'source_'.$arr[sizeof($arr)-1];
                    
                    $source = implode('/', $arr);
                    
                    $in = $this->config->path->root . $source;

                    $path = explode('/', $this->config->path->root . $this->params['image']);

                    $filename = array_pop($path);
                    /*
                      if (file_exists(implode('/',$path).'/full_'.$filename))
                      unlink(implode('/',$path).'/full_'.$filename);

                      copy($this->config->path->root.$this->params['new_im'],implode('/',$path).'/full_'.$filename);
                     * 
                     */

                    $full = $this->config->path->root . $this->params['full_im'];
                    if (file_exists($full))
                        unlink($full);

                    copy($this->config->path->root . $this->params['new_im'], $full);
                }else {
                    $im = $this->config->path->root . $this->params['image'];

                    $path = explode('/', $im);

                    $filename = array_pop($path);

                    //$full = implode('/',$path).'/full_'.$filename;
                    $full = $this->config->path->root . $this->params['full_im'];

                    $in = (file_exists($full)) ? $full : $im;
                }

                $crop_data = ($this->params['crop_data']['w'] != 'NaN' && ($this->params['crop_data']['w'] && $this->params['crop_data']['h'])) ? $this->params['crop_data'] : false;


                $fn = pathinfo($filename);

                //if (strtolower($fn['extension']) != 'png') {
                if (!tools_images::thumb([
                    'width'     => $size[0],
                    'height'    => $size[1],
                    'src'       => $in,
                    'dst'       => $out,
                    'quality'   => 90,
                    'cut'       => $this->params['cut'],
                    'crop_data' => $this->params['crop_data'],
                    'force_size'    => $this->params['force_size'],
                ],$error)) throw new Exception('Ошибка изменения размера изображения ' . $error);
                
                /*
                    if (!create_thumb(
                            $size[0], 
                            $size[1], 
                            $in, 
                            $out, 
                            90, 
                            true, 
                            false, 
                            $crop_data, 
                            $error, 
                            true
                    )) throw new Exception('Ошибка изменения размера изображения ' . $error);
                    /*
                }else {

                    $new_im_size = getimagesize($in);

                    if ($new_im_size[0] <> $size[0] || $new_im_size[1] <> $size[1])
                        throw new Exception("При обновлении изображений типа PNG, размер загружаемого изображения должен в точности совпадать с исходным ({$size[0]}px X {$size[1]}px)");

                    unlink($out);
                    copy($in, $out);
                }
                     * 
                     */
            }

            $this->model->updateLink($this->params['image'], $this->params['href']);
        } catch (Exception $e) {
            errorReport($e, get_defined_vars());
            $error = $e->getMessage();
        }

        if ($this->params['new_im']) {
            unlink($this->config->path->root . $this->params['new_im']);
            if (isset($source))
                unlink($source);
        }

        $this->view->content = Zend_Json::encode(array(
                    'error' => $error,
                    'img_url' => $this->params['image']
        ));
    }

    
    function cropAction() {
        $this->disableLayout();
        
        try {

            $im = base64_decode($this->params['f']);
            $tmp_dir = $this->config->path->root . 'temp';
            $ext = strtolower(pathinfo($im, PATHINFO_EXTENSION));
            $temp_name = tools_string::randString(12).'.'.$ext;
        
            if (!tools_photo::create_thumb(
                    $this->params['w'], 
                    60000, 
                    $this->config->path->root.$im, 
                    $tmp_dir . '/' . $temp_name, 
                    90, 
                    false, 
                    false, 
                    false, 
                    $error)
            ) throw new Exception($error);                
                
            header("Content-type: image/jpeg");
            echo file_get_contents($tmp_dir . '/' . $temp_name);
            unlink($tmp_dir . '/' . $temp_name);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

    }
}

