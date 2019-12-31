var chartFix = echarts.init(document.getElementById('chartFix'));
option_fix = {
	title: {
		text: '报修统计',
		textStyle: {
			fontSize: 16
		},
		x: 'left',
		y: 'bottom'
	},
	legend: {
		orient: 'horizontal',
		x: 'left',
		y: 'top',
		data: ['待处理', '待处中', '已处理', '', '已经结', '已回复', '不显示'],

	},
	toolbox: {
		show: true,
		feature: {
			restore: {
				show: true
			},
		}
	},
	calculable: true,
	series: [{
		name: '访问来源',
		type: 'pie',
		radius: '40%',
		center: ['50%', '55%'],
		itemStyle: {
			normal: {
				label: {
					show: true,
					formatter: "{a}({d}%)",
				},
				labelLine: {
					show: true
				}
			},
		},
		data: [{
				value: 335,
				name: '待处理'
			},
			{
				value: 310,
				name: '待处中'
			},
			{
				value: 234,
				name: '已处理'
			},
			{
				value: 135,
				name: '已经结'
			},
			{
				value: 1548,
				name: '已回复'
			},
			{
				value: 1548,
				name: '不显示'
			}
		]
	}]
};

var chartComplain = echarts.init(document.getElementById('chartComplain'));
option_complain = {
	title: {
		text: '投诉统计',
		textStyle: {
			fontSize: 16
		},
		x: 'left',
		y: 'bottom'
	},
	legend: {
		orient: 'horizontal',
		x: 'left',
		y: 'top',
		data: ['待处理', '待处中', '已处理', '', '已经结', '已回复', '不显示'],

	},
	toolbox: {
		show: true,
		feature: {
			restore: {
				show: true
			},
		}
	},
	calculable: true,
	series: [{
		name: '访问来源',
		type: 'pie',
		radius: '40%',
		center: ['50%', '55%'],
		itemStyle: {
			normal: {
				label: {
					show: true,
					formatter: "{a}({d}%)"
				},
				labelLine: {
					show: true
				}
			},
		},
		data: [{
				value: 335,
				name: '待处理'
			},
			{
				value: 310,
				name: '待处中'
			},
			{
				value: 234,
				name: '已处理'
			},
			{
				value: 135,
				name: '已经结'
			},
			{
				value: 1548,
				name: '已回复'
			},
			{
				value: 1548,
				name: '不显示'
			}
		]
	}]
};

var chartBind = echarts.init(document.getElementById('chartBind'));
option_bind = {
	title: {
		text: '绑定用户统计',
		textStyle: {
			fontSize: 16
		},
		x: 'left',
		y: 'bottom'
	},
	tooltip: {
		trigger: 'axis'
	},
	legend: {
		data: ['微信绑定']
	},
	toolbox: {
		show: true,
		feature: {
			restore: {
				show: true
			},
		}
	},
	calculable: true,
	xAxis: [{
		type: 'category',
		boundaryGap: false,
		data: ['7-01', '7-02', '7-03', '7-04', '7-05', '7-06', '7-07']
	}],
	yAxis: [{
		type: 'value'
	}],
	series: [{
		name: '微信绑定',
		type: 'line',
		stack: '总量',
		data: [120, 132, 101, 134, 90, 230, 210]
	}]
};
initChart();

function initChart() {
	chartComplain.setOption(option_complain);
	chartFix.setOption(option_fix);
	chartBind.setOption(option_bind);
}

function resizeChart() {
	chartComplain.resize();
	chartFix.resize();
	chartBind.resize();
}
window.onresize = function () {
	resizeChart();
};
