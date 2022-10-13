console.log("Vehicle Additional JS loaded!");


function closeRemarkModal(){
  remarkModal.hide();
  return;
}

function showCreateRemarkModal() {
  createRemarkModal.show();
}

function hideCreateRemarkModal() {
  createRemarkModal.hide();
}

function createVhc() {
  var vehicleObj = {};

  vehicleObj["licplate"] = document.getElementById("licplate").value;
  vehicleObj["vhctype"] = document.getElementById("vhctype").value;
  vehicleObj["company"] = document.getElementById("compsel").value;

  const xhttp = new XMLHttpRequest();
  xhttp.open("POST", "/vehicles/create");
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't create vehicle!";
       }
    };
  xhttp.send(JSON.stringify(vehicleObj));

  return;
}

function deleteVehicle(id){
  const xhttp = new XMLHttpRequest();
  xhttp.open("GET", "/vehicles/delete/"+ id);
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't delete vehicle!";
       }
    };
  xhttp.send();
  return;
}

function showRemarks(id, licplate){
  chosenVhcId = id;
  const xhttp = new XMLHttpRequest();
  xhttp.open("GET", "/vehicles/remarks/"+ id);
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          document.getElementById("RemarkModalContent").innerHTML = this.responseText;
          document.getElementById("RemarkModalLabel").innerText = "Remarks for " + licplate;
          remarkModal.show();
       }
    };
  xhttp.send();
  return;
}

function closeRemark(remarkid, vhcid){
  const xhttp = new XMLHttpRequest();
  xhttp.open("GET", "/vehicles/remarks/close/"+ remarkid);
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          showRemarks(vhcid);
       }
    };
  xhttp.send();
  return;
}

function createRemark() {
  var remark = {};
  remark["author"] = document.getElementById("createRemarkAuthor").value;
  remark["description"] = document.getElementById("createRemarkDescription").value;
  remark["vhcid"] = chosenVhcId;
  if (remark["author"] == "" || remark["description"] == "") {
    document.getElementById("CreateRemarkModalAlert").style = "";
    document.getElementById("CreateRemarkModalAlert").innerText = "Please fill all the fields!";
    return;
  }

  const xhttp = new XMLHttpRequest();
  xhttp.open("POST", "/vehicles/remark/create");
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          hideCreateRemarkModal();
          showRemarks(chosenVhcId);
       }
       else if(this.readyState == 4){
         document.getElementById("CreateRemarkModalAlert").style = "";
         document.getElementById("CreateRemarkModalAlert").innerText = "Couldn't create remark!";
       }
    };
  xhttp.send(JSON.stringify(remark));
  return;
}
