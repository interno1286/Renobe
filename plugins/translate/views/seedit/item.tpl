<form enctype="multipart/form-data" method="post">
    <table id="language_fields">
        <tr>
            <td>Код элемента:</td>
            <td>
                <input type="text" name="code" id="item_code" value="" />
            </td>
        <tr>

        <tr>
            <td>Перевод на текущем языке:</td>
            <td>
                <input type="text" name="txt" id="item_txt" value="" />
                <input type="hidden" name="lng" id="item_lng" value="{$lng}" />
            </td>
        <tr>
    </table>
</form>