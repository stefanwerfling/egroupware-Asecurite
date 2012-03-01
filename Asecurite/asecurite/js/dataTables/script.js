function push_toggle_all(name)
{
    alert('rrr');
    var all_set = true;
    /* this is for use with a sub-grid. To use it pass "true" as third parameter */
    if(push_toggle_all.arguments.length > 2 && push_toggle_all.arguments[2] == true)
    {
        el = eTemplate.getElementsByTagName("input");
        for (var i = 0; i < el.length; i++)
        {
            if(el[i].name.substr(el[i].name.length-12,el[i].name.length) == '[checkbox][]' && el[i].checked)
            {
                all_set = false;
                break;
            }
        }
        for (var i = 0; i < el.length; i++)
        {
            if(el[i].name.substr(el[i].name.length-12,el[i].name.length) == '[checkbox][]')
            {
                el[i].checked = all_set;
            }
        }
    }
    else
    {
        var checkboxes = document.getElementsByName(name);
        for (var i = 0; i < checkboxes.length; i++)
        {
            if (!checkboxes[i].checked)
            {
                all_set = false;
                break;
            }
        }
        for (var i = 0; i < checkboxes.length; i++)
        {
            checkboxes[i].checked = !all_set;
        }
    }
}