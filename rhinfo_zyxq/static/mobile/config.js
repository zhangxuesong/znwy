var version = +new Date();
require.config({
    urlArgs: 'v=' + version, 
    baseUrl: '../addons/rhinfo_zyxq/static/mobile/js',
    paths: {
        'jquery': '../../lib/jquery/jquery-1.11.1.min',
        'jquery.gcjs': '../../lib/jquery/jquery.gcjs',
        'tpl':'../../lib/tmodjs/tmodjs',
        'rhui':'../../lib/rhui/js/rhui.min',
        'rhui.picker':'../../lib/rhui/js/rhui.picker.min',
		'rhui.citydata':'../../lib/rhui/js/rhui.citydata.min',
		'rhui.citydatanew':'../../lib/rhui/js/rhui.citydatanew.min',
        'jquery.qrcode':'../../lib/jquery/jquery.qrcode.min',
        'ydb':'../../lib/Ydb/YdbOnline',
        'swiper':'../../lib/swiper/swiper.min',
		'jquery.fly': '../../lib/jquery/jquery.fly',
		'mui':'../../lib/rhui/js/mui.min'
    },
    shim: {
        'rhui':{
            deps:['jquery']
        },
        'rhui.picker': {
            exports: "rhui",
            deps: ['rhui','rhui.citydata']
        },
		'jquery.gcjs': {
	        deps:['jquery']
		},
		'jquery.fly': {
			deps:['jquery']
		}
    },
    waitSeconds: 0
});
