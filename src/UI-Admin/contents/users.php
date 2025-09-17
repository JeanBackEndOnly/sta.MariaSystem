<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="mx-2">
        <h4>Users Management</h4>
    </div>
</div>

<!-- Search and Filters -->

<div class="row g-2  justify-content-between">
    <div class="col-md-5">
        <input type="text" id="searchInput" name="search" class="form-control" placeholder="Search...."
            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
    </div>
    <div class="col-md-5">
        <select id="categoryFilter" name="category" class="form-select">
            <option value="">All Categories</option>
            <?php
                // Fetch unique categories
                $catStmt = $pdo->query("SELECT DISTINCT user_role FROM users ORDER BY created_date ASC");
                while ($cat = $catStmt->fetch(PDO::FETCH_ASSOC)): ?>
            <option value="<?= htmlspecialchars($cat['user_role']) ?>"
                <?= (isset($_GET['category']) && $_GET['category'] === $cat['user_role']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['user_role']) ?>
            </option>
            <?php endwhile; ?>
        </select>

    </div>
    <div class="col-md-2">
        <button type="button" class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#AddNewAccount"
            id="add_new"><i class="fa fa-plus"></i> New Account</button>
    </div>
    <!-- Adding account modal -->
    <div class="modal fade" id="AddNewAccount" tabindex="-1" aria-labelledby="AddNewAccountLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="AddNewAccountLabel">Create New User Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                     
                    <form action="../../authentication/auth.php" class="row g-3" id="Account-form" method="post">
                        <!-- Name Section -->
                         <input type="hidden" name="adminAccReg" value="true">
                         <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                        <div class="col-md-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="lastName" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="firstName" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middleName">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Suffix</label>
                            <select class="form-select" name="suffix">
                                <option value="" disabled selected>Select suffix (optional)</option>
                                <option value="Jr">Jr</option>
                                <option value="Sr">Sr</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">User Role <span class="text-danger">*</span></label>
                            <select class="form-select" name="user_role" required>
                                <option value="" disabled selected>Select User Role</option>
                                <option value="TEACHER">Teacher</option>
                                <option value="STUDENT">Student</option>
                                <option value="PARENT">Parent</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" name="gender" required>
                                <option value="" disabled selected>Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-md-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="contact" required>
                        </div>

                        <!-- Account Credentials -->
                        <div class="col-md-4">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="cpassword" required>
                        </div>

                        <!-- Form Submission -->
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary px-5">
                                 Create Account
                            </button>
                        </div>
                    </form>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div> -->
            </div>
        </div>
    </div>
</div>
<?php if (
        isset($_GET['username']) || 
        isset($_GET['registration']) || 
        isset($_GET['NewPassword']) || 
        isset($_GET['incorrectPass'])
    ): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // SweetAlert2 messages (still inside DOMContentLoaded)
            const messages = {
                username: { icon: 'success', title: 'Profile updated successfully!' },
                registration: { icon: 'success', title: 'Password changed successfully!' },
                NewPassword: { icon: 'error', title: 'New passwords do not match!' },
                incorrectPass: { icon: 'error', title: 'Current password is incorrect!' }
            };

            for (const key in messages) {
                const value = new URLSearchParams(window.location.search).get(key);
                if (value) {
                    Swal.fire({
                        toast: true,
                        icon: messages[key].icon,
                        title: messages[key].title,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didClose: () => removeUrlParams([key])
                    });
                    break;
                }
            }

            function removeUrlParams(params) {
                const url = new URL(window.location);
                params.forEach(param => url.searchParams.delete(param));
                window.history.replaceState({}, document.title, url.toString());
            }
        });
    </script>
<?php endif; ?>