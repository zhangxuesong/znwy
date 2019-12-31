var version = +new Date();
var myconfig = {
    path: '../addons/rhinfo_zyxq/static/web/',
    alias: {
        'layer': '../lib/layer3.0.3/layer',
		'jquery.contextmenu': '../lib/jquery.contextmenu/jquery.contextmenu.r2'
    },
    map: {
        'js': '.js?v=' + version,
        'css': '.css?v=' + version
    },
    css: {

    }
    , preload: ['jquery']
}

var myrequire = function (arr, callback) {
    var newarr = [];
    $.each(arr, function () {
        var js = this;
        if (myconfig.css[js]) {
            var css = myconfig.css[js].split(',');
            $.each(css, function () {
                newarr.push("css!" + myconfig.path + this + myconfig.map['css']);
            });
        }

        var jsitem = this;
        if (myconfig.alias[js]) {
            jsitem = myconfig.alias[js];
        }
        newarr.push(myconfig.path + jsitem + myconfig.map['js']);
    });
    require(newarr, callback);
}
