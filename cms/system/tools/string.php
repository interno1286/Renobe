<?php

class tools_string {
    
    /**
     *
     * @desc Очищает multibyte строку от непечатаемых символов
     * @param string $string
     */
    static function clearStringFromNPChars($string) {
        $out = "";

        $valis_code_ranges = array(
            array('min'=>65,'max'=>90), //A-Z
            array('min'=>97,'max'=>122), //a-z
            array('min'=>1072,'max'=>1105), //а-я
            array('min'=>1025,'max'=>1071), //А-Я
            array('min'=>30,'max'=>70), //numbers & symbols
            array('min'=>90,'max'=>100) //brackets and other
        );

        for ($x=0;$x<mb_strlen($string);$x++) {
            $char = mb_substr($string,$x,1);
            $c = uniord($char);
            foreach ($valis_code_ranges as $range) {
                if ($c>=$range['min'] && $c<=$range['max']) {
                    $out .= $char;
                    continue 2;
                }
            }
        }

        return $out;
        
    }

    /**
     * @desc Аналог ORD для multibyte строк
     * @param string $c
     */
    static function uniord($c) {
        $h = ord($c{0});
        if ($h <= 0x7F) {
            return $h;
        } else if ($h < 0xC2) {
            return false;
        } else if ($h <= 0xDF) {
            return ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
        } else if ($h <= 0xEF) {
            return ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6
                                     | (ord($c{2}) & 0x3F);
        } else if ($h <= 0xF4) {
            return ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12
                                     | (ord($c{2}) & 0x3F) << 6
                                     | (ord($c{3}) & 0x3F);
        } else {
            return false;
        }
        
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
    static function xss_html_clean($html) {
        //-----------------------------------------
        // Opening script tags...
        // Check for spaces and new lines...
        //-----------------------------------------

        $html = preg_replace( "#<(\s+?)?s(\s+?)?c(\s+?)?r(\s+?)?i(\s+?)?p(\s+?)?t#is"        , "&lt;script" , $html );
        $html = preg_replace( "#<(\s+?)?/(\s+?)?s(\s+?)?c(\s+?)?r(\s+?)?i(\s+?)?p(\s+?)?t#is", "&lt;/script", $html );

        //-----------------------------------------
        // Basics...
        //-----------------------------------------

        $html = preg_replace( "/javascript/i" , "j&#097;v&#097;script", $html );
        $html = preg_replace( "/alert/i"      , "&#097;lert"          , $html );
        $html = preg_replace( "/about:/i"     , "&#097;bout:"         , $html );
        $html = preg_replace( "/onmouseover/i", "&#111;nmouseover"    , $html );
        $html = preg_replace( "/onclick/i"    , "&#111;nclick"        , $html );
        $html = preg_replace( "/onload/i"     , "&#111;nload"         , $html );
        $html = preg_replace( "/onsubmit/i"   , "&#111;nsubmit"       , $html );
        $html = preg_replace( "/<body/i"      , "&lt;body"            , $html );
        $html = preg_replace( "/<html/i"      , "&lt;html"            , $html );
        $html = preg_replace( "/document\./i" , "&#100;ocument."      , $html );

        return $html;
    }
    
    
    static function safeDbString ( $str='' ) {
        $str = trim($str);

        if ( empty($str) ) {
            return '';
        }

        // XSS hack ?
        $str = xss_html_clean($str);

        // Подготавливает строку для вставки в тектовое поле БД
        $str = pg_escape_string($str);

        return $str;
    }
    
    
    /**
    * Убираем HTML и подчищаем значения.
    * Возможно использовать для обработки _GET _POST значений
    * @param string Входящее значение
    * @return string Очищенное значение
    */
    static function parseCleanValue ($val) {
        if ( $val == '' ) {
            return '';
        }

        // Если включены магические кавычки добавить обработку: (Включать на сервере магические кавычки очень плохо)
        // $val = stripslashes($val);
        // $val = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $val );
        $val = str_replace( "&#032;", " ", $val );

        $val = str_replace( "&"				, "&amp;"         , $val );
        $val = str_replace( "<!--"			, "&#60;&#33;--"  , $val );
        $val = str_replace( "-->"			, "--&#62;"       , $val );
        $val = preg_replace( "/<script/i"	, "&#60;script"   , $val );
        $val = str_replace( ">"				, "&gt;"          , $val );
        $val = str_replace( "<"				, "&lt;"          , $val );
        $val = str_replace( '"'				, "&quot;"        , $val );
        $val = str_replace( "$"				, "&#036;"        , $val );
        $val = str_replace( "\r"			, ""              , $val ); // Убираем символы табуляции
        $val = str_replace( "!"				, "&#33;"         , $val );
        $val = str_replace( "'"				, "&#39;"         , $val ); // ВАЖНО: Это помогает обезопасить SQL запросы

        // Если в тексте были юникоды, вернем их к первоначальному состоянию
        $val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );
        // Попробуем пофиксить HTML сущности с пропущеной ;
        $val = preg_replace( "/&#(\d+?)([^\d;])/i", "&#\\1;\\2", $val );

        return $val;
    }
    
 
    /**
     * @desc Функция генерирует до 40 символов
     * @return string sha1 строка обрезанная до заданной длинны
     */
    static function randString ( $length=32 ) {
        return substr( sha1( uniqid( rand(0,999999),true ).microtime(true) ),0,$length );
    }
    
    static function trimStr($str) {
        $ret_str = preg_replace('/\s\s+/u', ' ', trim($str));
        return $ret_str;
    }
    
    
    static function translit( $text ) {
        $text = mb_strtolower($text,"UTF-8");

        $text = preg_replace('#([^a-zа-я0-9\.\s_-])#Uusi','',$text);

        $ru = explode(",","а,б,в,г,д,е,ё,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ъ,ь,ы,э,ю,я, ");
        $en = explode(",","a,b,v,g,d,e,e,zh,z,i,i,k,l,m,n,o,p,r,s,t,u,f,h,c,ch,sh,sch,,,i,e,yu,ya,_");
        $text = str_replace($ru,$en,$text);
        return $text;
    }

    
    
    /**
     * Возвращает имя и расширение файла
     * return array(name,ext)
     */
    static function explode_filename( $filename ) {

        $data = pathinfo($filename);

        $f_name = (isset($data['filename'])) ? $data['filename'] : '';
        $f_ext = (isset($data['extension'])) ? $data['extension'] : '';

        return array( 'name' => $f_name, 'ext' => $f_ext, 0=>$f_name, 1=>$f_ext );
    }

    
    
    /**
     * @desc Преобразует арабское число в римское
     * @param integer $number — арабское число
     */
    static function number_to_roman($value) {
        if($value<0) return "";
        if(!$value) return "0";
        $thousands=(int)($value/1000);
        $value-=$thousands*1000;
        $result=str_repeat("M",$thousands);
        $table=array(
            900=>"CM",500=>"D",400=>"CD",100=>"C",
            90=>"XC",50=>"L",40=>"XL",10=>"X",
            9=>"IX",5=>"V",4=>"IV",1=>"I"
        );
        while($value) {
            foreach($table as $part=>$fragment) if($part<=$value) break;
            $amount=(int)($value/$part);
            $value-=$part*$amount;
            $result.=str_repeat($fragment,$amount);
        }
        return $result;
    }

    /**
     * @desc Возвращает текстовое описание размера
     * @param integer $size — размер в байтах
     */
    static function getFileSizeString( $size ) {
        $size = floor($size);
        $names = array('б','Кб','Мб','Гб');
        $end = '';
        foreach ($names as $v) if ($end=='') { if ($size>800) $size = $size/1024; else $end = $v; }
        $size = round($size,1);
        return str_replace('.',',',$size).' '.$end;
    }

    
    
    /**
     * Возвращает сумму прописью
     * @ author runcore
     * @ uses morph(...)
     */
    static function num2str($num) {
        $nul='ноль';
        $ten=array(
            array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
            array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
        );
        $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
        $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
        $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
        $unit=array( // Units
            array('копейка' ,'копейки' ,'копеек',	 1),
            array('рубль'   ,'рубля'   ,'рублей'    ,0),
            array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
            array('миллион' ,'миллиона','миллионов' ,0),
            array('миллиард','милиарда','миллиардов',0),
        );
        //
        list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub)>0) {
            foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit)-$uk-1; // unit key
                $gender = $unit[$uk][3];
                list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk>1) $out[]= self::morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
            }
        }
        else $out[] = $nul;
        $out[] = self::morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
        $out[] = $kop.' '.self::morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
        $data = trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
        
        $fc = mb_strtoupper(mb_substr($data, 0, 1));
        return $fc.mb_substr($data, 1);
    }

    /**
     * Склоняем словоформу
     * @ author runcore
     */
    static function morph($n, $f1, $f2, $f5) {
        $n = abs(intval($n)) % 100;
        if ($n>10 && $n<20) return $f5;
        $n = $n % 10;
        if ($n>1 && $n<5) return $f2;
        if ($n==1) return $f1;
        return $f5;
    }
    
    static function mb_ucfirst($str) {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));
        return $fc.mb_substr($str, 1);
    }    
}
