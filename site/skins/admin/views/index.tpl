<html>
    <head>
        {include file="{$config->path->base}cms/views/head_block.tpl"}
        <style>
            .parserstate {
                position: fixed;
                right: 20px;
                top: 60px;
                border: 1px solid #999;
                padding: 10px;
                background-color: #fff;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="parserstate on">
            парсер 
            {if file_exists("parser_on")} 
                работает<br />
                <button class='btn btn-primary' onclick="location.href='/admin/parser/off';"> выключить</button>
            {else}
                отключен<br />
                <button class='btn btn-dark' onclick="location.href='/admin/parser/on';">включить</button>
            {/if}
        </div>
        
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <a class="navbar-brand" href="/">{$smarty.server.SERVER_NAME|capitalize}</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="/">На сайт</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link active" href="/admin">Новеллы</a>
                        </li>
                        
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              Перевод
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="/admin/translate">Пользовательский</a>
                                
                                <a class="dropdown-item" href="/admin/translators">Сервисы переводчики</a>
                                <a class="dropdown-item" href="/admin/translate/errors">Ошибки</a>
                            </div>
                        </li>                            
                        
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/news">Новости</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="/users/admin/list">Пользователи</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/settings">Настройки</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/admin/comments">Комментарии</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/admin/interkassa">Interkassa</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/admin/tags">Тэги</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="window.open('/admin/translate/viewlog', '_blank', 'width=1000, height=600');return false;">Лог парсера</a>
                        </li>
                        
                        
                    </ul>
                </div>
            </nav>

            {site::getContent()}
        </div>
{*        
        <link rel="stylesheet" href="/cms/public/bootstrap4/css/bootstrap.min.css" />
        <script src="/cms/public/bootstrap4/js/bootstrap.min.js"></script>
        <script src="/cms/public/js/bsDialog.js"></script>
*}        
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
        <script src="/site/skins/admin/public/js/script.js?r=2"></script>
        
        <script src="/site/skins/admin/public/tags/bootstrap-tagsinput.js"></script>
        
        <link rel="stylesheet" href="/site/skins/admin/public/tags/bootstrap-tagsinput.css" />
    </body>
</html>