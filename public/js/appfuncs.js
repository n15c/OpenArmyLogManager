function changeCompany(){
  var selComp = document.getElementById("selectedCompany").value;

  const xhttp = new XMLHttpRequest();
  xhttp.open("GET", "/changeCompany/"+ selComp);
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("infogood").style = "";
            document.getElementById("infogood").innerText = "Company was changed!";
            location.reload();
       }
    };
  xhttp.send();

  return;
}
