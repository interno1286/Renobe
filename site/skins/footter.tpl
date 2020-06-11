        <footer>
            <div class="footer">
                <div class="container">
                    <div style="display: table; width: 100%;">
                        <div class="footer-left hidden-xs">
                            <h3>{$smarty.server.SERVER_NAME|capitalize} <small>&copy; 2019</small></h3>
                            <ul>
                                <li><a href="/">Главная</a></li>
                                <li><a href="/faq">Faq</a></li>
                                <li><a href="/about">О нас</a></li>
                            </ul>
                            {literal}
                            <!--LiveInternet counter-->
                            <script type="text/javascript">
                            document.write('<a href="//www.liveinternet.ru/click" '+
                            'target="_blank"><img src="//counter.yadro.ru/hit?t19.6;r'+
                            escape(document.referrer)+((typeof(screen)=='undefined')?'':
                            ';s'+screen.width+'*'+screen.height+'*'+(screen.colorDepth?
                            screen.colorDepth:screen.pixelDepth))+';u'+escape(document.URL)+
                            ';h'+escape(document.title.substring(0,150))+';'+Math.random()+
                            '" alt="" title="LiveInternet: показано число просмотров за 24'+
                            ' часа, посетителей за 24 часа и за сегодня" '+
                            'border="0" width="88" height="31"><\/a>')
                            </script>
                            <!--/LiveInternet-->                            
                            {/literal}
                        </div>
                            
                    </div>
                </div>
            </div>
        </footer>
                            {*
        <script src="/site/skins/main/public/assets/js/app-ddf2cc5dd6.js"></script> 
        *}
        <link href="/cms/public/bootstrap3/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="/cms/public/css/main.css" />
        <link rel="stylesheet" href="/site/skins/main/public/css/style.css">
        <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet" />
        <link href="/site/skins/main/public/font/droidsans/droidserif.css" rel="stylesheet" />
        
        <link class="settings font" href="/site/skins/main/public/css/settings/font/{$smarty.session.font|default:'roboto'}.css" rel="stylesheet" />
        <link class="settings colorSchema" href="/site/skins/main/public/css/settings/colorSchema/{$smarty.session.colorSchema|default:'dark'}.css?r=2" rel="stylesheet" />
        <link class="settings fontSize" href="/site/skins/main/public/css/settings/fontSize/{$smarty.session.fontSize|default:'auto'}.css" rel="stylesheet" />
        
        <script src="/cms/public/js/bsDialog.js" async="async"></script>
        <script src="/cms/public/js/main.js" async="async"></script>
        
        <script src="/site/skins/main/public/js/script.js?r=2" async="async"></script>
        <script src="/cms/public/bootstrap3/js/bootstrap.min.js" async="async"></script>
        <script src="/cms/public/js/cms.js"></script>
        
        <script>
            cms.framework.bootstrap=3;
        </script>

        {literal}
        <!-- Yandex.Metrika counter -->
        <script type="text/javascript" >
            (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
                    m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

            ym(56452648, "init", {
                clickmap:true,
                trackLinks:true,
                accurateTrackBounce:true,
                webvisor:true
            });
        </script>
        <noscript><div><img src="https://mc.yandex.ru/watch/56452648" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
        <!-- /Yandex.Metrika counter -->
        {/literal}
