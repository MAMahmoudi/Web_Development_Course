function loadDoc() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("demo").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "SRC/JS/My_File.txt", true);
  xhttp.send();
}

var xhttp = new XMLHttpRequest();
xhttp.onload = function () {
  if (this.status == 200) {
    document.getElementById("demo2").innerHTML = this.responseText;
  }
};
xhttp.open("GET", "SRC/JS/My_File2.txt", true);
xhttp.send();
//////////////////////////////////////////////////////////////////////
var xhttp5 = new XMLHttpRequest();
function Start() {
  window.setInterval(Repeate, 2000);
  function Repeate() {
    console.log(xhttp5.responseText);
    xhttp5.onreadystatechange = function () {
      if (xhttp5.readyState == 4 && xhttp5.status == 200) {
        document.getElementById("demo3").innerHTML +=
          xhttp5.responseText + "<br>";
      }
    };
    xhttp5.open("GET", "SRC/JS/My_File3.txt", true);
    xhttp5.send();
  }
}

function abortFunction() {
  console.log("Abort");
  xhttp5.abort();
}
//////////////////////////////////////////////////////////
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function () {
  if (this.readyState == 4 && this.status == 200) {
    document.getElementById("demo4").innerHTML = this.getAllResponseHeaders();
  }
};
xhttp.open("GET", "SRC/JS/My_File3.txt", true);
xhttp.send();
/////////////////////////////////////////////////////////
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function () {
  if (this.readyState == 4 && this.status == 200) {
    document.getElementById("demo5").innerHTML =
      this.getResponseHeader("Content-Type");
  }
};
xhttp.open("GET", "SRC/JS/My_File3.txt", true);
xhttp.send();
////////////////////////////////////////////////////////
