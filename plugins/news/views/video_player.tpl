<a
	 href="{$config->news_upload_video_path}/{$video_file}"
	 class="video_news_block"
	 id="news_player_{$use_uniq}">
</a>

<script>
	flowplayer("news_player_{$use_uniq}", "/plugins/news/public/flowplayer/flowplayer-3.2.16.swf",{
		clip:  {
			autoPlay: false,
			autoBuffering: false
		}
	});
</script>

