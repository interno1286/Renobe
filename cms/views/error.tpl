<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<head>
    <title>Ошибка {$error_status}</title>
    
    <link type="text/css" rel="stylesheet" href="{$config->url->base}cms/public/bootstrap/css/datepicker.css" />
    <link type="text/css" rel="stylesheet" href="{$config->url->base}cms/public/css/jquery/smoothness/jquery-ui.custom.min.css" />


    <script type="text/javascript" src="/cms/public/js/jquery/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="/cms/public/js/jquery/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/cms/public/js/jquery/jquery.ui.datepicker-ru.js"></script>
    <script type="text/javascript" src="/cms/public/js/bsDialog.js"></script>
    <script type="text/javascript" src="/cms/public/bootstrap3/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/cms/public/bootstrap/js/bootstrap-datepicker.js"></script>
    <link type="text/css" rel="stylesheet" href="{$config->url->base}cms/public/bootstrap3/css/bootstrap.min.css" />
    <link type="text/css" rel="stylesheet" href="{$config->url->base}cms/public/css/main.css" />

    <script src="/cms/public/js/cms.js"></script>
    <script src="/cms/public/js/main.js"></script>
  
</head>
<body>
    
    {if $not_found==="1" && $user_data->user_type==='admin'}
        <div class="container">
            <div class="jumbotron">
                <h1>Страница не найдена!</h1>
                <p>Не беда! Можно её создать.</p>
                <p>
                    <a class="btn btn-primary btn-lg" href="#" onclick="createPage('{$_SERVER['REQUEST_URI']}');return false;" role="button">Создать страницу {$_SERVER['REQUEST_URI']}</a>
                </p>
                


                {if isset($newFiles)}
                    <h4>Используя шаблон</h4>
                    <ul style="list-style-type: lower-latin;">
                        {foreach $newFiles as $f}
                            <li><a href="#" onclick="use_file='{$f}';createPage('{$_SERVER['REQUEST_URI']}');return false;">{$f}</li>
                        {/foreach}
                    </ul>
                {/if}
                
              
              
              <script>
                var new_page_path = '';
                
                use_file='';
                
                function createPage(path) {
                    new_page_path = path;
                    showLoadingProcessLayer('Создаю...');
                    
                    var data = {
                        content: '<div>Новая страница!</div>',
                        name: path,
                        description: '',
                        keywords: '',
                        path: path,
                        useFile: use_file
                    };

                    cms.http.post('/pages/index/save',data,function(){
                        if (!window.parent)
                            location.href="{$config->url->base}skineditor/customize/frame/fr/1";
                        else
                            location.href=new_page_path;
                    });
                }
              </script>
              
                        
               <br />
               
               <a href='#' onclick="$('#addInfo').slideDown();return false;">Больше информации</a>
              <div style="display: none;" id='addInfo'>
                {$content}
              </div>
              
            </div>            
        </div>
    {else}
        {$content}
    {/if}
	
</body>
</html>

