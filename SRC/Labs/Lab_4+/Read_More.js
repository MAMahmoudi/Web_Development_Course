// "Read more" for long texts: the text is cut after a few lines and a button shows the rest.
(() => {
  const Targets = [
    [".Trip_Card p", 6],
    [".Destination_Text p", 10],
  ];

  function Setup() {
    Targets.forEach(([Selector, Lines]) => {
      document.querySelectorAll(Selector).forEach((Text) => {
        if (Text.nextElementSibling?.classList.contains("Read_More_Btn")) return;

        Text.style.setProperty("--Lines", Lines);
        Text.classList.add("Clamped");
        // short texts fit in the lines: nothing to hide
        if (Text.scrollHeight <= Text.clientHeight + 1) {
          Text.classList.remove("Clamped");
          return;
        }

        const Button = document.createElement("button");
        Button.type = "button";
        Button.className = "Read_More_Btn";
        Button.textContent = "اقرأ المزيد";
        Button.setAttribute("aria-expanded", "false");
        Button.addEventListener("click", (event) => {
          event.stopPropagation();
          const Open = Text.classList.toggle("Clamped") === false;
          Button.textContent = Open ? "عرض أقل" : "اقرأ المزيد";
          Button.setAttribute("aria-expanded", Open);
        });
        Text.after(Button);
      });
    });
  }

  // wait for the web font: the number of lines depends on it
  if (document.fonts) document.fonts.ready.then(Setup);
  else addEventListener("load", Setup);
})();
