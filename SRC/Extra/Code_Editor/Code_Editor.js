let HTML_Code = document.querySelector("#HTML_Code"),
  CSS_Code = document.querySelector("#CSS_Code"),
  JS_Code = document.querySelector("#JS_Code"),
  Output_Area = document.querySelector("#Output_Area"),
  Run_Btn = document.querySelector("#Run_Btn");

function Run() {
  localStorage.setItem("HTML_Code", HTML_Code.value);
  localStorage.setItem("CSS_Code", CSS_Code.value);
  localStorage.setItem("JS_Code", JS_Code.value);
  Output_Area.contentDocument.body.innerHTML =
    HTML_Code.value + "<style>" + CSS_Code.value + "</style>";
  Output_Area.contentWindow.eval(JS_Code.value);
}

Run_Btn.addEventListener("click", Run);
HTML_Code.addEventListener("keyup", Run);
CSS_Code.addEventListener("keyup", Run);
