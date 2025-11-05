<?php
// forgot-password.php (UI + JS)
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Forgot Password | CEIT Thesis Hub</title>
    <link rel="icon" type="image/png" href="pictures/Logo.png">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <main class="login-main">
        <div class="login-container">
            <div class="login-box">
                <img src="pictures/Logo.png" alt="CEIT Logo" class="logo">
                <h2>Forgot Password</h2>

                <form id="sendForm">
                    <div class="input-group">
                        <input type="email" id="emailInput" name="email" placeholder="Enter your registered email" required>
                    </div>
                    <button type="submit" class="btn">Send Code</button>
                </form>

                <p style="margin-top:15px;"><a href="index.php">Back to Login</a></p>
                <div id="msg" class="small-muted"></div>
            </div>
        </div>
    </main>

    <!-- Modal 1: Code entry -->
    <div id="modalCode" class="modal-backdrop">
        <div class="modal" role="dialog" aria-modal="true">
            <h3>Code Sent</h3>
            <p class="small-muted">A verification code has been sent to <span id="maskedEmail" class="masked"></span></p>
            <div class="input-group">
                <input type="text" id="codeInput" placeholder="Enter 6-digit code" maxlength="6" inputmode="numeric" />
            </div>
            <div id="codeErr" class="error" style="display:none;"></div>
            <div style="display:flex;gap:8px;margin-top:10px;">
                <button id="confirmCodeBtn" class="btn">Confirm Code</button>
                <button id="resendBtn" class="btn secondary">Resend</button>
            </div>
            <div style="margin-top:10px;"><small class="small-muted">Code valid for 10 minutes.</small></div>
        </div>
    </div>

    <!-- Modal 2: Reset password -->
    <div id="modalReset" class="modal-backdrop">
        <div class="modal" role="dialog" aria-modal="true">
            <h3>Reset Password</h3>
            <p class="small-muted">Email: <span id="resetEmail" class="masked"></span></p>
            <div class="input-group"><input type="password" id="newPass" placeholder="New password" /></div>
            <div class="input-group"><input type="password" id="confirmPass" placeholder="Confirm password" /></div>
            <div id="resetErr" class="error" style="display:none;"></div>
            <div id="resetSuccess" class="success" style="display:none;"></div>
            <div style="display:flex;gap:8px;margin-top:10px;">
                <button id="resetBtn" class="btn">Reset Password</button>
                <button id="cancelReset" class="btn secondary">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        const sendForm = document.getElementById('sendForm');
        const msg = document.getElementById('msg');
        const modalCode = document.getElementById('modalCode');
        const maskedEmailEl = document.getElementById('maskedEmail');
        const codeInput = document.getElementById('codeInput');
        const codeErr = document.getElementById('codeErr');
        const confirmCodeBtn = document.getElementById('confirmCodeBtn');
        const resendBtn = document.getElementById('resendBtn');

        const modalReset = document.getElementById('modalReset');
        const resetEmailEl = document.getElementById('resetEmail');
        const newPass = document.getElementById('newPass');
        const confirmPass = document.getElementById('confirmPass');
        const resetErr = document.getElementById('resetErr');
        const resetSuccess = document.getElementById('resetSuccess');
        const resetBtn = document.getElementById('resetBtn');
        const cancelReset = document.getElementById('cancelReset');

        let currentEmail = '';

        function showModal(el) {
            el.style.display = 'flex';
        }

        function hideModal(el) {
            el.style.display = 'none';
        }
        hideModal(modalCode);
        hideModal(modalReset);

        sendForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('emailInput').value.trim();
            if (!email) {
                msg.textContent = 'Please enter your email.';
                return;
            }
            msg.textContent = 'Sending code...';

            fetch('send_code.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        email
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        currentEmail = email;
                        maskedEmailEl.textContent = data.masked;
                        resetEmailEl.textContent = data.masked;
                        msg.textContent = 'Code sent — check your email.';
                        // Show code modal
                        showModal(modalCode);
                        codeInput.value = '';
                        codeErr.style.display = 'none';
                    } else {
                        msg.textContent = data.msg || 'Error sending code.';
                    }
                })
                .catch(err => {
                    console.error(err);
                    msg.textContent = 'Network error.';
                });
        });

        // confirm code
        confirmCodeBtn.addEventListener('click', function() {
            const code = codeInput.value.trim();
            codeErr.style.display = 'none';
            if (!/^\d{6}$/.test(code)) {
                codeErr.textContent = 'Enter a valid 6-digit code.';
                codeErr.style.display = 'block';
                return;
            }

            fetch('verify_code.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        email: currentEmail,
                        code
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        hideModal(modalCode);
                        // show reset modal
                        showModal(modalReset);
                        resetErr.style.display = 'none';
                        resetSuccess.style.display = 'none';
                        newPass.value = '';
                        confirmPass.value = '';
                    } else {
                        codeErr.textContent = data.msg || 'Invalid code.';
                        codeErr.style.display = 'block';
                    }
                }).catch(err => {
                    codeErr.textContent = 'Network error.';
                    codeErr.style.display = 'block';
                });
        });

        // resend (re-trigger send_code)
        resendBtn.addEventListener('click', function() {
            if (!currentEmail) return;
            fetch('send_code.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        email: currentEmail
                    })
                })
                .then(r => r.json()).then(data => {
                    if (data.success) {
                        maskedEmailEl.textContent = data.masked;
                        msg.textContent = 'New code sent.';
                    } else {
                        msg.textContent = data.msg || 'Could not resend.';
                    }
                }).catch(() => msg.textContent = 'Network error.');
        });

        // Reset password
        resetBtn.addEventListener('click', function() {
            resetErr.style.display = 'none';
            resetSuccess.style.display = 'none';
            const p = newPass.value.trim();
            const c = confirmPass.value.trim();
            if (p.length < 6) {
                resetErr.textContent = 'Password must be at least 6 characters.';
                resetErr.style.display = 'block';
                return;
            }
            if (p !== c) {
                resetErr.textContent = 'Passwords do not match.';
                resetErr.style.display = 'block';
                return;
            }

            // We need the code they entered earlier. We'll ask the user to re-enter code if we don't keep it.
            // To avoid storing code in JS, ask for code again OR store it temporarily when verified.
            // Here we'll re-use codeInput.value (user entered earlier) — ensure it's still present.
            const code = codeInput.value.trim();
            if (!/^\d{6}$/.test(code)) {
                resetErr.textContent = 'Code missing. Please confirm code first.';
                resetErr.style.display = 'block';
                return;
            }

            fetch('reset_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        email: currentEmail,
                        code: code,
                        password: p
                    })
                })
                .then(r => r.json()).then(data => {
                    if (data.success) {
                        resetSuccess.textContent = 'Password reset successful. Redirecting to login...';
                        resetSuccess.style.display = 'block';
                        // clear fields and close after a moment
                        setTimeout(() => {
                            hideModal(modalReset);
                            window.location.href = 'index.php';
                        }, 1500);
                    } else {
                        resetErr.textContent = data.msg || 'Reset failed.';
                        resetErr.style.display = 'block';
                    }
                }).catch(() => {
                    resetErr.textContent = 'Network error.';
                    resetErr.style.display = 'block';
                });
        });

        cancelReset.addEventListener('click', function() {
            hideModal(modalReset);
            showModal(modalCode);
        });

        // close modal when clicking backdrop (optional)
        modalCode.addEventListener('click', function(e) {
            if (e.target === modalCode) hideModal(modalCode);
        });
        modalReset.addEventListener('click', function(e) {
            if (e.target === modalReset) hideModal(modalReset);
        });
    </script>
</body>

</html>