simpleText = {
    
    lastCaller: null,
    lastParams: null,
    lastName: null,
    
    edit: function(name, params, elem, e) {
        
        if (e.ctrlKey) return true;
        
        simpleText.lastCaller = elem;
        simpleText.lastName   = name;
        simpleText.lastParams = params;
        e.stopPropagation();
        e.preventDefault();
        
        cms.dialog.show({
            url: '/simpletext/index/edit',
            title: 'Редактор текста',
            params: {
                name: name,
                config: params
            },
            
            buttons: {
                Сохранить: simpleText.saveData
            }
        });
    },
    
    
    saveData: function() {
        
        var p = JSON.parse(atob(simpleText.lastParams));
        
        var d = '';
        
        if (p.editor) {
            d = CKEDITOR.instances.st_edit_value.getData();
        }else {
            d = $('#st_edit_value').val();
        }
        
        $(simpleText.lastCaller).html(d);
        
        cms.dialog.hide();
        
        cms.http.post({
            url: '/simpletext/index/savedata',
            params: {
                name: simpleText.lastName,
                data: d,
                editor: p.editor
            },
            
            success: function(){}
        });
    }
};

$(function(){
    $('st.tt, .sttt').tooltip({
        container: "body"
    });
}); 

