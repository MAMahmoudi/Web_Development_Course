var Dialog_ID = document.getElementById("Dialog_ID"),
  Dialog_2_ID = document.getElementById("Dialog_2_ID"),
  Repeating_Gradients_Try_It_Btn_ID = document.getElementById(
    "Repeating_Gradients_Try_It_Btn_ID"
  ),
  Radial_Gradients_Try_It_Btn_ID = document.getElementById(
    "Radial_Gradients_Try_It_Btn_ID"
  ),
  Radial_Gradients_ID = document.getElementById("Radial_Gradients_ID"),
  Progress_Bar_ID = document.getElementById("Progress_Bar_ID");

function Change_Background() {
  if (Progress_Bar_ID.classList.contains("Progress_Bar_Background_2")) {
    //alert("Hello");
    Progress_Bar_ID.classList.remove("Progress_Bar_Background_2");
  } else {
    //alert("Hello2");
    Progress_Bar_ID.classList.add("Progress_Bar_Background_2");
  }
}

function Go_Sleep() {
  if (Radial_Gradients_ID.classList.contains("Radial_Gradients_2")) {
    //alert("Hello");
    Radial_Gradients_ID.classList.remove("Radial_Gradients_2");
  } else {
    //alert("Hello2");
    Radial_Gradients_ID.classList.add("Radial_Gradients_2");
  }
}

if (Repeating_Gradients_Try_It_Btn_ID != null) {
  Repeating_Gradients_Try_It_Btn_ID.addEventListener("click", function (event) {
    event.preventDefault();
    Dialog_ID.style.display = "block";
    setInterval(Change_Background, 250);
  });
}

if (Radial_Gradients_Try_It_Btn_ID != null) {
  Radial_Gradients_Try_It_Btn_ID.addEventListener("click", function (event) {
    event.preventDefault();
    Dialog_2_ID.style.display = "block";
    setInterval(Go_Sleep, 250);
  });
}

if (Dialog_ID != null) {
  Dialog_ID.addEventListener("click", function (event) {
    event.preventDefault();
    Dialog_ID.style.display = "none";
    clearTimeout(Timer);
  });
}

if (Dialog_2_ID != null) {
  Dialog_2_ID.addEventListener("click", function (event) {
    event.preventDefault();
    Dialog_2_ID.style.display = "none";
    clearTimeout(Timer);
  });
}
