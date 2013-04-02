this.imagePreview = function(){	
    /* CONFIG */
		
    var xOffset = 10;
    var yOffset = 30;
		
    // these 2 variable determine popup's distance from the cursor
    // you might want to adjust to get the right result
		
    /* END CONFIG */   
    var pos ;
    var winHeight;
    var pTop ;
    var returnHeight ;
    $("span.tooltip").hover(function(e){
        pos = $(this).offset();
        winHeight = $(window).height();
        pTop = pos.top - $(window).scrollTop();
        
        this.t = this.title;
        this.title = "";	
        var c = (this.t != "") ? "<br/>" + this.t : "";
        $("body").append("<p id='preview'><img src='"+ $(this).attr('rel') +"' alt='Image preview' />"+ c +"</p>");
        $("#preview")       
        .css("top",(e.pageY - xOffset) + "px")
        .css("left",(e.pageX + yOffset) + "px")
        .fadeIn("fast");
        
        returnHeight = $('#preview').height();
        if(returnHeight + pTop > winHeight) {            
            $("#preview").css({
                left: (e.pageX + yOffset) + 'px',
                top: pos.top - (returnHeight - winHeight + pTop + 20) + 'px'
            });
        }
    },
    function(){
        this.title = this.t;	
        $("#preview").remove();
    });	
    $("span.tooltip").mousemove(function(e){
        $("#preview")
        .css("top",(e.pageY - xOffset) + "px")
        .css("left",(e.pageX + yOffset) + "px");
         if(returnHeight + pTop > winHeight) {            
            $("#preview").css({
                left: (e.pageX + yOffset) + 'px',
                top: pos.top - (returnHeight - winHeight + pTop + 20) + 'px'
            });
        }
        
    });
};


// starting the script on page load
$(document).ready(function(){
    imagePreview();
});