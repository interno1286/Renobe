<!-- STYLES -->
{if isset($styles) && is_array($styles)}
	{foreach from=$styles item=item}
        {if !isset($item[0])}{continue}{/if}
		<link type="text/css" rel="stylesheet" href="{if $item[0]!='/' && substr($item,0,4)!='http'}{$config->url->site}public/css/{/if}{$item}{if isset($config->refresh_css) && $config->refresh_css}{if strpos($item,'?')!==false}&{else}?{/if}t={$smarty.now}{/if}" />
	{/foreach}
{/if}
{*
{if file_exists("`$conf.path.skin`/public/css/style.css")}
	<link type="text/css" rel="stylesheet" href="{$config->url->skin}public/css/style.css" />
{/if}
*}
<!-- SCRIPTS -->
{if isset($scripts)}
	{foreach from=$scripts item=item}
        {if !isset($item[0])}{continue}{/if}
		<script type="text/javascript" src="{if $item[0]=='/' or substr($item,0,4)=='http'}{$item}{else}{$config->url->site}public/js/{$item}{/if}{if isset($config->refresh_script) && $config->refresh_script}{if strpos($item,'?')!==false}&{else}?{/if}t={$smarty.now}{/if}"></script>
	{/foreach}
{/if}

<script src="{$config->url->base}cms/public/js/cms.js{if isset($config->refresh_script) && $config->refresh_script}{if strpos($item,'?')!==false}&{else}?{/if}t={$smarty.now}{/if}"></script>


{if $config->bootstrap3}
    <script>
    cms.framework.bootstrap = 3;
    </script>
{/if}
    
{if $config->bootstrap4}
    <script>
    cms.framework.bootstrap = 4;
    cms.zend.controller = '{$controller}';
    cms.zend.plugin = '{$plugin}';
    cms.zend.action = '{$action}';
    </script>    
    
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">    

{/if}
    
