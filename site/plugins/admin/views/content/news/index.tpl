<h4>Новости</h4>
<button class="btn btn-primary" onclick="site.news.add();">Добавить</button>
<style>
    .news_item {
        margin-bottom: 20px;
        border-bottom: 1px solid #CCC;
        padding-bottom: 10px;
    }
</style>
<br /><br />
{foreach ormModel::getInstance('newsModel')->getNews() as $n}
    <div class="news_item">
        <h4>{$n.header}</h4>
        {$n.description}
        <br />
        <div class="float-right">{$n.create_date|date_format:'%d.%m.%Y'}</div>
        <br />
        <button class="btn btn-info btn-sm " onclick="site.news.edit({$n.id},this);">редактировать</button>
        <button class="btn btn-info btn-sm "  onclick="site.news.del({$n.id},this);">удалить</button>
    </div>
{/foreach}

<script src="/cms/public/js/ckeditor/ckeditor.js"></script>
<script src="/cms/public/js/ckeditor/adapters/jquery.js"></script>

