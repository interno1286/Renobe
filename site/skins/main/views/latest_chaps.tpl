{$data=ormModel::getInstance('chaptersModel')->getLastChapters(20, $params.p|default:1)}

{$total_pages=ormModel::getInstance('chaptersModel')->getLastChaptersPagesCount(10)}

<div style="text-align: center;">
    <ul class="pagination">
        {$page=$params.p|default:1}
        
        {if $page<=5}
            {$start=1}

            {section name=p start=1 loop=11 step=1}
                <li p="{$smarty.section.p.index}" {if $smarty.section.p.index==$page}class="active"{/if}><a href="/?p={$smarty.section.p.index}" onclick="loadLast({$smarty.section.p.index},this);return false;">{$smarty.section.p.index}</a></li>
            {/section}

        {/if}

        {if $page>5}
            
            {$start=$page}
            {$loop=$start+5}
            
            {if $loop>$total_pages}
                {$start=$total_pages-5}
                {$loop=$total_pages+1}
            {/if}
            
            <li p="1" {if $page==1}class="active"{/if}><a href="/?p=1" onclick="loadLast(1, this);return false;">1</a></li>
            <li><a>...</a></li>
                {section name=p start=$start loop=$loop step=1}
                <li p="{$smarty.section.p.index}" {if $smarty.section.p.index==$page}class="active"{/if}><a href="/?p={$smarty.section.p.index}" onclick="loadLast({$smarty.section.p.index}, this);return false;">{$smarty.section.p.index}</a></li>
                {/section}

        {/if}

    </ul>                          
</div>




{foreach $data as $c}
    <div class="latest">

        {*
        <img src="{tools_images::preview("/public/avatar/6_e559.png","64x64")}"/>
        *}
        <div style="display: inline-block;width: 80px;">
            <img src="{tools_images::preview("/public/novellas/`$c.novella_image`","64x64")}"/>
        </div>

        <div style="display: inline-block;width: calc( 100% - 90px );">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-4 col-xs-12">
                    <a class="head" href="/novellas/{tools_string::translit($c.novella_name)}/{$c.novella_id}">{$c['novella_name']}</a>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">

                    <div class="chapnum">
                        
                        {if $c.volume_number}
                            <span class="badge chapter-badge">Том №{$c.volume_number}</span>  
                        {/if}
                        
                        {if $c.number_parsed}
                            <span class="badge chapter-badge">Глава {$c.number_parsed}</span>
                        {/if}
                    </div>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">    
                    <span class="badgetime">{$c.translate_finish|date_format:'%d.%m.%Y %H:%M'}</span>
                </div>
            </div>
            <a class="name" href="/chapter/{tools_string::translit($c.name_ru)}/{$c.id}">
                {$c.name_ru}
            </a>
        </div>

    </div>
{/foreach}


<div style="text-align: center;">
    <ul class="pagination">
        {$page=$params.p|default:1}
        
        {if $page<=5}
            {$start=1}

            {section name=p start=1 loop=11 step=1}
                <li p="{$smarty.section.p.index}" {if $smarty.section.p.index==$page}class="active"{/if}><a href="/?p={$smarty.section.p.index}" onclick="loadLast({$smarty.section.p.index},this);return false;">{$smarty.section.p.index}</a></li>
            {/section}

        {/if}

        {if $page>5}
            
            {$start=$page}
            {$loop=$start+5}
            
            {if $loop>$total_pages}
                {$start=$total_pages-5}
                {$loop=$total_pages+1}
            {/if}
            
            <li p="1" {if $page==1}class="active"{/if}><a href="/?p=1" onclick="loadLast(1, this);return false;">1</a></li>
            <li><a>...</a></li>
                {section name=p start=$start loop=$loop step=1}
                <li p="{$smarty.section.p.index}" {if $smarty.section.p.index==$page}class="active"{/if}><a href="/?p={$smarty.section.p.index}" onclick="loadLast({$smarty.section.p.index}, this);return false;">{$smarty.section.p.index}</a></li>
                {/section}

        {/if}

    </ul>                          
</div>
