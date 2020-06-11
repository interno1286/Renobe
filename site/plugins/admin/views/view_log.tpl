{if file_exists("site/system/sync_log.txt")}
    {$data=file_get_contents("site/system/sync_log.txt")}

    <pre>
        {$data}
    </pre>

    <script>
        window.scrollTo(0,document.body.scrollHeight);

        setTimeout(()=>{
            location.reload();
        },3000);
    </script>
{else}    
    <h2>Лог файл отсутствует. Проверьте - запущен ли парсер.</h2>
{/if}