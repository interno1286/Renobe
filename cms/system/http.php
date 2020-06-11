<?php

class http {
    
    public $last_headers;
    

    function GET($url, $headers = []) {

        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => $headers
            )
        );

        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        $data = file_get_contents($url, false, $context);

        $this->last_headers = $http_response_header;
        
        return $data;
    }
    
    
    function POST($url, $params, $headers = []) {

        $postdata = http_build_query($params);        
        
        $opts = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => $headers,
                'content' => $postdata
            )
        );

        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        $data = file_get_contents($url, false, $context);

        $this->last_headers = $http_response_header;
        
        return $data;
    }
    

}
