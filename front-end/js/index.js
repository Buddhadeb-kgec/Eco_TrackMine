document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById('loginForm');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        try {
            const formData = new FormData(loginForm);
            const data = {
                username: formData.get('username'),
                password: formData.get('password')
            };

            const response = await fetch('../backend/api/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                // Store user data in localStorage
                localStorage.setItem('user', JSON.stringify(result.user));
                
                // Redirect based on role
                if (result.user.role === 'planter') {
                    window.location.href = 'planter_dashboard.html';
                } else if (result.user.role === 'admin') {
                    window.location.href = 'admin_dashboard.html';
                }
            } else {
                alert(result.message || 'Login failed. Please try again.');
            }
        } catch (error) {
            console.error('Login error:', error);
            alert('An error occurred during login. Please try again.');
        }
    });

    // Add hover and click effects
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
});
