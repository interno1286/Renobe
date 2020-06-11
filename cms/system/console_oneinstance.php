<?php

$f = basename($argv[0]);
$process_count = shell_exec("ps ax | grep '$f' | grep -v grep | grep -v '/bin/sh' | grep -v '/bin/bash' | wc -l");
if ($process_count>1) die();