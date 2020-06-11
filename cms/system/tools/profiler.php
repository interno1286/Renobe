<?php

class tools_profiler {

    private static $checkPoints = array(); //////// чекпоинты для фиксации времени выполнения
    
    static function checkpoint($name = '') {
        global $start_time, $config;

        $debug_on = (is_object($config)) ? $config->debug->on : $config['debug']['on'];

        if ($debug_on) {
            $prev_cp = [];
            
            if (isset(self::$checkPoints[sizeof(self::$checkPoints) - 1]))
                $prev_cp = @self::$checkPoints[sizeof(self::$checkPoints) - 1];
            
            $prev_time = (isset($prev_cp['time'])) ? (float) $prev_cp['time'] : $start_time;

            self::$checkPoints[] = array(
                'name' => $name,
                'time' => microtime(true),
                'execute_time' => floatval(microtime(true) - $prev_time)
            );
            
            if (site::$liveTrace) {
                $d = @file_get_contents('run.log');
                $d .= "\n ".strftime('%H:%M:%S ').$name;
                file_put_contents('run.log', $d);
            }
        }
    }
    
    static function report() {
        global $start_time, $config;

        $out = '';

        $debug_on = (is_object($config)) ? $config->debug->on : $config['debug']['on'];

        if ($debug_on && !site::isAjax() && !site::isPost()) {

            $out .= "<!--
				\n\n------------- Execution Report -------------";

            $total_time = 0;

            foreach (self::$checkPoints as $cp) {
                $out.= "\n{$cp['name']}: " . round(($cp['execute_time'] * 1000), 2) . " ms";
                $total_time+=$cp['time'];
            }

            $out .= "\n Total: " . round(floatval((microtime(true) - $start_time) * 1000), 2) . ' ms';
        
            $out .= "\n\n---cache----\n".((@tools_cache::$cache['default']===false) ? 'off' : 'on')."\n------------";
                
            $out .= "\n\n------------- Execution Report -------------
                !-->";
        }

        echo $out;
        return $out;
        
    }
}
