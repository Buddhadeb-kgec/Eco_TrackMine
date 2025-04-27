document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const userRole = document.getElementById('userRole');
    const dynamicFields = document.getElementById('dynamicFields');

    // Store user ID and verification status
    let userId = null;
    let verificationStatus = {
        email: false,
        phone: false
    };

    // Dynamic fields for different roles
    const roleFields = {
        planter: `
            <div class="input-box">
                <input type="text" name="full_name" placeholder="Full Name" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="tel" name="phone" placeholder="Phone Number" required>
                <i class='bx bxs-phone'></i>
            </div>
            <div class="input-box">
                <textarea name="address" placeholder="Address" required></textarea>
                <i class='bx bxs-map'></i>
            </div>
            <div id="verification-section" style="display: none;">
                <div class="verification-box">
                    <h3>Email Verification</h3>
                    <button type="button" class="btn request-otp" data-type="email">Request Email OTP</button>
                    <div class="input-box">
                        <input type="text" name="email_otp" placeholder="Enter Email OTP" maxlength="6">
                        <button type="button" class="btn verify-otp" data-type="email">Verify Email</button>
                    </div>
                    <div class="status"></div>
                </div>
                <div class="verification-box">
                    <h3>Phone Verification</h3>
                    <button type="button" class="btn request-otp" data-type="phone">Request Phone OTP</button>
                    <div class="input-box">
                        <input type="text" name="phone_otp" placeholder="Enter Phone OTP" maxlength="6">
                        <button type="button" class="btn verify-otp" data-type="phone">Verify Phone</button>
                    </div>
                    <div class="status"></div>
                </div>
            </div>
        `,
        admin: `
            <div class="input-box">
                <input type="text" name="company_name" placeholder="Company Name" required>
                <i class='bx bxs-building'></i>
            </div>
            <div class="input-box">
                <input type="text" name="license_number" placeholder="License Number" required>
                <i class='bx bxs-id-card'></i>
            </div>
            <div class="input-box">
                <input type="tel" name="phone" placeholder="Phone Number" required>
                <i class='bx bxs-phone'></i>
            </div>
            <div id="verification-section" style="display: none;">
                <div class="verification-box">
                    <h3>Email Verification</h3>
                    <button type="button" class="btn request-otp" data-type="email">Request Email OTP</button>
                    <div class="input-box">
                        <input type="text" name="email_otp" placeholder="Enter Email OTP" maxlength="6">
                        <button type="button" class="btn verify-otp" data-type="email">Verify Email</button>
                    </div>
                    <div class="status"></div>
                </div>
                <div class="verification-box">
                    <h3>Phone Verification</h3>
                    <button type="button" class="btn request-otp" data-type="phone">Request Phone OTP</button>
                    <div class="input-box">
                        <input type="text" name="phone_otp" placeholder="Enter Phone OTP" maxlength="6">
                        <button type="button" class="btn verify-otp" data-type="phone">Verify Phone</button>
                    </div>
                    <div class="status"></div>
                </div>
            </div>
        `
    };

    // Update dynamic fields when role changes
    userRole.addEventListener('change', function() {
        const selectedRole = this.value;
        dynamicFields.innerHTML = roleFields[selectedRole] || '';
        
        // Reset verification status
        userId = null;
        verificationStatus = {
            email: false,
            phone: false
        };
        
        // Hide verification section
        const verificationSection = document.getElementById('verification-section');
        if (verificationSection) {
            verificationSection.style.display = 'none';
        }
    });

    // Handle OTP request
    document.addEventListener('click', async function(e) {
        if (!e.target.matches('.request-otp')) return;
        
        const type = e.target.dataset.type;
        const button = e.target;
        const statusDiv = button.closest('.verification-box').querySelector('.status');
        
        try {
            button.disabled = true;
            button.textContent = 'Sending...';
            
            const response = await fetch('../backend/api/request_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    type: type
                })
            });

            const result = await response.json();
            
            if (result.success) {
                statusDiv.textContent = `OTP sent to your ${type}`;
                statusDiv.className = 'status success';
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            statusDiv.textContent = error.message;
            statusDiv.className = 'status error';
        } finally {
            button.disabled = false;
            button.textContent = `Request ${type} OTP`;
        }
    });

    // Handle OTP verification
    document.addEventListener('click', async function(e) {
        if (!e.target.matches('.verify-otp')) return;
        
        const type = e.target.dataset.type;
        const button = e.target;
        const verificationBox = button.closest('.verification-box');
        const otpInput = verificationBox.querySelector(`input[name="${type}_otp"]`);
        const statusDiv = verificationBox.querySelector('.status');
        
        if (!otpInput.value) {
            statusDiv.textContent = 'Please enter OTP';
            statusDiv.className = 'status error';
            return;
        }

        try {
            button.disabled = true;
            button.textContent = 'Verifying...';
            
            const response = await fetch('../backend/api/verify_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    type: type,
                    otp: otpInput.value
                })
            });

            const result = await response.json();
            
            if (result.success) {
                verificationStatus[type] = true;
                statusDiv.textContent = `${type} verified successfully`;
                statusDiv.className = 'status success';
                
                // Disable verification controls
                otpInput.disabled = true;
                button.disabled = true;
                verificationBox.querySelector('.request-otp').disabled = true;
                
                if (result.account_activated) {
                    alert('Account verification complete! You can now log in.');
                    window.location.href = 'index.html';
                }
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            statusDiv.textContent = error.message;
            statusDiv.className = 'status error';
            button.disabled = false;
        } finally {
            button.textContent = `Verify ${type}`;
        }
    });

    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitButton = form.querySelector('button[type="submit"]');
        const verificationSection = document.getElementById('verification-section');

        try {
            submitButton.disabled = true;
            submitButton.textContent = 'Registering...';

            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => data[key] = value);

            const response = await fetch('../backend/api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                userId = result.user_id;
                
                // Hide registration form and show verification section
                form.querySelectorAll('input, select, textarea').forEach(input => {
                    if (input.type !== 'submit') {
                        input.disabled = true;
                    }
                });
                
                verificationSection.style.display = 'block';
                submitButton.style.display = 'none';
                
                // Scroll to verification section
                verificationSection.scrollIntoView({ behavior: 'smooth' });
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            alert(error.message);
            submitButton.disabled = false;
        } finally {
            submitButton.textContent = 'Register';
        }
    });
});
