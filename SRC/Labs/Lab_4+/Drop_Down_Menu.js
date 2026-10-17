// Hamburger menu of the navigation bar (small screens, see Lab_3/Nav_Bar_Responsive.css).
// The menu is open when it has the class "active".
(() => {
  const Menu_Icon = document.getElementById("Menu_Icon_ID"),
    Nav_Bar_Menu = document.getElementById("Nav_Bar_Menu_ID"),
    Burger_Icon = document.getElementById("Burger_Icon_ID");

  if (!Menu_Icon || !Nav_Bar_Menu || !Burger_Icon) return;

  // the icon is a <div>: make it usable with the keyboard and by screen readers
  Menu_Icon.setAttribute("role", "button");
  Menu_Icon.setAttribute("tabindex", "0");
  Menu_Icon.setAttribute("aria-controls", "Nav_Bar_Menu_ID");

  function Set_Menu(Open) {
    Nav_Bar_Menu.classList.toggle("active", Open);
    Burger_Icon.className = Open ? "fas fa-times" : "fas fa-bars";
    Menu_Icon.setAttribute("aria-expanded", Open);
    Menu_Icon.setAttribute("aria-label", Open ? "إغلاق القائمة" : "فتح القائمة");
  }

  Set_Menu(false);

  Menu_Icon.addEventListener("click", () => {
    Set_Menu(!Nav_Bar_Menu.classList.contains("active"));
  });

  Menu_Icon.addEventListener("keydown", (event) => {
    if (event.key === "Enter" || event.key === " ") {
      event.preventDefault();
      Menu_Icon.click();
    }
  });

  // close the menu when a link is chosen, with Escape, or with a click outside the bar
  Nav_Bar_Menu.addEventListener("click", (event) => {
    if (event.target.closest("a")) Set_Menu(false);
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") Set_Menu(false);
  });

  document.addEventListener("click", (event) => {
    if (!event.target.closest?.(".Nav_Bar_Items")) Set_Menu(false);
  });

  // back to a desktop width (e.g. a tablet rotated): the hamburger disappears, so close the menu
  matchMedia("(min-width: 851px)").addEventListener("change", (event) => {
    if (event.matches) Set_Menu(false);
  });
})();
