{$volumes=ormModel::getInstance("volumesModel")->getAll("novella_id=`$params.id`"," number, id")}

{*
<div style="display: flex;" class="volumeselect">
    <div class="volumes">
        {foreach $volumes as $v}
            <div class="v">
                #{$v.number}<br />
                {$v.title}
            </div>
        {/foreach}
    </div>
</div>
*}
<style>
    .chaps {
        margin-left: 20px;
        display: none;
    }
    
    .v_name {
        cursor: pointer;
        color: blue;
        padding: 5px 0 0 0;
    }
    
    .actions {
        margin-bottom: 5px;
    }
    
    .actions a, .actions span {
        color: #aaa;
        font-size: 11px;
    }
    
    .chaps li {
        margin-bottom: 10px;
    }
</style>

<button onclick="site.novella.volumes.reget({$params.id}, this);" class="btn btn-primary">получить главы и тома с источника, удалив текущие (полная замена)</button>
<button onclick="site.novella.volumes.refresh({$params.id}, this);" class="btn btn-primary">обновить</button>

{foreach $volumes as $v}
    <div class="volume">
        <div class="v_name" id="volume{$v.id}" onclick="$(this).parent().find('.chaps').slideToggle();site.novella.volumes.currentID = {$v.id};">
            #{$v.number} {$v.title}
        </div>
        <div class="actions">
            <a href="#" onclick="site.novella.volumes.sync({$v.id},this);return false">
            синхронизировать назания глав и самого тома с источником
            </a>
            <span>&nbsp&nbsp|&nbsp&nbsp</span>
            <a href="#" onclick="site.novella.volumes.clearNames({$v.id}, {$params.id}, this);return false">
            удалить перевод названий
            </a>                          
        </div>
        
        {$chapters = ormModel::getInstance('chaptersModel')->getChaptersForAdmin($v.id)}

        <ul class="chaps chapters{$v.id}">
            {foreach $chapters as $c}
                <li>
                    {$c.name_original} (#{$c.number}{if $c.number_parsed} / {$c.number_parsed}{/if})
                    {if $c.name_ru}
                        (<a href="/chapter/{tools_string::translit($c.name_ru)}/{$c.id}" target="_blank">{$c.name_ru}</a>)
                    {/if}
                    
                    {if $c.total_pars>0}
                        {$percent=round(($c.trans_pars/$c.total_pars)*100)}
                    {else}
                        {$percent=0}
                    {/if}
                    <span style="color: #aaa;font-size: 11px;">Параграфов в главе: <span id="chapars{$c.id}">{$c.total_pars}</span>; переведено: {$c.trans_pars}</span>
                    <br />
                    
                    Переведено
                    <div style="display: inline-block;width: 400px;">
                        <div class="progress">
                          <div class="progress-bar" role="progressbar" style="width: {$percent|default:0}%;" aria-valuenow="{$percent|default:0}" aria-valuemin="0" aria-valuemax="100">{$percent|default:0}%</div>
                        </div>
                    </div>
                        
                    <span style="color: #aaa;font-size: 11px;">
                        <a href="#" onclick="site.novella.chapters.reloadChapter({$c.id});">Загрузить содержимое заново </a>
                    </span>
                </li>
            {/foreach}
        </ul>
    </div>
{/foreach}
