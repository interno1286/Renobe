<br/><br/>
<h4>Комментарии</h4>
<br/><br/>
{$comments=ormModel::getInstance('commentsModel')->getAll("")}

{foreach $comments as $comment}
    <div class="comment_item">
        <h4>{$comment.text}</h4>
        {$chapter_id=$comment.for|replace:'chapter':''}
        {$novella_info=ormModel::getInstance('chaptersModel')->getNovellaByChapter($chapter_id)}
        {$chapter_info=ormModel::getInstance('chaptersModel')->getRow("id=`$chapter_id`")}
        <a href="/chapter/{tools_string::translit($chapter_info.name_ru)}/{$chapter_id}" class="chapter-link">
            {if trim($chapter_info.name_ru," -\r\n")}
                {$chapter_info.name_ru}
            {else}
                <span class="badge chapter-badge">Глава {$chapter_info.number_parsed|default:$chapter_info.number}</span>{$chapter_info.name_ru}
            {/if}
        </a>
        <br/>
        <div class="float-right">{$comment.date|date_format:'%d.%m.%Y'}</div>
        <br/>
    </div>
    <hr>
{/foreach}
