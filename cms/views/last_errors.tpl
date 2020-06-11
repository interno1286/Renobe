{if isset($errors) && $errors && $this->user_data->role=='admin'}
    <style>
        .debug_last_errors {
            position: fixed;
            top: 0px;
            right: 0px;
            width: 400px;
            height: 200px;
            padding: 10px;
            border: 1px dashed #ccc;
        }
    </style>
    
    
    
    <div class="debug_last_errors">
        <h2>Последние ошибки</h2>
        {foreach $errors as $e}
            <a href="{$config->url->base}temp/error/{$e}">{$e|preg_replace:'#(_[a-f0-9]+\.html)#ui':''}</a>
        {/foreach}
    </div>
{/if}