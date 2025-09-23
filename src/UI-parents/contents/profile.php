<?php
    isset($_GET["student_id"]) ? $student_id = $_GET["student_id"] : '';
    $query = "SELECT student.*, stuenrolmentinfo.*, users.* FROM student
    INNER JOIN users ON student.guardian_id = users.user_id
    INNER JOIN stuenrolmentinfo ON student.student_id = stuenrolmentinfo.student_id 
    WHERE student.student_id = '$student_id'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $student_info = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<style>
    /* Custom styling for the tab buttons */
    .col-md-8 button {
        padding: 10px 20px;
        border-radius: 5px;
        transition: all 0.3s ease;
        font-weight: 500;
        color: #495057;
        position: relative;
        cursor: pointer;
    }

    .col-md-8 button:hover {
        background-color: #e9ecef;
        color: #dc3545;
    }

    .col-md-8 button.Active {
        color: #dc3545;
        font-weight: 600;
    }

    .col-md-8 button.Active::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #dc3545;
        border-radius: 3px;
    }

    /* Content area styling */
    #displayStudentInfo,
    #displayAttendance,
    #displayMedical,
    #displayGrades {
        height:600px !important;
        padding: 20px;
        border-radius: 8px;
        background-color: #f8f9fa;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        overflow-y: scroll;
    }

    /* Sidebar styling */
    .col-md-4 .border.rounded.shadow {
        padding: 20px;
        background-color: white;
    }

    .col-md-4 img {
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .col-md-4 span {
        display: block;
        margin-bottom: 10px;
        font-size: 15px;
    }
</style>
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="mx-2 col-md-4">
        <h4><i class="fa-solid fa-user me-2"></i>Learners Profile</h4>
    </div>
    <div class="col-md-8 d-flex justify-content-between px-5">
        <button id="personal_info" class="border-0 bg-transparent Active">Personal Information</button>
        <button id="attendance" class="border-0 bg-transparent">Attendance</button>
        <button id="medical" class="border-0 bg-transparent">Medical</button>
        <button id="grades" class="border-0 bg-transparent">Grades</button>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="col-md-11 border rounded shadow d-flex flex-column align-items-center justify-conten-center">
            <div class="col-md-12">
                <a href="index.php?page=contents/learners" class="btn btn-sm btn-danger">Back</a>
            </div>    
            
            <img src="../../assets/image/users.png" style="width: 200px; height: auto;">
            <span>Lrn: <strong><?= $student_info["lrn"] ?></strong></span>
            <span>Stduent: <strong><?= htmlSpecialChars($student_info["fname"]) . " " .
                    htmlspecialchars(substr($student_info["mname"], 0,1)) . ". " .
                    htmlspecialchars($student_info["lname"]) ?></strong></span>
            <span>Guardian: <strong><?= htmlSpecialChars($student_info["firstname"]) . " " .
                    htmlspecialchars(substr($student_info["middlename"], 0,1)) . ". " .
                    htmlspecialchars($student_info["lastname"]) ?></strong></span>
        </div>
    </div>
    <div class="col-md-8">
        <form id="displayStudentInfo" class="student-Info gap-2" style="display: flex; flex-wrap: wrap !important;">
            <div class="col-md-3">
                <label class="form-label">First Name</label>
                <input type="text" readonly name="fname" class="form-control"
                    value="<?= htmlspecialchars($student_info["fname"]) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Middle Name</label>
                <input type="text" readonly name="mname" class="form-control"
                    value="<?= htmlspecialchars($student_info["mname"]) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Last Name</label>
                <input type="text" readonly name="lname" class="form-control"
                    value="<?= htmlspecialchars($student_info["lname"]) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">suffix</label>
                <input type="text" readonly name="suffix" class="form-control"
                    value="<?= htmlspecialchars($student_info["suffix"] ?? 'NA') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Grade Level</label>
                <input type="text" readonly name="gradeLevel" class="form-control"
                    value="<?= htmlspecialchars($student_info["gradeLevel"]) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">LRN</label>
                <input type="text" readonly name="lrn" class="form-control"
                    value="<?= htmlspecialchars($student_info["lrn"]) ?>">
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
            <div class="col-md-3">
                <label class="form-label">Age</label>
                <input type="text" readonly name="age" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Religion</label>
                <input type="text" name="religion" class="form-control"
                    value="<?= htmlspecialchars($student_info["religion"] ?? 'NA') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Mother Tongue</label>
                <input type="text" name="mother_tongue" class="form-control"
                    value="<?= htmlspecialchars($student_info["mother_tongue"] ?? 'NA') ?>">
            </div>
            <div class="col-md-12">
                <strong class="fs-5">Student Address</strong>
            </div>
            <div class="col-md-3">
                <label class="form-label">House No</label>
                <input type="text" name="house_no" class="form-control"
                    value="<?= htmlspecialchars($student_info["house_no"] ?? 'NA') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Street</label>
                <input type="text" name="street" class="form-control"
                    value="<?= htmlspecialchars($student_info["street"] ?? 'NA') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Brangay</label>
                <input type="text" name="barnagay" class="form-control"
                    value="<?= htmlspecialchars($student_info["barnagay"] ?? 'NA') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">City/Mutinlupa</label>
                <input type="text" name="city" class="form-control"
                    value="<?= htmlspecialchars($student_info["city"] ?? 'NA') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Province</label>
                <input type="text" name="province" class="form-control"
                    value="<?= htmlspecialchars($student_info["province"] ?? 'NA') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Country</label>
                <input type="text" name="country" class="form-control"
                    value="<?= htmlspecialchars($student_info["country"] ?? 'NA') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Zip Code</label>
                <input type="text" name="zip_code" class="form-control"
                    value="<?= htmlspecialchars($student_info["zip_code"] ?? 'NA') ?>">
            </div>
            <div class="col-md-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-danger mt-2 text-white px-5 fw-bold">Update</button>
            </div>
            
        </form>
        <div id="displayAttendance" class="attendance" style="display:none">

        </div>
        <div id="displayMedical" class="medical" style="display:none">

        </div>
        <div id="displayGrades" class="gading-system" style="display:none">

        </div>
    </div>
</div>