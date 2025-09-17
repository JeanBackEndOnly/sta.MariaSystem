    <section>
        <div class="d-flex flex-column justify-content-between align-items-center mb-3 mt-2">
            <div class="mx-2 w-100 d-flex flex-row justify-content-between marginToMedia">
                <h4></i>Settings</h4>
                <label style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#changePassword"><i class="fa-solid fa-key me-1"></i>Change Password</label>
            </div>
        </div>
        <?php
        $query = "SELECT * FROM admin WHERE admin_id = :admin_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':admin_id', $user_id);
        $stmt->execute();
        $LibrarianInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <form action="../../authentication/auth.php" method="post" enctype="multipart/form-data" class="d-flex flex-column justify-content-between align-items-center w-100">
        <input type="hidden" name="adminID" value="<?= htmlspecialchars($user_id); ?>">
        <input type="hidden" name="adminProfile" value="true">
        <div class="profileSettings d-flex col-md-12 col-12 flex-wrap" style="height: auto !important; overflow-y: hidden !important;">
            <div class="profilePicture h-100 col-md-4 col-12 d-flex flex-column justify-content-center align-items-center profileBG rounded-3 flex-wrap">
                <?php if($profile) {?>
                    <label for="profilePreview"><img src="../../authentication/uploads/<?= $LibrarianInfo["admin_picture"] ?>" class="mediaProfile" alt="" style="width: 200px; height: 200px; border-radius: 50%;" id="settingsProfile"></label>
                <?php }else{ ?>
                    <img src="../../assets/image/users.png" alt="" style="width: 200px; height: 200px; border-radius: 50%;">
                <?php } ?>
                
                <label for="user_profile" id="profilePreview" class="cameraLabelMedia  rounded-3 m-0 p-2 d-flex align-items-center justify-content-center" style="cursor: pointer; border: solid 1px #00000077;">
                    Change profile<img src="../../assets/image/ProfileInput.png" alt="" class="ms-2 p-0 " style="width: 20px; height: 20px;">
                </label>
                <input type="hidden" name="current_profile_image" value="<?= $LibrarianInfo["admin_picture"] ?>">
                <input type="file" name="user_profile" id="user_profile" class="form-control w-75" style="display: none;" onchange="previewImageFaculty(event)">
            </div>
            <div class="LibrarianInfo d-flex flex-row flex-wrap h-100 col-md-8 col-12 justify-content-start align-items-start noScrollBar">
                <div class="m-1 col-md-5 col-11">
                    <label class="mb-0" for="">Surname</label>
                    <input type="text" name="admin_lastname" value="<?= $LibrarianInfo["admin_lastname"] ?>" class="form-control">
                </div>
                <div class="m-1 col-md-5 col-11">
                    <label class="mb-0" for="">First Name</label>
                    <input type="text" name="admin_firstname" value="<?= $LibrarianInfo["admin_firstname"] ?>" class="form-control">
                </div>
                <div class="m-1 col-md-5 col-11">
                    <label class="mb-0" for="">Middle Name</label>
                    <input type="text" name="admin_middlename" value="<?= $LibrarianInfo["admin_middlename"] ?>" class="form-control">
                </div>
                <div class="m-1 col-md-5 col-11">
                    <label class="mb-0" for="">Name Extention</label>
                   <select name="admin_suffix" class="form-select">
                        <option value="" <?= empty($LibrarianInfo["admin_suffix"]) ? 'selected' : '' ?>>None</option>
                        <option value="Jr." <?= $LibrarianInfo["admin_suffix"] == "Jr." ? 'selected' : '' ?>>Jr.</option>
                        <option value="Sr." <?= $LibrarianInfo["admin_suffix"] == "Sr." ? 'selected' : '' ?>>Sr.</option>
                        <option value="II" <?= $LibrarianInfo["admin_suffix"] == "II" ? 'selected' : '' ?>>II</option>
                        <option value="III" <?= $LibrarianInfo["admin_suffix"] == "III" ? 'selected' : '' ?>>III</option>
                        <option value="IV" <?= $LibrarianInfo["admin_suffix"] == "IV" ? 'selected' : '' ?>>IV</option>
                    </select>
                </div>
                <div class="m-1 col-md-5 col-11">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="admin_email" value="<?= $LibrarianInfo["admin_email"] ?>">
                </div>
               
                <!-- Update profile MODAL -->
                <div class="modal fade" id="updateProfile" tabindex="-1" aria-labelledby="borrowConfirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md modal-dialog-top">
                        <div class="modal-content">
                            <div class="modal-header modalBG">
                                <h5 class="modal-title text-white" id="borrowConfirmModalLabel">Update Confirmation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body text-center">
                                <h5>Are you sure you want to update your profile?</h5>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Yes</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <div class="buttonUpdateSettings mt-2 w-100 d-flex justify-content-end">
            <button type="button" class="btn btn-success"  data-bs-toggle="modal" data-bs-target="#updateProfile">Update</button>
        </div>
        
        <!-- CHANGE PASSWORD MODAL -->
        <div class="modal fade" id="changePassword" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="../../authentication/profileAuth.php" class="modal-content">
                <input type="hidden" name="usersForgottenPassLibrarian" value="true">
                <input type="hidden" name="Users_id" value="<?= $user_id ?>">
                <div class="modal-header modalBG">
                    <h5 class="modal-title text-start w-100" id="passwordModalLabel" style="color: #fff;">Change Password:</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="usernameConfim">Current Password:</label>
                    <input type="password" name="current_password" id="usernameConfim" class="form-control" required>
                </div>
                <div class="modal-body">
                    <label for="usernameConfim">New Password:</label>
                    <input type="password" name="new_password" id="usernameConfim" class="form-control" required>
                </div>
                <div class="modal-body">
                    <label for="usernameConfim">Confirm Password:</label>
                    <input type="password" name="confirm_password" id="usernameConfim" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
            </div>
        </div>
    </section>
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    console.log("Image Loaded: ", e.target.result);
                    document.getElementById("settingsProfile").src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                console.log("No file selected");
            }
        }
    </script>
<?php if (
    isset($_GET['update']) || 
    isset($_GET['passwordChange']) || 
    isset($_GET['NewPassword']) || 
    isset($_GET['CurrentPasswoed'])
): ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const messages = {
        update: { icon: 'success', title: 'Profile updated successfully!' },
        passwordChange: { icon: 'success', title: 'Password changed successfully!' },
        NewPassword: { icon: 'error', title: 'New passwords do not match!' },
        CurrentPasswoed: { icon: 'error', title: 'Current password is incorrect!' }
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
    </section>
