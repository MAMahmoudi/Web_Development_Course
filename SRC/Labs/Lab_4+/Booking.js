// Booking dialog: a "book now" button on each trip card opens a modal <dialog> with a form.
// The form is checked with the browser's Constraint Validation API, with messages in Arabic.
(() => {
  const Dialog = document.getElementById("Booking_Dialog");
  if (!Dialog) return;

  const Form = Dialog.querySelector("form"),
    Trip = Form.querySelector("#Booking_Trip"),
    People = Form.querySelector("#Booking_People"),
    Departure = Form.querySelector("#Booking_Date"),
    Total = Form.querySelector("#Booking_Total"),
    Message = Form.querySelector("#Booking_Message"),
    Fields = Form.querySelectorAll("input, select");

  // without this, the browser would check the form itself and never fire "submit" while a field is invalid,
  // so our Arabic messages would never be set. We call reportValidity() ourselves instead.
  Form.noValidate = true;

  // the earliest departure is in 3 days: the min attribute is computed when the page loads
  const Earliest = new Date();
  Earliest.setDate(Earliest.getDate() + 3);
  const Pad = (n) => String(n).padStart(2, "0");
  Departure.min = `${Earliest.getFullYear()}-${Pad(Earliest.getMonth() + 1)}-${Pad(Earliest.getDate())}`;

  // one Arabic message per validity state (the browser would show its own messages in its own language)
  const Messages = {
    valueMissing: () => "هذا الحقل إجباري.",
    typeMismatch: () => "الصيغة غير صحيحة.",
    tooShort: (Field) => `أدخل ${Field.minLength} أحرف على الأقل.`,
    rangeUnderflow: (Field) =>
      Field.type === "date" ? "يجب أن يكون تاريخ الانطلاق بعد 3 أيام على الأقل." : `القيمة الدنيا هي ${Field.min}.`,
    rangeOverflow: (Field) => `القيمة القصوى هي ${Field.max}.`,
    badInput: () => "القيمة غير صحيحة.",
  };

  function Explain(Field) {
    Field.setCustomValidity(""); // forget the previous message, then ask the browser again
    if (Field.validity.valid) return true;
    const State = Object.keys(Messages).find((Name) => Field.validity[Name]);
    Field.setCustomValidity(State ? Messages[State](Field) : "القيمة غير صحيحة.");
    return false;
  }

  // the total is computed from the price stored in the chosen <option data-price="...">
  const Money = new Intl.NumberFormat("fr-DZ");
  function Update_Total() {
    const Price = Number(Trip.selectedOptions[0]?.dataset.price || 0),
      Count = Number(People.value) || 0;
    Total.value = `${Money.format(Price * Count)} دج`;
  }

  Fields.forEach((Field) =>
    Field.addEventListener("input", () => {
      Explain(Field);
      Update_Total();
    })
  );

  let Close_Timer;

  function Open(Title) {
    clearTimeout(Close_Timer);
    Form.reset();
    Message.textContent = "";
    Fields.forEach((Field) => Field.setCustomValidity(""));
    // preselect the trip of the card that was clicked
    const Option = [...Trip.options].find((O) => O.textContent.trim().startsWith(Title));
    if (Option) Trip.value = Option.value;
    Update_Total();
    Dialog.showModal();
    Form.querySelector("#Booking_Name").focus();
  }

  // a "book now" button at the bottom of each trip card
  document.querySelectorAll(".Trip_Card").forEach((Card) => {
    const Button = document.createElement("button");
    Button.type = "button";
    Button.className = "Book_Btn";
    Button.innerHTML = '<i class="fa-solid fa-calendar-check"></i> احجز الآن';
    Button.addEventListener("click", () => Open(Card.querySelector("h4").textContent.trim()));
    Card.appendChild(Button);
  });

  Form.querySelector("#Booking_Cancel").addEventListener("click", () => Dialog.close());

  // a click on the dark backdrop reaches the <dialog> itself, not the form inside it
  Dialog.addEventListener("click", (event) => {
    if (event.target === Dialog) Dialog.close();
  });

  Form.addEventListener("submit", (event) => {
    event.preventDefault();
    const All_Valid = [...Fields].map(Explain).every(Boolean);
    if (!All_Valid) {
      Form.reportValidity(); // shows the message of the first invalid field
      return;
    }
    // with a back end (Lab 5), the form has an action: send it for real; the server checks it again
    if (Form.getAttribute("action")) {
      Form.submit();
      return;
    }
    // static demo (Labs 2 to 4): nothing is sent, show a confirmation
    const Date_Text = new Date(Departure.value).toLocaleDateString("ar-DZ", { dateStyle: "long" });
    Message.textContent = `تم تسجيل حجزكم لرحلة ${Trip.selectedOptions[0].textContent.trim().split(" (")[0]} ` +
      `لـ ${People.value} مسافر(ين) يوم ${Date_Text}. المجموع: ${Total.value}`;
    Close_Timer = setTimeout(() => Dialog.close(), 3500);
  });
})();
