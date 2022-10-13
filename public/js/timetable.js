var timeline;

function loadTransports(){
  const xhttp = new XMLHttpRequest();
  xhttp.open("GET", "/timetable/getTransports");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const trsps = JSON.parse(this.responseText);
            renderPTrspTimetable(trsps);
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't fetch vehicles!";
       }
    };
  xhttp.send();
}

function getGroups(){
  const xhttp = new XMLHttpRequest();
  xhttp.open("GET", "/timetable/getVhcGroups");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const groups = JSON.parse(this.responseText);
            getTransportsPVhc(groups);
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't fetch vehicles!";
       }
    };
  xhttp.send();
}

function getTransportsPVhc(groups){
  const xhttp = new XMLHttpRequest();
  xhttp.open("GET", "/timetable/getTransportsPerVhc");
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var tline_trsps = JSON.parse(this.responseText);
            const items = new vis.DataSet(tline_trsps)
            renderPVhcTimetable(items, groups);
       }
       else if(this.readyState == 4){
         document.getElementById("infoalert").style = "";
         document.getElementById("infoalert").innerText = "Couldn't fetch vehicles!";
       }
    };
  xhttp.send();
}


function renderPVhcTimetable(items, groups){
  const container = document.getElementById("visualizationpvhc");
  // Configuration for the Timeline
  const options = {
    orientation:'top',
    groupOrder:'type',
    start:Date.now()- 86400000,
    end: Date.now() + (86400000*7),
  };

  // Create a Timeline
  timeline = new vis.Timeline(container, items, groups, options);

}

function renderPTrspTimetable(items){
  const container = document.getElementById("visualizationptrsp");
  // Configuration for the Timeline
  const options = {
    orientation:'top',
    start:Date.now()- 86400000,
    end: Date.now() + (86400000*7),
  };

  // Create a Timeline
  timeline = new vis.Timeline(container, items, options);

}
loadTransports();
getGroups();
