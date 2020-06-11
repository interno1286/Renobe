{if sizeof($main_menu_elements)>0}
    <style>
        #pages_admin {
            position: absolute;
            top: 0px;
            left: 0px;
            margin-left: 20%;
            z-index: 10000;
        }
        
        
        #pages_admin .nav a::after {
            background-image: none !important;
        }

    </style>

    {*
    <div class="dropdown" id="pages_admin">
      <a class="dropdown-toggle" data-toggle="dropdown" href="#">Dropdown trigger</a>
      <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
            {foreach $main_menu_elements as $elem}
                <li onclick="{$elem.onclick}"><a href="#">{$elem.name}</a></li>
            {/foreach}
      </ul>
    </div>
    *}

    <nav class="navbar navbar-default" role="navigation" id="pages_admin">
      <div class="container-fluid">
          
            <ul class="nav navbar-nav">
            {foreach $main_menu_elements as $elem}
                <li><a href="#" onclick="{$elem.onclick}">{$elem.name}</a></li>
            {/foreach}
            </ul>
          
      </div>
    </nav>

    {*
    <div id="pages_admin">

        <div class="btn-group btn-group-vertical">
            {foreach $main_menu_elements as $elem}
                <button class="btn btn-large btn-primary" onclick="{$elem.onclick}">{$elem.name}</button>
            {/foreach}
        </div>	
    </div>
    *}		

    <script>

        $(document).ready(function(){

            $('#pages_admin').draggable({
                stop: function(event, ui) {
                $.cookie("main_menu_top", $(this).css('top'));
                $.cookie("main_menu_left", $(this).css('left'));
                }
            }).css('position','fixed');

            if ($.cookie("main_menu_top"))
                $("#pages_admin").css( { "left" : $.cookie("main_menu_left"), "top" : $.cookie("main_menu_top") });
        });
    </script>
{/if}