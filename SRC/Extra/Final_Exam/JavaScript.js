let Container = document.querySelector(".Container");
// Create the first label and add it to the container
let Label_1 = document.createElement("label");
Label_1.innerHTML = "Enter First Number";
Container.appendChild(Label_1);
// Create the first input and add it to the container
let Input_1 = document.createElement("input");
Container.appendChild(Input_1);
// Create the second label and add it to the container
let Label_2 = document.createElement("label");
Label_2.innerHTML = "Enter Second Number";
Container.appendChild(Label_2);
// Create the second input and add it to the container
let Input_2 = document.createElement("input");
Container.appendChild(Input_2);
// Create the button and add it to the container
let Button = document.createElement("button");
Button.innerHTML = "Click Me";
Container.appendChild(Button);
// Create the event that compute result and add it to the container
Button.addEventListener("click", () => {
  let Multiplcation = Input_1.value * Input_2.value;
  let Result_H2 = document.createElement("h2");
  Result_H2.innerHTML = Multiplcation;
  Container.appendChild(Result_H2);
});
