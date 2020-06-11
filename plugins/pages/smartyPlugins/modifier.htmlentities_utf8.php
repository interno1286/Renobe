<?php

function smarty_modifier_htmlentities_utf8($string) {
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}