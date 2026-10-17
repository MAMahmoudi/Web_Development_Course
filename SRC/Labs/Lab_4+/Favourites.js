// Favourite trips: a heart on each trip card, remembered in localStorage (shared by the home and services pages).
(() => {
  const Key = "Syaha_Favourites";

  // localStorage only stores strings: the list is saved as JSON.
  // It can be unavailable (private browsing, blocked storage), hence the try/catch.
  function Load() {
    try {
      return JSON.parse(localStorage.getItem(Key)) || [];
    } catch {
      return [];
    }
  }

  function Save(List) {
    try {
      localStorage.setItem(Key, JSON.stringify(List));
    } catch {
      // the favourites will only last until the page is closed
    }
  }

  let Favourites = Load();

  // small message at the bottom of the screen, hidden after 2.5 s
  const Toast = document.createElement("div");
  Toast.className = "Toast";
  Toast.setAttribute("role", "status");
  document.body.appendChild(Toast);
  let Toast_Timer;

  function Show_Toast(Text) {
    Toast.textContent = Text;
    Toast.classList.add("Shown");
    clearTimeout(Toast_Timer);
    Toast_Timer = setTimeout(() => Toast.classList.remove("Shown"), 2500);
  }

  const Renders = [];

  document.querySelectorAll(".Trip_Card").forEach((Card) => {
    const Title = Card.querySelector("h4").textContent.trim(),
      Frame = Card.querySelector(".Trip_Image");

    const Button = document.createElement("button");
    Button.type = "button";
    Button.className = "Favourite_Btn";

    function Render() {
      const On = Favourites.includes(Title);
      Button.innerHTML = `<i class="${On ? "fa-solid" : "fa-regular"} fa-heart"></i>`;
      Button.classList.toggle("On", On);
      Button.setAttribute("aria-pressed", On);
      Button.setAttribute("aria-label", On ? `إزالة ${Title} من المفضلة` : `إضافة ${Title} إلى المفضلة`);
    }

    Button.addEventListener("click", (event) => {
      event.stopPropagation();
      if (Favourites.includes(Title)) {
        Favourites = Favourites.filter((Item) => Item !== Title);
        Show_Toast(`تمت إزالة «${Title}» من المفضلة`);
      } else {
        Favourites.push(Title);
        Show_Toast(`تمت إضافة «${Title}» إلى المفضلة`);
      }
      Save(Favourites);
      Render();
    });

    Render();
    Renders.push(Render);
    Frame.appendChild(Button);
  });

  // the favourites were changed in another tab: update this one
  addEventListener("storage", (event) => {
    if (event.key !== Key) return;
    Favourites = Load();
    Renders.forEach((Render) => Render());
  });
})();
