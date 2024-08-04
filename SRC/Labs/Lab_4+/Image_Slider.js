var Slides_Previous_Btn = document.querySelector("#Slides_Previous_Btn"),
  Slides_Next_Btn = document.querySelector("#Slides_Next_Btn"),
  Slides_Container = document.querySelector("#Slides_Container"),
  Slides_Width = 0,
  Counter = 1,
  Slides = document.querySelectorAll(".Slide");

//Sliding Images in Home Page--begin
Slides.forEach((Slide, index) => {
  Slide.style.left = index * 100 + "%";
});
const Slide_Images = () => {
  Slides.forEach((Slide) => {
    Slide.style.transform =
      "translateX(-" + (Counter % Slides.length) * 100 + "%)";
  });
};

Slides_Previous_Btn.addEventListener("click", Slide_Previous);

function Slide_Previous(event) {
  Counter--;
  Slide_Images();
}

Slides_Next_Btn.addEventListener("click", Slide_Next);

function Slide_Next(event) {
  Counter++;
  Slide_Images();
}

//Sliding Images in Home Page--end

function Auto_Slide() {
  Counter %= Slides.length;
  Slide_Images();
  Counter++;
}
//setInterval(Auto_Slide, 2000);
