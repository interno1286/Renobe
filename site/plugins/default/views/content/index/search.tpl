<h1>Результаты поиска</h1>
<style>
    .r {
        border-bottom: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 20px;
        height: 187px;
        font-size: 13px;
        color: #7b7b77;
    }
    h4 a {
        color: #91e26f;
    }
    
    .infoShowed .pagination li a {
        background-color: #000;
        border-color: #333;
    }
    
    .infoShowed .pagination li a {
        color: #FFF;
    }
    
    .pagination>li>a:focus, .pagination>li>a:hover, .pagination>li>span:focus, .pagination>li>span:hover {
        z-index: 2;
        color: #bbdfff !important;
        background-color: #2e2f2e !important;
        border-color: #666 !important;
    }    
</style>

Искать в&nbsp;&nbsp;
<div class="btn-group" style="margin: 15px 0;">
    
    <button class="stype btn btn-primary {if (isset($stypes) && in_array('n',$stypes)) || !isset($stypes)}active{/if}" type="n" onclick="$(this).toggleClass('active');">Названиях новэлл</button>
    <button class="stype btn btn-primary {if isset($stypes) && in_array('c',$stypes)}active{/if}" type="c" onclick="$(this).toggleClass('active');">Названиях глав</button>
    <button class="stype btn btn-primary {if isset($stypes) && in_array('p',$stypes)}active{/if}" type="p" onclick="$(this).toggleClass('active');">Тексте</button>
</div>

<div class="search_results">
{$page=$params.p|default:1}
{$c=0}
{foreach $results as $r}
    {$c=$c+1}
    {if $c<=($page*10-10)}{continue}{/if}
    {if $c>($page*10)}{break}{/if}
    
    {if $r.type=='nov'}
    <div class="r">
        <img 
            src="/public/novellas/{$r.image}" 
            style="margin-right: 20px;max-height: 95%;" 
            align="left" 
        />
        <h4>{$c}. <a target="_blank" href="/novellas/{tools_string::translit($r.name)}/{$r.id}">{$r.name}</a></h4>
        {$r.description}
    </div>
    {/if}
    
    {if $r.type=='chapter'}
    <div 
        class="r"
    >
        <img 
            src="/public/novellas/{$r.novella_image}" 
            style="margin-right: 20px;max-height: 95%;" 
            align="left" 
        />
        
        <h4>{$c}. <a target="_blank" href="/novellas/{tools_string::translit($r.novella_name)}/{$r.novella_id}">{$r.novella_name}</a></h4>
        
        <a href="/chapter/{tools_string::translit($r.chapter_name)}/{$r.chapter_id}">
            <h5>{if $r.volume_number}Том {$r.volume_number}, {/if}
                
                {if $r.number_parsed}
                    Глава #{$r.number_parsed} 
                {/if}
                
                {$r.chapter_name}
            </h5>
        </a>
    </div>
    {/if}
    
    
    
    {if $r.type=='par'}
    <div 
        class="r"
    >
        <img 
            src="/public/novellas/{$r.novella_image}" 
            style="margin-right: 20px;max-height: 95%;" 
            align="left" 
        />
        
        <h4>{$c}. <a target="_blank" href="/novellas/{tools_string::translit($r.novella_name)}/{$r.novella_id}">{$r.novella_name}</a></h4>
        
        <a href="/chapter/{tools_string::translit($r.chapter_name)}/{$r.chapter_id}">
        <h5>Том {$r.volume_number}, Глава #{$r.chapter_number} {$r.chapter_name}</h5>
        ...{$r.text_ru}...
        </a>
    </div>
    {/if}
    
{/foreach}



        
    {if sizeof($results)>10}
        {$pages=ceil(sizeof($results)/10)}

        
            {if $pages>1}
            {$p=$params.page|default:1}
            <nav>
                <ul class="pagination">
                    {$showDots=false}
                    {section start=1 loop=$pages+1 name=p}
                        {if !$smarty.section.p.last && !$smarty.section.p.first}
                            {if abs($smarty.section.p.index-$p)>5}
                                {if !$showDots}
                                    <li class="pages"><a href="#" onclick="return false;">...</a></li>
                                    {$showDots=true}
                                    {continue}
                                {else}
                                    {continue}
                                {/if}
                            {/if}
                        {/if}
                        
                        <li class="{if $smarty.section.p.index==$p} active {/if}"><a href="/search?t={$params.t|urlencode}&p={$smarty.section.p.index}{if isset($params.types)}&types={$params.types}{/if}">{$smarty.section.p.index}</a></li>
                        
                        {if $smarty.section.p.index==$p && $showDots==true}
                            {$showDots=false}
                        {/if}
                    {/section}
                    {if $p!=$pages}
                        <li><a href="/search?t={$params.t|urlencode}&p={$p+1}{if isset($params.types)}&types={$params.types}{/if} "aria-label="Следующая"><span aria-hidden="true">»</span></a></li>
                    {/if}
                </ul>
            </nav>
            {/if}
        
    {/if}






{*
    {if sizeof($results)>10}
        {$pages=ceil(sizeof($results)/10)}
        <div >
            <ul class="pagination">
                {section start=1 loop=$pages name=p step=1}
                    <li><a href="/search?t={$params.t|urlencode}&p={$smarty.section.p.index}">{$smarty.section.p.index}</a></li>
                {/section}
                
            </ul>
        </div>
    {/if}
*}
    {if !$results} ничего не найдено{/if}
</div>