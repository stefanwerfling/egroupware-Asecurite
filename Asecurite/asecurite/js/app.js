function push_toggle_all(name){    var all_set = true;    /* this is for use with a sub-grid. To use it pass "true" as third parameter */    if(push_toggle_all.arguments.length > 2 && push_toggle_all.arguments[2] == true)    {        el = eTemplate.getElementsByTagName("input");        for (var i = 0; i < el.length; i++)        {            if(el[i].name.substr(el[i].name.length-12,el[i].name.length) == '[checkbox][]' && el[i].checked)            {                all_set = false;                break;            }        }        for (var i = 0; i < el.length; i++)        {            if(el[i].name.substr(el[i].name.length-12,el[i].name.length) == '[checkbox][]')            {                el[i].checked = all_set;            }        }    }    else    {        var checkboxes = document.getElementsByName(name);        for (var i = 0; i < checkboxes.length; i++)        {            if (!checkboxes[i].checked)            {                all_set = false;                break;            }        }        for (var i = 0; i < checkboxes.length; i++)        {            checkboxes[i].checked = !all_set;        }    }}function js_btn_book_selected(){    elmt = '';    el = eTemplate.getElementsByTagName("input");    for (var i = 0; i < el.length; i++)    {        if(el[i].name.substr(el[i].name.length-12,el[i].name.length) == '[checkbox][]' && el[i].checked)        {            if(elmt.length > 0)            {                elmt += ',';            }            elmt += 'r' + el[i].value;        }    }    if(elmt.length == 0)    {        alert("Aucune case n'a été cochée");        return false;    }    return elmt;}function open_popup(url, width, height){    var left = parseInt((screen.availWidth/2) - (width/2));    var top = parseInt((screen.availHeight/2) - (height/2));    var windowFeatures = "width=" + width + ",height=" + height + ",status,resizable,scrollbars=yes,left=" + left + ",top=" + top + "screenX=" + left + ",screenY=" + top;    myWindow = window.open(url, "subWind", windowFeatures);}function ajax_request(link){    var xhr;    try {        xhr = new ActiveXObject('Msxml2.XMLHTTP');    }    catch (e)    {        try {            xhr = new ActiveXObject('Microsoft.XMLHTTP');        }        catch (e2)        {            try {                xhr = new XMLHttpRequest();            }            catch (e3) {                xhr = false;            }        }    }    xhr.onreadystatechange  = function()    {        if(xhr.readyState  == 4)        {            if(xhr.status  == 200)                document.ajax.dyn="Received:"  + xhr.responseText;            else                document.ajax.dyn="Error code " + xhr.status;        }    };    xhr.open("GET", link,  true);    xhr.send(null);}function disable_enable_fin_contrat(){    if(document.eTemplate.elements['exec[type_contrat]'].value == 'CDI'){        document.getElementById("exec[date_fin_contrat][str]").type = "hidden";    }else{        document.getElementById("exec[date_fin_contrat][str]").type = "text";    }}function is_date_debut_sup_date_fin(){    if(document.eTemplate.elements['exec[date_debut_contrat]'].value != '' && document.eTemplate.elements['exec[date_fin_contrat]'].value != ''){    }}function check_add_horaire(type){    if(type == 'global'){        if(document.eTemplate.elements['exec[idasecurite_ville]'].value == ''){            alert("Veuillez choisir une ville.");            return false;        }    }    if(type == 'agent' || type == 'global' || type == 'ville'){        if(document.eTemplate.elements['exec[idasecurite_site]'].value == ''){            alert("Veuillez choisir un site.");            return false;        }    }    if(type == 'site' || type == 'global' || type == 'ville'){        if(document.eTemplate.elements['exec[idasecurite_agent]'].value == ''){            alert("Veuillez choisir un agent.");            return false;        }    }    if(document.eTemplate.elements['exec[heure_arrivee][str]'].value == ''){        alert("Veuillez indiquer le jour d'arrivée.");        return false;    }else if(document.eTemplate.elements['exec[heure_depart][str]'].value == ''){        alert("Veuillez indiquer le jour de depart.");        return false;    }else if(document.eTemplate.elements['exec[heure_arrivee][str]'].value == document.eTemplate.elements['exec[heure_depart][str]'].value         && document.eTemplate.elements['exec[heure_arrivee][H]'].value == document.eTemplate.elements['exec[heure_depart][H]'].value        && document.eTemplate.elements['exec[heure_arrivee][i]'].value == document.eTemplate.elements['exec[heure_depart][i]'].value){        alert("L'heure d'arrivée doit être différente de l'heure de départ");        return false;    }else if(document.eTemplate.elements['exec[pause]'].value == ''){        alert('Veuillez indiquer le temps de pause.');        return false;    }    else if(document.eTemplate.elements['exec[idasecurite_site]'].value == ''){        alert('Veuillez choisir un site');        return false;    }    return true;}function check_change_planning(){    if(document.eTemplate.elements['exec[idasecurite_ville]'].value == ''){        alert("Veuillez choisir une ville.");        return false;    }else if(document.eTemplate.elements['exec[idasecurite_site]'].value == ''){        alert("Veuillez choisir un site.");        return false;    }else if(document.eTemplate.elements['exec[agent_from]'].value == ''){        alert("Veuillez choisir l'agent donneur.");        return false;    }else if(document.eTemplate.elements['exec[agent_to]'].value == ''){        alert("Veuillez choisir l'agent receveur.");        return false;    }    if(document.eTemplate.elements['exec[agent_from]'].value == document.eTemplate.elements['exec[agent_to]'].value){        alert("Veuillez choisir deux agents différents.");        return false;    }    return true;}function check_for_print(type){    if(document.eTemplate.elements['exec[mois]'].value == 0){        alert("Veuillez choisir un mois.");        return false;    }else if(document.eTemplate.elements['exec[annee]'].value == 0){        alert("Veuillez choisir une année.");        return false;    }    if(type == 'global'){        if(document.eTemplate.elements['exec[idasecurite_ville]'].value == ''){            alert("Veuillez choisir une ville.");            return false;        }    }    return true;}function changeToogleText(){    if(document.getElementById('toggle').innerHTML == 'Afficher les statistiques'){        document.getElementById('toggle').innerHTML = 'Cacher les statistiques';    }else if(document.getElementById('toggle').innerHTML == 'Cacher les statistiques'){        document.getElementById('toggle').innerHTML = 'Afficher les statistiques';    }}function check_all(){     var checkedLength = $('input[name*="checkbox"]:checked').length;    if(checkedLength != 0){        $('input[name*="checkbox"]').removeAttr('checked')    }else{        $('input[name*="checkbox"]').attr('checked', true);    }  }function getChecked(){        var checkedLength = $('input[name*="checkbox"]:checked').length;    if(checkedLength == 0){        alert("Aucune case n'a été cochée");        return false;    }else{        var ids = '';        $('input[name*="checkbox"]:checked').each(function(){            ids += $(this).attr('value') +'-';        });        return  ids.substr(0, ids.length-1) ;    }  }function initHighlight(oTable){    $('td', oTable.fnGetNodes()).hover( function() {        var iCol = $('td').index(this) % 5;        var nTrs = oTable.fnGetNodes();        $('td:nth-child('+(iCol+1)+')', nTrs).addClass( 'highlighted' );    }, function() {        $('td.highlighted', oTable.fnGetNodes()).removeClass('highlighted');    } );}function deleteElement(id, confirmMsg, deleteFunction, redirectLink){    if(confirm(confirmMsg)){        $.ajax({            type:"POST",            url:deleteFunction,            data:"id="+id,            success:function(){                document.location.href=redirectLink+"&msg="+"La suppression a été effectuée avec succès&save=success";            }        });    }}function displayPopin(link, width, height, titleBar){     $('#dialog').remove();    $('body').append('<div id="dialog"><div>');          $('#dialog').dialog({        open: function(){            $(this).html('<iframe id="TB_iframeContent" src="' + link + '" width="' + width + '" height="' + height + '"></iframe>');        },        title: titleBar,        modal: true,        close : $(this).text(''),           width : width+40,        height : height+50,        resize : function (){            $('#TB_iframeContent').css('height', $(this).height()-10 +'px').css('width', $(this).width()-10 +'px');        }    });}function closePopin(){    $('#dialog').dialog('close');    return false;}function loadSitePlanningData(myaction){    if(!myaction) myaction = form.action.replace(/.+myaction=/,'');    var mois = $('#exec\\\[mois\\\]').attr('value');    var annee = $('#exec\\\[annee\\\]').attr('value');     var agent = $('#exec\\\[idasecurite_agent\\\]').attr('value');     xajax_doXMLHTTP(myaction,mois, annee, agent);}