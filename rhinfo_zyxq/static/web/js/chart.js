// 基于准备好的dom，初始化echarts实例
var chartPop = echarts.init(document.getElementById('chartPop'));
// 所属住户-option_pop 
option_pop = {
    tooltip: {
        trigger: 'axis',
        axisPointer: { // 坐标轴指示器，坐标轴触发有效
            type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
        }
    },
    legend: {
        data: ['爱琴堡','爱情公寓'] //所属主体
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
	toolbox: {
		show : true,
		feature : {
			restore : {show: true},
			saveAsImage : {show: true}
		},
	},
    xAxis: {
        type: 'value'
    },
    yAxis: {
        type: 'category',
        data: ['单元1', '单元2', '单元3', '单元4' ,'单元5','单元6', '总人口']
    },
    series: [{
            name: '爱琴堡',
            type: 'bar',
            stack: '总量',
            
            itemStyle: {
                normal: {
                    //柱形图圆角，初始化效果
                    barBorderRadius: [0, 0, 0, 0],
                },
                emphasis: {
                    barBorderRadius: [0, 0, 0, 0],
                }
            },
            label: {
                normal: {
                    show: true,
                    position: 'insideRight'
                }
            },
            data: [320, 302, 301, 666, 390 , 200, 2220],
        },
		{
				name: '爱情公寓',
				type: 'bar',
				stack: '总量',
				
				itemStyle: {
					normal: {
						//柱形图圆角，初始化效果
						barBorderRadius: [0, 0, 0, 0],
					},
					emphasis: {
						barBorderRadius: [0, 0, 0, 0],
					}
				},
				label: {
					normal: {
						show: true,
						position: 'insideRight'
					}
				},
				data: [320, 343, 301, 334, 390 ,0 , 1636],
			}
    ]
};

 var chartBind = echarts.init(document.getElementById('chartBind'));
// 绑定用户统计 option_bind
option_bind = {
	
    tooltip : {
        trigger: 'axis'
    },
    legend: {
        data:['业主','用户']
    },
    toolbox: {
        show : true,
        feature : {
            magicType : {show: true, type: ['line', 'bar']},
			restore : {show: true},
			saveAsImage : {show: true}
        },
		
    },
    calculable : true,
    dataZoom : {
        show : true,
        realtime : true,
        start : 20,
        end : 80
    },
    xAxis : [
        {
            type : 'category',
            boundaryGap : false,
            data : function (){
                var list = [];
                for (var i = 1; i <= 30; i++) {
                    list.push('2018-01-' + i);
                }
                return list;
            }()
        }
    ],
    yAxis : [
        {
            type : 'value'
        }
    ],
    series : [
        {
            name:'业主',
            type:'line',
            data:function (){
                var list = [];
                for (var i = 1; i <= 30; i++) {  //自定义区间
                    list.push(Math.round(Math.random()* 30));
                }
                return list;
            }()
        },
        {
            name:'用户',
            type:'line',
            data:function (){
                var list = [];
                for (var i = 1; i <= 30; i++) {
                    list.push(Math.round(Math.random()* 10));
                }
                return list;
            }()
        }
    ]
};

var chartFix = echarts.init(document.getElementById('chartFix'));
var idx = 1;
//自定义itemStyle样式
var myItemStyle={   
		normal : {
			label : {
				show : false
			},
			labelLine : {
				show : false
			}
		},
		emphasis : {
			label : {
				show : true,
				position : 'center',
				textStyle : {
					fontSize : '14',
					fontWeight : 'bold'
				}
			},
			
		}
};
// 维修统计 option_fix
option_fix = {
    timeline : {
        data : [
            '07-01', '07-02', '07-03', '07-04', '07-05',
            { name:'07-06', symbol:'emptyStar5', symbolSize:8 },
            '07-07', '07-08', '07-09', '07-10', '07-11',
            { name:'07-12', symbol:'star5', symbolSize:8 }
        ],
        label : {
            formatter : function(s) {
                return s.slice(0, 7);
            }
        },
// 		autoPlay : true,
// 		playInterval : 3000,
		loop:true,
    },
	
    options : [
		{
			
			tooltip : {
				trigger: 'item',
				formatter: "{a} <br/>{b} : {c} ({d}%)"
			},
			toolbox: {
				show : true,
				feature : {
					restore : {show: true},
					saveAsImage : {show: true}
				},
				x:'right',
				y:'bottom'
			},
			legend: {
				orient: 'vertical',
				x: 'right',
				data:['业主报修待处理','业主报修处理中','业主报修已处理','业主报修已结案','业主报修已回复','投诉建议待处理','投诉建议处理中','','投诉建议已处理','投诉建议已结案','投诉建议已回复','内部工单待处理','内部工单处理中','内部工单已处理','内部工单已结案','内部工单已回复']
			},
			
			calculable : true,
			//第一个默认的series
			series: [
				{
					name: '业主报修',
					type: 'pie',
					radius: ['50%','60%'],//半径
					center: ['20%', '35%'],
					data:[
						{value:250, name:'业主报修待处理'},
						{value:150, name:'业主报修处理中'},
						{value:250, name:'业主报修已处理'},
						{value:150, name:'业主报修已结案'},
						{value:250, name:'业主报修已回复'},
					],
					itemStyle : myItemStyle,
					
				},
				{
					name: '本科',
					type: 'pie',
					radius: ['50%','60%'],//半径
					center: ['50%', '35%'],
					data: [
						{value: 500, name:'投诉建议待处理'},
						{value: 300, name: '投诉建议处理中'},
						{value: 500, name:'投诉建议已处理'},
						{value: 300, name: '投诉建议已结案'},
						{value: 500, name:'投诉建议已回复'}
					],
					itemStyle : myItemStyle,
				},
				{
					name: '本科',
					type: 'pie',
					radius: ['50%','60%'],//半径
					center: ['80%', '35%'],
					data: [
						{value: 500, name:'内部工单待处理'},
						{value: 300, name: '内部工单处理中'},
						{value: 500, name:'内部工单已处理'},
						{value: 300, name: '内部工单已结案'},
						{value: 500, name:'内部工单已回复'}
					],
					itemStyle : myItemStyle,
				}	
			]
		},
		//第二个，第三个依次往后
		{
			series: [
					{
						name: '业主报修',
						type: 'pie',
						radius: ['50%','60%'],//半径
						center: ['20%', '35%'],
						data:[
							{value:600, name:'业主报修待处理'},
							{value:150, name:'业主报修待处理中'},
							{value:250, name:'业主报修待已处理'},
							{value:150, name:'业主报修已结案'},
							{value:250, name:'业主报修已回复'},
						],
						itemStyle : myItemStyle,
					},
					{
						name: '本科',
						type: 'pie',
						radius: ['50%','60%'],//半径
						center: ['50%', '35%'],
						data: [
							{value: 500, name:'投诉建议待处理'},
							{value: 300, name: '投诉建议处理中'},
							{value: 500, name:'投诉建议已处理'},
							{value: 300, name: '投诉建议已结案'},
							{value: 500, name:'投诉建议已回复'}
						],
						itemStyle : myItemStyle,
					},
					{
						name: '本科',
						type: 'pie',
						radius: ['50%','60%'],//半径
						center: ['80%', '35%'],
						data: [
							{value: 500, name:'内部工单待处理'},
							{value: 300, name: '内部工单处理中'},
							{value: 500, name:'内部工单已处理'},
							{value: 300, name: '内部工单已结案'},
							{value: 500, name:'内部工单已回复'}
						],
						itemStyle : myItemStyle,
					}	
				]
		},
    ]
};



//收入支出 budget
var chartBudget = echarts.init(document.getElementById('chartBudget'));
option_budget = {
    tooltip : {
        trigger: 'axis',
        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
        }
    },
    legend: {
        data:['利润', '支出', '收入']
    },
    toolbox: {
        show : true,
        feature : {
            magicType : {show: true, type: ['line', 'bar']},
            restore : {show: true},
            saveAsImage : {show: true}
        }
    },
    calculable : true,
    xAxis : [
		{
			type : 'value'
		}
    ],
    yAxis : [
		{
			type : 'category',
			axisTick : {show: false},
			data : ['01-01','01-02','01-03','01-04','01-05','01-06','01-07','01-08','01-09','01-10','01-11','01-12','01-13','01-14']
		}
    ],
    series : [
        {
            name:'利润',
            type:'bar',
            itemStyle : { normal: {label : {show: true, position: 'inside'}}},
            data:[200, 170, 240, 244, 200, 220, 210]
        },
        {
            name:'收入',
            type:'bar',
            stack: '总量',
            barWidth : 15,
            itemStyle: {normal: {
                label : {show: true}
            }},
            data:[320, 302, 341, 374, 390, 450, 420]
        },
        {
            name:'支出',
            type:'bar',
            stack: '总量',
            itemStyle: {normal: {
                label : {show: true, position: 'left'}
            }},
            data:[-120, -132, -101, -134, -190, -230, -210]
        }
    ]
};


 
//自助服务缴费统计 option_self
var chartSelf = echarts.init(document.getElementById('chartSelf'));
option_self = {

    tooltip : {
        trigger: 'axis'
    },
    legend: {
        data:['智能充电','自助设备','停车缴费']
    },
    toolbox: {
        show : true,
        feature : {
            magicType : {show: true, type: ['line', 'bar']},
			restore : {show: true},
			saveAsImage : {show: true}
        },
		
    },
    calculable : true,
    dataZoom : {
        show : true,
        realtime : true,
        start : 20,
        end : 80
    },
    xAxis : [
        {
            type : 'category',
            boundaryGap : false,
            data : function (){
                var list = [];
                for (var i = 1; i <= 30; i++) {
                    list.push('2018-01-' + i);
                }
                return list;
            }()
        }
    ],
    yAxis : [
        {
            type : 'value'
        }
    ],
    series : [
        {
            name:'智能充电',
            type:'line',
			smooth:true,  //曲线
            data:function (){
                var list = [];
                for (var i = 1; i <= 30; i++) {  //自定义区间
                    list.push(Math.round(Math.random()* 30));
                }
                return list;
            }()
        },
        {
            name:'自助设备',
            type:'line',
			smooth:true,  //曲线
            data:function (){
                var list = [];
                for (var i = 1; i <= 30; i++) {
                    list.push(Math.round(Math.random()* 10));
                }
                return list;
            }()
        },
		{
			name:'停车缴费',
			type:'line',
			smooth:true,  //曲线
			data:function (){
				var list = [];
				for (var i = 1; i <= 30; i++) {
					list.push(Math.round(Math.random()* 10));
				}
				return list;
			}()
		}
    ]
};

//物业缴费

var chartPay = echarts.init(document.getElementById('chartPay'));
option_pay = {
    tooltip : {
        trigger: 'axis'
    },
    legend: {
        data:['物业缴费']
    },
    toolbox: {
        show : true,
        feature : {
            magicType : {show: true, type: ['line', 'bar']},
            restore : {show: true},
            saveAsImage : {show: true}
        }
    },
    calculable : true,
    xAxis : [
        {
            type : 'category',
            boundaryGap : false,
            data : ['7-01','7-02','7-03','7-04','7-05','7-06','7-07','7-07','7-08','7-09','7-10','7-11','7-12','7-13']
        }
    ],
    yAxis : [
        {
            type : 'value'
        }
    ],
    series : [
        {
            name:'物业缴费',
            type:'line',
            smooth:true,
            itemStyle: {normal: {areaStyle: {type: 'default'}}},
            data:[10, 12, 21, 54, 260, 300, 410,10]
        }
    ]
};

var chartNoPay = echarts.init(document.getElementById('chartNoPay'));
option_nopay = {
    tooltip : {
        trigger: 'axis'
    },
    legend: {
        data:['物业未缴费']
    },
    toolbox: {
        show : true,
        feature : {
            magicType : {show: true, type: ['line', 'bar']},
            restore : {show: true},
            saveAsImage : {show: true}
        }
    },
    calculable : true,
    xAxis : [
        {
            type : 'category',
            boundaryGap : false,
            data : ['B-03','B-04','B-05','B-06','B-07','B-07','B-05','B-08','B-09','B-05','B-05','B-05','B-05','B-05']
        }
    ],
    yAxis : [
        {
            type : 'value'
        }
    ],
    series : [
        {
            name:'物业未缴费',
            type:'line',
            smooth:true,
            itemStyle: {normal: {areaStyle: {type: 'default'}}},
            data:[10, 12, 21, 54, 260, 300, 410,10]
        }
    ]
};

// 使用刚指定的配置项和数据显示图表。
initChart();
function initChart(){
	chartPop.setOption(option_pop);
	chartBind.setOption(option_bind);
	chartFix.setOption(option_fix);
	chartBudget.setOption(option_budget);
	chartSelf.setOption(option_self);
	chartPay.setOption(option_pay);
	chartNoPay.setOption(option_nopay);
}
setInterval(function () {
    initChart();
}, 10*60*1000)
function resizeChart(){
	chartPop.resize();
	chartBind.resize();
	chartFix.resize();
	chartBudget.resize();
	chartSelf.resize();
	chartPay.resize();
	chartNoPay.resize();
}
window.onresize = function() {
	resizeChart();
};
