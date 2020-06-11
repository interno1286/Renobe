<?php

class admin_InterkassaController extends adminController {

    function indexAction()
    {
        $ch = curl_init('https://api.interkassa.com/v1/co-invoice');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic NWUxOGNiMzUxYWUxYmQxZDAwOGI0NTZiOlpjN3JzWGZKYzJlaVM4aWFpOUwyRUw4TllpODhTcXhS']);

        $this->view->assign('incomes', json_decode(curl_exec($ch), true));
    }

}
