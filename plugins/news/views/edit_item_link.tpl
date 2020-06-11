{if $edit_allowed}
    <div class="news_edit">
            <button class="btn btn-xs btn-info" onclick="seEdit('news', {$item.id},'index','news'); return false;">редактировать</button>
            <button class="btn btn-xs btn-danger" onclick="document.location.href='/news/index/do/act/del/id/{$item.id}';">удалить</button>
    </div>
{/if}