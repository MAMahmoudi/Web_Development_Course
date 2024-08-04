let To_Do_List = document.querySelector(".To_Do_List"),
  Add_Task = document.querySelector("#Add_Task"),
  Settings = document.querySelector("#Settings"),
  Settings_Menu = document.querySelector("#Settings_Menu"),
  Scheduler = document.querySelector(".Scheduler"),
  Task = document.querySelector("#Task");

Settings.addEventListener("click", () => {
  console.log("Settings Clicked");
  Settings_Menu.style.display = "block";
});

Add_Task.addEventListener("click", () => {
  if (Task.value !== "") {
    let Task_To_Do = document.createElement("div");
    Task_To_Do.setAttribute("class", "Task_To_Do");
    let Task_Text_Div = document.createElement("div");
    Task_Text_Div.setAttribute("class", "Task_Text_Div");
    let Task_Text = document.createElement("p");
    Task_Text.setAttribute("class", "Task_Text");
    Task_Text.setAttribute("id", "Task_Text");
    Task_Text.innerHTML = Task.value;
    Task_Text_Div.append(Task_Text);
    let Task_Btns = document.createElement("div");
    Task_Btns.setAttribute("class", "Task_Btns");
    let Timer = document.createElement("i");
    Timer.classList.add("fa-sharp", "fa-solid", "fa-clock", "fa-2xl");
    Timer.setAttribute("id", "Timer");
    let Done = document.createElement("i");
    Done.classList.add("fa-solid", "fa-calendar-check", "fa-2xl");
    Done.setAttribute("id", "Done_Task");
    let Delete = document.createElement("i");
    Delete.classList.add("fa-solid", "fa-trash-can", "fa-2x");
    Delete.setAttribute("id", "Delete_Task");
    Task_Btns.append(Timer, Scheduler, Delete, Done);
    Task_To_Do.append(Task_Text_Div, Task_Btns);
    To_Do_List.append(Task_To_Do);
    Task.value = "";
  } else {
    alert("Please Enter a Task");
  }
});

To_Do_List.addEventListener("click", (event) => {
  console.log(event.target.id);
  switch (event.target.id) {
    case "Task_Text":
      event.target.classList.toggle("Checked");
      break;
    case "Delete_Task":
      event.target.parentElement.parentElement.remove();
      break;
    case "Timer":
      Scheduler.classList.toggle("Scheduler_Active");
      break;
  }
});
