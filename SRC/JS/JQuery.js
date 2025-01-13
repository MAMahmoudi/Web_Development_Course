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
/////////////////////////////////////////
$(document).ready(function () {
  $("#button11").click(function () {
    $("#p11").css("color", "red");
    color = $("#p11").css("color");
    alert(color);
  });
});
/////////////////////////////////////////
$(document).ready(function () {
  $("#button81").click(function () {
    alert("ID Button Clicked");
  });
  $(".button82").click(function () {
    alert("Class Button Clicked");
  });
});
/////////////////////////////////////////
jQuery(document).ready(function () {
  jQuery("#button101").click(function () {
    jQuery("#p101").hide();
  });
});
/////////////////////////////////////////

$(document).ready(function () {
  $("#button102").click(function () {
    jq = $.noConflict();
    jq("#p102").hide();
  });
});
/////////////////////////////////////////
$(document).ready(function () {
  $("#button112").click(function () {
    $("#p112").hide();
  });
  $("#button113").click(function () {
    $("#p112").show();
  });
});
/////////////////////////////////////////
$(document).ready(function () {
  $("#button114").click(function () {
    $("#p114").slideUp();
  });
  $("#button115").click(function () {
    $("#p114").slideDown();
  });
});
/////////////////////////////////////////
$(document).ready(function () {
  $("#button116").click(function () {
    $("#div116").animate({
      width: "200px",
      height: "200px",
    });
  });
});
/////////////////////////////////////////
