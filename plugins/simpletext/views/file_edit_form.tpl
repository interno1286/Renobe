<div class="alert">Выберите новый файл чтобы загрузить его вместо текущего</div>

<form action="/simpletext/file/edit" method="post" enctype="multipart/form-data" id="editFileForm">
    
    <input type="file" name="file" />
    <input type="hidden" name="name" value="{$params.name}" />
    
</form>