bootstrap-treeview是一款效果非常酷的基于bootstrap的jQuery多级列表树插件。
该jQuery插件基于Twitter Bootstrap，以简单和优雅的方式来显示一些继承树结构，如视图树、列表树等等。

/*	bootstrap-treeview的用法：
 * 
 * $('#tree').treeview(options);  //其中options选项允许用户定制treeview的默认外观和行为。它们在初始化时作为一个对象被传递给插件。
 * 
 * 
 * 官网：https://jquery-plugins.net/bootstrap-tree-view
 * demo：http://jonmiles.github.io/bootstrap-treeview/
 * 
 * 
 * */
一、 可用的参数：

var options = {
	data: data, //data属性是必须的，是一个对象数组	Array of Objects.
	color: "", //所有节点使用的默认前景色，这个颜色会被节点数据上的backColor属性覆盖.		String
	backColor: "#000000", //所有节点使用的默认背景色，这个颜色会被节点数据上的backColor属性覆盖.		String
	borderColor: "#000000", //边框颜色。如果不想要可见的边框，则可以设置showBorder为false。		String
	nodeIcon: "glyphicon glyphicon-stop", //所有节点的默认图标
	checkedIcon: "glyphicon glyphicon-check", //节点被选中时显示的图标			String
	collapseIcon: "glyphicon glyphicon-minus", //节点被折叠时显示的图标		String
	expandIcon: "glyphicon glyphicon-plus", //节点展开时显示的图标		String
	emptyIcon: "glyphicon", //当节点没有子节点的时候显示的图标				String
	enableLinks: false, //是否将节点文本呈现为超链接。前提是在每个节点基础上，必须在数据结构中提供href值。		Boolean
	highlightSearchResults: true, //是否高亮显示被选中的节点		Boolean
	levels: 2, //设置整棵树的层级数	Integer
	multiSelect: false, //是否可以同时选择多个节点		Boolean
	onhoverColor: "#F5F5F5", //光标停在节点上激活的默认背景色		String
	selectedIcon: "glyphicon glyphicon-stop", //节点被选中时显示的图标		String

	searchResultBackColor: "", //当节点被选中时的背景色
	searchResultColor: "", //当节点被选中时的前景色
	
	selectedBackColor: "", //当节点被选中时的背景色
	selectedColor: "#FFFFFF", //当节点被选中时的前景色

	showBorder: true, //是否在节点周围显示边框
	showCheckbox: false, //是否在节点上显示复选框
	showIcon: true, //是否显示节点图标
	showTags: false, //是否显示每个节点右侧的标记。前提是这个标记必须在每个节点基础上提供数据结构中的值。
	uncheckedIcon: "glyphicon glyphicon-unchecked", //未选中的复选框时显示的图标，可以与showCheckbox一起使用
}
	

二、 可用的方法列表：
	
1.  checkAll(options);//选中所有树节点
2.  checkNode(node | nodeId, options);	//选中一个给定nodeId的树节点
3.  clearSearch();//清除查询结果
4.  collapseAll(options);//折叠所有树节点
5.  collapseNode(node | nodeId, options);//折叠一个给定nodeId的树节点和它的子节点
6.  disableAll(options);//禁用所有树节点
7.  disableNode(node | nodeId, options);//禁用一个给定nodeId的树节点
8.  enableAll(options);//激活所有树节点
9.  enableNode(node | nodeId, options);//激活给定nodeId的树节点
10. expandAll(options);//展开所有节点
11. expandNode(node | nodeId, options);//展开给定nodeId的树节点
12. getCollapsed();//返回被折叠的树节点数组
13. getDisabled();//返回被禁用的树节点数组
14. getEnabled();//返回被激活的树节点数组	
15. getExpanded();//返回被展开的树节点数组
16. getNode(nodeId);//返回与给定节点id相匹配的单个节点对象。
17. getParent(node | nodeId);//返回给定节点id的父节点
18. getSelected();//返回被选定节点的数组。
19. getSiblings(node | nodeId);//返回给定节点的兄弟节点数组
20. getUnselected();//返回未选择节点的数组
21. remove();//删除the tree view component.删除绑定的事件，内部附加的对象，并添加HTML元素。
22. revealNode(node | nodeId, options);//显示给定的树节点，将树从节点扩展到根。
23. search(pattern, options);//在树视图中搜索匹配给定字符串的节点，并在树中突出显示它们。返回匹配节点的数组。
24. selectNode(node | nodeId, options);//选择一个给定的树节点
25. toggleNodeChecked(node | nodeId, options);//Toggles a nodes checked state; checking if unchecked, unchecking if checked.
26. toggleNodeDisabled(node | nodeId, options);//切换节点的禁用状态;
27. toggleNodeExpanded(node | nodeId, options);//切换节点的展开与折叠状态
28. toggleNodeSelected(node | nodeId, options);//切换节点的选择状态
29. uncheckAll(options);//不选所有节点
30. uncheckNode(node | nodeId, options);//不选给定nodeId的节点
31. unselectNode(node | nodeId, options);//不选给定nodeId的节点

说明：可以通过两种方式来调用方法：

1、插件包装器：插件的包装器可以作为访问底层方法的代理。

$('#tree').treeview('methodName', args);  

其中，多个参数必须使用数组对象来传入。

2、直接使用treeview：你可以通过下面两种方法中的一种来获取treeview对象实例：

//该方法返回一个treeview的对象实例
$('#tree').treeview(true).methodName(args);
 
//对象实例也保存在DOM元素的data中， 可以使用'treeview'的id来访问它。
$('#tree').data('treeview').methodName(args); 


	
三、可用的事件列表：
	
1.  nodeChecked (event, node) - 一个节点被checked.
2.  nodeUnchecked (event, node) - 一个节点被unchecked.
3.  nodeCollapsed (event, node) - 一个节点被折叠.
4.  nodeDisabled (event, node) - 一个节点被禁用.
5.  nodeEnabled (event, node) - 一个节点被启用.
6.  nodeExpanded (event, node) - 一个节点被展开.
7.  nodeSelected (event, node) - 一个节点被选择.
8.  nodeUnselected (event, node) - 取消选择一个节点.
9.  searchComplete (event, results) - 搜索完成之后触发.
10. searchCleared (event, results) - 搜索结果被清除之后触发.

说明：事件的调用有两种方式：

第 1 种：在参数中使用回调函数来绑定任何事件：
$('#tree').treeview({
	//命名约定：以on为前缀，并将事件名的第一个字母转为大写，例如： nodeSelected -> onNodeSelected
	onNodeSelected:function(event, data) {
		// 事件代码...
	}
});      

第 2 种：使用标准的jQuery .on()方法来绑定事件:
$('#tree').on('nodeSelected',function(event, data) {
	// 事件代码...
});

