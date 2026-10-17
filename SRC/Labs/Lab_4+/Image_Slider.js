// Image slider of the home page hero: arrows, dots, keyboard, swipe and auto-play.
(() => {
  const Hero = document.querySelector(".Hero"),
    Slides = document.querySelectorAll(".Slide"),
    Previous_Btn = document.querySelector("#Slides_Previous_Btn"),
    Next_Btn = document.querySelector("#Slides_Next_Btn");

  if (!Hero || Slides.length === 0) return;

  const Delay = 5000,
    Reduced_Motion = matchMedia("(prefers-reduced-motion: reduce)").matches;
  let Current = 0,
    Timer = null;

  // place the slides side by side: 0%, 100%, 200%...
  Slides.forEach((Slide, index) => {
    Slide.style.left = index * 100 + "%";
  });

  // one dot per slide, named after the photo (its alt text)
  const Dots = document.createElement("div");
  Dots.className = "Slider_Dots";
  Slides.forEach((Slide, index) => {
    const Dot = document.createElement("button");
    Dot.type = "button";
    Dot.className = "Slider_Dot";
    Dot.setAttribute("aria-label", Slide.alt);
    Dot.addEventListener("click", () => {
      Go_To(index);
      Restart();
    });
    Dots.appendChild(Dot);
  });
  Hero.appendChild(Dots);

  function Go_To(index) {
    // (index + length) % length keeps the index between 0 and length - 1, even when index is -1
    Current = (index + Slides.length) % Slides.length;
    Slides.forEach((Slide) => {
      Slide.style.transform = `translateX(-${Current * 100}%)`;
    });
    Dots.querySelectorAll(".Slider_Dot").forEach((Dot, i) => {
      Dot.classList.toggle("Active", i === Current);
      Dot.setAttribute("aria-current", i === Current);
    });
  }

  const Next = () => Go_To(Current + 1),
    Previous = () => Go_To(Current - 1);

  function Start() {
    if (!Reduced_Motion && Timer === null) Timer = setInterval(Next, Delay);
  }

  function Stop() {
    clearInterval(Timer);
    Timer = null;
  }

  function Restart() {
    Stop();
    Start();
  }

  // the arrows are <i> icons: give them a role, a name and keyboard access
  [
    [Next_Btn, Next, "الصورة التالية"],
    [Previous_Btn, Previous, "الصورة السابقة"],
  ].forEach(([Button, Action, Label]) => {
    if (!Button) return;
    Button.setAttribute("role", "button");
    Button.setAttribute("tabindex", "0");
    Button.setAttribute("aria-label", Label);
    Button.addEventListener("click", () => {
      Action();
      Restart();
    });
    Button.addEventListener("keydown", (event) => {
      if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        Button.click();
      }
    });
  });

  // keyboard arrows, unless the visitor is typing in a field
  document.addEventListener("keydown", (event) => {
    if (event.target.closest?.("input, textarea, select")) return;
    if (event.key === "ArrowRight") Next_Btn?.click();
    if (event.key === "ArrowLeft") Previous_Btn?.click();
  });

  // swipe on touch screens
  let Touch_Start_X = null;
  Hero.addEventListener("touchstart", (event) => {
    Touch_Start_X = event.touches[0].clientX;
  }, { passive: true });
  Hero.addEventListener("touchend", (event) => {
    if (Touch_Start_X === null) return;
    const Distance = event.changedTouches[0].clientX - Touch_Start_X;
    if (Math.abs(Distance) > 50) {
      Distance < 0 ? Next() : Previous();
      Restart();
    }
    Touch_Start_X = null;
  });

  // pause while the mouse is over the hero or the tab is hidden
  Hero.addEventListener("mouseenter", Stop);
  Hero.addEventListener("mouseleave", Start);
  document.addEventListener("visibilitychange", () => (document.hidden ? Stop() : Start()));

  Go_To(0);
  Start();
})();
