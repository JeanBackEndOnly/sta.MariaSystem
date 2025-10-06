<body>
    <?php
      $studentCount = $pdo->query("SELECT COUNT(*) FROM student WHERE student.guardian_id = '$user_id' AND enrolment_status = 'active'")->fetchColumn();
      $teacherCount = $pdo->query("SELECT COUNT(*) FROM users WHERE user_role = 'TEACHER'")->fetchColumn();
      $parentCount = $pdo->query("SELECT COUNT(*) FROM users WHERE user_role = 'PARENT'")->fetchColumn();
      $PresentCounts = $pdo->query("SELECT COUNT(*) FROM attendance 
      INNER JOIN student ON attendance.student_id = student.student_id
      WHERE student.guardian_id = '$user_id' AND attendance.attendance_type = 'Present'")->fetchColumn();
      $AbsentCounts = $pdo->query("SELECT COUNT(*) FROM attendance 
      INNER JOIN student ON attendance.student_id = student.student_id
      WHERE student.guardian_id = '$user_id' AND attendance.attendance_type = 'Absent'")->fetchColumn();
      $LateCounts = $pdo->query("SELECT COUNT(*) FROM attendance 
      INNER JOIN student ON attendance.student_id = student.student_id
      WHERE student.guardian_id = '$user_id' AND attendance.attendance_type = 'Late'")->fetchColumn();

      $stmt = $pdo->prepare("SELECT section_name, grade_level FROM classes WHERE adviser_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $sectionName = $stmt->fetch(PDO::FETCH_ASSOC);


      $stmt = $pdo->prepare("SELECT school_year_name FROM school_year WHERE school_year_status = 'Active'");
      $stmt->execute();
      $activeSY = $stmt->fetch(PDO::FETCH_ASSOC);
   ?>
    <section class="">
        <div class="mb-4">
            <div class="mx-2 marginToMedia ">
                <h4>Student Dashboard</h4>
            </div>
        </div>

        <!-- Grid Row -->
        <div class="row text-center d-flex align-items-start justify-content-start ">
            <div class="row shadow border rounded-3 p-3 align-items-center justify-content-between col-md-3 mx-1 mb-3">
                <div class="d-flex flex-column align-items-center justify-content-end col-md-12">
                    <h1 class="m-0 p-0 text-center w-100"><?= $studentCount ?></h1>
                    <strong class="m-0 p-0 text-center w-100">Total Enrolled Children</strong>
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