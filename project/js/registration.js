document.addEventListener("DOMContentLoaded", () => {
  const userRole = document.getElementById("userRole");
  const dynamicFields = document.getElementById("dynamicFields");

  userRole.addEventListener("change", () => {
    dynamicFields.innerHTML = ""; // Clear existing fields

    if (userRole.value === "planter") {
      dynamicFields.innerHTML = `
        <div class="input-box">
          <input type="text" id="name" placeholder="Name" required>
          <i class='bx bxs-user'></i>
        </div>
        <div class="input-box">
          <input type="text" id="username" placeholder="Username" required>
          <i class='bx bxs-user-detail'></i>
        </div>
        <div class="input-box">
          <input type="text" id="phoneNumber" placeholder="Phone Number" required>
          <i class='bx bxs-phone'></i>
        </div>
        <button type="button" id="sendPhoneOtp" class="btn">Send OTP from phone</button>
        <div class="input-box">
          <input type="text" id="phoneOtp" placeholder="Enter OTP from phone" required>
          <i class='bx bxs-lock'></i>
        </div>
      `;
    } else if (userRole.value === "admin") {
      dynamicFields.innerHTML = `
        <div class="input-box">
          <input type="text" id="mineName" placeholder="Mine Name" required>
          <i class='bx bxs-factory'></i>
        </div>
        <div class="input-box">
          <input type="text" id="registrationNo" placeholder="Mine Registration No" required>
          <i class='bx bxs-id-card'></i>
        </div>
        <div class="input-box">
          <input type="text" id="mineAddress" placeholder="Mine Address" required>
          <i class='bx bxs-map'></i>
        </div>
        <div class="input-box">
          <input type="email" id="mineEmail" placeholder="Mine Registered Email" required>
          <i class='bx bxs-envelope'></i>
        </div>
        <div class="input-box">
          <input type="text" id="username" placeholder="Username" required>
          <i class='bx bxs-user-detail'></i>
        </div>
        <button type="button" id="sendEmailOtp" class="btn">Send OTP from mail</button>
        <div class="input-box">
          <input type="text" id="emailOtp" placeholder="Enter OTP from mail" required>
          <i class='bx bxs-lock'></i>
        </div>
      `;
    }

    // Add hover and click effects to dynamically added buttons
    const buttons = dynamicFields.querySelectorAll("button");
    buttons.forEach(button => {
      // Hover effect
      button.addEventListener("mouseover", () => {
        button.style.backgroundColor = "purple";
        button.style.color = "white";
      });
      button.addEventListener("mouseout", () => {
        button.style.backgroundColor = "";
        button.style.color = "";
      });

      // Click effect
      button.addEventListener("mousedown", () => {
        button.style.backgroundColor = "blue";
        button.style.color = "white";
      });
      button.addEventListener("mouseup", () => {
        button.style.backgroundColor = "purple"; // Optional: revert to hover state
      });
    });
  });

  const registerButton = document.querySelector("button[type='submit']");

  // Add hover effect for the Register button
  registerButton.addEventListener("mouseover", () => {
    registerButton.style.backgroundColor = "purple";
    registerButton.style.color = "white";
  });
  registerButton.addEventListener("mouseout", () => {
    registerButton.style.backgroundColor = "";
    registerButton.style.color = "";
  });

  // Add click effect for the Register button
  registerButton.addEventListener("mousedown", () => {
    registerButton.style.backgroundColor = "blue";
    registerButton.style.color = "white";
  });
  registerButton.addEventListener("mouseup", () => {
    registerButton.style.backgroundColor = ""; // Revert to default
    registerButton.style.color = "";
  });

  document.getElementById("registrationForm").addEventListener("submit", (e) => {
    e.preventDefault();
    alert("Registration successful!");
  });
});
