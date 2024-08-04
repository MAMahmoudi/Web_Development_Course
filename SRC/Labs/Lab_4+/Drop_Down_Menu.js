var clicked = false,
  Menu_Icon_ID = document.getElementById("Menu_Icon_ID"),
  Nav_Bar_Menu_ID = document.getElementById("Nav_Bar_Menu_ID"),
  Burger_Icon_ID = document.getElementById("Burger_Icon_ID");

function Change_State() {
  this.clicked = !clicked;
}

Menu_Icon_ID.addEventListener("click", () => {
  Change_State();
  if (!clicked) {
    Nav_Bar_Menu_ID.classList.remove("active");
    Burger_Icon_ID.className = "fas fa-bars";
  } else {
    Nav_Bar_Menu_ID.classList.toggle("active");
    Burger_Icon_ID.className = "fas fa-times";
  }
  console.log(clicked);
});
