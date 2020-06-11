<script>
    meta = {
        showEditor: function() {
            
            if (nothSkipOpen) {
                return false;
                nothSkipOpen = false;
            }
            
            cms.http.post('/meta/index/tags', {
                url: document.URL
            }, function(r){
                
                $('#meta_editor_title').val(r.title);
                $('#meta_editor_description').val(r.description);
                $('#meta_editor_keywords').val(r.keywords);
                
                
            });
            
            $('#metaEditorBlock').slideToggle();
        },
        
        hideEditor: function() {
            $('#metaEditorBlock').slideUp();
        },
        
        
        save: function() {
            
            cms.http.post('/meta/index/save', {
                title: $('#meta_editor_title').val(),
                description: $('#meta_editor_description').val(),
                keywords: $('#meta_editor_keywords').val(),
                url: document.URL
            }, function(){
                cms.info.show('Сохранено');
                meta.hideEditor();
            });
            
        }
    };
    nothSkipOpen = false;
    $(function(){
        $( "#meta_editor_badge" ).draggable({
            axis: "x",
            stop: function( event, ui ) {
                nothSkipOpen = true;
                localStorage.setItem('notchLeft',ui.offset.left);
                event.stopPropagation();
            }
        });        
    });
</script>


<style>
#meta_editor_badge {
    position: fixed;
    top: 0;
    left: 50%;
    width: 124px;
    height: 23px;
    background-image: url(/plugins/meta/public/meta_badge.png);
    color: #fff;
    text-align: center;
    margin-left: -72px;
    cursor: pointer;
    z-index: 20;
}

#metaEditorBlock {
    position: fixed;
    height: 0px;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 300px;
    display: none;
    z-index: 30;
    background-color: #fff;
    padding: 15px;
}
</style>

<div id="meta_editor_badge" onclick="meta.showEditor();">META</div>                      



<div id="metaEditorBlock">
    
    <div class="row">
        <div class="col-lg-12">
            <label>Заголовок страницы</label>
            <input type="text" class="form-control" id="meta_editor_title" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <label>Meta description</label>
            <input type="text" class="form-control" id="meta_editor_description" />
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <label>Meta keywords</label>
            <input type="text" class="form-control" id="meta_editor_keywords" />
        </div>
    </div>

    <div class="row" style="padding-top: 30px;">
        
        <div class="col-lg-12">
            <button class="btn btn-success" onclick="meta.save();">Сохранить</button>
            <button class="btn" onclick="meta.hideEditor();">Отмена</button>
        </div>
        
    </div>
    
</div>


<script>
    $(function(){
        
        var l = localStorage.getItem('notchLeft');
        
        if (l) {
            setTimeout(function(){
                $('#meta_editor_badge').css('left',localStorage.getItem('notchLeft')+'px');
            },200);
        }
    })
</script>