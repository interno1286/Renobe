<script>
	$(document).ready(function(){
		if (typeof CKEDITOR=='undefined') {
			var script = document.createElement( 'script' );
			script.type = 'text/javascript';
			script.src = '{$config->url->base}cms/public/js/ckeditor/ckeditor.js';
			document.body.appendChild(script);
		}
	});
</script>