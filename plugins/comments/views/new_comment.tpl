<form id="feedback_form" method="post" action="/feedback/index/new">
    
	<input type="text" name="name" placeholder="Ваше имя" value="{$data.author|default:''}" />
	{if $user_data->role=='admin'}
	    <input type="text" id="date" name="date" placeholder="Дата" value="{$data.datetime|date_format:'%d.%m.%Y'}"/>
	{/if}
	
	{if !isset($params.objectid)}
		<input type="text" name="from" placeholder="Из какого вы города" />
		<input type="text" name="age" placeholder="Сколько вам лет" />
	{/if}
	<br />
	Отзыв:<br />
	<textarea name="text">{$data.text|default:''}</textarea>
</form>



<script>
    $(document).ready(function(){
	$('#date').datepicker();
    })
</script>