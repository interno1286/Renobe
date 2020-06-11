{*
Всего на сайте новелл: {ormModel::getInstance('public','novella')->get('count(id)')}
*}
<br />
<button class="btn btn-primary" onclick="site.novella.add();">добавить новеллу</button>
<style>
    .nov {
        border-bottom: 1px solid #666;
        padding: 15px 0 15px 5px;
    }
</style>

<h4>Статистика</h4>
{$chapters=ormModel::getInstance('public','chapters')->get('count(id)','1=1')}
{$translated=ormModel::init('chaptersModel')->getTranslatedChaptersCount()}
{$translated2day=ormModel::init('chaptersModel')->get2dayTranslatedChaptersCount()}

Новэлл: {ormModel::getInstance('public','novella')->get('count(id)','1=1')}<br />
Глав: {$chapters}<br />
Переведено глав всего: {$translated}<br />
Переведено глав сегодня: {$translated2day}<br />
Осталось перевести: {$chapters-$translated}

<h4>Новэллы</h4>
<table class="table" style="margin-top: 20px;">
    {foreach ormModel::getInstance('public','novella')->getAll("1=1","date_add desc") as $n}
        <tr>
            <td>
                {if $n.image}
                    <img src='/public/novellas/{$n.image}' style="max-height: 100px;"/><br />
                {/if}
                &nbsp;
            </td>
            <td>
                <a href="/novellas/{tools_string::translit($n.name_original)}/{$n.id}" target="_blank">{$n.name}</a>
                <a href="#" title="оглавление" onclick="site.novella.volumes.edit({$n.id},this);return false;"><i class="fas fa-swatchbook"></i></a>
                <a href="#" title="редактировать глоссарий" onclick="site.novella.glossary.edit({$n.id},this);return false;"><i class="fas fa-atlas"></i></a>
                <a href="#" title="редактировать новеллу" onclick="site.novella.edit({$n.id});return false;"><i class="far fa-edit"></i></a>
                <a href="#" title="удалить" onclick="site.novella.del({$n.id},this);return false;"><i class="far fa-trash-alt"></i></a>
                
                
                <br />
                Синхронизация: 
                {if $n.last_sync}
                    {$n.last_sync|date_format:'%d.%m.%Y %H:%M'}
                {else}
                    небыло
                {/if}
                
                {*
                <a href="#" onclick="site.novella.showChapters({$n.id},this);"><i class="far fa-list-alt"></i></a>
                <div style="display: none;" class="chapters"></div>
                *}
            </td>
            <td>
            {$n.author}
            </td>
            <td>
            {$n.date_add|date_format:'%d.%m.%Y'}
            </td>

        </tr>
    {/foreach}
</table>
