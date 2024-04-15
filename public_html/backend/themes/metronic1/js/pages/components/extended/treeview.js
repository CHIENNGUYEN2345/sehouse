"use strict";

var KTTreeview = function () {
    var demo3 = function () {
        $('#kt_tree_3').jstree({
            'plugins': ["wholerow", "checkbox", "types"],
            'core': {
                "themes" : {
                    "responsive": false
                },
                'data': [{
                        "text": "Same but with checkboxes",
                        "children": [{
                            "text": "initially selected",
                            "state": {
                                "selected": true
                            }
                        }, {
                            "text": "custom icon",
                            "icon": "fa fa-warning kt-font-danger"
                        }, {
                            "text": "initially open",
                            "icon" : "fa fa-folder kt-font-default",
                            "state": {
                                "opened": true
                            },
                            "children": ["Another node"]
                        }, {
                            "text": "custom icon",
                            "icon": "fa fa-warning kt-font-waring"
                        }, {
                            "text": "disabled node",
                            "icon": "fa fa-check kt-font-success",
                            "state": {
                                "disabled": true
                            }
                        }]
                    },
                    "And wholerow selection"
                ]
            },
            "types" : {
                "default" : {
                    "icon" : "fa fa-folder kt-font-warning"
                },
                "file" : {
                    "icon" : "fa fa-file  kt-font-warning"
                }
            },
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            demo3();
        }
    };
}();

jQuery(document).ready(function() {    
    KTTreeview.init();
});