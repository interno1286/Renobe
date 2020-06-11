<?php

global $config;


if  (
        isset($_SESSION['user_data']['role'])
        && $_SESSION['user_data']['role']=='admin'
    )
    {
        site::addDataToContent('Simpletext_IndexController::addScripts');
        
}
