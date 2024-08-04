var Properties = document.querySelector("#Properties"),
  Attributes = document.querySelector("#Attributes"),
  Add_Event = document.querySelector("#Add_Event"),
  Localize_Me = document.querySelector("#Localize_Me"),
  Localize_B = document.querySelector("#Localize_B"),
  Top = document.querySelector("#Top"),
  Bottom = document.querySelector("#Bottom"),
  Smooth_Top = document.querySelector("#Smooth_Top"),
  To_Attributes = document.querySelector("#To_Attributes"),
  Document_Rect = document.querySelector("#Document_Rect"),
  Element_Rect = document.querySelector("#Element_Rect"),
  Properties_Result = document.querySelector("#Properties_Result"),
  Attributes_Result = document.querySelector("#Attributes_Result"),
  Localize_Me_Result = document.querySelector("#Localize_Me_Result"),
  Localize_B_Result = document.querySelector("#Localize_B_Result"),
  Btn_1_2_3_Result = document.querySelector("#Btn_1_2_3_Result"),
  Document_Rect_Result = document.querySelector("#Document_Rect_Result"),
  Element_Rect_Result = document.querySelector("#Element_Rect_Result"),
  Add_Event_Result = document.querySelector("#Add_Event_Result");

let H2 = document.createElement("h1");
H2.innerHTML = "You clicked on Attributes";

Properties.onclick = function (event) {
  let H1 = document.createElement("h1");
  H1.innerHTML = "You clicked on Properties";
  Properties_Result.appendChild(H1);
};

Properties.addEventListener("click", function (event) {
  console.log("admd:;d,s;dcldsnqsdcddEventListener()");
});

Attributes.addEventListener("click", function (event) {
  console.log("aqklfjlsdlsd,lsq,er() also");
});

Add_Event.addEventListener("click", function (event) {
  let H1 = document.createElement("h1");
  H1.innerHTML = "You clicked on Add Event";
  Add_Event_Result.appendChild(H1);
});

Top.addEventListener("click", function (event) {
  window.scrollTo(0, 0);
});

Bottom.addEventListener("click", function (event) {
  let documentHeight = document.documentElement.offsetHeight;
  let viewportHeight = window.innerHeight;
  window.scrollTo(0, documentHeight - viewportHeight);
});

Smooth_Top.addEventListener("click", function (event) {
  setInterval(() => {
    if (window.scrollY > 2000)
      window.scrollBy({ left: 0, top: -500, behavior: "smooth" });
  }, 1000);
});

To_Attributes.addEventListener("click", function (event) {
  Attributes.scrollIntoView();
});

Localize_Me.addEventListener("click", function (event) {
  let H1 = document.createElement("h1");
  H1.innerHTML =
    "top :" +
    Localize_Me.getBoundingClientRect().top +
    "<br>" +
    "left :" +
    Localize_Me.getBoundingClientRect().left +
    "<br>" +
    "right :" +
    Localize_Me.getBoundingClientRect().right +
    "<br>" +
    "bottom :" +
    Localize_Me.getBoundingClientRect().bottom +
    "<br>" +
    "width :" +
    Localize_Me.getBoundingClientRect().width +
    "<br>" +
    "height :" +
    Localize_Me.getBoundingClientRect().height;
  Localize_Me_Result.appendChild(H1);
});

Document_Rect.addEventListener("click", function (event) {
  let H1 = document.createElement("h1");
  H1.innerHTML =
    "width :" +
    document.documentElement.getBoundingClientRect().width +
    "<br>" +
    "height :" +
    document.documentElement.getBoundingClientRect().height +
    "<br>" +
    "offsetWidth :" +
    document.documentElement.offsetWidth +
    "<br>" +
    "offsetHeight :" +
    document.documentElement.offsetHeight;
  Document_Rect_Result.appendChild(H1);
});

Element_Rect.addEventListener("click", function (event) {
  let H1 = document.createElement("h1");
  H1.innerHTML =
    "offsetWidth :" +
    Element_Rect.offsetWidth +
    "<br>" +
    "offsetHeight :" +
    Element_Rect.offsetHeight +
    "<br>" +
    "offsetLeft :" +
    Element_Rect.offsetLeft +
    "<br>" +
    "offsetTop :" +
    Element_Rect.offsetTop +
    "<br>" +
    "offsetParent :" +
    Element_Rect.offsetParent +
    "<br>" +
    "clientWidth :" +
    Element_Rect.clientWidth +
    "<br>" +
    "clientHeight :" +
    Element_Rect.clientHeight +
    "<br>" +
    "clientLeft :" +
    Element_Rect.clientLeft +
    "<br>" +
    "clientTop :" +
    Element_Rect.clientTop +
    "<br>" +
    "scrollWidth :" +
    Element_Rect.scrollWidth +
    "<br>" +
    "scrollHeight :" +
    Element_Rect.scrollHeight +
    "<br>" +
    "scrollLeft :" +
    Element_Rect.scrollLeft +
    "<br>" +
    "scrollTop :" +
    Element_Rect.scrollTop;
  console.log(Element_Rect.offsetParent);
  Element_Rect_Result.appendChild(H1);
});
/*
Localize_B.addEventListener("click", function (event) {
  let H1 = document.createElement("h1");
  H1.innerHTML =
    "top :" +
    Localize_B.getBoundingClientRect().top +
    "<br>" +
    "left :" +
    Localize_B.getBoundingClientRect().left +
    "<br>" +
    "right :" +
    Localize_B.getBoundingClientRect().right +
    "<br>" +
    "bottom :" +
    Localize_B.getBoundingClientRect().bottom +
    "<br>" +
    "width :" +
    Localize_B.getBoundingClientRect().width +
    "<br>" +
    "height :" +
    Localize_B.getBoundingClientRect().height;
  Localize_B_Result.appendChild(H1);
});
*/
Add_Event.addEventListener("mouseover", function (event) {
  Add_Event.innerHTML = "Test it";
});

Add_Event.addEventListener("mouseout", function (event) {
  Add_Event.innerHTML = "Add Event";
});

Properties.addEventListener("mouseover", function (event) {
  Properties.innerHTML = "Test it";
});

Properties.addEventListener("mouseout", function (event) {
  Properties.innerHTML = "Properties";
});

Attributes.addEventListener("mouseover", function (event) {
  Attributes.innerHTML = "Test it";
});

Attributes.addEventListener("mouseout", function (event) {
  Attributes.innerHTML = "Attributes";
});

Localize_Me.addEventListener("mouseover", function (event) {
  Localize_Me.innerHTML = "Test it";
});

Localize_Me.addEventListener("mouseout", function (event) {
  Localize_Me.innerHTML = "Localize";
});

let H3 = document.createElement("h1");

Btn_1_2_3_Result.addEventListener("mouseover", function (event) {
  H3.innerHTML = document.elementFromPoint(event.clientX, event.clientY).id;
  Btn_1_2_3_Result.appendChild(H3);
});
