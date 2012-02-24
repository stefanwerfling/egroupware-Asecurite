<script type="text/javascript">          
    $(".planning_site").flexigrid({
        url : 'LINK_TO_REPLACE',
        dataType : 'xml',
        colModel : [ {
                display : 'Agent',
                name : 'agent',
                width : 100,
                sortable : true,
                align : 'left'
            }, {
                display : 'Arrivée',
                name : 'heure_arrivee',
                width : 100,
                sortable : true,
                align : 'left'
            }, {
                display : 'Temps de pause',
                name : 'pause',
                width : 100,
                sortable : true,
                align : 'left'
            }, {
                display : 'Départ',
                name : 'heure_depart',
                width : 100,
                sortable : true,
                align : 'left'
//                hide : true
            }, {
                display : 'Nombre d\'heures',
                name : 'nombre_heures',
                width : 100,
                sortable : true,
                align : 'right'
            
            }, {
                display : 'Paniers',
                name : 'panier',
                width : 40,
                sortable : true,
                align : 'right'
            
            }, {
                display : 'Nombre d\'heures jour',
                name : 'heures_jour',
                width : 100,
                sortable : true,
                align : 'right'
            }, {
                display : 'Nombre d\'heures nuit',
                name : 'heures_nuit',
                width : 100,
                sortable : true,
                align : 'right'
            }, {
                display : 'Nombre d\'heures jour dimanche',
                name : 'heures_jour_dimanche',
                width : 100,
                sortable : true,
                align : 'right'
           
            }, {
                display : 'Nombre d\'heures nuit dimanche',
                name : 'heures_nuit_dimanche',
                width : 100,
                sortable : true,
                align : 'right'
            } ],
        buttons : [ {
                name : 'Add',
                bclass : 'add',
                onpress : test
            }, {
                name : 'Delete',
                bclass : 'delete',
                onpress : test
            }, {
                separator : true
            } ],
        searchitems : [ {
                display : 'Agent',
                name : 'agent',
                isdefault : true
            }],
        sortname : "Agent",
        sortorder : "asc",
        usepager : true,
        title : 'Planning sites',
        useRp : true,
        rp : 15,
        showTableToggleBtn : true,
        width : 1024,
        height : 300
    });
    function test(com, grid) {
        if (com == 'Delete') {
            confirm('Delete ' + $('.trSelected', grid).length + ' items?');
            var sele = $('.trSelected', grid)[0].id;
            alert(sele);
        } else if (com == 'Add') {
            alert('Add New Item');
        }
    }
   
</script>

