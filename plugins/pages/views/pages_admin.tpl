{*
<style>
	#pages_admin {
		position: absolute;
		top: 50px;
		left: 0px;
		height: 200px;
		width: 150px;
	}
	
	#pages_admin button {
		width: 270px;
		margin-left: -3px;
	}
</style>


<div id="pages_admin">
	
	<div class="btn-group btn-group-vertical">
		<button class="btn btn-large btn-primary" onclick="editPage(undefined,'{$smarty.server.REQUEST_URI|base64_encode}');">Создать страницу</button>
		
		{if isset($params.page_id)}
			<button class="btn btn-large btn-danger" onclick="editPage({$params.page_id});">Редактировать эту страницу</button>
		{/if}
		
		<button class="btn btn-large btn-info">Список страниц</button>
	</div>	
</div>
*}		
{*		
<script src="/plugins/pages/public/js/editor.js"></script>		
<script>
	function editPage(id,preset_url) {
        
        var w = parseInt($(window).width()*0.8);
        var h = parseInt($(window).height()*0.9);
        
		var params = "menubar=no,location=no,resizable=yes,scrollbars=no,status=no,height="+h+",width="+w;
		window.open("http://"+location.host+'/pages/index/edit'+((typeof id!=='undefined')? '/id/'+id: '')+((typeof preset_url!=='undefined') ? '/url/'+preset_url : ''), "Редактор страниц", params);
	}
	
	
	function editSkin() {
        
        var w = parseInt($(window).width()*0.8);
        var h = parseInt($(window).height()*0.9);
        
		var params = "menubar=no,location=no,resizable=yes,scrollbars=no,status=no,height="+h+",width="+w;
		window.open("http://"+location.host+'/pages/index/editskin', "Редактор страниц", params);
	}
	
    function rollbackPage(id) {
        if (confirm('Вы действительно хотите откатить последние изменения на этой странице?'))
            document.location.href='/pages/index/rollback/id/'+id;
    }
*}    
{*	
	$(document).ready(function(){
		$(document).on('scroll',function(){
			$('#pages_admin').css('top',(parseInt($(document).scrollTop())+50)+'px');
		});
	});

</script>
*}