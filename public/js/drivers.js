console.log("Driver Additional JS loaded!");
function createDriver() {
  var driverObj = {};

  driverObj["lastname"] = document.getElementById("lastname").value;
  driverObj["company"] = document.getElementById("compsel").value;
  var options = document.getElementById("catsel").selectedOptions;
  var selcats = [];
  for (var i = 0; i < options.length; i++) {
    selcats[i] = options[i].value;
  }
  driverObj["categories"] = selcats;

  const xhttp = new XMLHttpRequest();
  xhttp.open("POST", "/createDriver");
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't create driver!";
       }
    };
  xhttp.send(JSON.stringify(driverObj));

  return false;
}

function deleteDriver(id){
  const xhttp = new XMLHttpRequest();
  xhttp.open("GET", "/drivers/delete/"+ id);
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't delete driver!";
       }
    };
  xhttp.send();

  return;
}
