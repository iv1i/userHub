<?php \core\Views::extends('main'); ?>

<?php \core\Views::section('styles') ?>
<link href="../../../resources/css/edit.css" rel="stylesheet" />
<?php \core\Views::endsection() ?>

<?php \core\Views::section('title') ?>
UserHub | Edit
<?php \core\Views::endsection() ?>

<?php \core\Views::section('content') ?>
<div class="layout-container">
        <div class="edit-card">
            <div class="edit-header">
                <h1 class="edit-title">Edit User: <?php echo isset($user) ? htmlspecialchars($user->username ?? 'Unknown') : 'Unknown User'; ?></h1>
                <div class="edit-actions">
                    <a href="/users" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i></a>
                    <?php if (isset($user) && !empty($user->id)): ?>
                        <a href="/users/<?php echo htmlspecialchars($user->id); ?>" class="btn btn-outline"><i class="fa-solid fa-eye"></i></a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!isset($user) || empty($user->id)): ?>
                <div class="alert alert-error">User not found or invalid user data.</div>
            <?php else: ?>
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

                <form method="POST" action="/users/<?php echo $user->id ?>" class="edit-form">
                    <input type="hidden" name="csrf_token" value="<?php echo core\Security::generateCSRFToken(); ?>">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user->id); ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="username" class="form-label">
                                Username <span class="required">*</span>
                            </label>
                            <input type="text" id="username" name="username" class="form-control"
                                   value="<?php echo htmlspecialchars($user->username ?? ''); ?>"
                                   required
                                   pattern="[a-zA-Z0-9_]{3,50}"
                                   title="Username must be 3-50 characters and can only contain letters, numbers, and underscores">
                            <span class="form-help">3-50 characters, letters, numbers, and underscores only</span>
                        </div>

                        <div class="form-group">
                            <label for="id" class="form-label">User ID</label>
                            <input type="text" id="id" class="form-control"
                                   value="<?php echo htmlspecialchars($user->id ?? ''); ?>"
                                   disabled>
                            <span class="form-help">User ID cannot be changed</span>
                        </div>

                        <div class="form-group">
                            <label for="first_name" class="form-label">
                                First Name <span class="required">*</span>
                            </label>
                            <input type="text" id="first_name" name="first_name" class="form-control"
                                   value="<?php echo htmlspecialchars($user->first_name ?? ''); ?>"
                                   required
                                   maxlength="100">
                            <span class="form-help">Maximum 100 characters</span>
                        </div>

                        <div class="form-group">
                            <label for="last_name" class="form-label">
                                Last Name <span class="required">*</span>
                            </label>
                            <input type="text" id="last_name" name="last_name" class="form-control"
                                   value="<?php echo htmlspecialchars($user->last_name ?? ''); ?>"
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
                                <option value="male" <?php echo ($user->gender ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo ($user->gender ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo ($user->gender ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="birthdate" class="form-label">
                                Birthdate <span class="required">*</span>
                            </label>
                            <input type="date" id="birthdate" name="birthdate" class="form-control"
                                   value="<?php echo htmlspecialchars($user->birthdate ?? ''); ?>"
                                   required
                                   min="1900-01-01"
                                   max="<?php echo date('Y-m-d'); ?>">
                            <span class="form-help">Must be a valid date</span>
                        </div>
                    </div>

                    <div class="password-section">
                        <h3 class="section-title">Password Management</h3>
                        <div class="password-fields">
                            <div class="form-group">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" id="password" name="password" class="form-control"
                                       minlength="6">
                                <span class="form-help">Minimum 6 characters. Leave empty to keep current password.</span>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">Update User</button>
                        <a href="/users/<?php echo htmlspecialchars($user->id); ?>/edit" class="btn btn-outline">Cancel</a>
                        <a href="/users/<?php echo htmlspecialchars($user->id); ?>/delete"
                           class="btn btn-danger"
                           onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                            Delete User
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <?php if (isset($user) && !empty($user->id)): ?>
            <div class="edit-card guidelines-card">
                <h3>Editing Guidelines</h3>
                <ul class="guidelines-list">
                    <li><strong>Username:</strong> Must be unique across all users</li>
                    <li><strong>Personal Data:</strong> Ensure accuracy of personal information</li>
                    <li><strong>Password:</strong> Only update if requested by the user</li>
                    <li><strong>Birthdate:</strong> Must be a valid date in the past</li>
                    <li><strong>Validation:</strong> All required fields must be filled correctly</li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
<?php \core\Views::endsection() ?>

<?php \core\Views::section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');

        if (form && password && confirmPassword) {
            form.addEventListener('submit', function(e) {
                if (password.value !== '') {
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
                }
            });

            function checkPasswordMatch() {
                if (password.value === '' && confirmPassword.value === '') {
                    confirmPassword.classList.remove('password-match', 'password-mismatch');
                } else if (password.value === confirmPassword.value) {
                    confirmPassword.classList.remove('password-mismatch');
                    confirmPassword.classList.add('password-match');
                } else {
                    confirmPassword.classList.remove('password-match');
                    confirmPassword.classList.add('password-mismatch');
                }
            }

            password.addEventListener('input', checkPasswordMatch);
            confirmPassword.addEventListener('input', checkPasswordMatch);

            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.classList.add('btn-loading');
                submitBtn.disabled = true;
            });
        }
    });
</script>
<?php \core\Views::endsection() ?>

<?php \core\Views::endpush() ?>
