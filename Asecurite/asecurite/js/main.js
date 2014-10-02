requirejs.config({
    "baseUrl": baseUrl+"/js/assets",
    "urlArgs": "bust=" + (new Date()).getTime(),
    "paths": {
//        "metro":"metro-ui-css/min/metro.min",
        "app" : "../app",
        "jquery": "jquery/dist/jquery.min",
        "jquery-ui": "jquery-ui/jquery-ui.min",
        "angular": "angular/angular.min",
        "bootstrap": "bootstrap/dist/js/bootstrap.min",
        "datatables" : "datatables/media/js/jquery.dataTables.min",
        "datatables.bootstrap" : "datatables-bootstrap3/BS3/assets/js/datatables"
    }
});
require(["jquery", "jquery-ui", "angular", "datatables",
    "bootstrap", "datatables.bootstrap", "app"]);