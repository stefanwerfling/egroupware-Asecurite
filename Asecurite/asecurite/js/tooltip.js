$(function()
    {        
        var hideDelay = 100;
        var hideTimer = null;
        // One instance that's reused to show info for the current segment
        var container = $('<div id="popupContainer">'
            + '<div id="popupContent"></div>'
            + '</div>');

        $('body').append(container);

        $('.popupTrigger').live('mouseover', function()
        {  // format of 'rel' tag: pageid,segmentguid
           
            var link = $(this).attr('url');           
            // If no guid in url rel tag, don't popup blank
         
            if (hideTimer)
                clearTimeout(hideTimer);

            var pos = $(this).offset();
            var width = $(this).width();     
            
            container.css({
                left: (pos.left + width) + 'px',
                top: pos.top - 5 + 'px'
            });
                       
            var winHeight = $(window).height();
            var pTop = pos.top - $(window).scrollTop();           
            
            $('#popupContent').html('&nbsp;');
            $.ajax({
                type: 'GET',
                url: link,                
                success: function(data)
                {
                    // Verify that we're pointed to a page that returned the expected results.
                    if (data.indexOf('popupResult') < 0)
                    {
                        $('#popupContent').html('<span >Not a valid return value</span>');
                    }

                    // Verify requested segment is this segment since we could have multiple ajax
                    // requests out if the server is taking a while.
                    if (data.indexOf('popupResult') > 0)
                    {                      
                        $('#popupContent').html(data);
                        var returnHeight = $('#popupContent').height();
                        if(returnHeight + pTop > winHeight) {
                            container.css({
                                left: (pos.left + width) + 'px',
                                top: pos.top - (returnHeight - winHeight + pTop +15) + 'px'
                            });
                        }
                    }
                }
            });
            
            container.css('display', 'block');
        });



        $('.popupTrigger').live('mouseout', function()
        {  
            if (hideTimer)
                clearTimeout(hideTimer);
            hideTimer = setTimeout(function()
            {
                container.css('display', 'none');
            }, hideDelay);
        });
        // Allow mouse over of details without hiding details
        $('#popupContainer').mouseover(function()
        {
            if (hideTimer)
                clearTimeout(hideTimer);
        });

        // Hide after mouseout
        $('#popupContainer').mouseout(function()
        {
            if (hideTimer)
                clearTimeout(hideTimer);
            hideTimer = setTimeout(function()
            {
                container.css('display', 'none');
            }, hideDelay);
        });
    });
