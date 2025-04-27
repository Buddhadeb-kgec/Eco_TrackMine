document.addEventListener("DOMContentLoaded", () => {
  // Add hover and click effects (as in your original script)
  const buttons = document.querySelectorAll("button, .btn");
  buttons.forEach(button => {
    button.addEventListener("mouseover", () => {
      button.style.backgroundColor = "purple";
      button.style.color = "white";
    });
    button.addEventListener("mouseout", () => {
      button.style.backgroundColor = "";
      button.style.color = "";
    });
    button.addEventListener("mousedown", () => {
      button.style.backgroundColor = "blue";
      button.style.color = "white";
    });
    button.addEventListener("mouseup", () => {
      button.style.backgroundColor = "purple";
    });
  });

  // Handle login button functionality
  const loginButton = document.getElementById("loginButton");
  loginButton.addEventListener("click", async () => {
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const userRole = document.getElementById("userRole").value;

    // Ensure all fields are filled
    if (!username || !password || !userRole) {
      alert("Please fill in all fields.");
      return;
    }

    try {
      const response = await fetch('/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password, userRole })
      });

      const result = await response.json();
      if (result.success) {
        alert("Login successful!");
        window.location.href = '/dashboard'; // Redirect to dashboard
      } else {
        alert(result.message || "Login failed!");
      }
    } catch (error) {
      console.error("Error:", error);
      alert("An error occurred. Please try again.");
    }
  });
});
