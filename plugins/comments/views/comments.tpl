<script src="/plugins/comments/public/js/comments.js"></script>
<script src="/cms/public/js/jquery/jquery.scrollTo.min.js"></script>


<link type="text/css" rel="stylesheet" href="/plugins/comments/public/css/style.css" />

<div class="comment_block" id="comments_block_{$params.for}" style="border: 2px solid #434857; padding: 30px;">
    
    
    {foreach $comments as $c}
        <div class="comment">
            <div class="author">{$c.author}</div>
            <div class="city">{$c.from}</div>

            <div class="text">
                
                {showAnsFor comment=$c}
                
                {$c.text}

            </div>

            <div class="date">{$c.date|date_format:'%d'} {tools_dateTime::getCyrMonthName($c.date|date_format:'%m')} {$c.date|date_format:'%Y'}</div>
            
            <div class="links">
                {if $edit_allowed}
                    <a href="#" onclick="removeComment({$c.id},this);return false;">удалить</a>&nbsp;&nbsp;&nbsp;
                {/if}
                
                <a href="#" onclick="answer({$c.id},this,'{$params.for}');return false;">ответить</a>
            </div>
        </div>
    {/foreach}
    
    <div class="leave_comment">
        <form id="comment_form" action="/comments/index/new">
            <fieldset>
                <input type="text" name="name" class="input form-control" placeholder="Ваше имя" value="{$user_data->email|default:''}" />
                <input type="hidden" name="inansfor" id="ansfor" value="" />
                <input type="hidden" name="for" value="{$params.for}" />
                <div class="ansForText">
                    <div class="orig">
                        <div id="af_ans_text">
                            Вы отвечаете на комментарий от 
                        </div>
                        <div id="af_author"></div>
                    </div>
                    <div id="af_text">
                        
                    </div>
                </div>
                <textarea class="form-control" name="comment" placeholder="Ваше мнение"></textarea>
                <br /><br />
            </fieldset>
        </form>
    </div>
    
    <div>
        <input id="comment_button" class="btn btn-primary" type="button" value="{if $comments|@count==0}Комментариев пока нет. Будьте первым!{else}Оставить комментарий{/if}" onclick="leaveComment('{$params.for}',this);" />
        
    </div>
</div>
