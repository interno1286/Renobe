<?php

global $config;


if  (
        isset($_SESSION['user_data']['role'])
        && $_SESSION['user_data']['role']=='admin'
        && strpos($_SERVER['REQUEST_URI'],'/admin')===false
    )
    {
        site::addDataToContent('Meta_IndexController::addNotch');
}
