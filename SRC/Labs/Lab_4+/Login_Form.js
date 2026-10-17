// Login page: show/hide the password and check the fields before sending.
(() => {
  document.querySelectorAll(".Logins .Form").forEach((Form) => {
    const Login = Form.querySelector('input[type="email"]'),
      Password = Form.querySelector('input[type="password"]');
    if (!Login || !Password) return;

    // the field accepts an e-mail address OR a phone number: type="email" would refuse phone numbers
    Login.type = "text";
    Login.autocomplete = "username";
    Password.autocomplete = "current-password";
    Form.noValidate = true;

    // eye button inside the password field
    const Wrapper = document.createElement("div");
    Wrapper.className = "Password_Field";
    Password.before(Wrapper);
    Wrapper.appendChild(Password);
    const Eye = document.createElement("button");
    Eye.type = "button";
    Eye.className = "Password_Toggle";
    Eye.setAttribute("aria-label", "Show password");
    Eye.innerHTML = '<i class="fa-solid fa-eye"></i>';
    Wrapper.appendChild(Eye);
    Eye.addEventListener("click", () => {
      const Show = Password.type === "password";
      Password.type = Show ? "text" : "password";
      Eye.innerHTML = `<i class="fa-solid ${Show ? "fa-eye-slash" : "fa-eye"}"></i>`;
      Eye.setAttribute("aria-label", Show ? "Hide password" : "Show password");
    });

    const Message = document.createElement("p");
    Message.className = "Login_Message";
    Message.setAttribute("role", "status");
    Form.appendChild(Message);

    Form.addEventListener("submit", (event) => {
      // no back end yet: logging in will work after the sessions lab (Lab 7)
      event.preventDefault();
      const Value = Login.value.trim(),
        Is_Email = /^[^\s@]+@[^\s@]+\.[a-z]{2,}$/i.test(Value),
        Is_Phone = /^0[5-7][0-9]{8}$/.test(Value);
      let Error = "";
      if (!Is_Email && !Is_Phone) Error = "Enter a valid e-mail or an Algerian phone number (05XXXXXXXX).";
      else if (Password.value.length < 8) Error = "The password has at least 8 characters.";
      Login.classList.toggle("Invalid", !Is_Email && !Is_Phone);
      Password.classList.toggle("Invalid", Error !== "" && (Is_Email || Is_Phone));
      Message.classList.toggle("Error", Error !== "");
      Message.textContent = Error || "Checked! Logging in will be connected to the server in Lab 7.";
    });
  });
})();
