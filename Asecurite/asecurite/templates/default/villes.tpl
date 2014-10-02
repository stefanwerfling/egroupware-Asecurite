<!-- BEGIN villes -->
<div id="addButton"><button onclick="egw_openWindowCentered2('{ADD_LINK}', '_blank', {WIDTH}, {HEIGHT}, 'yes');
        return false;">Ajouter une nouvelle ville</button></div>
<div class="ex_highlight">
    <div id="dynamic">
        <form name="form" id="form">
            <div>{MSG}</div>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="tableContent">
                <thead>
                    <tr>
                        <th width="3%">Id</th>
                        <th width="90%">Ville</th>
                        <th>Opérations</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>               
            </table>
            <div style="float: right; vertical-align: top;"><a href="javascript:void(0)" id="delete">{DELETE_BUTTON}</a><span style="cursor: pointer">{SELECT_ALL}</span></div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var oTable;
        $('#delete').click(function () {
            var checkedId = getChecked(); // in app.js
            if (checkedId) {
                if (confirm("Voulez vous supprimer les villes sélectionnées")) {
                    $.ajax({
                        type: "POST",
                        url: "{DELETE_LINK}",
                        data: "id=" + checkedId,
                        success: function () {
                            document.location.href = "{INDEX_LINK}&msg=" + "La suppression a été effectuée avec succès&save=success";
                        }
                    });
                }
            } else {
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
    {"mDataProp": "idasecurite_ville" },
    {"mDataProp": "nom" },
    {"mDataProp": "operation"}

            ],
            "aoColumnDefs": [
    {"bSortable": false, "aTargets": [2]},
    {"asSorting": ["desc"], "aTargets": [0]},
    {"bVisible": false, "aTargets": [0]}
            ],
            "language": {
                "url": "{BASE_URL}/js/assets/datatables-plugins/i18n/French.lang"
            },
            "iDisplayLength": 10,
            "aLengthMenu": [[10, 30, 50, 100, 200, -1], [10, 30, 50, 100, 200, "Tout"]]
        });
    });
</script>
<!-- END villes -->