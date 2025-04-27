document.addEventListener('DOMContentLoaded', () => {
    const roleSelect = document.getElementById('role');
    const dynamicFields = document.getElementById('dynamicFields');
    const submitButton = document.querySelector('#submitBtn');
    const resetPasswordBtn = document.querySelector('#resetPasswordBtn');
    const otpFields = document.getElementById('otpFields');
    
    // Listen for changes in the role dropdown
    roleSelect.addEventListener('change', () => {
      const selectedRole = roleSelect.value;
  
      // Clear existing dynamic fields
      dynamicFields.innerHTML = '';
  
      if (selectedRole === 'coal_mine_admin') {
        // Add email and phone number fields for Coal Mine Admin
        dynamicFields.innerHTML = ` 
          <div class="input-box">
            <input type="email" id="email" name="email" placeholder="Email" required>
            <i class='bx bxs-envelope'></i>
          </div>
          <div class="input-box">
            <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>
            <i class='bx bxs-phone'></i>
          </div>
        `;
      } else if (selectedRole === 'planter') {
        // Add only phone number field for Planter
        dynamicFields.innerHTML = ` 
          <div class="input-box">
            <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>
            <i class='bx bxs-phone'></i>
          </div>
        `;
      }
    });
  
    // Mouse hover effect for Submit Button (change to purple on hover)
    submitButton.addEventListener('mouseenter', () => {
      submitButton.style.backgroundColor = 'purple';
      submitButton.style.boxShadow = '0 0 10px rgba(128, 0, 128, 0.6)';
    });
  
    // Mouse leave effect (reset to original color)
    submitButton.addEventListener('mouseleave', () => {
      submitButton.style.backgroundColor = '#fff'; // Original color
      submitButton.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.1)'; // Reset shadow
    });
  
    // Mouse click effect (turns blue when clicked)
    submitButton.addEventListener('mousedown', () => {
      submitButton.style.backgroundColor = 'blue';
      submitButton.style.color = 'black';  // Ensure text color stays black when clicked
    });
  
    // Reset color when mouse is released (on mouse up)
    submitButton.addEventListener('mouseup', () => {
      submitButton.style.backgroundColor = 'purple'; // Maintain purple background after click
      submitButton.style.color = 'black';  // Ensure text color stays black after click
    });
  
    // Mouse hover effect for Reset Password Button (change to purple on hover)
    resetPasswordBtn.addEventListener('mouseenter', () => {
      resetPasswordBtn.style.backgroundColor = 'purple';
      resetPasswordBtn.style.boxShadow = '0 0 10px rgba(128, 0, 128, 0.6)';
    });
  
    // Mouse leave effect for Reset Password Button (reset to original color)
    resetPasswordBtn.addEventListener('mouseleave', () => {
      resetPasswordBtn.style.backgroundColor = '#fff'; // Original color
      resetPasswordBtn.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.1)'; // Reset shadow
    });
  
    // Mouse click effect for Reset Password Button (turns blue when clicked)
    resetPasswordBtn.addEventListener('mousedown', () => {
      resetPasswordBtn.style.backgroundColor = 'blue';
      resetPasswordBtn.style.color = 'black';  // Ensure text color stays black when clicked
    });
  
    // Reset color for Reset Password Button when mouse is released (on mouse up)
    resetPasswordBtn.addEventListener('mouseup', () => {
      resetPasswordBtn.style.backgroundColor = 'purple'; // Maintain purple background after click
      resetPasswordBtn.style.color = 'black';  // Ensure text color stays black after click
    });

    // Handle form submit to show OTP fields and Reset Password button
    submitButton.addEventListener('click', (event) => {
      event.preventDefault();  // Prevent the form from submitting immediately
      otpFields.style.display = 'block'; // Show OTP field and Reset Password button
      resetPasswordBtn.style.display = 'inline-block'; // Display the Reset Password button
    });
  
    // Handle Reset Password button click (this can be extended for real functionality)
    resetPasswordBtn.addEventListener('click', () => {
      const otp = document.getElementById('otp').value;
      if (otp) {
        alert('OTP Verified! You can now reset your password.');
        // Redirect to password reset page or add further functionality here
      } else {
        alert('Please enter a valid OTP.');
      }
    });
  });
