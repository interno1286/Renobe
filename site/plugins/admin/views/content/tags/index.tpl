<h4>Редактирование тэгов</h4>
<br/><br/>
{$tags=ormModel::init('tagsModel')->getTagsList()}

<form method="post" action="/admin/tags/save">
    {foreach $tags as $tag}
        <input type="text" value="{$tag.name}" name="tag[{$tag.id}]">
    {/foreach}
    <button class="btn btn-primary" type="submit">Сохранить</button>
</form>
