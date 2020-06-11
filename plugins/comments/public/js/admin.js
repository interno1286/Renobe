
celem = null;

function removeComment(id,elem) {
    celem = elem;
	if (confirm('Вы действительно хотите удалить этот комментарий?')) {
		cms.http.post({
                    url: '/comments/index/remove/id/'+id,
                    success: ()=>{
                        
			$(celem).parent().parent().remove();
                    }
                });
	}
}
