<!-- BEGIN agents -->
<script type="text/javascript">
    $(document).ready(function() {
        var oTable;
        $('#delete').click(function() {            
            var checkedId = getChecked(); // in app.js
            if(checkedId){
                if(confirm("Voulez vous supprimer les agents sélectionnés")){
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
                {"mDataProp": "idasecurite_agent" },
                {"mDataProp": "nom" },
                {"mDataProp": "type_contrat"},
                {"mDataProp": "adresse"},
                {"mDataProp": "code_postal"},
                {"mDataProp": "idasecurite_ville"},
                {"mDataProp": "telephone"},
                {"mDataProp": "date_debut_contrat"},
                {"mDataProp": "date_fin_contrat"},
                {"mDataProp": "operation"}
                    
            ],
             "aoColumnDefs": [
                { "bSortable": false, "aTargets": [ 9 ] },
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
            <div style="float: right"><button onclick="egw_openWindowCentered2('{ADD_LINK}', '_blank', 450, 400, 'yes'); return false;">Ajouter un nouvel agent</button></div>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="tableContent">
                <thead>
                    <tr>
                        <th width="3%">Id</th>
                        <th width="15%">Agent</th>
                        <th width="10%">Type de contrat</th>
                        <th width="16%">Adresse</th>
                        <th width="10%">Code postal</th>
                        <th width="10%">Ville</th>
                        <th width="12%">Téléphone</th>
                        <th width="12%">Date de début de contrat</th>
                        <th width="12%">Date de fin de contrat</th>
                        <th>Opérations</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>               
            </table>
            <div style="float: right"><a href="javascript:void(0)" id="delete">{DELETE_BUTTON}</a><span style="cursor: pointer">{SELECT_ALL}</span></div>
        </form>
    </div>
</div>
<!-- END agents -->