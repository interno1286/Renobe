<?php

class deeplDirect {
    
    public $cookies;
    
    static function getInstance($srcLng="") {
        
        if (self::$instance) {
            //self::$instance->key = settings::getVal('yandex_cloud_tr_key');
            self::$instance->srcLng = $srcLng;
            return self::$instance;
        }else self::$instance = new yandexCloudTranslate($srcLng);
        
        return self::$instance;
    }
    
    function __construct($srcLng="") {
        //$this->iamtoken = settings::getVal('yandex_cloud_iam');
        $this->srcLng = $srcLng;
        
        $firstPage = $this->get("https://www.deepl.com/translator");
        
        
    }
    
    
    function translate($text, $novella_id=false) {
        
        $data = '{"jsonrpc":"2.0","method": "LMT_split_into_sentences","params":{"texts":["'.$text.'"],"lang":{"lang_user_selected":"auto","user_preferred_langs":["EN","PL","RU"]}},"id":18130009}';
        
        $r = $this->post("https://www2.deepl.com/jsonrpc", $data, [
            //'cookie: LMTBID=bb66cb7c-9081-4739-84b8-cdb5b3727bc6|1d57ce85a6770c1e2d42caa09e364bd6; _gat=1',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36'
        ]);
        
        return $r;
    }
    
    function get($url) {
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        //curl_setopt($ch, CURLOPT_POST, true);
        
        
        // Submit the POST request
        $response = curl_exec($ch);
        
        file_put_contents("x.tpl",$response);
        
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);        
        
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        $e_h = "";
        
        //foreach ()
        
    }
    
    function post($url, $params, $headers=false) {
        // Prepare new cURL resource
        clog("DeeplDirect Request $url ".print_r($data,1));
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        // Set HTTP Header for POST request 
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        // Submit the POST request
        $result = curl_exec($ch);
        
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode>400) {
            clog("Deepl translate error CODE ".$httpcode."\n\n".$this->codesHelp);
            throw new Exception("DeepL translate error with CODE  ".$httpcode);
        }

        
        return json_decode($result);
    }
    
}
