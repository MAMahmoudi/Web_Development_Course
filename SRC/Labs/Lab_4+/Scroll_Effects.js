// Effects linked to scrolling, shared by every page that has the navigation bar:
// smaller navigation bar, cards appearing on scroll, "back to top" button and current year in the footer.
(() => {
  const Reduced_Motion = matchMedia("(prefers-reduced-motion: reduce)").matches;

  // 1. the navigation bar gets the class "Scrolled" once the page is scrolled
  const Nav_Bar = document.querySelector(".Nav_Bar_Items");
  const Update_Nav_Bar = () => Nav_Bar?.classList.toggle("Scrolled", scrollY > 50);

  // 2. cards fade in when they enter the screen
  const Cards = document.querySelectorAll(
    ".Destination_Item, .Destination_Item_Reverse, .Trip_Card, .Feature, .Service_Card, .Team_Card, .Contact_Info_Item"
  );
  if (!Reduced_Motion && "IntersectionObserver" in window) {
    const Observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const Card = entry.target;
        Card.classList.add("Visible");
        Observer.unobserve(Card);
        // once visible, remove the classes so that the card's own :hover transform works again
        // (transitionend also bubbles up from the card's children: only react to the card itself)
        Card.addEventListener("transitionend", function Done(event) {
          if (event.target !== Card) return;
          Card.classList.remove("Reveal", "Visible", "Delay_1", "Delay_2", "Delay_3");
          Card.removeEventListener("transitionend", Done);
        });
      });
    }, { threshold: 0.15 });

    Cards.forEach((Card) => {
      // the class is added by JavaScript: without JavaScript, the cards stay visible
      Card.classList.add("Reveal");
      // cards of the same row appear one after the other
      const Position = [...Card.parentElement.children].indexOf(Card) % 4;
      if (Position > 0) Card.classList.add("Delay_" + Position);
      Observer.observe(Card);
    });
  }

  // 3. "back to top" button, created here because it is only useful with JavaScript
  const Top_Btn = document.createElement("button");
  Top_Btn.type = "button";
  Top_Btn.className = "Back_To_Top";
  Top_Btn.setAttribute("aria-label", "العودة إلى أعلى الصفحة");
  Top_Btn.innerHTML = '<i class="fa-solid fa-arrow-up"></i>';
  Top_Btn.addEventListener("click", () => scrollTo({ top: 0 }));
  document.body.appendChild(Top_Btn);
  const Update_Top_Btn = () => Top_Btn.classList.toggle("Shown", scrollY > 600);

  // 4. the copyright always shows the current year
  const Copyright = document.querySelector(".Footer_Copyright");
  if (Copyright) Copyright.textContent = Copyright.textContent.replace(/\d{4}/, new Date().getFullYear());

  // the scroll event fires very often: keep the work done here small (two class toggles).
  // passive: true tells the browser that we never call preventDefault(), so scrolling stays smooth
  addEventListener("scroll", () => {
    Update_Nav_Bar();
    Update_Top_Btn();
  }, { passive: true });

  Update_Nav_Bar();
  Update_Top_Btn();
})();
