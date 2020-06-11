<div class="container-fluid">
    
<div class="jumbotron">
    <h1>Установка завершена</h1>

    <h4>Добро пожаловать в GLENN CMS!</h4>
    
    {if is_dir('plugins/pages')}
    <p>
        <a class="btn btn-primary btn-lg" href="/pages/create" role="button">Создать страницу</a>
    </p>
        
    {/if}
    
    {if is_dir('plugins/skineditor')}
    <p>
        <a class="btn btn-primary btn-lg" href="/skineditor" role="button">Редактировать страницу</a>
    </p>
    {/if}
    
</div>    
    
</div>