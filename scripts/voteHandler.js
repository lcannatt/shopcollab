
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
	if(votes>5){
		this.classList.remove("midPrio");
		this.classList.add("highPrio")
	}else if(votes>2){
		this.classList.remove("highPrio");
		this.classList.add("midPrio");
	}else{
		this.classList.remove("midPrio");
	}
	this.children[1].innerHTML=votes;	
}

var selectReX = new RegExp("selected");
var voteArray= [];

var items=document.getElementsByClassName("voteBox");
for(var i=0;i<items.length;i++){
	//initialize the vote tracking array based on server output
	if(items[i].classList.contains("selected")){
		voteArray[items[i].getAttribute("for")]=true;
	} else {
		voteArray[items[i].getAttribute("for")]=false;
	}
	items[i].addEventListener("click",toggleVote);
	if(items[i].children[1].innerHTML>5){
		items[i].classList.toggle("highprio");
	}else if(items[i].children[1].innerHTML>2){
		items[i].classList.toggle("midPrio");
	}
}

var killListener=false;

function sync(){
	var xhttp= new XMLHttpRequest();
	var FD= new FormData(form);
	xhttp.addEventListener("load",function(event){
		console.log("form synced");
	})
	xhttp.addEventListener("error",function(event){
		alert("Autosync is not working, click submit to vote.");
		killListener=true;
	})
	xhttp.open("POST","./vote.php");
	xhttp.send(FD)
}


var form=document.getElementById("voteList");
document.body.addEventListener("change",function(event){
	if(killListener){
		document.body.removeEventListener("change",arguments.callee);
	}
	if(event.target.name="VOTE[]"){
		sync();
	}
})