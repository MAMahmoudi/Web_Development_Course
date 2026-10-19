// Validation of the contact form (Contact_Us.html): one message under each field, checked on submit,
// then again while the visitor corrects the field. The server must validate again (Lab 6).
(() => {
  const Form = document.querySelector(".Contact_Us_Container form");
  if (!Form) return;

  const Max_Message = 500;

  // [field, test returning an error message or "" when the value is valid]
  const Rules = [
    ["#Name", (v) => (v.length < 3 ? "الرجاء إدخال الإسم الكامل (3 أحرف على الأقل)." : "")],
    ["#Email", (v) =>
      v === "" ? "الرجاء إدخال البريد الإلكتروني."
        : !/^[^\s@]+@[^\s@]+\.[a-z]{2,}$/i.test(v) ? "البريد الإلكتروني غير صحيح." : ""],
    // optional, but if given it must be an Algerian mobile number: 05, 06 or 07 followed by 8 digits
    ["#Phone", (v) => (v !== "" && !/^0[5-7][0-9]{8}$/.test(v) ? "رقم الهاتف يجب أن يكون من الشكل 05XXXXXXXX." : "")],
    ["#Subject", (v) => (v === "" ? "الرجاء اختيار موضوع." : "")],
    ["#Message", (v) => (v.length < 20 ? "الرسالة قصيرة جداً (20 حرفاً على الأقل)." : "")],
  ].map(([Selector, Test]) => [Form.querySelector(Selector), Test]);

  // our own messages replace the browser's bubbles
  Form.noValidate = true;

  // a message element after each field; the server (Lab 6) may already have written one: reuse it
  Rules.forEach(([Field]) => {
    let Error = Field.nextElementSibling;
    if (!Error || !Error.classList.contains("Field_Error")) {
      Error = document.createElement("small");
      Error.className = "Field_Error";
      Field.after(Error);
    }
    Error.id = Field.id + "_Error";
    Error.setAttribute("aria-live", "polite");
    Field.setAttribute("aria-describedby", Error.id);
  });

  // characters left in the message
  const Message = Form.querySelector("#Message");
  Message.maxLength = Max_Message;
  const Counter = document.createElement("small");
  Counter.className = "Char_Counter";
  Message.parentElement.insertBefore(Counter, Message.nextElementSibling.nextElementSibling);
  const Update_Counter = () => {
    Counter.textContent = `${Message.value.length} / ${Max_Message}`;
  };
  Message.addEventListener("input", Update_Counter);
  Update_Counter();

  function Check(Field, Test) {
    const Error = Test(Field.value.trim());
    Field.nextElementSibling.textContent = Error;
    Field.classList.toggle("Invalid", Error !== "");
    Field.classList.toggle("Valid", Error === "" && Field.value.trim() !== "");
    Field.setAttribute("aria-invalid", Error !== "");
    return Error === "";
  }

  // after a field has been left once, check it at each keystroke
  Rules.forEach(([Field, Test]) => {
    Field.addEventListener("blur", () => {
      Field.dataset.touched = "yes";
      Check(Field, Test);
    });
    Field.addEventListener("input", () => {
      if (Field.dataset.touched) Check(Field, Test);
    });
  });

  const Success = document.createElement("p");
  Success.className = "Form_Success";
  Success.setAttribute("role", "status");
  Form.appendChild(Success);

  Form.addEventListener("submit", (event) => {
    event.preventDefault();
    Success.textContent = "";
    const Results = Rules.map(([Field, Test]) => Check(Field, Test));
    if (Results.includes(false)) {
      Rules[Results.indexOf(false)][0].focus();
      return;
    }
    // with a back end (Lab 5), the form has a real action: send it; the server checks it again
    const Action = Form.getAttribute("action");
    if (Action && Action !== "#") {
      Form.submit();
      return;
    }
    // static demo (Labs 2 to 4): nothing is sent, show a confirmation
    Success.textContent = "شكراً لكم! تم استلام رسالتكم وسنرد عليكم في أقرب وقت.";
    Form.reset();
    Rules.forEach(([Field]) => {
      Field.classList.remove("Valid");
      delete Field.dataset.touched;
    });
    Update_Counter();
  });
})();
