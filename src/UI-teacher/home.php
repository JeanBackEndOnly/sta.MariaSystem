<body>
    <?php
      $studentCount = $pdo->query("SELECT COUNT(*) FROM student INNER JOIN enrolment ON student.student_id = enrolment.student_id WHERE enrolment.adviser_id = '$user_id'")->fetchColumn();
      $teacherCount = $pdo->query("SELECT COUNT(*) FROM users WHERE user_role = 'TEACHER'")->fetchColumn();
      $parentCount = $pdo->query("SELECT COUNT(*) FROM users WHERE user_role = 'PARENT'")->fetchColumn();
      $PresentCounts = $pdo->query("SELECT COUNT(*) FROM attendance WHERE adviser_id = '$user_id' AND Attendance_type = 'Present'")->fetchColumn();
      $AbsentCounts = $pdo->query("SELECT COUNT(*) FROM attendance WHERE adviser_id = '$user_id' AND Attendance_type = 'Absent'")->fetchColumn();
      $LateCounts = $pdo->query("SELECT COUNT(*) FROM attendance WHERE adviser_id = '$user_id' AND Attendance_type = 'Late'")->fetchColumn();

      $stmt = $pdo->prepare("SELECT section_name, grade_level FROM classes WHERE adviser_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $sectionName = $stmt->fetch(PDO::FETCH_ASSOC);


      $stmt = $pdo->prepare("SELECT school_year_name FROM school_year WHERE school_year_status = 'Active'");
      $stmt->execute();
      $activeSY = $stmt->fetch(PDO::FETCH_ASSOC);
   ?>

    <section>
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="mx-2 marginToMedia">
                <h4>Active School Year</h4>
            </div>
        </div>
        <div class="row col-md-5 border shadow m-0 p-3 rounded-3 mb-4">
           <span class="m-0 fs-5">SCHOOL YEAR: <strong><?= $activeSY["school_year_name"] ?? 'No Active School Year' ?></strong></span>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="mx-2 marginToMedia">
                <h4>Activities</h4>
            </div>
        </div>
        <div class="row justify-content-start align-items-center justify-content-start gap-2 d-flex text-center flex-wrap">
            <div class="row shadow border rounded-3 p-3 align-items-center justify-content-between col-md-3 mx-1 mb-2">
                <div class="d-flex flex-column align-items-center justify-content-end col-md-12">
                    <h1 class="m-0 p-0 text-center w-100"><?= $studentCount ?></h1>
                    <strong class="m-0 p-0 text-center w-100">Total Students</strong>
                </div>
            </div>
            <div class="row shadow border rounded-3 p-3 align-items-center justify-content-between col-md-3 mx-1 mb-2">
                <div class="d-flex flex-column align-items-center justify-content-end col-md-12">
                    <h1 class="m-0 p-0 text-center w-100"><?= round($PresentCounts / 2) ?></h1>
                    <strong class="m-0 p-0 text-center w-100">Total Present</strong>
                </div>
            </div>
            <div class="row shadow border rounded-3 p-3 align-items-center justify-content-between col-md-3 mx-1 mb-2">
                <div class="d-flex flex-column align-items-center justify-content-end col-md-12">
                    <h1 class="m-0 p-0 text-center w-100"><?= round($AbsentCounts / 2) ?></h1>
                    <strong class="m-0 p-0 text-center w-100">Total Absent</strong>
                </div>
            </div>
            <div class="row shadow border rounded-3 p-3 align-items-center justify-content-between col-md-3 mx-1 mb-2">
                <div class="d-flex flex-column align-items-center justify-content-end col-md-12">
                    <h1 class="m-0 p-0 text-center w-100"><?= round($LateCounts / 2) ?></h1>
                    <strong class="m-0 p-0 text-center w-100">Total Tardiness</strong>
                </div>
            </div>
        </div>

    </section>
</body>