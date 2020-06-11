{*
<style>
widgetbot {
    position: fixed;
    bottom: 0px;
    right:  98px;
    z-index: 3000;
    width: 350px;
    height: 500px;
}

.wbHidden {
    display: none !important;
}

#wbButton {
    background-image: url(/site/skins/main/public/cross.png);
    background-repeat: no-repeat;
    background-position: center center;
    background-size: 33px;
    position: fixed;
    right: 10px;
    bottom: 10px;
    width: 58px;
    height: 58px;
    background-color: #c7b73f;
    border-radius: 100%;    
    cursor: pointer;
}

.chatShow {
    background-image: url(/site/skins/main/public/chat.png) !important;
}
</style>

<widgetbot
  class="wbHidden"
  server="647109886055940096"
  channel="647109886055940099"
  width="800"
  height="600"
  shard="https://disweb.dashflo.net"
></widgetbot>

<script src="https://cdn.jsdelivr.net/npm/@widgetbot/html-embed"></script>

<div id="wbButton" class="chatShow" onclick="chatToggle();"></div>


<script>
    function chatToggle() {
        if ($('widgetbot').is(':visible')) {
            $('widgetbot').addClass('wbHidden');
            $('#wbButton').addClass('chatShow');
        }else {
            $('widgetbot').removeClass('wbHidden');
            $('#wbButton').removeClass('chatShow');
        }
    }
</script>

*}