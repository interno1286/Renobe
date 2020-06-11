<h1>Новости</h1>
<style>
    .nitem {
        border-bottom: 1px solid #BBB;
        margin-bottom: 20px;
    }
</style>
<div class="news">

{foreach ormModel::getInstance('newsModel')->getNews() as $n}
    <div class="nitem">
        <h2><a href="/news/{tools_string::translit({$n.header})}/{$n.id}">{$n.header}</a></h2>
        
        {$n.description}
        
        <div class="pull-right">{$n.create_date|date_format:'%d.%m.%Y'}</div>
    </div>
{/foreach}
</div>