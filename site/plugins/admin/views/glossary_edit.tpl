{$glossary=ormModel::getInstance('public','glossary')->getAll("novella_id=`$params.id`")}
<style>
    #glossary_table button {
        margin-left: 10px;
    }
</style>
<table id="glossary_table" class="table table-striped table-hover">
{foreach $glossary as $g}
    <tr>
        <td>
            <input placeholder="Оригинал" type="text" class="form-control" name="original" value="{$g.original}" />
        </td>
        
        <td>
            <input placeholder="Перевод" type="text" class="form-control" name="translate" value="{$g.translate}" />
        </td>
        
        <td>
            <button class="btn btn-primary" onclick="site.novella.glossary.save(this, {$g.id});">сохранить</button>
            <button class="btn btn-danger" onclick="site.novella.glossary.del(this, {$g.id});">удалить</button>
        </td>
    </tr>
{/foreach}

    <tr>
        <td>
            <input placeholder="Оригинал" type="text" class="form-control" name="original" />
        </td>
        
        <td>
            <input placeholder="Перевод" type="text" class="form-control" name="translate" />
        </td>
        
        <td>
            <button class="btn btn-primary" onclick="site.novella.glossary.save(this);">сохранить</button>
            
        </td>
        
    </tr>

</table>