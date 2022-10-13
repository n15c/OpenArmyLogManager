console.log("Loading Sys Additional JS loaded!");

function createPallet(){
  var palletObj = {};
  var trspuuid = document.getElementById("trspuuid").value;
  palletObj["palName"] = document.getElementById("palletName").value;
  palletObj["palHeight"] = document.getElementById("palletHeight").value;

  const xhttp = new XMLHttpRequest();
  xhttp.open("POST", "/transports/"+trspuuid+"/loading/createPallet");
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't create pallet!";
       }
    };
  xhttp.send(JSON.stringify(palletObj));

  return;
}

function setPallet(loading){
  const xhttp = new XMLHttpRequest();
  var trspuuid = document.getElementById("trspuuid").value;
  xhttp.open("POST", "/transports/"+trspuuid+"/loading/placePallet");
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // location.reload();
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't place pallet!";
         location.reload();
       }
    };
  xhttp.send(JSON.stringify(loading));

  return;
}

function allowDrop(ev) {
  ev.preventDefault();
}

function drag(ev) {
  ev.dataTransfer.setData("text", ev.target.id);
}

function PalletAddDrop(ev) {
  ev.preventDefault();
  var data = ev.dataTransfer.getData("text");
  var targetVhc = ev.target.id.split("-");
  var setload = {};
  setload["vhc"] = targetVhc[1];
  setload["palX"] = targetVhc[2];
  setload["palY"] = targetVhc[3];
  setload["load"] = data.split("-")[1];
  setPallet(setload);
  ev.target.appendChild(document.getElementById(data));
}

function deletePalletDrop(ev) {
  ev.preventDefault();
  var data = ev.dataTransfer.getData("text");
  var palid = data.split("-")[1];
  console.log(data);
  const xhttp = new XMLHttpRequest();
  var trspuuid = document.getElementById("trspuuid").value;
  xhttp.open("POST", "/transport/"+trspuuid+"/loading/unsetPallet/"+palid);
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't remove pallet!";
         location.reload();
       }
    };
  xhttp.send();

  return;

  // document.getElementById(data).remove();
}

function loadPallets(){
  const xhttp = new XMLHttpRequest();
  var trspuuid = document.getElementById("trspuuid").value;
  xhttp.open("GET", "/transport/"+trspuuid+"/loading/getPlacedPallets");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var placedPallets = JSON.parse(this.responseText);
            placedPallets.forEach((load) => {
              var placeid = "pal-"+load.vhcid+"-"+load.palX+"-"+load.palY;
              var placement = document.getElementById(placeid);
              var palletHtml = '<div id="load-'+load.loadid+'" ondragstart="drag(event)" draggable="true" class="palletObj">'
              +'<img draggable="false" src="/img/transport/pallet.png">'
              +'<p>'+load.loadName+'</p></div></td>';
              placement.innerHTML = palletHtml;
            });


       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't fetch vehicles!";
       }
    };
  xhttp.send();
  return;
}
