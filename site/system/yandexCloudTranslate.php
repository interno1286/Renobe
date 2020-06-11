<?php

class yandexCloudTranslate  {
    
    static $instance = false;
    public $lang = "";
    public $retrys = 0;
    public $folder_id = "b1g49dvi419otpnl3b6g";
    
    public $token = "AgAAAAAKrv8vAATuwQs2UHGXO0hxvsJv6Rm6Wko";
    public $iamtoken = 'CggVAgAAABoBMxKABFkdqyF4-zh7-LlMjgd8mEFk9YpyNUKVEg-gZL2hWNags66BWT2urCboFScBF0zyf-w9CdMvvHKfrNq5iQAcTtV_2w8lIGEOB-cKSkQiBipCynAahGaiPb8nbSL3cPI9hjmyfql0Lu0L572NNI2FzPF8NkLYgEBnjJbRqFPkKbu3Y1nelDGWtM13m2jCBuPpitymY-gASpy0YgvK4hjjTB6FlUEzKNqWa0GlwfnxWUiE3ee8P8vjzvc1laQLRsbplKgBQede8lRHJnXU2xR3iOiUfx9V11GyQL2j8_Z-MoThj-ehy9m0Zt9I1LSEJfufrNcKNfdXJrPUA3kVfjnAa1itRQHo7Jvyq_IailXV7znGvNozJSztov99yWrWMjcWW-CmxhlIK2mMCGkod83Q_Fjc5exT4xvugsg239kfcy8uu7y2jJOa3YZ2IQiMH_PSMwRIF8CAeBvxZD1oTqsDOH24MHCJSAccTi__cHhslMUpVeeYpuRb4_sea0XdC9JbInUYqJS5ZgDN8bpw1pYFXCEsMyQmLbpDfy4e8g6_moAAzfmHt6hlkFOhvOnK9hPRTKierAjTP-GcCv2OC-TJ2CM0kewTd83ARBPXT5r_CKqThyzFs24wyDNrtjqgUact9lH9Ik8gixHYA5lBTDSAaUVHkfo23jLkUe87q1BXA3VsGiQQ__OF7gUYv8WI7gUiFgoUYWplZ2oxMGRlZDM5NjQzOWRmbWc=';
    
    /*
     Идентификатор ключа:
    aje0phugg759c8mdbc7v
    Ваш секретный ключ:
    AQVNwMx9CBypYizVsP-OvwZ8BJoq-hH5adDnlIUl
    * 
     */
    //public $key = 'AQVNwMx9CBypYizVsP-OvwZ8BJoq-hH5adDnlIUl';
    
    static function getInstance($srcLng="") {
        
        if (self::$instance) {
            //self::$instance->key = settings::getVal('yandex_cloud_tr_key');
            self::$instance->lang = $srcLng;
            return self::$instance;
        }else self::$instance = new yandexCloudTranslate($srcLng);
        
        return self::$instance;
    }
    
    function __construct($srcLng="") {
        //$this->iamtoken = settings::getVal('yandex_cloud_iam');
        $this->lang = $srcLng;
        $this->token = settings::getVal('yandex_cloud_tr_token');
        $this->folder_id = settings::getVal('yandex_cloud_folder');
    }
    
    
    function translate($text, $novella_id=false) {
        $data = new stdClass();
        
        $data->folder_id = $this->folder_id;
        $data->texts = [$text];
        $data->targetLanguageCode = "ru";
        if ($this->lang)
            $data->sourceLanguageCode = $this->lang;
        
        if ($novella_id) {
            $m = ormModel::getInstance('public','glossary');
            $glossary = $m->getAll("novella_id=".$novella_id);
            $pairs = [];
            
            $pairsLen = 0;
                        
            foreach ($glossary as $g) {
                $itm = new stdClass();
                $itm->sourceText = $g['original'];
                $itm->translatedText = $g['translate'];
                $pairs[] = $itm;
                
                $pairsLen += mb_strlen($g['original'])+mb_strlen($g['translate']);
                
                if ($pairsLen>9500) break;/// max len 10000
            }
            
            if ($pairs) {
                
                $gdata = new stdClass();
                $gdata->glossaryPairs = $pairs;
                
                $gconfig = new stdClass();
                $gconfig->glossaryData = $gdata;
                
                $data->glossaryConfig = $gconfig;
            }
        }
        /*
        if (function_exists('clog'))
            clog("\n\nSEND DATA ".print_r($data,1));
        */
        $payload = json_encode($data);

        // Prepare new cURL resource
        $ch = curl_init('https://translate.api.cloud.yandex.net/translate/v2/translate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // Set HTTP Header for POST request 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
            'Authorization: Bearer '.$this->iamtoken
        ));

        // Submit the POST request
        $result = curl_exec($ch);
        
        $enc = json_decode($result);
        
        if (isset($enc->code)) {
            
            
            if ($this->retrys<4) {
                $this->retrys++;
                $this->refreshIamToken();
                return $this->translate($text);
            }else {
                if ($enc->code==8) throw new Exception($enc->message, 666);
                if ($enc->code==16) throw new Exception($enc->message, 666);
                throw new Exception('cannot translate something goes wrong');
            }
        };
        
        curl_close($ch);        
        
        return $enc->translations[0]->text;
        
        // Close cURL session handle
        
    }
    
    function refreshIamToken() {
        //AgAAAAAKrv8vAATuwQs2UHGXO0hxvsJv6Rm6Wko
        
        //curl -d "{\"yandexPassportOauthToken\":\"AgAAAAAKrv8vAATuwQs2UHGXO0hxvsJv6Rm6Wko\"}" "https://iam.api.cloud.yandex.net/iam/v1/tokens"
        $data = new stdClass();
        $data->yandexPassportOauthToken = $this->token;
        

        $payload = json_encode($data);

        // Prepare new cURL resource
        $ch = curl_init('https://iam.api.cloud.yandex.net/iam/v1/tokens');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // Set HTTP Header for POST request 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)
        ));

        // Submit the POST request
        $result = curl_exec($ch);
        $enc = json_decode($result);
        
        if (!isset($enc->iamToken)) throw new Exception('cannot get Iam Token');
        
        settings::setVal('yandex_cloud_iam', $enc->iamToken);
        
        $this->iamtoken = $enc->iamToken;
        // Close cURL session handle
        curl_close($ch);        
        
        return true;
        
    }
}
