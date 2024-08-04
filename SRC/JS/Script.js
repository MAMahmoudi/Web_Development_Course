import { Menu_Items, Labs_Items } from "./Menu_Items.js";

var Open_Dialog_Btn = document.getElementById("Open_Dialog_Btn_ID"),
  Close_Dialog_Btn = document.getElementById("Close_Btn_ID"),
  Dialog = document.getElementById("Dialog_ID"),
  // Menu_Icon_I_ID = document.getElementById("Menu_Icon_I_ID"),
  Menu_Icon_ID = document.getElementById("Menu_Icon_ID");

var clicked = false;

if (Open_Dialog_Btn != null) {
  console.log("Open_Dialog_Btn");
  Open_Dialog_Btn.addEventListener("click", function (event) {
    event.preventDefault();
    Dialog.style.display = "block";
  });
}

if (Close_Dialog_Btn != null) {
  Close_Dialog_Btn.addEventListener("click", function (event) {
    event.preventDefault();
    Dialog.style.display = "none";
  });
}

function Build_Nav_Bar() {
  var body = document.getElementsByTagName("body")[0];
  console.log(body.children[0]);
  var Header = document.createElement("header");
  var Nav_Bar_Items = document.createElement("nav");
  Nav_Bar_Items.setAttribute("class", "Nav_Bar_Items");
  Nav_Bar_Items.setAttribute("id", "Nav_Bar_Items_ID");
  Header.appendChild(Nav_Bar_Items);
  body.insertBefore(Header, body.children[0]);
  //body.appendChild(Nav_Bar_Items);
  var Nav_Bar_Logo = document.createElement("h1");
  Nav_Bar_Logo.setAttribute("class", "Nav_Bar_Logo");
  Nav_Bar_Logo.setAttribute("id", "Nav_Bar_Logo_ID");
  Nav_Bar_Logo.innerHTML = "Web Development";
  var Menu_Icon = document.createElement("div");
  Menu_Icon.setAttribute("class", "Menu_Icon");
  Menu_Icon.setAttribute("id", "Menu_Icon_ID");
  var Menu_Icon_I_ID = document.createElement("i");
  Menu_Icon_I_ID.setAttribute("id", "Menu_Icon_I_ID");
  Menu_Icon.appendChild(Menu_Icon_I_ID);
  document.getElementById("Nav_Bar_Items_ID").appendChild(Nav_Bar_Logo);
  document.getElementById("Nav_Bar_Items_ID").appendChild(Menu_Icon);
  var Nav_Bar_Menu = document.createElement("ul");
  Nav_Bar_Menu.setAttribute("id", "Nav_Bar_Menu_ID");
  Menu_Items.map((item, index) => {
    var Nav_Bar_Item = document.createElement("li");
    var Nav_Bar_Item_Link = document.createElement("a");
    Nav_Bar_Item_Link.setAttribute("href", item.url);
    Nav_Bar_Item_Link.setAttribute("class", item.cName);
    Nav_Bar_Item_Link.innerHTML = item.title;
    if (item.title == "Labs") {
      Nav_Bar_Item.setAttribute("id", "Nav_Bar_Item_Labs_ID");
      var Labs = document.createElement("ul");
      Labs.setAttribute("class", "Labs_Menu");
      Labs.setAttribute("id", "Labs_Menu_ID");
      Labs_Items.map((item2, index2) => {
        var Labs_Menu_Item = document.createElement("li");
        var Labs_Menu_Item_Link = document.createElement("a");
        Labs_Menu_Item_Link.setAttribute("href", item2.url);
        Labs_Menu_Item_Link.setAttribute("class", item2.cName);
        Labs_Menu_Item_Link.innerHTML = item2.title;
        Labs_Menu_Item.innerHTML = Labs_Menu_Item_Link.outerHTML;
        Labs.appendChild(Labs_Menu_Item);
        Nav_Bar_Item.innerHTML = Nav_Bar_Item_Link.outerHTML;
        Nav_Bar_Item.appendChild(Labs);
      });
    } else {
      Nav_Bar_Item.innerHTML = Nav_Bar_Item_Link.outerHTML;
    }
    if (item.title == "Settings") {
      Nav_Bar_Item.setAttribute("id", "Nav_Bar_Item_Settings_ID");
      var Settings = document.createElement("i");
      Settings.setAttribute("class", "fa-sharp fa-solid fa-gear fa-lg");
      Settings.style.color = "#ffffff";
      Nav_Bar_Item_Link.innerHTML = Settings.outerHTML;
      Nav_Bar_Item.innerHTML = Nav_Bar_Item_Link.outerHTML;
    }
    Nav_Bar_Menu.appendChild(Nav_Bar_Item);
    Nav_Bar_Items.appendChild(Nav_Bar_Menu);

    if (!clicked) {
      Nav_Bar_Menu.className = "Nav_Bar_Menu";
      Menu_Icon_I_ID.className = "fas fa-bars";
    }
  });
}

Build_Nav_Bar();

function handle_click() {
  clicked = !clicked;
  console.log(clicked);
  if (!clicked) {
    document.getElementById("Nav_Bar_Menu_ID").className = "Nav_Bar_Menu";
    document.getElementById("Menu_Icon_I_ID").className = "fas fa-bars";
  } else {
    document.getElementById("Nav_Bar_Menu_ID").className =
      "Nav_Bar_Menu_Active";
    document.getElementById("Menu_Icon_I_ID").className = "fas fa-times";
  }
}

document
  .getElementById("Menu_Icon_ID")
  .addEventListener("click", function (event) {
    event.preventDefault();
    handle_click();
  });
