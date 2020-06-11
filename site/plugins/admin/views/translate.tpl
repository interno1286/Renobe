{$tdata=ormModel::getInstance('userTranslateModel')->getUserTranslate($params.id)}

{if $tdata.paragraph_id}
    <h4>Параграф</h4>
    {$data=ormModel::getInstance('public','paragraph')->getRow("id=`$tdata.paragraph_id`")}
    {$text_original=$data.text_original}
    {$text_en=$data.text_en}
    {$text_ru=$data.text_ru}
{/if}    

{if $tdata.chapter_id}
    <h4>Название главы</h4>
    {$data=ormModel::getInstance('public','chapters')->getRow("id=`$tdata.chapter_id`")}
    {$text_original=$data.name_original}
    {$text_en=$data.name_original}
    {$text_ru=$data.name_ru}
{/if}    

{if $tdata.novella_id}
    <h4>Название новеллы</h4>
    {$data=ormModel::getInstance('public','novella')->getRow("id=`$tdata.novella_id`")}
    {$text_original=$data.name_original}
    {$text_en=$data.name_original}
    {$text_ru=$data.name}
{/if}    

{if $tdata.description_id}
    <h4>Примечание к новелле</h4>
    {$data=ormModel::getInstance('public','novella')->getRow("id=`$tdata.description_id`")}
    {$text_original=$data.description_original}
    {$text_en=$data.description_original}
    {$text_ru=$data.description}
{/if}    

<label>Оригинал</label>
<div class="cn">{$text_original}</div>

<label>Англ. перевод</label>
<div class="en">{$text_en}</div>

<label>Вариант пользователя</label>
<div class="ru">{$tdata.translate}</div>
<br />
<button class="btn btn-success" onclick="site.novella.translate.accept({$tdata.id});">принять</button>
<button class="btn btn-dark" onclick="site.novella.translate.deny({$tdata.id});">отклонить</button>
<button class="btn btn-primary" onclick="site.users.block({$tdata.user_id});">заблокировать пользователя</button>

<br /><br />
<script>
    hideLoadingProcessLayer();
</script>