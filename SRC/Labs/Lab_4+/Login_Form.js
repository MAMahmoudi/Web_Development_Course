// Login page: show/hide the password and check the fields before sending.
(() => {
  document.querySelectorAll(".Logins .Form").forEach((Form) => {
    const Login = Form.querySelector('input[type="email"]'),
      Password = Form.querySelector('input[type="password"]');
    if (!Login || !Password) return;

    // Lab 7: the real login of the back end accepts e-mail addresses only (data-email-only)
    const Email_Only = Login.hasAttribute("data-email-only"),
      Arabic = document.documentElement.lang === "ar";

    // the field of the demo accepts an e-mail address OR a phone number: type="email" would refuse phone numbers
    if (!Email_Only) Login.type = "text";
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
      event.preventDefault();
      const Value = Login.value.trim(),
        Is_Email = /^[^\s@]+@[^\s@]+\.[a-z]{2,}$/i.test(Value),
        Is_Phone = !Email_Only && /^0[5-7][0-9]{8}$/.test(Value),
        Login_Ok = Is_Email || Is_Phone;
      let Error = "";
      if (!Login_Ok) {
        Error = Email_Only
          ? (Arabic ? "أدخلوا بريداً إلكترونياً صحيحاً." : "Enter a valid e-mail address.")
          : "Enter a valid e-mail or an Algerian phone number (05XXXXXXXX).";
      } else if (Email_Only ? Password.value === "" : Password.value.length < 8) {
        // a login only checks that a password was typed; the static demo also checks its length
        Error = Email_Only ? (Arabic ? "أدخلوا كلمة المرور." : "Enter your password.") : "The password has at least 8 characters.";
      }
      Login.classList.toggle("Invalid", !Login_Ok);
      Password.classList.toggle("Invalid", Error !== "" && Login_Ok);
      Message.classList.toggle("Error", Error !== "");
      if (Error) {
        Message.textContent = Error;
        return;
      }
      // with a back end (Lab 7), the form has an action: send it; the server checks the password
      if (Form.getAttribute("action")) {
        Form.submit();
        return;
      }
      // static demo (Labs 2 to 6): nothing is sent
      Message.textContent = "Checked! Logging in is connected to the server in Lab 7.";
    });
  });
})();
