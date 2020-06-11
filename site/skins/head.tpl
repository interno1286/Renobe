        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{meta::get('title')|default:$meta_title|default:$smarty.server.SERVER_NAME}</title>
        <meta name="description" content="{meta::get('description')|default:$meta_description|default:'ранобе новеллы читать онлайн'}">
        <meta name="keywords" content="{meta::get('keywords')|default:$meta_keywords|default:'ранобе новеллы читать онлайн'}">

        <meta name="yandex-verification" content="4249c15c07bfcc10" />

        <meta property="og:title" content="{meta::get('title')|default:$meta_title|default:$smarty.server.SERVER_NAME}" />
        <meta property="og:description" content="{meta::get('description')|default:$meta_description|default:'ранобе новеллы читать онлайн'}" />
        <meta name="twitter:title" content="{meta::get('title')|default:$meta_title|default:$smarty.server.SERVER_NAME}" />
        <link rel="icon" href="/favicon.ico">
        <!--[if lt IE 9]> 
        <script src="https:/oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js" defer async></script> 
        <script src="https:/oss.maxcdn.com/respond/1.4.2/respond.min.js" defer async></script> 
        <![endif]--> 
        <script src="/cms/public/js/jquery/jquery-1.10.2.min.js"></script>
        <meta name="google-signin-client_id" content="748671317329-dpiqlhrvbvth26m6rsmtg6hnk9793m9d.apps.googleusercontent.com">
        <script id="chatBroEmbedCode">
                function ChatbroLoader(chats, async){
                                async=!1!==async;
                                var params = {
                                        embedChatsParameters:chats instanceof Array?chats:[chats],
                                        lang:navigator.language||navigator.userLanguage,
                                        needLoadCode:'undefined'==typeof Chatbro,
                                        embedParamsVersion:localStorage.embedParamsVersion,
                                        chatbroScriptVersion:localStorage.chatbroScriptVersion
                                },
                                xhr=new XMLHttpRequest;
                                xhr.withCredentials=!0,
                                xhr.onload=function(){
                                        eval(xhr.responseText)
                                },
                                xhr.onerror=function(){
                                        console.error('Chatbro loading error')
                                },
                                xhr.open('GET','//www.chatbro.com/embed.js?'+btoa(unescape(encodeURIComponent(JSON.stringify(params)))),async),
                                        xhr.send()
                }
                ChatbroLoader({
                        encodedChatId: '44m8d'
                });
        </script>
