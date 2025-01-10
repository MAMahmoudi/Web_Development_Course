$(document).ready(function () {
  $("#button").click(function () {
    $("#p").hide();
  });
});
//////////////////////////////////////////
$(document).ready(function () {
  $("#button2").click(function () {
    $("#div2").load("SRC/JS/demo_test.txt");
  });
});
///////////////////////////////////
$(document).ready(function () {
  $("#button3").click(function () {
    $("#div3").load("index.html #Course_Objectives");
  });
});
///////////////////////////////////////
$(document).ready(function () {
  $("#button4").click(function () {
    $("#div4").load(
      "index.html #Course_Objectives",
      function (responseTxt, statusTxt, xhr) {
        if (statusTxt == "success")
          alert("External content loaded successfully!");
        if (statusTxt == "error")
          alert("Error: " + xhr.status + ": " + xhr.statusText);
      }
    );
  });
});
/////////////////////////////////////////
$(document).ready(function () {
  $("#button10").click(function () {
    $.get("SRC/JS/demo_test.txt", function (data, status) {
      alert("Data: " + data + "\nStatus: " + status);
    });
  });
});
