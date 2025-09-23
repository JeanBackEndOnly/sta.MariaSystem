<body>
    <?php
      $studentCount = $pdo->query("SELECT COUNT(*) FROM student")->fetchColumn();
      $teacherCount = $pdo->query("SELECT COUNT(*) FROM users WHERE user_role = 'TEACHER'")->fetchColumn();
      $parentCount = $pdo->query("SELECT COUNT(*) FROM users WHERE user_role = 'PARENT'")->fetchColumn();
   
      $classroom = $pdo->query("SELECT COUNT(*) FROM classrooms")->fetchColumn();
      $sections = $pdo->query("SELECT COUNT(*) FROM sections")->fetchColumn();
      $subjects = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
      $school_year = $pdo->query("SELECT COUNT(*) FROM school_year")->fetchColumn();

      $stmt = $pdo->prepare("SELECT school_year_name FROM school_year WHERE school_year_status = 'Active'");
      $stmt->execute();
      $activeSY = $stmt->fetch(PDO::FETCH_ASSOC);
   ?>
<style>
    section::-webkit-scrollbar{
        display: none !important;
    }
</style>
    <section style="overflow-y: scroll !important; max-height: 85vh !important;" >
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="mx-2 marginToMedia sideAnimation">
                <h4>Active School Year</h4>
            </div>
        </div>
        <div class="row col-md-7 border shadow m-0 p-3 rounded-3 mb-4">
            <span class="m-0 fs-5">SCHOOL YEAR: <strong><?= $activeSY["school_year_name"] ?? 'No Active School Year' ?></strong></span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="mx-2 marginToMedia sideAnimation">
                <h4>Dashboard Analytics</h4>
            </div>
        </div>
        <div class="row justify-content-start align-items-center gap-3 d-flex text-center flex-wrap mx-2">
            <div class="row col-md-3 border shadow m-0 p-3 rounded-3">
                <div class="col-md-5 d-flex align-items-center justify-content-center">
                    <h2 class="m-0 p-0 text-start w-100"><i class="fa-solid fa-graduation-cap"></i></h2>
                </div>
                <div class="d-flex flex-column align-items-center justify-content-end col-md-7">
                    <h1 class="m-0 p-0 text-end w-100"><?= $studentCount ?></h1>
                    <strong class="m-0 p-0 text-end w-100">Total Students</strong>
                </div>
            </div>
            <div class="row col-md-3 border shadow m-0 p-3 rounded-3">
                <div class="col-md-5 d-flex align-items-center justify-content-center">
                    <h2 class="m-0 p-0 text-start w-100"><i class="fa-solid fa-user-tie"></i></h2>
                </div>
                <div class="d-flex flex-column align-items-center justify-content-end col-md-7">
                    <h1 class="m-0 p-0 text-end w-100"><?= $teacherCount ?></h1>
                    <strong class="m-0 p-0 text-end w-100">Total Teachers</strong>
                </div>
            </div>
            <div class="row col-md-3 border shadow m-0 p-3 rounded-3">
                <div class="col-md-5 d-flex align-items-center justify-content-center">
                    <h2 class="m-0 p-0 text-start w-100"><i class="fa-solid fa-user"></i></h2>
                </div>
                <div class="d-flex flex-column align-items-center justify-content-end col-md-7">
                    <h1 class="m-0 p-0 text-end w-100"><?= $parentCount ?></h1>
                    <strong class="m-0 p-0 text-end w-100">Total Parents</strong>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="mx-2 marginToMedia sideAnimation">
                <h4>Facility</h4>
            </div>
        </div>
        <div class="row justify-content-start align-items-center gap-3 d-flex text-center flex-wrap mx-2">
            <div class="col-md-3 border shadow m-0 p-3 rounded-3">

                <div class="d-flex flex-column align-items-center justify-content-end col-md-12">
                    <h2 class="m-0 p-0 center w-100"><?= $classroom ?></h2>
                    <strong class="m-0 p-0 center w-100">Total Classrooms</strong>
                </div>
            </div>
            <div class="col-md-3 border shadow m-0 p-3 rounded-3">

                <div class="d-flex flex-column align-items-center justify-content-end col-md-12">
                    <h2 class="m-0 p-0 center w-100"><?= $sections ?></h2>
                    <strong class="m-0 p-0 center w-100">Total Sections</strong>
                </div>
            </div>
            <div class="col-md-3 border shadow m-0 p-3 rounded-3">

                <div class="d-flex flex-column align-items-center justify-content-end col-md-12">
                    <h2 class="m-0 p-0 center w-100"><?= $subjects ?></h2>
                    <strong class="m-0 p-0 center w-100">Total Subjects</strong>
                </div>
            </div>
            <div class="col-md-3 border shadow m-0 p-3 rounded-3">

                <div class="d-flex flex-column align-items-center justify-content-end col-md-12">
                    <h2 class="m-0 p-0 center w-100"><?= $school_year ?></h2>
                    <strong class="m-0 p-0 center w-100">Total School Years</strong>
                </div>
            </div>
        </div>

    </section>
</body>