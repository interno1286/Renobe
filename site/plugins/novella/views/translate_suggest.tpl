{if $params.t=='paragraph'}
    {$data=ormModel::getInstance('public','paragraph')->getRow("id=`$params.id`")}
    {$text_original=$data.text_original}
    {$text_en=$data.text_en}
    {$text_ru=$data.text_ru}
{/if}    

{if $params.t=='chapter'}
    {$data=ormModel::getInstance('public','chapters')->getRow("id=`$params.id`")}
    {$text_original=$data.name_original}
    {$text_en=$data.name_original}
    {$text_ru=$data.name_ru}
{/if}    

{if $params.t=='novella'}
    {$data=ormModel::getInstance('public','novella')->getRow("id=`$params.id`")}
    {$text_original=$data.name_original}
    {$text_en=$data.name_original}
    {$text_ru=$data.name}
{/if}    

{if $params.t=='description'}
    {$data=ormModel::getInstance('public','novella')->getRow("id=`$params.id`")}
    {$text_original=$data.description_original}
    {$text_en=$data.description_original}
    {$text_ru=$data.description}
{/if}    

<label>Оригинал</label>
<p class="cn">{$text_original}</p>

{if $text_en!==$text_original}
<label>Вариант на английском</label>
<p class="en">{$text_en}</p>
{/if}
<br />
<style>
    #user_translate {
        height: 200px;
    }
</style>
<label>Ваш вариант перевода</label>
<textarea id="user_translate" class="form-control">{$text_ru}</textarea>