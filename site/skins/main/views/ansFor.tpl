{if isset($data.id)}
    <div class="inansfor">
        <div class="text">
            {*
            {if $data.in_ans_for}
                {showAnsFor comment=$data}
            {/if}
            *}
            <div class="author">{$data.author|default:'нд'}</div>
            {$data.text|default:'нд'}
        </div>
    </div>
{/if}