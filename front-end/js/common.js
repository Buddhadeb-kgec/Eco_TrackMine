// Profile Button Functionality
document.querySelector('.profile-btn').addEventListener('click', function() {
    // Placeholder for future profile actions
    console.log("Profile button clicked");
});

// JavaScript for the Carousel
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-image');
const totalSlides = slides.length;

function updateSlidePosition() {
    const carousel = document.querySelector('.carousel');
    carousel.style.transform = `translateX(-${currentSlide * 100}%)`; // Fixed the syntax error here
}

function moveSlide(direction) {
    currentSlide += direction;

    if (currentSlide < 0) {
        currentSlide = totalSlides - 1;
    } else if (currentSlide >= totalSlides) {
        currentSlide = 0;
    }

    updateSlidePosition();
}

let autoPlayInterval;
function autoPlay() {
    autoPlayInterval = setInterval(() => {
        moveSlide(1);
    }, 5000); // 5 seconds per slide
}

function pauseAutoPlay() {
    clearInterval(autoPlayInterval);
}

document.querySelector('.left-arrow').addEventListener('click', () => {
    pauseAutoPlay();
    moveSlide(-1);
    autoPlay();
});

document.querySelector('.right-arrow').addEventListener('click', () => {
    pauseAutoPlay();
    moveSlide(1);
    autoPlay();
});

// Automatically start autoplay on page load
updateSlidePosition();
autoPlay();

// Statistics Section: Update user count dynamically
const userCountElement = document.getElementById('user-count');

async function fetchUserCount() {
    try {
        const response = await fetch('/api/getUserCount');
        const data = await response.json();
        if (data && data.userCount) {
            // Update the displayed user count
            userCountElement.textContent = data.userCount;
        }
    } catch (error) {
        console.error('Error fetching user count:', error);
    }
}

function updateUserCountPeriodically() {
    fetchUserCount();
    setInterval(fetchUserCount, 5000); // Update every 5 seconds
}

updateUserCountPeriodically();

// Smooth Scroll for Navbar Links (Only for internal section links)
const navLinks = document.querySelectorAll('.nav-links a');
navLinks.forEach(link => {
    link.addEventListener('click', (event) => {
        const href = link.getAttribute('href');
        
        // Check if the link is an internal section (starts with #)
        if (href.startsWith('#')) {
            event.preventDefault(); // Prevent default anchor behavior
            const targetId = href.substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 50, // Scroll with offset for navbar height
                    behavior: 'smooth'
                });
            }
        }
        // Links to other pages should behave normally
    });
});
const profileDropdown = document.getElementById('profileDropdown');

// Mock user data
const isLoggedIn = false; // Change to true to simulate a logged-in user
const userProfile = {
  name: "John Doe",
  email: "john.doe@example.com"
};

// Populate profile dropdown dynamically
function updateProfileDropdown() {
  profileDropdown.innerHTML = ""; // Clear previous content
  if (isLoggedIn) {
    // Display user details
    profileDropdown.innerHTML = `
      <p><strong>Name:</strong> ${userProfile.name}</p>
      <p><strong>Email:</strong> ${userProfile.email}</p>
      <button onclick="logout()">Logout</button>
    `;
  } else {
    // Display login/signup options
    profileDropdown.innerHTML = `
      <button onclick="login()">Login</button>
      <button onclick="signup()">Sign Up</button>
    `;
  }
}

// Login function
function login() {
  alert("Redirecting to login page...");
  // Add login redirection logic here
}

function signup() {
  alert("Redirecting to sign-up page...");
  // Add sign-up redirection logic here
}

function logout() {
  alert("Logging out...");
  // Add logout logic here
}

// Initial setup
updateProfileDropdown();
