<?php

/**
 * @desc Smarty Autoloader
 * @global type $root
 * @param type $class
 * @return type
 */
function smartyAL($class) {
	global $root;
	$_class = strtolower($class);

	if (substr($_class, 0, 16) === 'smarty_internal_' || $_class == 'smarty_security') {
		$f_name = $root.'library/Smarty/sysplugins/'.$_class.'.php';
		if (file_exists($f_name)) {
			include_once($f_name);
			return true;
		}
	}

	return false;
}


/**
 * @desc My Plugin Autoloader
 * @global type $root
 * @param type $class
 * @return type boolean
 */
function myAL($class) {
	global $root;

        if (file_exists("{$class}.php")) {
            include "{$class}.php";
        
            if (class_exists($class)) return true;
        }
        
	$elems = explode("_",$class);
        
        

	if (sizeof($elems)!=2) return false;

	$plugin = strtolower($elems[0]);
	$controller = $elems[1];

	$root_plugin = $root."plugins/{$plugin}/controllers/{$controller}.php";

	if (file_exists($root_plugin)) {
		include_once($root_plugin);
		return true;
	}

        $site_plugin = $root."site/plugins/{$plugin}/controllers/{$controller}.php";

	if (file_exists($site_plugin)) {
		include_once($site_plugin);
		return true;
	}
    
        $tool = $root."cms/system/tools/{$controller}.php";
    
	if (file_exists($tool)) {
            include_once($tool);
            return true;
	}
    

	return false;
}

/**
 * @desc Генератор случайного имени файла
 */
function gen_filename ( $prefix='' ) {
	return $prefix.tools_string::randString(10);
};

/**
 * @desc Возвращает текстовое описание размера
 * @param integer $size — размер в байтах
 */
function getFileSizeString( $size ) {
    return tools_string::getFileSizeString( $size );
}


/**
 * @desc Преобразует арабское число в римское
 * @param integer $number — арабское число
 */
function number_to_roman($value) {
    return tools_string::number_to_roman($value);
}


function getmicrotime(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

function errorReport($e,$vars=false) {
    return tools_debug::errorReport($e,$vars);
}

/**
 * @desc Перевод объекта в массив
 **/
function getAssocArrayFromObj($obj){
    return tools_arrTools::getAssocArrayFromObj($obj);
}

/**
 * Возвращает имя и расширение файла
 * return array(name,ext)
 */
function explode_filename( $filename ) {
    return tools_string::explode_filename( $filename );
};


function translit( $text ) {
    return tools_string::translit($text);
}

function trimStr($str) {
    return tools_string::trimStr($str);
}

function create_thumb($max_w, $max_h, $img_in, $img_out, $quality=80, $cut=false, $border=false, $crop_data=false, &$error = "", $force_size=false) {
    return tools_photo::create_thumb($max_w, $max_h, $img_in, $img_out, $quality, $cut, $border, $crop_data, $error, $force_size);
}

# Функция генерирует пароль (строку заданной длины)
function gen_pass($length) {
    return tools_string::randString($length);
}


function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


/**
 * Безопасная вставка любой строки в БД
 * Добавлена проверка на XSS
 * @param string Текст
 * @return string Отформатированный текст
 */
function safeDbString ( $str='' ) {
    return tools_string::safeDbString($str);
}


/**
 * Безопасный массив с целыми значениями
 */
function cleanIntegerArray ( $array=array() ) {
	$return = array();
	if ( is_array( $array ) && sizeof( $array ) ) {
		foreach( $array as $k => $v ) {
			$return[ intval($k) ] = intval($v);
		}
	}
	return $return;
}



/*-------------------------------------------------------------------------*/
// XSS Clean: Nasty HTML
/*-------------------------------------------------------------------------*/

/**
* Remove script tags from HTML (well, best shot anyway)
*
* @param	string	HTML код
* @return	string  Вычещенный HTML
* @since	2.1.0
*/
function xss_html_clean( $html ) {
    return tools_string::xss_html_clean($html);
}



/*
function DateAdd($interval, $number, $date) {
    $date_time_array = getdate($date);
    $hours = $date_time_array['hours'];
    $minutes = $date_time_array['minutes'];
    $seconds = $date_time_array['seconds'];
    $month = $date_time_array['mon'];
    $day = $date_time_array['mday'];
    $year = $date_time_array['year'];

    switch ($interval) {
        case 'yyyy':
            $year+=$number;
            break;
        case 'q':
            $year+=($number*3);
            break;
        case 'm':
            $month+=$number;
            break;
        case 'y':
        case 'd':
        case 'w':
            $day+=$number;
            break;
        case 'ww':
            $day+=($number*7);
            break;
        case 'h':
            $hours+=$number;
            break;
        case 'n':
            $minutes+=$number;
            break;
        case 's':
            $seconds+=$number;
            break;
    }

    $timestamp= mktime($hours,$minutes,$seconds,$month,$day,$year);
    return $timestamp;
}
*/


/**
 * @desc Для фильтрации массива через array_walk
 * @param unknown_type $val
 */
function intArr(&$val) {
	$val = intval($val);
}


/**
 * @desc Аналог ORD для multibyte строк
 * @param string $c
 */
function uniord($c) {
    return tools_string::uniord($c);
}

/**
 *
 * @desc Очищает multibyte строку от непечатаемых символов
 * @param string $string
 */
function clearStringFromNPChars($string) {
    return tools_string::clearStringFromNPChars($string);
}



function PhotoUpload($object,$id) {
    return tools_photo::upload($object,$id);
}

function getPhoto($size="medium",$object="student",$id=0,$gender="U") {
    return tools_photo::getPhoto($size, $object, $id, $gender);
}


function delPhoto($object="student",$id=0) {
    return tools_photo::delPhoto($object, $id);
}


function timeAgo($time) {
    return tools_dateTime::timeAgo($time);
}


function randString($len) {
    return tools_string::randString($len);
}