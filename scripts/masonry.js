//Globals Vars

var columnCount=0;
var colWidthEm=19;
// Function to calculate absolute positions

function assignPositions(container){
	var fontSize=window.getComputedStyle(container,null).getPropertyValue("font-size");
	var pxPerEm=Number(fontSize.slice(0,2));
	columnCount=Math.floor(container.clientWidth/(colWidthEm*pxPerEm));
	if(columnCount<1){columnCount=1;}
	var computedWidth=(columnCount*colWidthEm*pxPerEm)+"px";
	container.children[0].style.width = computedWidth;
	var items=container.children[0].children;

	//Initialize Bookkeeping Arrays
	var heightTracking=[];
	for(var x=0;x<columnCount;x++){
		heightTracking[x]=0;
	}
	//Assign Positions to vote boxes
	for(var x=0;x<items.length;x++){
		var col=heightTracking.indexOf(Math.min.apply(Math,heightTracking));
		items[x].style.position="absolute";
		items[x].style.left=col*colWidthEm*pxPerEm+"px";
		items[x].style.top=heightTracking[col]+"px";
		heightTracking[col]+=items[x].clientHeight;
	}
	container.children[0].style.height=Math.max.apply(Math,heightTracking)+"px"
}

//Document Initialization
function initCols(){
	var divs=document.getElementsByClassName("floater");
	for(var i =0;i<divs.length;i++){
		assignPositions(divs[i]);
	}

}

function evalCols(){
	var preview=document.getElementsByClassName("floater")[0];
	var fontSize=window.getComputedStyle(preview,null).getPropertyValue("font-size");
	var pxPerEm=Number(fontSize.slice(0,2));
	if(Math.floor(preview.clientWidth/(colWidthEm*pxPerEm))!=columnCount){
		initCols()
	}
}