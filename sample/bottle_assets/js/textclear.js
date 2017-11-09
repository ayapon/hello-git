 function Form_Load(){
    if (this.searchForm.searchBox.value.length > 0){
     target = document.getElementById("clear_button");
     target.style.visibility = "visible";
   }
 }

 function ClearButton_Click(){
    this.searchForm.searchBox.value="";
    this.searchForm.searchBox.focus();
	
   target = document.getElementById("clear_button");
   target.style.visibility = "hidden";
 }

 function ClearButton_KeyDown(){
   target = document.getElementById("clear_button");
   target.style.visibility = "visible";
 }
