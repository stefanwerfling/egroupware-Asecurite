OPF = {};
OPF.utils  = {};
OPF.logger = {};

OPF.logger.captureKeys = function(e){
    if (typeof e == "undefined") {
        e = window.event;
    }
    
    if (e.ctrlKey && e.keyCode == 32) {
        var element = document.getElementById("OPFLog");
        element.style.top = window.scrollY + 50;
        OPF.logger.displayToggle(element);                  
    }
};

OPF.logger.displayToggle = function(element){
    var element = document.getElementById("OPFLog");
    OPF.utils.displayToggle(element);
};

OPF.logger.toggleDisplayBT = function(id){
    var element = document.getElementById(id);
    OPF.utils.displayToggle(element);
};

OPF.utils.displayToggle = function(element){
    var display = OPF.utils.getStyle(element,"display");
            
    if(display != "none"){
        element.style.displayPrev = display;
        element.style.display = "none";             
    }
    else{
        if(typeof element.style.displayPrev == "undefined"){
            element.style.displayPrev = "block";
        }
        element.style.display = element.style.displayPrev;
    }
};

OPF.utils.getStyle = function(element,styleProp){         
    if (element.currentStyle)
        var style = element.currentStyle[styleProp];
    else if (window.getComputedStyle)
        var style = document.defaultView.getComputedStyle(element,null).getPropertyValue(styleProp);
    return style;
};

OPF.utils.addEvent = function(element,type,delegate){
    if(element){
        if(element.attachEvent)
            element.attachEvent ("on"+type,delegate);  

        else if(element.addEventListener)
            element.addEventListener (type,delegate,false);  
           
        else
            element["on"+_type] = delegate;  
                  
    }
};
        
OPF.utils.addEvent(document,"keydown",OPF.logger.captureKeys);
