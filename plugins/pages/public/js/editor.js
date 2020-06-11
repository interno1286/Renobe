function pageTPLEdit() {
    var content_tpl = $('#content_tpl').val();
    var w = window.open('/pages/index/edittpl/tpl/'+btoa(content_tpl),'Редактор HTML','width=1000, height=400');
    $(w).focus();
}

