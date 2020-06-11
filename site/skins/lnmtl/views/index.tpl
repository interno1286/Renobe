<html>
<head>
    {include file="{$config->path->base}cms/views/head_block.tpl"}
</head>
<body>
    <header> 
        <nav class="navbar navbar-default navbar-fixed-top"> 
            <div class="container"> 
                <div class="navbar-header"> 
                    <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button"> 
                        <span class="sr-only">Toggle navigation</span> 
                        <span class="icon-bar"></span> 
                        <span class="icon-bar"></span> 
                        <span class="icon-bar"></span> 
                    </button> 
                    <a href="https://lnmtl.com" class="navbar-brand"> LNMTL<span class="hidden-xs"> - <strong>M</strong>achine <strong>T</strong>rans<strong>l</strong>ations</span> </a> 
                </div> 
                <div class="collapse navbar-collapse" id="navbar"> 
                    <form class="navbar-form navbar-left" role="search" autocomplete="false"> 
                        <div class="form-group" id="novel-typeahead"> 
                            <span class="twitter-typeahead" style="position: relative; display: inline-block;">
                                <input type="text" class="form-control typeahead tt-hint" aria-describedby="novel-search-prefix" spellcheck="false" autocomplete="off" readonly="" tabindex="-1" dir="ltr" style="position: absolute; top: 0px; left: 0px; border-color: transparent; box-shadow: none; opacity: 1; background: none 0% 0% / auto repeat scroll padding-box border-box rgb(67, 72, 87);">
                                <input id="novel-search" type="text" class="form-control typeahead tt-input" placeholder="Type novel's name" aria-describedby="novel-search-prefix" spellcheck="false" autocomplete="off" dir="auto" style="position: relative; vertical-align: top; background-color: transparent;"><pre aria-hidden="true" style="position: absolute; visibility: hidden; white-space: pre; font-family: Roboto, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant: normal; font-weight: 400; word-spacing: 0px; letter-spacing: 0px; text-indent: 0px; text-rendering: auto; text-transform: none;"></pre><div class="tt-menu" style="position: absolute; top: 100%; left: 0px; z-index: 100; display: none;"><div class="tt-dataset tt-dataset-novel"></div></div></span> </div> </form> <ul class="nav navbar-nav navbar-right"> <li id="js-toggle-discordbot"> <a href="#" style="color: green;"><i class="fab fa-discord"></i> <span class="hidden-sm hidden-md">Chat</span></a> </li> <li> <a href="#" class="chapter-display-options"><span class="glyphicon glyphicon-cog"></span> <span class="hidden-sm">Settings</span></a> </li>  <li class="hidden-md hidden-sm"> <a href="https://lnmtl.com/auth/register">Register</a> </li> <li class="hidden-md hidden-sm"> <a href="https://lnmtl.com/auth/login">Login</a> </li> <li class="dropdown visible-md visible-sm"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> <span class="caret"></span></a> <ul class="dropdown-menu"> <li> <a href="https://lnmtl.com/auth/register">Register</a> </li> <li> <a href="https://lnmtl.com/auth/login">Login</a> </li> </ul> </li>  </ul> <ul class="nav navbar-nav visible-xs"> <li><a href="https://lnmtl.com/novel">Novels</a></li> <li class="hidden-sm"><a href="https://lnmtl.com/about">About</a></li> <li class="hidden-sm"><a href="https://lnmtl.com/faq">FAQ</a></li> <li class="dropdown visible-sm"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Site <span class="caret"></span></a> <ul class="dropdown-menu"> <li><a href="https://lnmtl.com/about">About</a></li> <li><a href="https://lnmtl.com/faq">FAQ</a></li> </ul> </li>   </ul> </div> </div> </nav> <nav class="navbar navbar-default hidden-xs"> <div class="container"> <div class="collapse navbar-collapse" id="navbar-bottom"> <ul class="nav navbar-nav"> <li><a href="https://lnmtl.com/novel">Novels</a></li> <li class="hidden-sm"><a href="https://lnmtl.com/about">About</a></li> <li class="hidden-sm"><a href="https://lnmtl.com/faq">FAQ</a></li> <li class="dropdown visible-sm"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Site <span class="caret"></span></a> 
                                                    <ul class="dropdown-menu"> 
                                                        <li><a href="https://lnmtl.com/about">About</a></li> 
                                                        <li><a href="https://lnmtl.com/faq">FAQ</a></li> 
                                                    </ul> 
                                                </li>   
                                            </ul> 
                                        </div> 
                                    </div> 
                                </nav> 
    </header>    
{site::getContent()}
</body>
</html>