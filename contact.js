  (function() {
    const form = document.getElementById('contactForm');
    const statusDiv = document.getElementById('formStatus');
    const submitBtn = document.getElementById('submitBtn');
    const originalBtnHTML = submitBtn.innerHTML;

    // Helper to show status messages
    function showStatus(message, type) {
      statusDiv.classList.remove('hidden', 'status-success', 'status-error', 'status-loading');
      if (type === 'success') {
        statusDiv.classList.add('status-success');
        statusDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
      } else if (type === 'error') {
        statusDiv.classList.add('status-error');
        statusDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
      } else if (type === 'loading') {
        statusDiv.classList.add('status-loading');
        statusDiv.innerHTML = `<i class="fas fa-spinner fa-pulse"></i> ${message}`;
      }
      statusDiv.classList.remove('hidden');
      
      // Auto hide success/error after 6 seconds
      if (type !== 'loading') {
        setTimeout(() => {
          if (statusDiv && !statusDiv.classList.contains('hidden')) {
            if (statusDiv.innerHTML.includes(message) || type === 'success' || type === 'error') {
              statusDiv.classList.add('hidden');
            }
          }
        }, 6000);
      }
    }

    function resetButton(enable = true) {
      if (enable) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnHTML;
      } else {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span>Sending...</span> <i class="fas fa-spinner fa-pulse"></i>`;
      }
    }

    // Email validation
    function isValidEmail(email) {
      const emailRegex = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
      return emailRegex.test(email);
    }

    // Client-side validation
    function validateForm() {
      const nameInput = document.getElementById('name');
      const emailInput = document.getElementById('email');
      const messageInput = document.getElementById('message');
      
      const nameVal = nameInput.value.trim();
      const emailVal = emailInput.value.trim();
      const messageVal = messageInput.value.trim();
      
      if (!nameVal) {
        showStatus('Please enter your full name.', 'error');
        nameInput.focus();
        return false;
      }
      if (!emailVal) {
        showStatus('Please enter your email address.', 'error');
        emailInput.focus();
        return false;
      }
      if (!isValidEmail(emailVal)) {
        showStatus('Please enter a valid email address (e.g., name@domain.com).', 'error');
        emailInput.focus();
        return false;
      }
      if (!messageVal) {
        showStatus('Message cannot be empty. Please write your message.', 'error');
        messageInput.focus();
        return false;
      }
      return true;
    }

    // Handle form submission via fetch (no page reload)
    async function handleSubmit(event) {
      event.preventDefault();
      
      if (!validateForm()) {
        return;
      }
      
      const formData = new FormData(form);
      
      resetButton(false);
      showStatus('Submitting your message, please wait...', 'loading');
      
      try {
        const response = await fetch('https://api.web3forms.com/submit', {
          method: 'POST',
          body: formData,
          headers: {
            'Accept': 'application/json',
          }
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
          showStatus('✨ Message sent successfully! We’ll get back to you soon.', 'success');
          form.reset(); // clear all fields after success
          // Reset any extra state (textarea scroll position resets naturally)
        } else {
          let errorMsg = result.message || 'Submission failed. Please try again later.';
          if (errorMsg.toLowerCase().includes('spam') || errorMsg.toLowerCase().includes('bot')) {
            errorMsg = 'Spam protection triggered. Please ensure you are not a robot and try again.';
          }
          showStatus(`❌ ${errorMsg}`, 'error');
        }
      } catch (networkError) {
        console.error('Network error:', networkError);
        showStatus('⚠️ Network error: Could not connect to the server. Please check your internet and try again.', 'error');
      } finally {
        resetButton(true);
      }
    }
    
    form.addEventListener('submit', handleSubmit);
    
    // Optional: live error clearance on typing (UX improvement)
    const nameField = document.getElementById('name');
    const emailField = document.getElementById('email');
    const msgField = document.getElementById('message');
    
    function clearStatusOnInput() {
      if (statusDiv && !statusDiv.classList.contains('hidden') && 
          (statusDiv.classList.contains('status-error') || statusDiv.classList.contains('status-success'))) {
        statusDiv.classList.add('hidden');
      }
    }
    
    nameField.addEventListener('input', clearStatusOnInput);
    emailField.addEventListener('input', clearStatusOnInput);
    msgField.addEventListener('input', clearStatusOnInput);
    
    // Additional feature: show scroll hint when text overflows (just for fun, but not necessary)
    // Ensure textarea remains fixed size — already enforced by CSS with resize: none and fixed height.
    // For demonstration, we can add a small dynamic check but not needed.
    
    // Adding a small visual indicator when textarea content overflows (optional)
    const textarea = document.getElementById('message');
    function checkOverflow() {
      if (textarea.scrollHeight > textarea.clientHeight) {
        // content overflow - scroll is active (default behavior). Nothing to change.
        // We could add a subtle class but not needed.
        textarea.setAttribute('data-overflow', 'true');
      } else {
        textarea.setAttribute('data-overflow', 'false');
      }
    }
    textarea.addEventListener('input', checkOverflow);
    textarea.addEventListener('scroll', checkOverflow);
    // initial call
    checkOverflow();
    
    // Add a small note in console for developers (just for confirmation)
    console.log('Form ready — Message box has fixed size (130px) with scroll. No auto-grow.');
    
    // Disable any previous auto-resize scripts if any (none present)
    // Also ensure that the textarea doesn't get any inline style from previous scripts
    // Override any possible JS modifications: we lock height attribute via CSS important? Not needed.
    // Extra safety: prevent any script from dynamically changing textarea style height
    // (if some external script tries, but we have full control)
    Object.defineProperty(textarea.style, 'height', {
      set: function() {
        // do nothing to preserve fixed height, but we don't need to block. CSS already has !important? 
        // Actually CSS height with !important can be added, but we use standard specificity.
        // To be absolutely safe, add a setInterval? not recommended. CSS already strong enough.
      }
    });
    
    // Ensure that textarea never expands: watch for any attribute modifications? Not needed.
    // Just confirm style via CSS. The style rule has resize: none and fixed height which ensures it.
    // For better demonstration, add an extra MutationObserver? Not required.
  })();
