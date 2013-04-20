<!-- BEGIN planning_agents -->
<script type="text/javascript">
    $(document).ready(function() {
        var oTable;
        $('#delete').click(function() {            
            var checkedId = getChecked(); // in app.js
            if(checkedId){
                if(confirm("Voulez vous supprimer les plannings sélectionnés")){
                    $.ajax({
                        type: "POST",
                        url: "DELETE_LINK",
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
                {"mDataProp": "idasecurite_horaires_agent" },
                {"mDataProp": "heure_arrivee"},
                {"mDataProp": "pause"},
                {"mDataProp": "heure_depart"},
                {"mDataProp": "nombre_heures"},
                {"mDataProp": "panier"},
                {"mDataProp": "heures_jour" },
                {"mDataProp": "heures_nuit"},
                {"mDataProp": "heures_jour_dimanche"},
                {"mDataProp": "heures_nuit_dimanche"},
                {"mDataProp": "site"},
                {"mDataProp": "ville"},
                {"mDataProp": "operation"}
                    
            ],
             "aoColumnDefs": [
                { "bSortable": false, "aTargets": [ 12 ] },
                { "asSorting": [ "asc" ], "aTargets": [ 0 ] },
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
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="tableContent">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th width="10%">Arrivée</th>
                        <th width="10%">Pause</th>
                        <th width="10%">Départ</th>
                        <th width="10%">Nombre d'heures</th>
                        <th width="5%">Paniers</th>
                        <th width="15%">Heure jour</th>
                        <th width="15%">Heure nuit</th>
                        <th width="15%">Heure jour dimanche</th>
                        <th width="15%">Heure nuit dimanche</th>
                        <th width="10%">Site</th>
                        <th width="10%">Ville</th>
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
<!-- END planning_agents -->