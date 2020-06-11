

<div id="translate_block" style="display: none;"></div>
<style>
    .trtab tr {
        cursor: pointer;
    }
    
    #translate_block label {
        color: #cab2b2;
    }
    
    #translate_block .cn {
        color: blue;
    }
    
    #translate_block .en {
        color: #b1b126;
    }
    
    #translate_block .ru {
        font-weight: bold;
    }
    
    #translate_block .cn, #translate_block .en, #translate_block .ru {
        border: 1px solid #e6e6e6;
        padding: 5px;
        background-color: #efefef;
    }
</style>

<table class="table trtab">
    <tr>
        <th>дата</th>
        <th>произведение</th>
        <th>Том</th>
        <th>Глава</th>
        <th>Пользователь</th>
        
    </tr>
    {foreach ormModel::getInstance('userTranslateModel')->getTranslateForAdmin() as $t}
        
        {if $t.type=='paragraph'}
            <tr onclick="site.novella.translate.show({$t.id}, this, 'paragraph');">
                <td>{$t.created|date_format:'%d.%m.%Y %H:%M'}</td>
                <td>
                    <a target="_blank" href="/novellas/{tools_string::translit({$t.novella_name})}/{$t.novella_id}">{$t.novella_name}</a>
                </td>
                <td>
                    #{$t.volume_number}
                </td>

                <td>
                    <a href="/chapter/{tools_string::translit($t.novella_name)}/{$t.chapter_id}" target="_blank">#{$t.chapter_number}</a>
                </td>

                <td>
                    {$t.user_name}
                </td>
            </tr>
        {/if}
        
        {if $t.type=='chapter'}
            <tr onclick="site.novella.translate.show({$t.id}, this, 'chapter');">
                <td>{$t.created|date_format:'%d.%m.%Y %H:%M'}</td>
                <td>
                    <a target="_blank" href="/novellas/{tools_string::translit({$t.novella_name})}/{$t.novella_id}">{$t.novella_name}</a>
                </td>
                <td>
                    #{$t.volume_number}
                </td>

                <td>
                    <a href="/chapter/{tools_string::translit($t.novella_name)}/{$t.chapter_id}" target="_blank">#{$t.chapter_number}</a>
                </td>

                <td>
                    {$t.user_name}
                </td>
            </tr>
        {/if}
        
        
        {if $t.type=='novella' || $t.type=='description'}
            <tr onclick="site.novella.translate.show({$t.id}, this, '{$t.type}');">
                <td>{$t.created|date_format:'%d.%m.%Y %H:%M'}</td>
                <td>
                    <a target="_blank" href="/novellas/{tools_string::translit({$t.novella_name})}/{$t.novella_id}">{$t.novella_name}</a>
                </td>
                <td>
                    -
                </td>

                <td>
                    -
                </td>

                <td>
                    {$t.user_name}
                </td>
            </tr>
        {/if}
        
        
        
        
    {/foreach}
    
    
    
</table>


