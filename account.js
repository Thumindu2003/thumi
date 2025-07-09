// Validation functions
function validateUsername(username) {
  const regex = /^(?=.*[a-zA-Z])(?=.*\d).{5,}$/;
  return regex.test(username);
}

function validatePassword(password) {
  const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,14}$/;
  return regex.test(password);
}

function validatePhone(phone) {
  const regex = /^07[0-9]{8}$/; // Matches Sri Lankan phone numbers
  return regex.test(phone);
}

function validateName(name) {
  return /^[A-Za-z\s]+$/.test(name) && name.length >= 5 && name.length <= 30;
}

function validateEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

// Helper function to validate a single field
function validateField(input, error, validationFn, errorMessage) {
  if (!validationFn(input.value)) {
    error.textContent = errorMessage;
    error.style.display = 'block';
    return false;
  } else {
    error.style.display = 'none';
    return true;
  }
}

// Validate entire signup form
function validateSignUpForm() {
  let isValid = true;

  // Validate Name with Initial
  isValid &= validateField(
    document.getElementById('nameWithInitial'),
    document.getElementById('nameError'),
    validateName,
    'Name must be 5-30 characters long and contain only letters and spaces.'
  );

  // Validate Email
  isValid &= validateField(
    document.getElementById('email'),
    document.getElementById('emailError'),
    validateEmail,
    'Invalid email format.'
  );

  // Validate Phone Number
  isValid &= validateField(
    document.getElementById('phone'),
    document.getElementById('phoneError'),
    validatePhone,
    'Invalid phone number. Must be a valid Sri Lankan network.'
  );

  // Validate Username
  isValid &= validateField(
    document.getElementById('signupUsername'),
    document.getElementById('usernameError'),
    validateUsername,
    'Username must be at least 5 characters long and contain letters and numbers.'
  );

  // Validate Password
  isValid &= validateField(
    document.getElementById('signupPassword'),
    document.getElementById('passwordError'),
    validatePassword,
    'Password must contain uppercase, lowercase, numbers, and be 8-14 characters long.'
  );

  // Validate Confirm Password
  const password = document.getElementById('signupPassword').value;
  const confirmPassword = document.getElementById('confirmPassword').value;
  const confirmPasswordError = document.getElementById('confirmPasswordError');

  if (password !== confirmPassword) {
    confirmPasswordError.textContent = 'Passwords do not match.';
    confirmPasswordError.style.display = 'block';
    isValid = false;
  } else {
    confirmPasswordError.style.display = 'none';
  }

  if (isValid) {
    alert('Account created successfully!');
  }

  return isValid;
}

// Validate entire login form
function validateLoginForm() {
  let isValid = true;

  // Validate Username
  isValid &= validateField(
    document.getElementById('loginUsername'),
    document.getElementById('loginUsernameError'),
    validateUsername,
    'Please enter a valid username (min 5 chars with letters and numbers).'
  );

  // Validate Password
  isValid &= validateField(
    document.getElementById('loginPassword'),
    document.getElementById('loginPasswordError'),
    validatePassword,
    'Password must contain uppercase, lowercase, and numbers.'
  );

  if (isValid) {
    alert('Login successful!');
  }

  return isValid;
}

// Setup form submission handlers
function setupFormValidation() {
  const signupForm = document.getElementById('signupForm');
  if (signupForm) {
    signupForm.addEventListener('submit', function (e) {
      if (!validateSignUpForm()) {
        e.preventDefault();
      }
    });

    signupForm.addEventListener('submit', function (event) {
      const emailInput = document.getElementById('email');
      const emailError = document.getElementById('emailError');
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email regex pattern

      if (!emailPattern.test(emailInput.value)) {
        emailError.textContent = "Please enter a valid email address.";
        emailError.style.display = "block";
        event.preventDefault(); // Prevent form submission
      } else {
        emailError.style.display = "none";
      }
    });
  }

  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      if (!validateLoginForm()) {
        e.preventDefault();
      }
    });
  }

  document.getElementById('phone').addEventListener('input', function () {
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phoneError');
    const phonePattern = /^[0-9]{10}$/; // Accepts exactly 10 digits

    if (!phonePattern.test(phoneInput.value)) {
        phoneError.textContent = "Enter a valid 10-digit phone number.";
        phoneError.style.display = "block";
    } else {
        phoneError.style.display = "none";
    }
  });

  document.getElementById('phone').addEventListener('blur', function () {
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phoneError');
    const phonePattern = /^[0-9]{10}$/; // Accepts exactly 10 digits

    if (!phonePattern.test(phoneInput.value)) {
        phoneError.textContent = "Enter a valid 10-digit phone number.";
        phoneError.style.display = "block";
    } else {
        phoneError.style.display = "none";
    }
  });

  document.getElementById('email').addEventListener('input', function () {
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('emailError');
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email regex pattern

    if (!emailPattern.test(emailInput.value)) {
        emailError.textContent = "Please enter a valid email address.";
        emailError.style.display = "block";
    } else {
        emailError.style.display = "none";
    }
  });
}

// Initialize validation when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
  setupFormValidation();
});

// Function to show the Sign-Up page
function showSignUp() {
  document.getElementById('login-page').style.display = 'none';
  document.getElementById('signup-page').style.display = 'block';
}

// Function to show the Login page
function showLogin() {
  document.getElementById('signup-page').style.display = 'none';
  document.getElementById('login-page').style.display = 'block';
}

// Optional: Add event listeners for buttons if needed
document.addEventListener('DOMContentLoaded', () => {
  const signupButton = document.querySelector('.signup-button');
  const loginLink = document.querySelector('.alternate-action a');

  if (signupButton) {
    signupButton.addEventListener('click', showSignUp);
  }

  if (loginLink) {
    loginLink.addEventListener('click', showLogin);
  }
});