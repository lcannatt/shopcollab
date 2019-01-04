var catSelector=document.getElementById("catSelect");
var dropDown=document.getElementById("categoryList");

function changeDropdown(){
	var cat=catSelector.value;
	dropDown.setAttribute("list",cat);
}

catSelector.addEventListener("change",changeDropdown);
changeDropdown();

var btn=document.getElementById("login");
if(btn){
	btn.addEventListener("click",function(event){
		var pwd=document.createElement("input");
		pwd.type='password';
		pwd.name='password';
		var submit=document.createElement("input");
		submit.type='submit';
		submit.value='Authenticate';
		event.target.parentElement.appendChild(pwd);
		event.target.parentElement.appendChild(submit);
		event.target.parentElement.removeChild(event.target);
	})
}