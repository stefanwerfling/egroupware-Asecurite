<!-- BEGIN feries -->
<script type="text/javascript">
    $(document).ready(function() {
        var oTable;
        $('#delete').click(function() {            
            var checkedId = getChecked(); // in app.js
            if(checkedId){
                if(confirm("Voulez vous supprimer les jours sélectionnés")){
                    $.ajax({
                        type: "POST",
                        url: "{DELETE_LINK}",
                        data: "id="+checkedId,
                        success: function(){
                            document.location.href= "{INDEX_LINK}&msg="+"La suppression a été effectuée avec succès&save=success";
                        }
                    } );
                }
            }else{
                alert("Aucune ligne n'a été cochée");
            }
        });
                                
        oTable = $('#tableContent').dataTable({
            "bJQueryUI": true,
            "bSortClasses": false,
            "sPaginationType": "full_numbers",
            "bProcessing": true,
            "sAjaxSource": "{DATA_LINK}",
            "aoColumns": [
                {"mDataProp": "idasecurite_ferie" },
                {"mDataProp": "jour" },
                {"mDataProp": "description"},
                {"mDataProp": "operation"}
                    
            ],
             "aoColumnDefs": [
                { "bSortable": false, "aTargets": [ 3 ] },
                { "asSorting": [ "desc" ], "aTargets": [ 0 ] },
                { "bVisible": false, "aTargets": [ 0 ] }
            ] 
        });
        
      //  initHighlight(oTable);
       
    } );
</script>
<div class="ex_highlight">
    <div id="dynamic">
        <form name="form" id="form">
            <div>{MSG}</div>
            <div style="float: right"><button onclick="egw_openWindowCentered2('{ADD_LINK}', '_blank', {WIDTH}, {HEIGHT}, 'yes'); return false;">Ajouter un nouvel agent</button></div>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="tableContent">
                <thead>
                    <tr>
                        <th width="3%">Id</th>
                        <th width="15%">Jour</th>
                        <th width="30%">Description</th>
                        <th width="5%">Opérations</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>               
            </table>
            <div style="float: right"><a href="javascript:void(0)" id="delete">{DELETE_BUTTON}</a><span style="cursor: pointer">{SELECT_ALL}</span></div>
        </form>
    </div>
</div>
<!-- END feries -->