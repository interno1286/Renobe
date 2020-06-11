<?php

/**
 * Description of modifier
 *
 * @author v0yager
 */
function smarty_modifier_smiles($text) {
    
    $smiles = [
        
        
        '[SAD]'=>"-252px 174px",
        'X('=>"-60px 115px",
        
        ":\'("=>"-203px -6px",
        "[NONO]"=>"-107px -184px",
        ":("=>"-156px 114px",
        
        'X=O' => "-107px -67px",
        
        
        ":\')"=>"-251px -6px",
        
        // en
        ";p"  => "-314px -6px",
        ";)"    => "-106px 173px",
        ";o)"  => "-154px 53px",
        ";P"  => "-314px -6px",
        "X)"    => "-361px -6px",
        ":X" => "-204px -185px",
        "X|"    => "-551px 115px",
        ":')"   => "-249px -6px",
        
        //rus
        
        ";р"  => "-314px -6px",
        //";o)" => "-157px -247px",
        ";Р"  => "-314px -6px",
        "Х)"    => "-361px -6px",
        "Ж)"    => "-361px -6px",
        
        ":|"=>" -12px -64px",
        "8)"=>"292px 116px",
        "8D"=>"-57px -68px",
        "B)"=>"-12px -184px",
        ':('=>"143px 116px",
        '[NOWAY]'=>"-57px 53px",
        
        ":'("   => "-205px -6px",
        ':LOVE:' => "-155px -67px",
        ':BRRR:' => "-252px -66px",
        '[ANGRY]'   => "-204px -67px",
        ':CHILL:'   => "-203px -128px",
        
        ':MASK:'  => "-11px -247px",
        
        ':LOL:' => "-11px -125px",
        '0:)'=> "-252px -247px",
        ':*'=> "-157px -126px",
        "X'P" => "95px -547px",
        ':)'=>"-109px -6px",
        ':/'=>"-157px -6px",
        /// en
        ':p'=>"-8px -8px",
        ':P'=>"-8px -8px",
        //// rus
        ':Р'=>"-8px -8px",
        ':р'=>"-8px -8px",
        
         ':-*'=>"-57px -126px",
        'X-*'=>"-109px 54px",
        ':D'=>"-61px -6px",
        
    ];
    
    foreach ($smiles as $s=>$pos) {
        
        $img = "
        <div style='display: inline-block;
                    
                    background-image: url(/site/skins/inside/public/img/smiles_small.png);
                    width: 40px;
                    height: 40px;
                    background-position: $pos;
        '></div>";
        
        $text = str_replace($s, $img, $text);
    }
    
    return $text;
}
