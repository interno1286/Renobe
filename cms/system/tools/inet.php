<?php

class tools_inet {
    
    static function whois($ip) {
        
        $ip = preg_replace('#([^0-9.:/])#ui','',$ip);
        
        exec('/usr/bin/whois '.$ip, $output);
        
        return implode("\n",$output);
    }
    
    
    static function whois_address($ip) {
        $ip = preg_replace('#([^0-9.:/])#ui','',$ip);
        
        $output = array();
        
        exec('/usr/bin/whois '.$ip.' | grep country', $output);
        
        exec('/usr/bin/whois '.$ip.' | grep address', $output);
/*        
        foreach ($output as &$o)
            $o = trim(str_replace('address:', '', $o));
*/        
        return implode("\n",$output);
    }
}
