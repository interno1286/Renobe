{if $params.id}
    {$data=ormModel::getInstance('newsModel')->getEvent($params.id)}
{/if}

<form id="news_form" onsubmit="site.news.save();">
    <label>Заголовок</label>
    <input type="text" class="form-control" name='header' value="{$data.header|default:''}" />

    <label>Описание</label>
    <textarea id="news_desc" class="form-control" name='description' >{$data.description|default:''}</textarea>

    <label>Текст</label>
    <textarea id="news_text" class="form-control" name='text' >{$data.text|default:''}</textarea>
    <br /><br />
    <label>Изображение</label>
    <input type="file" name="photo" />
</form>

<script>
    $('#news_desc').ckeditor({
        toolbar: 'Advanced'
    });
    
    $('#news_text').ckeditor({
        toolbar: 'Advanced'
    });
    
</script>
