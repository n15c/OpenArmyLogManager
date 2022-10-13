console.log("Transport Additional JS loaded!");

function allowDrop(ev) {
  ev.preventDefault();
}

function drag(ev) {
  ev.dataTransfer.setData("text", ev.target.id);
}

function VhcDrop(ev) {
  ev.preventDefault();
  var data = ev.dataTransfer.getData("text");
  ev.target.appendChild(document.getElementById(data));
  var target_id = ev.target.id;
  var vehicle = data.split("-")[1];
  var transportUUID = document.getElementById('trspuuid').value;
  var method = "";
  if (target_id.includes("selected")) {
    method = "CREATE";
  }
  else if (target_id.includes("all")) {
    method = "DELETE";
  }
  else {
    fetchAllVhc();
    return false;
  }
  const xhttp = new XMLHttpRequest();

  xhttp.open(method, "/transport/assignVhc/" + transportUUID + "/" + vehicle);
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          fetchAllVhc();
          document.getElementById("infoalert").style = "display:none;";
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't change value!";
       }
    }
  xhttp.send();

}

function ajaxchangeval(key,value) {
  var changeVal = {};
  changeVal["key"] = key;
  changeVal["value"] = value;
  var transportUUID = document.getElementById('trspuuid').value;
  const xhttp = new XMLHttpRequest();
  xhttp.open("POST", "/transport/update/" + transportUUID);
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if (key=="alias") {
              document.getElementById("transporttitle").innerText = value;
            }
            document.getElementById("infoalert").style = "display:none;";
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't change value!";
       }
    }
  xhttp.send(JSON.stringify(changeVal));

  return;
}


function fetchAllVhc(){
  const xhttp = new XMLHttpRequest();
  xhttp.open("GET", "/transport/getReadyVhc");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var allVhcs = JSON.parse(this.responseText);
            fetchSelectedVhc(allVhcs);
            // Object.values(allVhcs).forEach(element =>
            //   addVehicleToList("all" ,element)
            // );
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't fetch vehicles!";
       }
    };
  xhttp.send();
  return;
}

function fetchSelectedVhc(allVhcs){
  const xhttp = new XMLHttpRequest();
  var trspuuid = document.getElementById("trspuuid").value;
  xhttp.open("GET", "/transport/getChosenVhc/"+trspuuid);
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var vhcdragitems = Array.prototype.slice.call(document.getElementsByClassName('vhc-drag-item'), 0);
            vhcdragitems.forEach(x => x.remove())
            var selVhcs = JSON.parse(this.responseText);
            var difference = allVhcs.filter(function(objOne) {
                return !selVhcs.some(function(objTwo) {
                    return objOne.licplate == objTwo.licplate;
                });
            });
            difference.forEach(element =>
              addVehicleToList("all" ,element)
            );
            Object.values(selVhcs).forEach(element =>
              addVehicleToList("selected" ,element)
            );
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't fetch vehicles!";
       }
    };
  xhttp.send();
  return;
}

function addVehicleToList(listtype,vhc){
  var listElement = document.getElementById("vhc-list-"+listtype+"-"+vhc.cat);
  var element = "<li draggable='true' ondragstart='drag(event)' ondrop='return false;'' id='vhc-"+vhc.licplate+"' class='list-group-item vhc-drag-item'>"+vhc.licplate+"</li>";
  listElement.innerHTML += element;
}


// function returnKey(id){
//   const xhttp = new XMLHttpRequest();
//   xhttp.open("GET", "/keys/return/"+ id);
//   xhttp.setRequestHeader("Content-type", "text/json");
//   xhttp.onreadystatechange = function() {
//         if (this.readyState == 4 && this.status == 200) {
//             location.reload();
//        }
//        else if(this.readyState == 4){
//          document.getElementById("infoalert").style = "";
//          document.getElementById("infoalert").innerText = "Couldn't return key!";
//        }
//     };
//   xhttp.send();
//
//   return;
// }
