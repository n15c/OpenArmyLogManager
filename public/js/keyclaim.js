console.log("KeyClaim Additional JS loaded!");
function issueKey() {
  var claimObj = {};

  claimObj["vehicle"] = document.getElementById("licplate").value;
  claimObj["person"] = document.getElementById("recname").value;

  const xhttp = new XMLHttpRequest();
  xhttp.open("POST", "/keys/issueing");
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't issue key!";
       }
    };
  xhttp.send(JSON.stringify(claimObj));

  return;
}

function returnKey(id){
  const xhttp = new XMLHttpRequest();
  xhttp.open("GET", "/keys/return/"+ id);
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't return key!";
       }
    };
  xhttp.send();

  return;
}
