<?php

use core\Security;
use core\Views;

Views::extends('main');
?>

<?php Views::section('styles') ?>
    <link href="../../../resources/css/create.css" rel="stylesheet" />
<?php Views::endsection() ?>

<?php Views::section('title') ?>
    UserHub | Users
<?php Views::endsection() ?>

<?php Views::section('content') ?>
<div class="layout-container">
    <div class="create-card">
        <h1 class="create-title">Create New User</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['validation_errors'])): ?>
            <div class="alert alert-error"><pre><?php
                foreach ($_SESSION['validation_errors'] as $error) {
                    echo '<p>'. $error[0] . '</p>';
                }
                ?></pre></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="create-form">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="username" class="form-label">
                        Username <span class="required">*</span>
                    </label>
                    <input type="text" id="username" name="username" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           required
                           pattern="[a-zA-Z0-9_]{3,50}"
                           title="Username must be 3-50 characters and can only contain letters, numbers, and underscores">
                    <span class="form-help">3-50 characters, letters, numbers, and underscores only</span>
                </div>

                <div class="form-group">
                    <label for="first_name" class="form-label">
                        First Name <span class="required">*</span>
                    </label>
                    <input type="text" id="first_name" name="first_name" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                           required
                           maxlength="100">
                    <span class="form-help">Maximum 100 characters</span>
                </div>

                <div class="form-group">
                    <label for="last_name" class="form-label">
                        Last Name <span class="required">*</span>
                    </label>
                    <input type="text" id="last_name" name="last_name" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                           required
                           maxlength="100">
                    <span class="form-help">Maximum 100 characters</span>
                </div>

                <div class="form-group">
                    <label for="gender" class="form-label">
                        Gender <span class="required">*</span>
                    </label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="male" <?php echo ($_POST['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo ($_POST['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?php echo ($_POST['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="birthdate" class="form-label">
                        Birthdate <span class="required">*</span>
                    </label>
                    <input type="date" id="birthdate" name="birthdate" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['birthdate'] ?? ''); ?>"
                           required
                           min="1900-01-01"
                           max="<?php echo date('Y-m-d'); ?>">
                    <span class="form-help">Must be a valid date in the past</span>
                </div>
            </div>

            <div class="password-section">
                <h3 class="section-title">Account Security</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="password" class="form-label">
                            Password <span class="required">*</span>
                        </label>
                        <input type="password" id="password" name="password" class="form-control"
                               required
                               minlength="6">
                        <div class="password-strength">
                            <div class="strength-bar" id="password-strength-bar"></div>
                        </div>
                        <div class="strength-text" id="password-strength-text"></div>
                        <span class="form-help">Minimum 6 characters. Use a strong, unique password.</span>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">
                            Confirm Password <span class="required">*</span>
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                               required>
                        <span class="form-help">Re-enter your password for verification</span>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Create User</button>
                <a href="/users" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php Views::endsection() ?>

<?php Views::section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const strengthBar = document.getElementById('password-strength-bar');
        const strengthText = document.getElementById('password-strength-text');

        
        function checkPasswordStrength(password) {
            let strength = 0;
            let feedback = '';

            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            
            strengthBar.className = 'strength-bar';
            if (password.length === 0) {
                strengthText.textContent = '';
            } else if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
                strengthText.textContent = 'Weak password';
                strengthText.style.color = 'var(--error)';
            } else if (strength <= 4) {
                strengthBar.classList.add('strength-medium');
                strengthText.textContent = 'Medium password';
                strengthText.style.color = 'var(--warning)';
            } else {
                strengthBar.classList.add('strength-strong');
                strengthText.textContent = 'Strong password';
                strengthText.style.color = 'var(--success)';
            }
        }

        
        function checkPasswordMatch() {
            if (password.value === '' && confirmPassword.value === '') {
                confirmPassword.classList.remove('password-match', 'password-mismatch');
            } else if (password.value === confirmPassword.value && password.value.length >= 6) {
                confirmPassword.classList.remove('password-mismatch');
                confirmPassword.classList.add('password-match');
            } else {
                confirmPassword.classList.remove('password-match');
                confirmPassword.classList.add('password-mismatch');
            }
        }

        
        if (password) {
            password.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                checkPasswordMatch();
            });
        }

        if (confirmPassword) {
            confirmPassword.addEventListener('input', checkPasswordMatch);
        }

        
        if (form) {
            form.addEventListener('submit', function(e) {
                
                if (password.value !== confirmPassword.value) {
                    e.preventDefault();
                    alert('Passwords do not match. Please check your entries.');
                    password.focus();
                    return false;
                }

                
                if (password.value.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters long.');
                    password.focus();
                    return false;
                }

                
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.classList.add('btn-loading');
                submitBtn.disabled = true;

                
                <?php if (!empty($success)): ?>
                document.querySelector('.create-card').classList.add('success-pulse');
                <?php endif; ?>
            });
        }

        
        const username = document.getElementById('username');
        if (username) {
            username.addEventListener('input', function() {
                const isValid = /^[a-zA-Z0-9_]{3,50}$/.test(this.value);
                if (this.value && !isValid) {
                    this.style.borderColor = 'var(--error)';
                } else if (this.value && isValid) {
                    this.style.borderColor = 'var(--success)';
                } else {
                    this.style.borderColor = 'var(--border)';
                }
            });
        }
    });
</script>
<?php Views::endsection() ?>

<?php Views::endpush() ?>
?>