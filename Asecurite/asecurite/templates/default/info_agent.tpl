<!-- BEGIN info_agent -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="fr" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Fiche agent</title>
    </head>
    <style type="text/css">
        body{
            font-family: Arial,Helvetica,sans-serif;
            background-color: #D8D8D8 ; 
            font-size: 12px;
        }
        .agent_info{            
            margin-left: 15px;
            border: solid 1px #909090 ;
        }

        #info{
            margin: 5px;
        }

        #contrat{
            margin: 5px;
        }

        #piece_id{
            margin: 5px; 
        }
        
    </style>
    <body>

        <div class="agent_info">
            <center><div><h4>Fiche d'information</h4></div></center>
            <div id="info">
                <div><b>Agent:</b> {agent_name}</div>
                <div><b>Date de naissance:</b>  {date_naissance}</div>
                <div><b>Adresse:</b>  {adresse}</div>
                <div><b>Téléphone: </b> {telephone}</div>
            </div>
            <div id="contrat">
                <div><b>Type de contrat:</b>  {type_contrat}</div>
                <div><b>Date de début du contrat:</b>  {date_debut_contrat}</div>
                <div><b>Date de fin du contrat:</b>  {date_fin_contrat}</div>
            </div>
            <div id="piece_id">
                <div><b>Type: </b> {type_piece_identite}</div>
                <div><b>Numéro: </b> {numero_piece_identite}</div>
                <div><b>Date de début de validité: </b> {date_debut_piece_identite}</div>
                <div><b>Date de fin de validité: </b> {date_fin_piece_identite}</div>
                <div><b>Commune/Préfecture:</b>  {commune_piece_identite}</div>
                <div><b>Pays: </b> {pays_piece_identite}</div>
            </div> 
            <div><center><button onclick="window.print();">Imprimer</button></center></div>
        </div>
    </body>
</htm>
<!-- END info_agent -->