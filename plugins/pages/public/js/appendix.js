function pagesAppendix(page_path,elem_to_copy,destination_element) {
    
    var data = {
      page_path: page_path,
      elem_to_copy: elem_to_copy,
      destination_element: destination_element
    };
   
    showLoadingProcessLayer('Добавляю...');
    
    sendPost('/pages/index/appendix',data,function(){
        document.location.reload();
    });
}

function removeAppendix(page_path,elem_id) {
    showLoadingProcessLayer('Удаляю...');
    var data = {
      page_path: page_path,
      elem_id: elem_id
    };
    
    sendPost('/pages/index/remappendix',data,function(){
        document.location.reload();
    });
}

function moveUp(page_path,elem_id) {
    var data = {
      page_path: page_path,
      elem_id: elem_id
    };
    
    sendPost('/pages/index/moveup',data,function(){
        document.location.reload();
    });
    
}


function moveDown(page_path,elem_id) {
    var data = {
      page_path: page_path,
      elem_id: elem_id
    };
    
    sendPost('/pages/index/movedown',data,function(){
        document.location.reload();
    });
    
}


function archiveAppendix(page_path,elem_id) {
    var data = {
      page_path: page_path,
      elem_id: elem_id
    };
    
    sendPost('/pages/index/archive',data,function(ret){
	document.location.reload();
    });
    
}