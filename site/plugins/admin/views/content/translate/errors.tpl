<h1>Ошибки перевода</h1>

<h2>Параграфы</h2>
<div style="text-align: right;">
    <button class="btn btn-sm btn-danger" style="margin-bottom: 10px;" onclick="site.novella.translate.delAllErrors();">удалить все</button>
</div>
<table class="table table-hover table-condensed">
    <tr>
        <th>Дата</th>
        <th>Глава</th>
        <th>Текст</th>
    </tr>
    
    {foreach ormModel::init('paragraphModel')->getErrorParagraph() as $p}
    <tr class="data" paragraph_id="{$p.id}">
        <td>{$p.date|date_format:'%d.%m.%Y %H:%M'}</td>
        <td>
            {if !$p.chapter_name}
                {$p.chapter_name_original}
            {else}
                <a href="/chapter/{tools_string::translit($p.chapter_name)}/{$p.chapter_id}" target="_blank">{$p.chapter_name}</a>
            {/if}
        </td>
        <td>
            {$p.text_en|default:$p.text_original}
            <div class="text-right">
                <button class="btn btn-sm btn-primary" onclick="site.novella.translate.showError({$p.id}, this)">перевести</button>
                <button class="btn btn-sm btn-danger" onclick="site.novella.translate.delErr({$p.id},this);">удалить</button>
            </div>
            
        </td>
    </tr>
        
    {/foreach}
</table>