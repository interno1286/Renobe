<?php

class DeepL {
    
    public $key = '';
    public $srcLng = "EN";
    public $targetLang = "RU";
    
    public $codesHelp = "
400	Bad request. Please check error message and your parameters.
403	Authorization failed. Please supply a valid auth_key parameter.
404	The requested resource could not be found.
413	The request size exceeds the limit.
429	Too many requests. Please wait and resend your request.
456	Quota exceeded. The character limit has been reached.
503	Resource currently unavailable. Try again later.
5**	Internal error
    ";
    
    function __construct($srcLng="EN") {
        
        if ($srcLng=='en') $srcLng = 'EN';
        
        $this->srcLng = $scrLng;
        
        $this->key = settings::getVal('deepl_key');
    }
    
    function translate($text, $novella_id=false) {
        
        if (!$text) return '';
        
        $result = $this->post("https://api.deepl.com/v2/translate", [
            'source_lang'   => $this->srcLng,
            'text'          => $text,
            'target_lang'   => $this->targetLang,
            'auth_key'      => $this->key
        ]);
        
        if (!$result->translations[0]->text) throw new Exception('Deepl unknown translate error');
        
        return $result->translations[0]->text;
    }
    
    function stat() {
        $url = 'https://api.deepl.com/v2/usage?auth_key='.$this->key;
        
        return json_decode($this->getPage($url));
        
    }
    
    
    
    function post($url, $data) {
        
        // Prepare new cURL resource
        clog("Deepl Request $url ".print_r($data,1));
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
/*
        // Set HTTP Header for POST request 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
            'Authorization: Bearer '.$this->iamtoken
        ));
*/
        // Submit the POST request
        $result = curl_exec($ch);
        
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode>400) {
            clog("Deepl translate error CODE ".$httpcode."\n\n".$this->codesHelp);
            throw new Exception("DeepL translate error with CODE  ".$httpcode);
        }

        
        return json_decode($result);
    }
    
    
 
    function getPage($url) {
        clog("GET PAGE $url");
        $key = 'dpl'.sha1($url);
        $cached = tools_cache::get($key);
        
        if ($cached && $cached!=='!!nocache!!') {
            clog(" - FROM CACHE!");
            return $cached;
        }
        $ctx = stream_context_create([
            'ssl' => [
                'verify_peer'   => false,
                'verify_peer_name' => false
            ],
        ]);
        
        $html = file_get_contents($url, false, $ctx);
        clog(" - ".(($html===false) ? "fail" : ' ok '));
        if ($html) tools_cache::save ($key, $html, 3600*2);
            
        return $html;
        
    }    
}
