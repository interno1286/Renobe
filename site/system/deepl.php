<?php
$ch = curl_init();
$information = curl_getinfo($ch);
//curl_setopt($ch, CURLOPT_URL, "https://www2.deepl.com/jsonrpc");
curl_setopt($ch, CURLOPT_URL, "https://www2.deepl.com/jsonrpc");
curl_setopt($ch, CURLOPT_POSTFIELDS, [
        "jsonrpc" => "2.0",
        "method" => "LMT_handle_jobs",
        "params" => [
            "jobs" => [
                [
                    "kind" => "default",
                    "raw_en_sentence" => "something",
                    "raw_en_context_before" => [],
                    "raw_en_context_after" => [],
                    "preferred_num_beams" => 4,
                    "quality" => "fast"
                ]
            ],
            "lang" => [
                "user_preferred_langs" => ["DE", "RU", "EN"],
                "source_lang_user_selected" => "auto",
                "target_lang" => "RU"
            ],
            "priority" => -1,
            "timestamp" => time()
        ],
        "id" => 33690005
    ]
);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Connection: Keep-Alive',
    'Host: www2.deepl.com',
]);
curl_setopt($ch,CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Rigor/1.0.0; http://rigor.com)');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
var_dump($server_output);
var_dump(curl_error($ch));
var_dump($information);
curl_close($ch);

