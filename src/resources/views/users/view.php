<?php \core\Views::extends('main'); ?>

<?php \core\Views::section('styles') ?>
<link href="/css/view.css" rel="stylesheet" />
<?php \core\Views::endsection() ?>

<?php \core\Views::section('title') ?>
UserHub | <?php echo htmlspecialchars($user->username ?? ''); ?>
<?php \core\Views::endsection() ?>

<?php \core\Views::section('content') ?>
<div class="layout-container">
        <div class="detail-card">
            <div class="detail-header">
                <h1 class="detail-title">User Details</h1>
                <div class="detail-actions">
                    <a href="/users" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i></a>
                    <?php if (isset($user) && !empty($user->id)): ?>
                        <a href="/users/<?php echo htmlspecialchars($user->id); ?>/edit" class="btn btn-primary"><i class="fa-solid fa-user-pen"></i></a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!isset($user) || empty($user->id)): ?>
                <div class="alert alert-error">User not found or invalid user data.</div>
            <?php else: ?>
                <!-- Optional: User Avatar Section -->
                <div class="user-avatar">
                    <div class="avatar-placeholder">
                        <?php
                        $initials = '';
                        if (!empty($user->first_name)) $initials .= mb_strtoupper(mb_substr($user->first_name, 0, 1, 'UTF-8'), 'UTF-8');
                        if (!empty($user->last_name)) $initials .= mb_strtoupper(mb_substr($user->last_name, 0, 1, 'UTF-8'), 'UTF-8');
                        echo $initials ?: 'U';
                        ?>
                    </div>
                    <div>
                        <div class="user-initials">
                            <?php echo htmlspecialchars($user->first_name ?? '') . ' ' . htmlspecialchars($user->last_name ?? ''); ?>
                        </div>
                        <div style="color: var(--text-light); font-size: 0.95rem;">
                            @<?php echo htmlspecialchars($user->username ?? ''); ?>
                        </div>
                    </div>
                </div>

                <div class="detail-grid">
                    <div class="detail-group">
                        <label class="detail-label">User ID</label>
                        <div class="detail-value"><?php echo htmlspecialchars($user->id ?? 'N/A'); ?></div>
                    </div>

                    <div class="detail-group">
                        <label class="detail-label">Username</label>
                        <div class="detail-value"><?php echo htmlspecialchars($user->username ?? 'N/A'); ?></div>
                    </div>

                    <div class="detail-group">
                        <label class="detail-label">First Name</label>
                        <div class="detail-value"><?php echo htmlspecialchars($user->first_name ?? 'N/A'); ?></div>
                    </div>

                    <div class="detail-group">
                        <label class="detail-label">Last Name</label>
                        <div class="detail-value"><?php echo htmlspecialchars($user->last_name ?? 'N/A'); ?></div>
                    </div>

                    <div class="detail-group">
                        <label class="detail-label">Gender</label>
                        <div class="detail-value">
                            <?php
                            $genderDisplay = [
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other'
                            ];
                            echo htmlspecialchars($genderDisplay[$user->gender] ?? ($user->gender ?? 'N/A'));
                            ?>
                        </div>
                    </div>

                    <div class="detail-group">
                        <label class="detail-label">Birthdate</label>
                        <div class="detail-value">
                            <?php
                            if (!empty($user->birthdate)) {
                                $birthdate = DateTime::createFromFormat('Y-m-d', $user->birthdate);
                                echo $birthdate ? htmlspecialchars($birthdate->format('F j, Y')) : htmlspecialchars($user->birthdate);
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </div>
                    </div>

                    <div class="detail-group detail-full">
                        <label class="detail-label">Age</label>
                        <div class="detail-value">
                            <?php
                            $age = 'Unknown';
                            if (!empty($user->birthdate)) {
                                $birthdate = DateTime::createFromFormat('Y-m-d', $user->birthdate);
                                $today = new DateTime();
                                if ($birthdate) {
                                    $age = $today->diff($birthdate)->y;
                                }
                            }
                            echo htmlspecialchars($age) . ' years old';
                            ?>
                        </div>
                    </div>
                </div>

                <div class="quick-actions">
                    <h3>Quick Actions</h3>
                    <div class="quick-actions-grid">
                        <a href="/users/<?php echo htmlspecialchars($user->id); ?>/delete"
                           class="btn btn-danger"
                           onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                            Delete This User
                        </a>
                        <a href="/users/create" class="btn btn-success">Create New User</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($user) && !empty($user->id)): ?>
            <div class="detail-card tips-card">
                <h3>User Management Tips</h3>
                <ul class="tips-list">
                    <li>Always verify user information before making changes</li>
                    <li>Ensure usernames are unique across the system</li>
                    <li>Passwords are stored securely using bcrypt hashing</li>
                    <li>Deleted users cannot be recovered</li>
                    <li>Regularly backup user data to prevent data loss</li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
<?php \core\Views::endsection() ?>

<?php \core\Views::endpush() ?>
