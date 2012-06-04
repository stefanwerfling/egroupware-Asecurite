
function check_all(){    
    var all_set = true;
    var el = form.getElementsByTagName("input");
    var l = el.length;
    for(var i=0; i<l; i++){
        if(el[i].name.length >= 8){
            if(el[i].name.substr(0, 8) == 'checkbox' && el[i].checked){
                all_set = false;
                break;
            }
        }
    }
    for (var i = 0; i < l; i++)
    {
        if(el[i].name.length >= 8){
            if(el[i].name.substr(0, 8) == 'checkbox')
            {
                el[i].checked = all_set;
            }
        }
    }
}

function getChecked(){
    var el = form.getElementsByTagName("input");
    var l = el.length;
    var ids = '';
    for(var i=0; i<l; i++){
        if(el[i].name.length >= 8){
            if(el[i].name.substr(0, 8) == 'checkbox' && el[i].checked){
                ids += el[i].value + '-';
            }
        }
    }
    if(ids == ''){        
        return false;
    }else{
        return  ids.substr(0, ids.length-1) ;
    }
}

function initHighlight(oTable){
    $('td', oTable.fnGetNodes()).hover( function() {
        var iCol = $('td').index(this) % 5;
        var nTrs = oTable.fnGetNodes();
        $('td:nth-child('+(iCol+1)+')', nTrs).addClass( 'highlighted' );
    }, function() {
        $('td.highlighted', oTable.fnGetNodes()).removeClass('highlighted');
    } );
}

function deleteElement(id, confirmMsg, deleteFunction, redirectLink){
    if(confirm(confirmMsg)){
        $.ajax({
            type:"POST",
            url:deleteFunction,
            data:"id="+id,
            success:function(){
                document.location.href=redirectLink+"&msg="+"La suppression a été effectuée avec succès&save=success";
            }
        });
    }
}



