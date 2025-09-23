<?php
    isset($_GET["user_id"]) ? $user_id = $_GET["user_id"] : '';
    $query = "SELECT * FROM users
    WHERE user_id = '$user_id'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $student_info = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="mx-2 col-md-4">
        <h4><i class="fa-solid fa-user me-2"></i>Users Profile</h4>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="col-md-11 border p-2 rounded shadow d-flex flex-column align-items-center justify-conten-center">
            <div class="col-md-12">
                <a href="index.php?page=contents/users" class="btn btn-sm btn-danger">Back</a>
            </div>    
            
            <img src="../../assets/image/users.png" style="width: 200px; height: auto;">
            <span>Stduent: <strong><?= htmlSpecialChars($student_info["firstname"]) . " " .
                    htmlspecialchars(substr($student_info["middlename"], 0,1)) . ". " .
                    htmlspecialchars($student_info["lastname"]) ?></strong></span>
        </div>
    </div>
    <div class="col-md-8">
        <form id="displayStudentInfo" class="student-Info gap-2" style="display: flex; flex-wrap: wrap !important;">
            <div class="col-md-3">
                <label class="form-label">First Name</label>
                <input type="text" readonly name="firstname" class="form-control"
                    value="<?= htmlspecialchars($student_info["firstname"]) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Middle Name</label>
                <input type="text" readonly name="middlename" class="form-control"
                    value="<?= htmlspecialchars($student_info["middlename"]) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Last Name</label>
                <input type="text" readonly name="lastname" class="form-control"
                    value="<?= htmlspecialchars($student_info["lastname"]) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">suffix</label>
                <input type="text" readonly name="suffix" class="form-control"
                    value="<?= htmlspecialchars($student_info["suffix"] ?? 'NA') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">SEX</label>
                <input type="text" name="sex" class="form-control"
                    value="<?= htmlspecialchars($student_info["sex"] ?? 'NA') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Birth Date</label>
                <input type="date" name="birthdate" class="form-control"
                    value="<?= htmlspecialchars($student_info["birthdate"] ?? 'NA') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Birth Place</label>
                <input type="text" name="birthplace" class="form-control"
                    value="<?= htmlspecialchars($student_info["birthplace"] ?? 'NA') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Age</label>
                <input type="text" readonly name="age" class="form-control">
            </div>
            <!-- <div class="col-md-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-danger mt-2 text-white px-5 fw-bold">Update</button>
            </div> -->
            
        </form>
    </div>
</div>