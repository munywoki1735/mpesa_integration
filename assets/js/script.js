// Utility function to show/hide loading state
function setLoading(button, isLoading) {
    if (isLoading) {
        button.classList.add('loading');
        button.disabled = true;
    } else {
        button.classList.remove('loading');
        button.disabled = false;
    }
}

// Utility function to display results
function showResult(elementId, type, title, message, data = null) {
    const resultDiv = document.getElementById(elementId);
    resultDiv.className = `result ${type} show`;
    
    let html = `<strong>${title}</strong><p>${message}</p>`;
    
    if (data) {
        html += `<pre>${JSON.stringify(data, null, 2)}</pre>`;
    }
    
    resultDiv.innerHTML = html;
    
    // Scroll to result
    resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// STK Push Form
document.getElementById('stkForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const button = e.target.querySelector('button[type="submit"]');
    const resultDiv = document.getElementById('stkResult');
    
    setLoading(button, true);
    resultDiv.style.display = 'none';
    
    try {
        const response = await fetch('api/stk_push.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.status == 200 && data.response.ResponseCode == '0') {
            showResult('stkResult', 'success', 
                'Payment Request Sent Successfully',
                'The payment request has been sent to the customer\'s phone. They will receive a prompt to enter their M-Pesa PIN.',
                data.response
            );
        } else {
            showResult('stkResult', 'error',
                'Request Failed',
                data.response.errorMessage || data.response.ResponseDescription || 'An error occurred',
                data.response
            );
        }
    } catch (error) {
        showResult('stkResult', 'error',
            'Request Failed',
            error.message || 'Network error occurred'
        );
    } finally {
        setLoading(button, false);
    }
});

// STK Query Form
document.getElementById('queryForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const button = e.target.querySelector('button[type="submit"]');
    const resultDiv = document.getElementById('queryResult');
    
    setLoading(button, true);
    resultDiv.style.display = 'none';
    
    try {
        const response = await fetch('api/stk_query.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.status == 200) {
            const resultCode = data.response.ResultCode;
            let statusMessage = '';
            
            if (resultCode == '0') {
                statusMessage = 'Transaction completed successfully';
            } else if (resultCode == '1032') {
                statusMessage = 'Transaction cancelled by user';
            } else if (resultCode == '1037') {
                statusMessage = 'Transaction timed out (user did not enter PIN)';
            } else {
                statusMessage = data.response.ResultDesc || 'Transaction status retrieved';
            }
            
            showResult('queryResult', resultCode == '0' ? 'success' : 'error',
                'Transaction Status',
                statusMessage,
                data.response
            );
        } else {
            showResult('queryResult', 'error',
                'Query Failed',
                'Could not retrieve transaction status',
                data.response
            );
        }
    } catch (error) {
        showResult('queryResult', 'error',
            'Query Failed',
            error.message || 'Network error occurred'
        );
    } finally {
        setLoading(button, false);
    }
});

// C2B Register Form
document.getElementById('c2bRegisterForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const button = e.target.querySelector('button[type="submit"]');
    const resultDiv = document.getElementById('c2bRegisterResult');
    
    setLoading(button, true);
    resultDiv.style.display = 'none';
    
    try {
        const response = await fetch('api/c2b_register.php', {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.status == 200 && data.response.ResponseCode == '0') {
            showResult('c2bRegisterResult', 'success',
                'URLs Registered Successfully',
                'Your validation and confirmation URLs have been registered with M-Pesa.',
                data.response
            );
        } else {
            showResult('c2bRegisterResult', 'error',
                'Registration Failed',
                data.response.errorMessage || data.response.ResponseDescription || 'Failed to register URLs',
                data.response
            );
        }
    } catch (error) {
        showResult('c2bRegisterResult', 'error',
            'Registration Failed',
            error.message || 'Network error occurred'
        );
    } finally {
        setLoading(button, false);
    }
});

// C2B Simulate Form
document.getElementById('c2bSimulateForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const button = e.target.querySelector('button[type="submit"]');
    const resultDiv = document.getElementById('c2bSimulateResult');
    
    setLoading(button, true);
    resultDiv.style.display = 'none';
    
    try {
        const response = await fetch('api/c2b_simulate.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.status == 200 && data.response.ResponseCode == '0') {
            showResult('c2bSimulateResult', 'success',
                'Payment Simulated Successfully',
                'C2B payment has been simulated. Check the confirmation callback logs.',
                data.response
            );
        } else {
            showResult('c2bSimulateResult', 'error',
                'Simulation Failed',
                data.response.errorMessage || data.response.ResponseDescription || 'Failed to simulate payment',
                data.response
            );
        }
    } catch (error) {
        showResult('c2bSimulateResult', 'error',
            'Simulation Failed',
            error.message || 'Network error occurred'
        );
    } finally {
        setLoading(button, false);
    }
});

// Token Form
document.getElementById('tokenForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const button = e.target.querySelector('button[type="submit"]');
    const resultDiv = document.getElementById('tokenResult');
    
    setLoading(button, true);
    resultDiv.style.display = 'none';
    
    try {
        const response = await fetch('api/generate_token.php', {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showResult('tokenResult', 'success',
                'Access Token Generated',
                'Authentication successful. Token is valid for 1 hour.',
                { token: data.token.substring(0, 100) + '...' }
            );
        } else {
            showResult('tokenResult', 'error',
                'Token Generation Failed',
                data.error || 'Failed to generate access token'
            );
        }
    } catch (error) {
        showResult('tokenResult', 'error',
            'Token Generation Failed',
            error.message || 'Network error occurred'
        );
    } finally {
        setLoading(button, false);
    }
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
