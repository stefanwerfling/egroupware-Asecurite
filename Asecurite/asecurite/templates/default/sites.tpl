<!-- BEGIN sites -->
<script type="text/javascript">
    $(document).ready(function() {
        var oTable;
        $('#delete').click(function() {
            var checkedId = getChecked(); // in app.js
            if (checkedId) {
                if (confirm("Voulez vous supprimer les sites sélectionnés")) {
                    $.ajax({
                        type: "POST",
                        url: "{DELETE_LINK}",
                        data: "id=" + checkedId,
                        success: function() {
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
    {"mDataProp": "idasecurite_site" },
    {"mDataProp": "nom" },
    {"mDataProp": "adresse"},
    {"mDataProp": "code_postal"},
    {"mDataProp": "idasecurite_ville"},
    {"mDataProp": "telephone"},
    {"mDataProp": "operation"}

            ],
            "aoColumnDefs": [
    {"bSortable": false, "aTargets": [6]},
    {"asSorting": ["desc"], "aTargets": [0]},
    {"bVisible": false, "aTargets": [0]}
            ],
            "oLanguage": {
                "sProcessing": "Traitement en cours...",
                "sSearch": "Rechercher&nbsp;:",
                "sLengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
                "sInfo": "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
                "sInfoEmpty": "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
                "sInfoFiltered": "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
                "sInfoPostFix": "",
                "sLoadingRecords": "Chargement en cours...",
                "sZeroRecords": "Aucun &eacute;l&eacute;ment &agrave; afficher",
                "sEmptyTable": "Aucune donnée disponible dans le tableau",
                "oPaginate": {
                    "sFirst": "Premier",
                    "sPrevious": "Pr&eacute;c&eacute;dent",
                    "sNext": "Suivant",
                    "sLast": "Dernier"
                },
                "oAria": {
                    "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                    "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
                }
            },
            "iDisplayLength": 30,
            "aLengthMenu": [[30, 50, 100, 200, -1], [30, 50, 100, 200, "Tout"]]
        });

        //  initHighlight(oTable);

    });

</script>
<div id="addButton"><button onclick="egw_openWindowCentered2('{ADD_LINK}', '_blank', {WIDTH}, {HEIGHT}, 'yes');
        return false;">Ajouter un nouveau site</button></div>
<div class="ex_highlight">
    <div id="dynamic">
        <form name="form" id="form">
            <div>{MSG}</div>

            <table cellpadding="0" cellspacing="0" border="0" class="display" id="tableContent">
                <thead>
                    <tr>
                        <th width="3%">Id</th>
                        <th width="30%">Nom de site</th>
                        <th width="30%">Adresse</th>
                        <th width="10%">Code postal</th>
                        <th width="10%">Ville</th>
                        <th width="10%">Téléphone</th>
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
<!-- END sites -->