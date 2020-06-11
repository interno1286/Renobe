<?php
/*
 * Developed by GLENN.ru
 */
class provider {
    
    public $volume_id_field = '';
    
    function getPage($url) {
        clog("GET PAGE $url");
        $key = 'p'.sha1($url);
        $cached = tools_cache::get($key);
        
        if ($cached && $cached!=='!!nocache!!') {
            clog(" - FROM CACHE!");
            return $cached;
        }
        // Requiring TLS 1.0 or better when using file_get_contents():
        $ctx = stream_context_create([
            'ssl' => [
                'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT,
                'verify_peer'   => false,
                'verify_peer_name' => false,
                "allow_self_signed"=>true
            ],
        ]);
        
        $html = file_get_contents($url, false, $ctx);
        
        file_put_contents('info_html.txt', $html.' '. print_r(error_get_last(),1) );
        
        
        clog(" - ".(($html===false) ? "fail" : ' ok '));
        if ($html) tools_cache::save ($key, $html, 3600*2);
            
        return $html;
        
    }
}
