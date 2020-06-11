<script>
	current_controller = '{$controller}';
	current_action = '{$action}';
	current_plugin = '{$module}';
	base_url = '{$config->url->base}';
	dialog_type = '{$config->dialog_type|default:'jquery'}';
        debug_mode = {if $config->debug->on}true{else}false{/if};
        
        if (typeof cms === 'object') {
            cms.url.base = '{$config->url->base}';
            
            cms.zend.controller = '{$controller}';
            cms.zend.action = '{$action}';
            cms.zend.plugin = '{$module}';
        }

	params = {
        };
	{foreach $params as $key=>$value}
		{if !in_array($key,array('module','controller','action')) && !is_array($value)}
			params['{$key}'] = '{$value}';
		{/if}
	{/foreach}
</script>
