<header>
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container">
                    <div class="navbar-header"> 

                        <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button"> 

                            <span class="sr-only">Toggle navigation</span> 
                            <span class="icon-bar"></span> 
                            <span class="icon-bar"></span> 
                            <span class="icon-bar"></span> 
                        </button> 

                        <a href="/" class="navbar-brand"> {$smarty.server.SERVER_NAME|capitalize}<span class="hidden-xs"></a> 
                        {if $user_data->role=='admin'}
                        <a href="/admin">админка</a>
                        {/if}
                    </div>

                    <div class="collapse navbar-collapse" id="navbar">
                        <script>
                            function searchParams() {
                                if ($('.stype').length>0) {
                                    var s = [];
                                    
                                    $('.stype.active').each((ind,el)=>{
                                        
                                        s.push($(el).attr('type'));
                                    });
                                    
                                    $('#stypes').val(s.join('|'));
                                }
                            }
                        </script>
                        <form 
                            class="navbar-form navbar-left" 
                            role="search" 
                            autocomplete="false"
                            action="/search"
                            onsubmit="return searchParams();"
                        >
                            
                            <div class="form-group" id="novel-typeahead"> 
                                <input value="{$params.t|default:''|htmlentities}" name="t" id="novel-search" type="text" class="form-control typeahead" placeholder="Поиск новелл" aria-describedby="novel-search-prefix" spellcheck="false" autocomplete="false">
                            </div>
                            <input type="hidden" id="stypes" name="types" value="n"/>
                        </form>
                            
                        <ul class="nav navbar-nav navbar-right">

                            
                            <li>
                                <a href="#" class="chapter-display-options" onclick="site.settings.dialog();return false;">
                                    <span class="glyphicon glyphicon-cog"></span>&nbsp;<span class="hidden-sm">Настройки</span>
                                </a>
                            </li>
                            
                            {if $user_data->id}
                                <script type="text/javascript">
                                    function onLoad() {
                                        gapi.load('auth2', function () {
                                            gapi.auth2.init();
                                        });
                                    }
                                </script>
                                <script src="https://apis.google.com/js/platform.js?onload=onLoad" async defer></script>

                                <li class="hidden-md hidden-sm"> <a href="/users/profile">{$user_data->email}</a> </li>
                                <li class="hidden-md hidden-sm"> <a href="#" onclick="site.login.logout();">Выход</a> </li>
                            {else}
                                <li class="hidden-md hidden-sm"> <a href="#" onclick="site.register.modal();">Регистрация</a> </li>
                                <li class="hidden-md hidden-sm"> <a href="#" onclick="site.login.modal();">Вход</a> </li>
                            {/if}

                            <li class="dropdown visible-md visible-sm">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <span class="glyphicon glyphicon-user"></span>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    {if $user_data->id}
                                        <li><a href="/users/profile">{$user_data->email}</a></li>
                                        <li><a href="#" onclick="site.login.logout();">Выход</a></li>
                                    {else}
                                        <li><a href="#" onclick="site.register.modal();">Регистрация</a></li>
                                        <li><a href="#" onclick="site.login.modal();">Вход</a></li>
                                    {/if}
                                </ul>
                            </li>

                        </ul>

                        <ul class="nav navbar-nav visible-xs">
                            <li><a href="/novellas">Новеллы</a></li>
                            <li class="hidden-sm"><a href="/about">О Нас</a></li>
                            <li class="hidden-sm"><a href="/faq">FAQ</a></li>
                            <li class="dropdown visible-sm">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Site <span class="caret"></span></a> 
                                <ul class="dropdown-menu">
                                    <li><a href="/about">О нас</a></li>
                                    <li><a href="/faq">FAQ</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <nav class="navbar navbar-default hidden-xs">
                <div class="container">
                    <div class="collapse navbar-collapse" id="navbar-bottom">
                        <ul class="nav navbar-nav">
                            <li><a href="/novellas">Новеллы</a></li>
                            <li class="hidden-sm"><a href="/about">О нас</a></li>
                            <li class="hidden-sm"><a href="/faq">FAQ</a></li>
                            <li class="dropdown visible-sm">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Site <span class="caret"></span></a> 
                                <ul class="dropdown-menu">
                                    <li><a href="/about">О нас</a></li>
                                    <li><a href="/faq">FAQ</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>