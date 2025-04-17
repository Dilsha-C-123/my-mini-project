document.getElementById("contactNumber").addEventListener('input', function() {
    let phone = this.value;
    
    // Remove any non-numeric characters
    phone = phone.replace(/[^0-9]/g, '');
    this.value = phone;
    
    // Validate phone number format
    if (phone.match(/^[6-9][0-9]{9}$/)) {
        this.classList.remove('invalid');
        this.classList.add('valid');
        document.getElementById('phone-error').style.display = 'none';
    } else {
        this.classList.remove('valid');
        this.classList.add('invalid');
        document.getElementById('phone-error').style.display = 'block';
    }
});

// Email validation function
function isValidEmail(email) {
    email = email.trim().toLowerCase();
    
    // Common email domains
    const commonDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];
    
    // Educational institution domain endings
    const eduDomains = ['.edu', '.edu.in', '.ac.in'];
    
    // Split email into username and domain
    const [username, domain] = email.split('@');

    // Basic format validation
    if (!email.includes('@') || !username || username.length < 2) {
        return false;
    }

    // Check if domain is either a common domain or an educational domain
    const isCommonDomain = commonDomains.includes(domain);
    const isEduDomain = eduDomains.some(eduDomain => domain.endsWith(eduDomain));

    // Username validation (allow letters, numbers, dots, underscores, hyphens)
    const validUsername = /^[a-z0-9._-]+$/.test(username);

    return validUsername && (isCommonDomain || isEduDomain);
}

// Email input validation
document.getElementById('email').addEventListener('input', function() {
    const email = this.value.trim();
    const errorElement = document.getElementById('email-error');
    const submitButton = document.querySelector('button[type="submit"]');
    
    if (!isValidEmail(email)) {
        this.classList.remove('valid');
        this.classList.add('invalid');
        errorElement.textContent = 'Please use a valid email address (gmail.com, yahoo.com, outlook.com, hotmail.com, or educational institution email)';
        errorElement.style.display = 'block';
        submitButton.disabled = true;
    } else {
        this.classList.remove('invalid');
        this.classList.add('valid');
        errorElement.style.display = 'none';
        submitButton.disabled = false;
    }
});

// Add this function to check email existence
async function checkEmailExists(email) {
    try {
        const response = await fetch('check_email.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `email=${encodeURIComponent(email)}`
        });
        const data = await response.json();
        return data.exists;
    } catch (error) {
        console.error('Error checking email:', error);
        return false;
    }
}

// Add these new functions
function checkFoodType() {
    const foodType = document.getElementById('foodType').value;
    const wasteChargesSection = document.getElementById('wasteChargesSection');
    const quantity = document.getElementById('quantity').value;

    if (foodType === 'Waste Food') {
        wasteChargesSection.style.display = 'block';
        calculateCharges();
    } else {
        wasteChargesSection.style.display = 'none';
    }
}

function calculateCharges() {
    const foodType = document.getElementById('foodType').value;
    const quantity = document.getElementById('quantity').value;
    const ratePerKg = 10; // ₹10 per kg for waste food

    if (foodType === 'Waste Food' && quantity > 0) {
        const totalCharges = quantity * ratePerKg;
        document.getElementById('totalCharges').textContent = totalCharges;
    }
}
