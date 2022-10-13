var vehicleSelectCounter = 0;
var vehicles = "";
const xhttp = new XMLHttpRequest();
xhttp.open("GET", "/vehicles/get");
xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
          vehicles = JSON.parse(this.Response);
     }
  };
xhttp.send();

function deleteFzSelContainer(FzSelID) {
  document.getElementById("fzSel-container-"+FzSelID).remove();
}

function addVehicleSelect() {
  vehicleSelectCounter++;
  var vsa = document.getElementById("vehicleSelectionArea");
  vsa.innerHTML +=`
  <div class="mb-3 fzSel" id="fzSel-container-`+vehicleSelectCounter+`">
  <div class="row">
    <div class="col">
      <select class="form-select fzSelector" id="fzsel-`+vehicleSelectCou nter+`">
        <option selected></option>
      </select>
    </div>
    <div class="col">
      <button type="button" class="btn btn-danger" onclick="deleteFzSelContainer(`+vehicleSelectCounter+`)">x</button>
    </div>
    </div>
  </div>
  `;
  var fzSelectors = document.getElementsById("fzsel-"+vehicleSelectCounter);


}
