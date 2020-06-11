{$volume_info=ormModel::getInstance('public','volumes')->getRow("id=`$volume_id`")}

<div class="v-cloak--hidden">
    <div class="panel panel-default volume">
        <div class="panel-heading">
            {if $user_data->id}
            <div class="pull-right">
                <div class="btn-group btn-group-sm">
                    <button onclick="site.novella.chapters.lost({$volume_id});" type="button" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="" data-original-title=""> Сообщить о потерянной главе в томе #{$volume_info.number} </button>
                </div>
            </div>
            {/if}
            <h3 id="volume_name">
                {if $volume_info.name}
                    {$volume_info.name}
                {else if $volume_info.number}
                Том #{$volume_info.number}
                {/if}
                
            </h3>
            <div class="clearfix"></div>
        </div>
        {$pages=ormModel::getInstance('chaptersModel')->getTotalPages($volume_id)}

        <div class="panel-heading text-center" style="overflow: auto">
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
                        <li class="pages p{$smarty.section.p.index} {if $smarty.section.p.index==$p} active {/if}"><a href="#" onclick="site.novella.loadChapters({$volume_id},{$smarty.section.p.index},this);return false;">{$smarty.section.p.index}</a></li>
                        
                        {if $smarty.section.p.index==$p && $showDots==true}
                            {$showDots=false}
                        {/if}
                    {/section}
                    {if $p!=$pages}
                        <li><a href="#" onclick="site.novella.loadChapters({$volume_id},{$p+1},this);return false;" aria-label="Следующая"><span aria-hidden="true">»</span></a></li>
                    {/if}
                </ul>
            </nav>
            {/if}
        </div>



        <table class="table">
            <tbody>
                {foreach $data as $c}
                    <tr>
                        <td>
                            <a class="chapter-link" href="/chapter/{tools_string::translit($c.name_ru)}/{$c.id}">
                                {if trim($c.name_ru," -\r\n")}
                                    {$c.name_ru}
                                {else}
                                    <span class="badge chapter-badge">Глава {$c.number_parsed|default:$c.number}</span> {$c.name_ru}
                                {/if}
                            </a>
                        </td>
                        <td class="text-right"> 
                            <span class="label label-success">0 <span class="glyphicon glyphicon-comment"></span></span>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>


        <div class="panel-footer text-center" style="overflow: auto">
            {if $pages>1}
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
                        <li class="pages p{$smarty.section.p.index} {if $smarty.section.p.index==$p} active {/if}"><a href="#" onclick="site.novella.loadChapters({$volume_id},{$smarty.section.p.index},this);return false;">{$smarty.section.p.index}</a></li>
                        
                        {if $smarty.section.p.index==$p && $showDots==true}
                            {$showDots=false}
                        {/if}
                    {/section}
                    {if $p!=$pages}
                        <li><a href="#" onclick="site.novella.loadChapters({$volume_id},{$p+1},this);return false;" aria-label="Следующая"><span aria-hidden="true">»</span></a></li>
                    {/if}
                </ul>
                
            </nav>
            {/if}
        </div>

    </div>
</div>






