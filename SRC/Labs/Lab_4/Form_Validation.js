let Submit_Btn = document.querySelector("#Submit_Btn"),
  Name = document.querySelector("#Name"),
  User_Name = document.querySelector("#User_Name"),
  Password = document.querySelector("#Password"),
  Confirm_Password = document.querySelector("#Confirm_Password"),
  Birthdate = document.querySelector("#Birthdate"),
  Email = document.querySelector("#Email"),
  Phone_Number = document.querySelector("#Phone_Number"),
  small = document.querySelector("small");
//Email.classList.contains("error");
Submit_Btn.addEventListener("click", (event) => {
  event.preventDefault();
  checkInputs();
});

function checkInputs() {
  // get the values from the inputs
  const NameValue = Name.value.trim();
  const User_NameValue = User_Name.value.trim();
  const PasswordValue = Password.value.trim();
  const Confirm_PasswordValue = Confirm_Password.value.trim();
  const BirthdateValue = Birthdate.value.trim();
  const EmailValue = Email.value.trim();
  const Phone_NumberValue = Phone_Number.value.trim();
  const Today_Date = Date.now();
  const BirthdateValue_MS = Date.parse(BirthdateValue);

  if (NameValue === "") {
    // show error
    // add error class
    setErrorFor(Name, "Name cannot be blank");
  } else {
    // add success class
    setSuccessFor(Name);
  }

  if (User_NameValue === "") {
    setErrorFor(User_Name, "Username cannot be blank");
  } else {
    setSuccessFor(User_Name);
  }

  if (PasswordValue === "") {
    setErrorFor(Password, "Password cannot be blank");
  } else {
    setSuccessFor(Password);
  }

  if (Confirm_PasswordValue === "") {
    setErrorFor(Confirm_Password, "Confirm Password cannot be blank");
  } else if (PasswordValue !== Confirm_PasswordValue) {
    setErrorFor(Confirm_Password, "Passwords does not match");
  } else {
    setSuccessFor(Confirm_Password);
  }

  if (BirthdateValue === "") {
    setErrorFor(Birthdate, "Birthdate cannot be blank");
  } else if (BirthdateValue_MS > Today_Date) {
    setErrorFor(Birthdate, "You can't be born in the future");
  } else if (
    (Today_Date - BirthdateValue_MS) / (1000 * 60 * 60 * 24 * 365) <
    18
  ) {
    setErrorFor(Birthdate, "You must be 18 years old or older");
  } else {
    setSuccessFor(Birthdate);
  }

  if (EmailValue === "") {
    setErrorFor(Email, "Email cannot be blank");
  } else if (!isEmail(EmailValue)) {
    setErrorFor(Email, "Email is not valid");
  } else {
    setSuccessFor(Email);
  }

  if (Phone_NumberValue === "") {
    setErrorFor(Phone_Number, "Phone Number cannot be blank");
  } else if (!isPhone_Number(Phone_NumberValue)) {
    setErrorFor(Phone_Number, "Phone Number is not valid");
  } else {
    setSuccessFor(Phone_Number);
  }
}

function setErrorFor(input, message) {
  // add error message inside small
  small.innerText += "\n" + message;

  // add error class
  if (input.classList.contains("success")) input.classList.remove("success");

  input.classList.add("error");
}

function setSuccessFor(input) {
  if (input.classList.contains("error")) input.classList.remove("error");
  input.classList.add("success");
}

function isEmail(Email) {
  return /^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/.test(
    Email
  );
}

function isPhone_Number(Phone_Number) {
  return /^([0]{1}[5-7]{1}[0-9]{8})$/.test(Phone_Number);
}
