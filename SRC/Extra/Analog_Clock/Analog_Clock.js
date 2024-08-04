let Hour = document.querySelector("#Hour"),
  Minute = document.querySelector("#Minute"),
  Second = document.querySelector("#Second");

function Display_Time() {
  let The_Date = new Date();
  let The_Hour = The_Date.getHours();
  let The_Minute = The_Date.getMinutes();
  let The_Second = The_Date.getSeconds();
  let Hour_Rotate = 30 * The_Hour + The_Minute / 2;
  let Minute_Rotate = 6 * The_Minute;
  let Second_Rotate = 6 * The_Second;
  Hour.style.transform = `rotate(${Hour_Rotate}deg)`;
  Minute.style.transform = `rotate(${Minute_Rotate}deg)`;
  Second.style.transform = `rotate(${Second_Rotate}deg)`;
}

setInterval(Display_Time, 1000);


// document.addEventListener("timeupdate", (event) => {
//     console.log("The currentTime attribute has been updated. Again.");
//   });