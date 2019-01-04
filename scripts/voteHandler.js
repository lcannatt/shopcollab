
function toggleVote(){
	//do something to send the vote
	//increment the vote count as appropriate
	var votes=this.children[1].innerHTML;
	var item=this.getAttribute("for");
	if (!voteArray[item]){
		voteArray[item]=true;
		votes++;
	} else {
		voteArray[item]=false;
		votes--;
	}
	//change the class
	this.classList.toggle("selected");
	this.children[1].innerHTML=votes;	
}

var selectReX = new RegExp("selected");
var voteArray= [];

var items=document.getElementsByClassName("voteBox");
for(var i=0;i<items.length;i++){
	//initialize the vote tracking array
	if(selectReX.test(items[i].className)){
		voteArray[items[i].getAttribute("for")]=true;
	} else {
		voteArray[items[i].getAttribute("for")]=false;
	}
	items[i].addEventListener("click",toggleVote);
}
