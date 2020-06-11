<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        {include file="`$config->path->root`cms/views/head_block.tpl"}

        <script language="Javascript" type="text/javascript" src="/plugins/skineditor/public/edit_area/edit_area_full.js"></script>

        <link rel="stylesheet" type="text/css" href="/cms/public/bootstrap3/css/bootstrap-theme.css" />    

        <script src="/plugins/pages/public/js/admin.js"></script>
        {*
        <script src="/cms/public/js/jquery/rangyinputs-jquery-1.1.2.min.js"></script>
        *}

{*        
        <script src="/plugins/pages/public/js/CodeMirror-master/lib/codemirror.js"></script>
        <link rel="stylesheet" href="/plugins/pages/public/js/CodeMirror-master/lib/codemirror.css" />
        <link rel="stylesheet" href="/plugins/pages/public/js/CodeMirror-master/addon/dialog/dialog.css" />

        <script src="/plugins/pages/public/js/CodeMirror-master/mode/xml/xml.js"></script>	
        <script src="/plugins/pages/public/js/CodeMirror-master/mode/htmlmixed/htmlmixed.js"></script>	
        <script src="/plugins/pages/public/js/CodeMirror-master/addon/search/search.js"></script>	
        <script src="/plugins/pages/public/js/CodeMirror-master/addon/search/searchcursor.js"></script>	
        <script src="/plugins/pages/public/js/CodeMirror-master/addon/dialog/dialog.js"></script>
*}

        
        <style>
            textarea {
                width: 100%;
                height: 400px;
                font-size: 12px;
                background-color: #000;
                color: #FF0;
            }

            #replace input[type=text] {
                width: 250px;
                font-size: 12px;
            }

            #preview {
                width: 100%;
                height: 100%;
                border: 0;
            }


            #preview_container {
                width: 100%;
                height: 20px;
            }


            #resizer {
                border: 2px dotted #999;
                width: 100%;
                height: 11px;
                cursor: n-resize;
                /* margin-top: 10px; */
                background-color: #F3F3F3;                
            }

            body {
                margin: 0px;
            }
        </style>

    </head>
    <body>
        <form id="edit_page_form" onsubmit="return false;">

            <nav class="navbar navbar-default navbar-form" role="navigation">
                <div class="container-fluid">
                    <div class="navbar-header">
            

                        <div class="form-group">
                            {if $action=='edit'}
                                URL:
                                <input class="form-control" placeholder="URL: (типа /page1.html)" type="text" name="path" id="path" value="{if isset($page_data.path)}{$page_data.path}{else}{$params.url|default:''|base64_decode}{/if}" />
                                Заголовок:
                                <input class="form-control" placeholder="Название"  type="text" id="name" name="name" value="{$page_data.name|default:''}" />
                                Meta Description:
                                <input  class="form-control" placeholder="meta description" type="text" id="description" name="description" value="{$page_data.description|default:''}" />
                                Meta keywords:
                                <input class="form-control" placeholder="meta keywords" type="text" id="keywords" name="keywords" value="{$page_data.keywords|default:''}" />
                            {/if}

                            Тема: <select name="skin" class="form-control">
                                <option value="">skin по умолчанию</option>
                                {foreach $skins as $skin}
                                    <option value="{$skin}" {if isset($page_data.skin) && $page_data.skin==$skin}selected="selected"{/if}>{$skin}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="navbar-header">
               
                        <button class="btn btn-danger navbar-btn" onclick="editor.undo();
                            return false;">отмена</button>
                        <button class="btn btn-info navbar-btn" onclick="editor.redo();
                            return false;">повтор</button>

                        <button class="btn btn-inverse navbar-btn" onclick="$('#replace').toggle();
                            return false;">замена</button>
                        <button class="btn btn-inverse navbar-btn" onclick="insertST();">simpleText</button>
                        <button class="btn btn-inverse navbar-btn" onclick="insertSI();">simpleImage</button>
                        <button class="btn btn-inverse navbar-btn" onclick="insertAppendix();">pageAppendix</button>
                        <button class="btn btn-inverse navbar-btn" onclick="insertRemoveAppendix();">removeAppendix</button>

                        <button class="btn btn-danger navbar-btn" onclick="save({$page_data.id|default:''});">сохранить</button>
                        <button class="btn btn-info navbar-btn" onclick="preview({$page_data.id|default:''});">предпросмотр</button>
                        {if $action=='edit' && isset($page_data.id) && $page_data.id}				
                            <button class="btn btn-primary navbar-btn" onclick="rollback({$page_data.id|default:''});">отменить изменения</button>
                        {/if}
                    </div>
                
                </div>
            </nav>
            

            <div id="replace" style="display:none;">
                <input type="text" id="rep_from" placeholder="что заменить" /> → <input type="text" placeholder="на что заменить" id="rep_to" />

                <button class="btn" onclick="replace();">☭</button>
            </div>
            <textarea name="content" id="content">{$page_data.content|htmlentities_utf8|default:''}</textarea>

            <div id="resizer">&nbsp;</div>
        </form>


        <div id="preview_container">
            <iframe src="{if isset($page_data.id) && $page_data.id}http://{$smarty.server.SERVER_NAME}{$page_data.path}{/if}" id="preview"></iframe>
        </div>


        <form id="edit_page_form_source" style="display:none;">
            <input type="hidden" name="path" value="{$page_data.path|default:''}" />
            <input type="hidden" name="name" value="{$page_data.name|default:''}" />
            <input type="hidden" name="skin" value="{$page_data.skin|default:''}" />
            <textarea style="display:none;" name="content">{$page_data.content|default:''|htmlentities}</textarea>
        </form>

        <script>
            var editor;

            var do_resize = false;
            var resize_start = 0;
            var coef = 0.5;

            $(document).ready(function() {
                
                // initialisation
                editAreaLoader.init({
                    id: "content"	// id of the textarea to transform		
                    ,start_highlight: true	// if start with highlight
                    ,allow_resize: "both"
                    ,allow_toggle: false
                    ,word_wrap: false
                    ,language: "ru"
                    ,syntax: "html_smarty"
                    ,plugins: "saver"
                });
                
                
                {*
                editor = CodeMirror.fromTextArea(document.getElementById("content"), {
                    lineNumbers: true,
                    indentWithTabs: true,
                    mode: "htmlmixed"
                });
*}
                setTimeout(resze,2000);

                $(window).on('resize', function() {
                    resizeWindow();
                });

                //$('#preview_container, .cm-s-default').resizable();

                $('#resizer')
                        .on('mousedown', function(e) {
                            do_resize = true;
                            $('body').disableSelection();
                            $('#preview').hide();
                        });

                $(document).on('mousemove', function(e) {

                    if (do_resize) {
                        var current_top = e.pageY;

                        coef = getCoef(current_top);

                        resze();
                    };
                    

                }).on('mouseup', function(e) {
                    do_resize = false;
                    $('#preview').show();
                });
                
                resizeWindow();

            });

        </script>


    </body>
</html>