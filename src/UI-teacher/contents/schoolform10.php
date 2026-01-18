<?php

/**
 * SF10 Form - Scholastic Record Transfer
 * 
 * DEPENDENCY: This file depends on schoolform9.php structure
 * - The number of learning areas (subjects) matches SF9 schema
 * - Learning areas are queried from sf9_data table columns (subject_1 to subject_15)
 * - Scholastic records store grades for up to 8 school years
 * - Each scholastic record has $num_subjects learning areas with 4 quarterly grades
 */
require_once __DIR__ . '/../../../tupperware.php';
require_once __DIR__ . '/../../../authentication/config.php';
$result = checkURI('teacher', 2);

if ($result['res']) {
  header($result['uri']);
  exit;
}
if (!isset($showSuccess)) $showSuccess = false;
if (!isset($successMessage)) $successMessage = '';

$pdo = db_connect();

// AJAX Handler for deleting remedial entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_remedial') {
  header('Content-Type: application/json');

  try {
    $remedial_id = $_POST['remedial_id'] ?? null;

    if (!$remedial_id) {
      throw new Exception('Missing remedial ID');
    }

    // First, get the sf10_rem_id before deleting
    $stmt_get_parent = $pdo->prepare("SELECT sf10_rem_id FROM remedial_class WHERE remedial_id = ?");
    $stmt_get_parent->execute([$remedial_id]);
    $remedial_record = $stmt_get_parent->fetch(PDO::FETCH_ASSOC);

    if (!$remedial_record) {
      throw new Exception('Remedial entry not found');
    }

    $sf10_rem_id = $remedial_record['sf10_rem_id'];

    // Delete the remedial entry
    $stmt_delete = $pdo->prepare("DELETE FROM remedial_class WHERE remedial_id = ?");
    $stmt_delete->execute([$remedial_id]);

    // Check if there are any remaining entries in this remedial class group
    $stmt_check_remaining = $pdo->prepare("SELECT COUNT(*) as count FROM remedial_class WHERE sf10_rem_id = ?");
    $stmt_check_remaining->execute([$sf10_rem_id]);
    $remaining = $stmt_check_remaining->fetch(PDO::FETCH_ASSOC);

    // If no more entries, delete the parent sf10_remedial_class
    if ($remaining['count'] == 0) {
      $stmt_delete_parent = $pdo->prepare("DELETE FROM sf10_remedial_class WHERE sf10_rem_id = ?");
      $stmt_delete_parent->execute([$sf10_rem_id]);
    }

    echo json_encode(['success' => true, 'message' => 'Remedial entry deleted successfully']);
    exit;
  } catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
  }
}

// AJAX Handler for adding remedial data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_remedial') {
  header('Content-Type: application/json');

  try {
    $student_id = $_POST['student_id'] ?? null;
    $scholastic_index = $_POST['scholastic_index'] ?? null;
    $school_year = $_POST['school_year'] ?? '';
    $area = $_POST['area'] ?? '';
    $final_rating = $_POST['final_rating'] ?? '';
    $class_mark = $_POST['class_mark'] ?? '';
    $recomputed_rating = $_POST['recomputed_rating'] ?? '';
    $remarks = $_POST['remarks'] ?? '';

    if (!$student_id || !$scholastic_index || !$area) {
      throw new Exception('Missing required fields');
    }

    // Get or create SF10 record
    $stmt_check = $pdo->prepare("SELECT id FROM sf10_data WHERE student_id = ?");
    $stmt_check->execute([$student_id]);
    $sf10_record = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$sf10_record) {
      $stmt_insert_sf10 = $pdo->prepare("INSERT INTO sf10_data (student_id) VALUES (?)");
      $stmt_insert_sf10->execute([$student_id]);
      $sf10_id = $pdo->lastInsertId();
    } else {
      $sf10_id = $sf10_record['id'];
    }

    // Create remedial class group for this scholastic record
    $stmt_group = $pdo->prepare("INSERT INTO sf10_remedial_class (sf10_data_id, school_year) VALUES (?, ?)");
    $stmt_group->execute([$sf10_id, $school_year]);
    $sf10_rem_id = $pdo->lastInsertId();

    // Insert remedial entry
    $stmt_remedial = $pdo->prepare(
      "INSERT INTO remedial_class (sf10_rem_id, area, final_rating, class_mark, recomputed_rating, remarks) 
             VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt_remedial->execute([
      $sf10_rem_id,
      $area,
      $final_rating,
      $class_mark,
      $recomputed_rating,
      $remarks
    ]);

    echo json_encode(['success' => true, 'message' => 'Remedial data added successfully']);
    exit;
  } catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
  }
}

// AJAX Handler for showing remedial records by school year
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'show_remedial_records') {
  header('Content-Type: text/html');

  try {
    $student_id = $_POST['student_id'] ?? null;
    $school_year = $_POST['school_year'] ?? '';

    // Debug logging
    error_log("DEBUG: show_remedial_records - student_id=$student_id, school_year=$school_year");

    if (!$student_id || !$school_year) {
      echo '<div class="alert alert-warning">Invalid parameters: student_id=' . htmlspecialchars($student_id) . ', school_year=' . htmlspecialchars($school_year) . '</div>';
      exit;
    }

    // Get SF10 record for this student
    $stmt_sf10 = $pdo->prepare("SELECT id FROM sf10_data WHERE student_id = ?");
    $stmt_sf10->execute([$student_id]);
    $sf10_data = $stmt_sf10->fetch(PDO::FETCH_ASSOC);

    error_log("DEBUG: sf10_data lookup - Found: " . json_encode($sf10_data));

    if (!$sf10_data) {
      echo '<div class="alert alert-info"><small>No SF10 record found for this student. You may need to save the form first.</small></div>';
      exit;
    }

    // Get remedial class group for this school year
    $stmt_rem_group = $pdo->prepare("
      SELECT sf10_rem_id FROM sf10_remedial_class 
      WHERE sf10_data_id = ? AND school_year = ?
    ");
    $stmt_rem_group->execute([$sf10_data['id'], $school_year]);
    $rem_groups = $stmt_rem_group->fetchAll(PDO::FETCH_COLUMN);

    if (empty($rem_groups)) {
      echo '<div class="alert alert-info"><small>No remedial records for this scholastic record.</small></div>';
      exit;
    }

    // Build the table HTML
    echo '<div class="table-responsive">
      <table class="table table-bordered table-sm">
        <thead>
          <tr>
            <th>Learning Area</th>
            <th>Final Rating</th>
            <th>Remedial Class Mark</th>
            <th>Recomputed Final Grade</th>
            <th>Remarks</th>
            <th style="width: 80px; text-align: center;">Action</th>
          </tr>
        </thead>
        <tbody>';

    foreach ($rem_groups as $sf10_rem_id) {
      $stmt_entries = $pdo->prepare("
        SELECT * FROM remedial_class 
        WHERE sf10_rem_id = ?
        ORDER BY remedial_id ASC
      ");
      $stmt_entries->execute([$sf10_rem_id]);
      $entries = $stmt_entries->fetchAll(PDO::FETCH_ASSOC);

      foreach ($entries as $rem) {
        echo '<tr>
          <td>' . htmlspecialchars($rem['area'] ?? '') . '</td>
          <td>' . htmlspecialchars($rem['final_rating'] ?? '') . '</td>
          <td>' . htmlspecialchars($rem['class_mark'] ?? '') . '</td>
          <td>' . htmlspecialchars($rem['recomputed_rating'] ?? '') . '</td>
          <td>' . htmlspecialchars($rem['remarks'] ?? '') . '</td>
          <td style="text-align: center;">
            <button type="button" class="btn btn-sm btn-danger" title="Delete" onclick="deleteRemedialEntry(' . $rem['remedial_id'] . ', \'' . htmlspecialchars($school_year) . '\')">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>';
      }
    }

    echo '</tbody>
      </table>
    </div>';
    exit;
  } catch (Exception $e) {
    echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit;
  }
}

// This matches the learning areas in schoolform9.php
$num_subjects = 15; // Maximum columns in SF9 schema (loop through all, skip empty ones)

// Get the number of behaviors dynamically
$behavior_columns = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'sf9_data' AND COLUMN_NAME LIKE 'behavior_%' ORDER BY COLUMN_NAME")->fetchAll(PDO::FETCH_COLUMN);
$num_behaviors = count($behavior_columns) / 4; // Divide by 4 because each behavior has 4 quarters

// Get the number of scholastic records dynamically from sf9_data
// Query all sf9 records for this student to determine how many school years they have
$num_scholastic_records = 0;
$sf9_records = [];
$student_id = $_GET['student_id'] ?? null;
$student = null;
if ($student_id) {
  $stmt = $pdo->prepare("SELECT * FROM student WHERE student_id = ?");
  $stmt->execute([$student_id]);
  $student = $stmt->fetch(PDO::FETCH_ASSOC);

  // Get all SF9 records for this student
  $stmt_sf9 = $pdo->prepare("SELECT * FROM sf9_data WHERE student_id = ? ORDER BY school_year");
  $stmt_sf9->execute([$student_id]);
  $sf9_records = $stmt_sf9->fetchAll(PDO::FETCH_ASSOC);
  $num_scholastic_records = count($sf9_records);
  // If no SF9 records exist, allow at least 1 scholastic record
  if ($num_scholastic_records == 0) {
    $num_scholastic_records = 1;
  }
}

// Initialize scholastic data structure first
$scholastic_data = [
  'grades' => [],
  'sections' => [],
  'school_years' => [],
  'adviser_name' => [],
  'learning_areas' => [],
  'q1' => [],
  'q2' => [],
  'q3' => [],
  'q4' => [],
  'final_ratings' => [],
  'remarks_table' => [],
  'general_average' => []
];
$remedial_data = [];
$sf10_data = [];

// POST Handler - Save SF10 and Remedial Data (skip if AJAX action)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
  try {
    $student_id = $_GET['student_id'] ?? null;
    if (!$student_id) {
      throw new Exception('Student ID is required');
    }

    // Prepare SF10 data
    $sf10_insert = [
      'student_id' => $student_id,
      'last_name' => $_POST['last_name'] ?? '',
      'first_name' => $_POST['first_name'] ?? '',
      'middle_name' => $_POST['middle_name'] ?? '',
      'suffix' => $_POST['suffix'] ?? '',
      'lrn' => $_POST['lrn'] ?? '',
      'birthdate' => !empty($_POST['birthdate']) ? $_POST['birthdate'] : null,
      'sex' => $_POST['sex'] ?? '',
      'school_name' => $_POST['school_name'] ?? '',
      'school_id' => $_POST['school_id'] ?? '',
      'school_address' => $_POST['school_address'] ?? '',
      'kinder_progress_report' => isset($_POST['kinder_progress_report']) ? 1 : 0,
      'eccd_checklist' => isset($_POST['eccd_checklist']) ? 1 : 0,
      'kinder_certificate' => isset($_POST['kinder_certificate']) ? 1 : 0,
      'pept_passer' => isset($_POST['pept_passer']) ? 1 : 0,
      'pept_text' => $_POST['pept_text'] ?? '',
      'exam_date' => !empty($_POST['exam_date']) ? $_POST['exam_date'] : null,
      'others_check' => isset($_POST['others_check']) ? 1 : 0,
      'others_text' => $_POST['others_text'] ?? '',
      'testing_center_name' => $_POST['testing_center_name'] ?? '',
      'testing_center_address' => $_POST['testing_center_address'] ?? '',
      'remark' => $_POST['remark'] ?? ''
    ];

    // Check if SF10 record exists
    $stmt_check = $pdo->prepare("SELECT id FROM sf10_data WHERE student_id = ?");
    $stmt_check->execute([$student_id]);
    $existing_sf10 = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($existing_sf10) {
      // Update existing SF10 record
      $update_cols = implode(', ', array_map(fn($k) => "$k = ?", array_keys($sf10_insert)));
      $stmt_update = $pdo->prepare("UPDATE sf10_data SET $update_cols WHERE id = ?");
      $stmt_update->execute([...$sf10_insert, $existing_sf10['id']]);
      $sf10_id = $existing_sf10['id'];
    } else {
      // Insert new SF10 record
      $cols = implode(', ', array_keys($sf10_insert));
      $placeholders = implode(', ', array_fill(0, count($sf10_insert), '?'));
      $stmt_insert = $pdo->prepare("INSERT INTO sf10_data ($cols) VALUES ($placeholders)");
      $stmt_insert->execute(array_values($sf10_insert));
      $sf10_id = $pdo->lastInsertId();
    }

    // Delete existing remedial data for this SF10
    $pdo->prepare("DELETE FROM remedial_class WHERE sf10_rem_id IN (
            SELECT sf10_rem_id FROM sf10_remedial_class WHERE sf10_data_id = ?
        )")->execute([$sf10_id]);
    $pdo->prepare("DELETE FROM sf10_remedial_class WHERE sf10_data_id = ?")->execute([$sf10_id]);

    // Process remedial data for each scholastic record
    for ($i = 1; $i <= $num_scholastic_records; $i++) {
      $rem_areas = $_POST["rem{$i}_area"] ?? [];

      if (!empty($rem_areas) && is_array($rem_areas)) {
        // Create a remedial class group for this scholastic record
        $stmt_rem_insert = $pdo->prepare(
          "INSERT INTO sf10_remedial_class (sf10_data_id) VALUES (?)"
        );
        $stmt_rem_insert->execute([$sf10_id]);
        $sf10_rem_id = $pdo->lastInsertId();

        // Insert remedial entries
        foreach ($rem_areas as $idx => $area) {
          if (!empty($area)) {
            $stmt_rem_data = $pdo->prepare(
              "INSERT INTO remedial_class (sf10_rem_id, area, final_rating, class_mark, recomputed_rating, remarks) 
                             VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt_rem_data->execute([
              $sf10_rem_id,
              $area,
              $_POST["rem{$i}_final"][$idx] ?? '',
              $_POST["rem{$i}_class_mark"][$idx] ?? '',
              $_POST["rem{$i}_recomputed"][$idx] ?? '',
              $_POST["rem{$i}_remarks"][$idx] ?? ''
            ]);
          }
        }
      }
    }

    $showSuccess = true;
    $successMessage = 'SF10 data saved successfully!';
  } catch (Exception $e) {
    error_log("SF10 Error: " . $e->getMessage());
    die("Error: " . htmlspecialchars($e->getMessage()));
  }
}

if (isset($_GET['student_id'])) {
  $stmt = $pdo->prepare("SELECT * FROM sf10_data WHERE student_id = ? ORDER BY id DESC LIMIT 1");
  $stmt->execute([$_GET['student_id']]);
  $sf10_data = $stmt->fetch(PDO::FETCH_ASSOC);

  // Populate learning areas and grades from sf9_records
  foreach ($sf9_records as $idx => $sf9_record) {
    $scholastic_index = $idx + 1; // Start from 1, not 0

    // Set basic info from SF9
    $scholastic_data['grades'][$scholastic_index] = $sf9_record['grade'] ?? '';
    $scholastic_data['sections'][$scholastic_index] = $sf9_record['section'] ?? '';
    $scholastic_data['school_years'][$scholastic_index] = $sf9_record['school_year'] ?? '';
    $scholastic_data['adviser_name'][$scholastic_index] = $sf9_record['teacher'] ?? '';

    // Extract learning areas and grades from SF9
    $scholastic_data['learning_areas'][$scholastic_index] = [];
    $scholastic_data['q1'][$scholastic_index] = [];
    $scholastic_data['q2'][$scholastic_index] = [];
    $scholastic_data['q3'][$scholastic_index] = [];
    $scholastic_data['q4'][$scholastic_index] = [];
    $scholastic_data['final_ratings'][$scholastic_index] = [];
    $scholastic_data['remarks_table'][$scholastic_index] = [];

    // Populate learning areas and quarterly grades from SF9 columns
    for ($r = 1; $r <= $num_subjects; $r++) {
      $scholastic_data['learning_areas'][$scholastic_index][$r - 1] = $sf9_record["subject_{$r}"] ?? '';
      $scholastic_data['q1'][$scholastic_index][$r - 1] = $sf9_record["q1_{$r}"] ?? '';
      $scholastic_data['q2'][$scholastic_index][$r - 1] = $sf9_record["q2_{$r}"] ?? '';
      $scholastic_data['q3'][$scholastic_index][$r - 1] = $sf9_record["q3_{$r}"] ?? '';
      $scholastic_data['q4'][$scholastic_index][$r - 1] = $sf9_record["q4_{$r}"] ?? '';
      $scholastic_data['final_ratings'][$scholastic_index][$r - 1] = $sf9_record["final_{$r}"] ?? '';
      $scholastic_data['remarks_table'][$scholastic_index][$r - 1] = $sf9_record["remarks_{$r}"] ?? '';
    }

    // Calculate general average
    $total = 0;
    $count = 0;
    for ($r = 0; $r < $num_subjects; $r++) {
      $final = $scholastic_data['final_ratings'][$scholastic_index][$r] ?? '';
      if (!empty($final) && is_numeric($final)) {
        $total += floatval($final);
        $count++;
      }
    }
    $scholastic_data['general_average'][$scholastic_index] = $count > 0 ? round($total / $count, 2) : '';
  }

  // Load remedial data from database for each scholastic record
  $remedial_loaded = [];

  // Initialize all scholastic records with empty arrays
  for ($i = 1; $i <= $num_scholastic_records; $i++) {
    $remedial_loaded[$i] = [
      'area' => [],
      'final' => [],
      'class_mark' => [],
      'recomputed' => [],
      'remarks' => []
    ];
  }

  if (!empty($sf10_data['id'])) {
    // Get all remedial groups for this SF10 in order
    $stmt_groups = $pdo->prepare("
            SELECT sf10_rem_id FROM sf10_remedial_class 
            WHERE sf10_data_id = ?
            ORDER BY sf10_rem_id ASC
        ");
    $stmt_groups->execute([$sf10_data['id']]);
    $remedial_groups = $stmt_groups->fetchAll(PDO::FETCH_COLUMN);

    // For each group, load its remedial entries
    foreach ($remedial_groups as $group_index => $sf10_rem_id) {
      $scholastic_index = $group_index + 1; // Convert 0-based to 1-based

      if ($scholastic_index <= $num_scholastic_records) {
        $stmt_entries = $pdo->prepare("
                    SELECT * FROM remedial_class 
                    WHERE sf10_rem_id = ?
                    ORDER BY remedial_id ASC
                ");
        $stmt_entries->execute([$sf10_rem_id]);
        $entries = $stmt_entries->fetchAll(PDO::FETCH_ASSOC);

        foreach ($entries as $rem) {
          $remedial_loaded[$scholastic_index]['area'][] = $rem['area'] ?? '';
          $remedial_loaded[$scholastic_index]['final'][] = $rem['final_rating'] ?? '';
          $remedial_loaded[$scholastic_index]['class_mark'][] = $rem['class_mark'] ?? '';
          $remedial_loaded[$scholastic_index]['recomputed'][] = $rem['recomputed_rating'] ?? '';
          $remedial_loaded[$scholastic_index]['remarks'][] = $rem['remarks'] ?? '';
        }
      }
    }
  }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>SF10 Fill</title>
  <link href="<?= base_url() ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= BASE_FR ?>/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet">
  <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet\"> -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/fontawesome/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', Arial, sans-serif;
      background: #f4f5f7;
      margin: 0;
      padding: 0;
    }

    /* Header */
    .header-brand {
      border-bottom: 1px solid rgba(0, 0, 0, .2);
      height: 75px;
      background: #f5365c;
    }

    .header-brand img {
      width: 65px;
      height: 65px;
      border-radius: 50%;
      margin-right: 15px;
      object-fit: cover;
    }

    .header-brand h4 {
      font-size: 1.3rem;
      font-weight: 700;
      color: #fff;
      margin: 0;
    }

    /* Containers */
    .sidebar,
    .eligibility-container,
    .scholastic-container {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      margin-bottom: 20px;
    }

    .sidebar h5,
    .eligibility-container h5,
    .scholastic-container h5 {
      font-weight: 700;
      font-size: 1.2rem;
      color: #333;
      margin-bottom: 15px;
      text-align: center;
    }

    /* Sidebar behavior */
    .sidebar {
      position: sticky;
      top: 90px;
      height: fit-content;
    }

    /* Main content centering */
    .main-content {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .main-content>.eligibility-container,
    .main-content>.scholastic-container {
      width: 100%;
      max-width: 1100px;
    }

    /* Forms */
    .form-label {
      font-weight: 600;
      margin-top: 10px;
    }

    .form-control.form-control-sm {
      font-weight: 500;
      padding: 8px 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .btn-lg {
      padding: 10px 18px;
      font-size: 16px;
      border-radius: 6px;
    }

    .table input {
      width: 100%;
    }

    /* Remedial carousel */
    .remedial-carousel-container {
      width: 100%;
      background: #fff;
      /* same as other containers */
      padding: 20px;
      /* match other containers */
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      margin: 30px auto 20px;
      /* space from Scholastic and below */
      position: relative;
      overflow: hidden;
    }

    /* Wrapper inside carousel */
    .remedial-wrapper {
      position: relative;
    }

    /* Individual slides */
    .remedial-slide {
      display: none;
      width: 100%;
    }

    .remedial-slide.active {
      display: block;
    }

    /* Table inside slides */
    .remedial-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    .remedial-table th,
    .remedial-table td {
      border: 1px solid #ccc;
      padding: 5px;
      text-align: center;
    }

    /* Arrows */
    .arrow {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: #8c2b2b;
      color: white;
      border: none;
      font-size: 24px;
      padding: 8px 12px;
      cursor: pointer;
      border-radius: 50%;
    }

    .arrow.prev {
      left: -40px;
    }

    .arrow.next {
      right: -40px;
    }

    .arrow:hover {
      background: #b03a3a;
    }

    /* Remedial Records Table Styling */
    .table-responsive {
      margin-top: 15px;
      border-radius: 8px;
      overflow: hidden;
    }

    .table {
      margin-bottom: 0;
    }

    .table thead {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .table thead th {
      padding: 12px;
      font-weight: 600;
      border: none;
    }

    .table tbody td {
      padding: 12px;
      vertical-align: middle;
      border-color: #e0e0e0;
    }

    .table tbody tr:hover {
      background-color: #f8f9fa;
    }

    .table tbody tr:last-child td {
      border-bottom: 2px solid #667eea;
    }

    .btn-danger {
      padding: 5px 10px;
      font-size: 0.875rem;
      transition: all 0.3s ease;
    }

    .btn-danger:hover {
      transform: scale(1.1);
      box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    .btn-danger i {
      margin-right: 3px;
    }

    /* Card Styling */
    .card {
      border: none;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      border-radius: 10px;
      overflow: hidden;
      margin-top: 30px;
    }

    .card-header {
      border: none;
      padding: 15px 20px;
      font-weight: 600;
    }

    .card-body {
      padding: 20px;
    }
  </style>

</head>

<body>
  <div class="d-flex align-items-center justify-content-between col-12 m-0 p-0 header-brand">
    <div class="d-flex align-items-center ps-4">
      <img src="<?= BASE_FR ?>/assets/image/logo2.png" alt="Logo">
      <h4>STA.MARIA WEB SYSTEM</h4>
    </div>
  </div>
  <div class="container-fluid p-3">
    <form method="post">
      <input type="hidden" id="form_student_id" value="<?= htmlspecialchars($student_id ?? '') ?>">
      <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4">
          <div class="sidebar">
            <h5>Learner's Personal Information</h5>
            <label class="form-label">Last Name</label>
            <input readonly type="text" class="form-control form-control-sm" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? ($student['lname'] ?? ($sf10_data['last_name'] ?? ''))) ?>">
            <label class="form-label">First Name</label>
            <input readonly type="text" class="form-control form-control-sm" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? ($student['fname'] ?? ($sf10_data['first_name'] ?? ''))) ?>">
            <label class="form-label">Middle Name</label>
            <input readonly type="text" class="form-control form-control-sm" name="middle_name" value="<?= htmlspecialchars($_POST['middle_name'] ?? ($student['mname'] ?? ($sf10_data['middle_name'] ?? ''))) ?>">
            <label class="form-label">Name suffix.</label>
            <input readonly type="text" class="form-control form-control-sm" name="suffix" value="<?= htmlspecialchars($_POST['suffix'] ?? ($student['suffix'] ?? ($sf10_data['suffix'] ?? ''))) ?>">
            <label class="form-label">LRN</label>
            <input readonly type="text" class="form-control form-control-sm" name="lrn" value="<?= htmlspecialchars($_POST['lrn'] ?? ($student['lrn'] ?? ($sf10_data['lrn'] ?? ''))) ?>">
            <label class="form-label">Birthdate (MM/DD/YY)</label>
            <input readonly type="text" class="form-control form-control-sm" name="birthdate" value="<?= htmlspecialchars($_POST['birthdate'] ?? ($student['birthdate'] ?? ($sf10_data['birthdate'] ?? ''))) ?>">
            <label class="form-label">Sex</label>
            <input readonly type="text" class="form-control form-control-sm" name="sex" value="<?= htmlspecialchars($_POST['sex'] ?? ($student['sex'] ?? ($sf10_data['sex'] ?? ''))) ?>">
            <div class="text-center mt-3 d-flex justify-content-center gap-2 flex-wrap">
              <button type="submit" class="btn btn-primary btn-lg">Save</button>
              <button type="button" class="btn btn-secondary btn-lg" onclick="window.history.back();">Back</button>
            </div>
          </div>
        </div>


        <div class="col-lg-9 col-md-8">
          <div class="eligibility-container">
            <h5>Elementary School Eligibility</h5>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="kinder_progress_report" id="kinder_progress_report" value="1" <?= (!empty($_POST['kinder_progress_report']) || (!empty($sf10_data['kinder_progress_report']) && $sf10_data['kinder_progress_report'])) ? 'checked' : '' ?>>
              <label class="form-check-label" for="kinder_progress_report">Kinder Progress Report</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="eccd_checklist" id="eccd_checklist" value="1" <?= (!empty($_POST['eccd_checklist']) || (!empty($sf10_data['eccd_checklist']) && $sf10_data['eccd_checklist'])) ? 'checked' : '' ?>>
              <label class="form-check-label" for="eccd_checklist">ECCD Checklist</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="kinder_certificate" id="kinder_certificate" value="1" <?= (!empty($_POST['kinder_certificate']) || (!empty($sf10_data['kinder_certificate']) && $sf10_data['kinder_certificate'])) ? 'checked' : '' ?>>
              <label class="form-check-label" for="kinder_certificate">Kindergarten Certificate of Completion</label>
            </div>
            <label class="form-label">Name of School</label>
            <input type="text" class="form-control form-control-sm" name="school_name" value="<?= htmlspecialchars($_POST['school_name'] ?? ($sf10_data['school_name'] ?? '')) ?>">
            <label class="form-label">School ID</label>
            <input type="text" class="form-control form-control-sm" name="school_id" value="<?= htmlspecialchars($_POST['school_id'] ?? ($sf10_data['school_id'] ?? '')) ?>">
            <label class="form-label">Address of School</label>
            <input type="text" class="form-control form-control-sm" name="school_address" value="<?= htmlspecialchars($_POST['school_address'] ?? ($sf10_data['school_address'] ?? '')) ?>">
            <h6 class="mt-3">Other Credential Presented</h6>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="pept_passer" id="pept_passer" value="1" <?= (!empty($_POST['pept_passer']) || (!empty($sf10_data['pept_passer']) && $sf10_data['pept_passer'])) ? 'checked' : '' ?>>
              <label class="form-check-label" for="pept_passer">PEPT Passer Rating</label>
            </div>
            <label class="form-label">PEPT Passer Rating (text)</label>
            <input type="text" class="form-control form-control-sm" name="pept_text" value="<?= htmlspecialchars($_POST['pept_text'] ?? ($sf10_data['pept_text'] ?? '')) ?>">
            <label class="form-label">Date of Examination/Assessment (dd/mm/yyyy)</label>
            <input type="text" class="form-control form-control-sm" name="exam_date" value="<?= htmlspecialchars($_POST['exam_date'] ?? ($sf10_data['exam_date'] ?? '')) ?>">
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" name="others_check" id="others_check" value="1" <?= (!empty($_POST['others_check']) || (!empty($sf10_data['others_check']) && $sf10_data['others_check'])) ? 'checked' : '' ?>>
              <label class="form-check-label" for="others_check">Others, pls specify</label>
            </div>
            <label class="form-label">Others (text)</label>
            <input type="text" class="form-control form-control-sm" name="others_text" value="<?= htmlspecialchars($_POST['others_text'] ?? ($sf10_data['others_text'] ?? '')) ?>">
            <label class="form-label">Name of Testing Center</label>
            <input type="text" class="form-control form-control-sm" name="testing_center_name" value="<?= htmlspecialchars($_POST['testing_center_name'] ?? ($sf10_data['testing_center_name'] ?? '')) ?>">
            <label class="form-label">Address of Testing Center</label>
            <input type="text" class="form-control form-control-sm" name="testing_center_address" value="<?= htmlspecialchars($_POST['testing_center_address'] ?? ($sf10_data['testing_center_address'] ?? '')) ?>">
            <label class="form-label">Remark</label>
            <input type="text" class="form-control form-control-sm" name="remark" value="<?= htmlspecialchars($_POST['remark'] ?? ($sf10_data['remark'] ?? '')) ?>">
          </div>
        </div>


        <div class="col-lg-9 col-md-8">
          <div class="scholastic-container">
            <h5>Scholastic Records</h5>
            <ul class="nav nav-tabs mb-3" id="srTabs" role="tablist">
              <?php for ($i = 1; $i <= $num_scholastic_records; $i++): ?>

                <li class="nav-item" role="presentation">
                  <button class="nav-link <?= $i === $num_scholastic_records ? 'active' : '' ?>" id="tab<?= $i ?>" data-bs-toggle="tab" data-bs-target="#sr<?= $i ?>" type="button" role="tab">
                    Scholastic <?= $i ?> <?= $i === $num_scholastic_records ? ' (Latest)' : '' ?>
                  </button>
                </li>
              <?php endfor; ?>
            </ul>


            <div class="tab-content">
              <?php for ($i = 1; $i <= $num_scholastic_records; $i++): ?>

                <div class="tab-pane fade <?= $i === $num_scholastic_records ? 'show active' : '' ?>" id="sr<?= $i ?>" role="tabpanel">
                  <div class="alert alert-info"><small>Scholastic records are read-only and cannot be edited.</small></div>
                  <label class="form-label">Grade</label>
                  <input type="text" class="form-control form-control-sm" disabled name="grade<?= $i ?>"
                    value="<?= htmlspecialchars($_POST['grade' . $i] ?? ($scholastic_data['grades'][$i] ?? '')) ?>">

                  <label class="form-label">Section</label>
                  <input type="text" class="form-control form-control-sm" disabled name="section<?= $i ?>"
                    value="<?= htmlspecialchars($_POST['section' . $i] ?? ($scholastic_data['sections'][$i] ?? '')) ?>">

                  <label class="form-label">School Year</label>
                  <input type="text" class="form-control form-control-sm" disabled name="school_year<?= $i ?>"
                    value="<?= htmlspecialchars($_POST['school_year' . $i] ?? ($scholastic_data['school_years'][$i] ?? '')) ?>">

                  <label class="form-label">Name of Adviser</label>
                  <input type="text" class="form-control form-control-sm" disabled name="adviser_name<?= $i ?>"
                    value="<?= htmlspecialchars($_POST['adviser_name' . $i] ?? ($scholastic_data['adviser_name'][$i] ?? '')) ?>">

                  <h6 class="mt-3">Grades Table</h6>
                  <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                      <thead>
                        <tr>
                          <th>Learning Area</th>
                          <th>1</th>
                          <th>2</th>
                          <th>3</th>
                          <th>4</th>
                          <th>Final Rating</th>
                          <th>Remarks</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        // Count non-empty subjects for this scholastic record
                        $subjects_count = 0;
                        if (!empty($scholastic_data['learning_areas'][$i])) {
                          foreach ($scholastic_data['learning_areas'][$i] as $subj) {
                            if (!empty($subj)) {
                              $subjects_count++;
                            }
                          }
                        }
                        
                        // Loop through actual subjects only
                        $subj_idx = 0;
                        for ($r = 0; $r < 15; $r++):
                          if (!empty($scholastic_data['learning_areas'][$i][$r])):
                        ?>
                          <tr>
                            <td>
                              <input type="text" class="form-control form-control-sm" disabled
                                name="learning_area<?= $i ?>[]"
                                value="<?= htmlspecialchars($_POST['learning_area' . $i][$subj_idx] ?? ($scholastic_data['learning_areas'][$i][$r] ?? '')) ?>">
                            </td>
                            <td>
                              <input type="text" class="form-control form-control-sm" disabled
                                name="q1_<?= $i ?>[]"
                                value="<?= htmlspecialchars($_POST['q1_' . $i][$subj_idx] ?? ($scholastic_data['q1'][$i][$r] ?? '')) ?>">
                            </td>
                            <td>
                              <input type="text" class="form-control form-control-sm" disabled
                                name="q2_<?= $i ?>[]"
                                value="<?= htmlspecialchars($_POST['q2_' . $i][$subj_idx] ?? ($scholastic_data['q2'][$i][$r] ?? '')) ?>">
                            </td>
                            <td>
                              <input type="text" class="form-control form-control-sm" disabled
                                name="q3_<?= $i ?>[]"
                                value="<?= htmlspecialchars($_POST['q3_' . $i][$subj_idx] ?? ($scholastic_data['q3'][$i][$r] ?? '')) ?>">
                            </td>
                            <td>
                              <input type="text" class="form-control form-control-sm" disabled
                                name="q4_<?= $i ?>[]"
                                value="<?= htmlspecialchars($_POST['q4_' . $i][$subj_idx] ?? ($scholastic_data['q4'][$i][$r] ?? '')) ?>">
                            </td>
                            <td>
                              <input type="text" class="form-control form-control-sm" disabled
                                name="final_rating_<?= $i ?>[]"
                                value="<?= htmlspecialchars($_POST['final_rating_' . $i][$subj_idx] ?? ($scholastic_data['final_ratings'][$i][$r] ?? '')) ?>">
                            </td>
                            <td>
                              <input type="text" class="form-control form-control-sm" disabled
                                name="remarks_table_<?= $i ?>[]"
                                value="<?= htmlspecialchars($_POST['remarks_table_' . $i][$subj_idx] ?? ($scholastic_data['remarks_table'][$i][$r] ?? '')) ?>">
                            </td>
                          </tr>
                        <?php 
                            $subj_idx++;
                          endif;
                        endfor;
                        ?>
                      </tbody>
                    </table>
                  </div>



                  <label class="form-label">General Average</label>
                  <input type="text" class="form-control form-control-sm" disabled name="general_average_<?= $i ?>" value="<?= htmlspecialchars($_POST['general_average_' . $i] ?? ($scholastic_data['general_average'][$i] ?? '')) ?>">
                  <!-- Form to add new remedial entries -->
                  <div class="card card-body bg-light mb-3">
                    <h6 class="mb-3">Add New Remedial Entry for Scholastic Record <?= $i ?> (School Year: <?= htmlspecialchars($_POST['school_year' . $i] ?? ($scholastic_data['school_years'][$i] ?? '')) ?>)</h6>
                    <input type="hidden" id="school_year_<?= $i ?>" value="<?= htmlspecialchars($_POST['school_year' . $i] ?? ($scholastic_data['school_years'][$i] ?? '')) ?>">
                    <div class="row">
                      <div class="col-md-6 mb-2">
                        <label class="form-label">Learning Area</label>
                        <select class="form-control form-control-sm" id="rem_area_<?= $i ?>" onchange="updateFinalRating(<?= $i ?>)">
                          <option value="" data-final-rating="">-- Select Learning Area --</option>
                          <?php
                          // Get subjects from this scholastic record with final ratings
                          if (!empty($scholastic_data['learning_areas'][$i])) {
                            foreach ($scholastic_data['learning_areas'][$i] as $subj_idx => $subject) {
                              if (!empty($subject)) {
                                $final_rating = $scholastic_data['final_ratings'][$i][$subj_idx] ?? '';
                                echo '<option value="' . htmlspecialchars($subject) . '" data-final-rating="' . htmlspecialchars($final_rating) . '">' . htmlspecialchars($subject) . '</option>';
                              }
                            }
                          }
                          ?>
                        </select>
                      </div>
                      <div class="col-md-2 mb-2">
                        <label class="form-label">Final Rating</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Final Rating"
                          id="rem_final_<?= $i ?>" disabled />
                      </div>
                      <div class="col-md-2 mb-2">
                        <label class="form-label">Class Mark</label>
                        <input type="text" class="form-control form-control-sm" placeholder="PASSED/FAILED"
                          id="rem_class_mark_<?= $i ?>" disabled />
                      </div>
                      <div class="col-md-2 mb-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-sm btn-success w-100" onclick="addRemedialAjax(<?= $i ?>)">
                          <i class="fas fa-plus"></i> Add
                        </button>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3 mb-2">
                        <label class="form-label">Recomputed Final Grade</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Recomputed Final Grade"
                          id="rem_recomputed_<?= $i ?>" onchange="generateClassMark(<?= $i ?>)" oninput="generateClassMark(<?= $i ?>)" />
                      </div>
                      <div class="col-md-3 mb-2">
                        <label class="form-label">Remarks (Comment)</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Teacher remarks"
                          id="rem_remarks_input_<?= $i ?>" />
                      </div>
                    </div>
                  </div>

                </div>

              <?php endfor; ?>
            </div>

            <!-- Remedial Records Display Section (Updated dynamically by tab) -->
            <div class="card mt-4">
              <div class="card-header bg-primary text-white">
                <h5 style="color: white !important;" class="mb-0">Remedial Records for <span id="active-scholastic-label">Scholastic Record <?= $num_scholastic_records ?></span></h5>
              </div>
              <div class="card-body">
                <div id="remedial-records-container">
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </form>
  </div>



  <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-success">
        <div class="modal-body text-center text-success">
          <?= htmlspecialchars($successMessage) ?>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= BASE_FR ?>/assets/js/bootstrap.min.js"></script>
  <script src="<?= BASE_FR ?>/assets/libs/sweetalert2/sweetalert2.min.js"></script>
  <?php if ($showSuccess): ?>
    <script>
      const successModal = new bootstrap.Modal(document.getElementById('successModal'));
      successModal.show();
      setTimeout(() => {
        successModal.hide();
      }, 2000);
    </script>
  <?php endif; ?>

  <script>
    function updateFinalRating(i) {
      const dropdown = document.getElementById(`rem_area_${i}`);
      const finalRatingInput = document.getElementById(`rem_final_${i}`);
      const selectedSubject = dropdown.value;
      
      if (selectedSubject) {
        // Find the subject in the grades table within the same tab pane
        const tabPane = document.getElementById(`sr${i}`);
        if (tabPane) {
          const table = tabPane.querySelector('table');
          if (table) {
            const rows = table.querySelectorAll('tbody tr');
            for (let row of rows) {
              // Get all TD elements - first TD has the learning area
              const tds = row.querySelectorAll('td');
              if (tds.length >= 5) {
                const firstInput = tds[0].querySelector('input');
                if (firstInput && firstInput.value === selectedSubject) {
                  // Found the subject row - get Q1-Q4 from TD 1-4
                  const q1Input = tds[1].querySelector('input');
                  const q2Input = tds[2].querySelector('input');
                  const q3Input = tds[3].querySelector('input');
                  const q4Input = tds[4].querySelector('input');
                  
                  const q1 = parseFloat(q1Input?.value) || 0;
                  const q2 = parseFloat(q2Input?.value) || 0;
                  const q3 = parseFloat(q3Input?.value) || 0;
                  const q4 = parseFloat(q4Input?.value) || 0;
                  
                  // Calculate average from quarters
                  const average = ((q1 + q2 + q3 + q4) / 4).toFixed(2);
                  finalRatingInput.value = average;
                  break;
                }
              }
            }
          }
        }
      } else {
        finalRatingInput.value = '';
      }

      // Update dropdown to hide already-selected subjects
      filterDropdownDuplicates(i);
    }

    function fetchRemedial(sy) {
      const studentIdInput = document.getElementById('form_student_id');
      const studentId = studentIdInput ? studentIdInput.value : '<?= $student_id ?>';
      
      const formData = new FormData();
      formData.append('action', 'show_remedial_records');
      formData.append('student_id', studentId);
      formData.append('school_year', sy);
      
      console.log('fetchRemedial called with sy:', sy, 'studentId:', studentId);
      
      fetch(window.location.href, {
          method: 'POST',
          body: formData
        })
        .then(response => response.text())
        .then(html => {
          console.log('AJAX Response received, length:', html.length);
          console.log('Response HTML:', html.substring(0, 200));
          document.getElementById('remedial-records-container').innerHTML = html;
          console.log('Remedial records loaded for school year:', sy);
          // Refresh all dropdown filters since remedial data has changed
          const dropdowns = document.querySelectorAll('[id^="rem_area_"]');
          dropdowns.forEach(dd => {
            const indexMatch = dd.id.match(/\d+$/);
            if (indexMatch) {
              const idx = parseInt(indexMatch[0]);
              filterDropdownDuplicates(idx);
            }
          });
        })
        .catch(error => {
          console.error('Fetch Error:', error);
        });
    }

    function deleteRemedialEntry(remedialId, schoolYear) {
      Swal.fire({
        icon: 'warning',
        title: 'Delete Remedial Entry',
        text: 'Are you sure you want to delete this remedial entry?',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        confirmButtonColor: '#dc3545'
      }).then((result) => {
        if (result.isConfirmed) {
          const formData = new FormData();
          formData.append('action', 'delete_remedial');
          formData.append('remedial_id', remedialId);
          
          fetch(window.location.href, {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Deleted',
                text: 'Remedial entry has been deleted successfully',
                timer: 1500,
                showConfirmButton: false
              }).then(() => {
                // Reload remedial records for this school year
                fetchRemedial(schoolYear);
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to delete remedial entry'
              });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'An error occurred while deleting the entry'
            });
          });
        }
      });
    }

    function addRemedialAjax(i) {
      const areaSelect = document.getElementById(`rem_area_${i}`);
      const finalRatingInput = document.getElementById(`rem_final_${i}`);
      const classMarkInput = document.getElementById(`rem_class_mark_${i}`);
      const recomputedInput = document.getElementById(`rem_recomputed_${i}`);
      const remarksInput = document.getElementById(`rem_remarks_input_${i}`);
      const schoolYearInput = document.getElementById(`school_year_${i}`);
      const studentIdInput = document.getElementById('form_student_id');

      const area = areaSelect.value;
      const finalRating = finalRatingInput.value;
      const classMark = classMarkInput.value;
      const recomputed = recomputedInput.value;
      const remarks = remarksInput.value;
      const schoolYear = schoolYearInput.value;
      const studentId = studentIdInput ? studentIdInput.value : '<?= $student_id ?>';

      if (!area) {
        Swal.fire({
          icon: 'warning',
          title: 'Missing Field',
          text: 'Please select a learning area'
        });
        return;
      }
      if (!recomputed) {
        Swal.fire({
          icon: 'warning',
          title: 'Missing Field',
          text: 'Please enter a recomputed final grade'
        });
        return;
      }

      // Send to backend via AJAX
      const formData = new FormData();
      formData.append('action', 'add_remedial');
      formData.append('student_id', studentId);
      formData.append('scholastic_index', i);
      formData.append('school_year', schoolYear);
      formData.append('area', area);
      formData.append('final_rating', finalRating);
      formData.append('class_mark', classMark);
      formData.append('recomputed_rating', recomputed);
      formData.append('remarks', remarks);

      fetch(window.location.href, {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: 'Remedial data added successfully!',
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              // Clear form
              areaSelect.value = '';
              finalRatingInput.value = '';
              classMarkInput.value = '';
              recomputedInput.value = '';
              remarksInput.value = '';
              // Reload remedial records for this school year
              fetchRemedial(schoolYear);
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.message
            });
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while adding remedial data'
          });
        });
    }

    function filterDropdownDuplicates(i) {
      const dropdown = document.getElementById(`rem_area_${i}`);
      const allOptions = dropdown.querySelectorAll('option');
      
      // Get all currently used areas from the remedial records table in the AJAX container
      const usedAreas = new Set();
      const remedialContainer = document.getElementById('remedial-records-container');
      
      if (remedialContainer) {
        const table = remedialContainer.querySelector('table tbody');
        if (table) {
          const rows = table.querySelectorAll('tr');
          rows.forEach(row => {
            // First cell contains the learning area name
            if (row.cells.length > 0) {
              const areaText = row.cells[0].textContent.trim();
              if (areaText && areaText !== '') {
                usedAreas.add(areaText);
              }
            }
          });
        }
      }
      
      // Show/hide options based on whether they're already selected for this school year
      allOptions.forEach(option => {
        if (option.value === '' || option.value === undefined) {
          option.disabled = false; // Always show the blank option
          option.style.display = '';
        } else if (usedAreas.has(option.value)) {
          option.disabled = true; // Disable if already used for this school year
          option.style.display = 'none';
        } else {
          option.disabled = false;
          option.style.display = '';
        }
      });
    }

    function generateClassMark(i) {
      const recomputedInput = document.getElementById(`rem_recomputed_${i}`);
      const classMarkInput = document.getElementById(`rem_class_mark_${i}`);
      const recomputedValue = parseFloat(recomputedInput.value);

      if (!isNaN(recomputedValue)) {
        // 75 is passing grade
        classMarkInput.value = recomputedValue >= 75 ? 'PASSED' : 'FAILED';
      } else {
        classMarkInput.value = '';
      }
    }

    function calculateFinalRating(i) {
      // Calculate final rating from quarters for remedial entry
      // This is for the add remedial form
      const q1Input = document.querySelector(`input[name="rem_q1_${i}"]`);
      const q2Input = document.querySelector(`input[name="rem_q2_${i}"]`);
      const q3Input = document.querySelector(`input[name="rem_q3_${i}"]`);
      const q4Input = document.querySelector(`input[name="rem_q4_${i}"]`);
      const finalRatingInput = document.getElementById(`rem_final_${i}`);
      
      if (q1Input && q2Input && q3Input && q4Input && finalRatingInput) {
        const q1 = parseFloat(q1Input.value) || 0;
        const q2 = parseFloat(q2Input.value) || 0;
        const q3 = parseFloat(q3Input.value) || 0;
        const q4 = parseFloat(q4Input.value) || 0;
        
        if (q1 || q2 || q3 || q4) {
          const average = ((q1 + q2 + q3 + q4) / 4).toFixed(2);
          finalRatingInput.value = average;
        }
      }
    }



    function recalc(i) {
      let total = 0,
        count = 0;
      let q1s = document.querySelectorAll(`[name='q1_${i}[]']`);
      let q2s = document.querySelectorAll(`[name='q2_${i}[]']`);
      let q3s = document.querySelectorAll(`[name='q3_${i}[]']`);
      let q4s = document.querySelectorAll(`[name='q4_${i}[]']`);
      let finals = document.querySelectorAll(`[name='final_rating_${i}[]']`);
      
      // Loop through actual number of subjects (not hardcoded 15)
      for (let r = 0; r < q1s.length; r++) {
        if (!q1s[r] || !q2s[r] || !q3s[r] || !q4s[r] || !finals[r]) break;
        
        let q1 = parseFloat(q1s[r].value) || 0;
        let q2 = parseFloat(q2s[r].value) || 0;
        let q3 = parseFloat(q3s[r].value) || 0;
        let q4 = parseFloat(q4s[r].value) || 0;
        let final = 0;
        if (q1 || q2 || q3 || q4) {
          final = ((q1 + q2 + q3 + q4) / 4).toFixed(2);
          total += parseFloat(final);
          count++;
        }
        finals[r].value = final ? final : '';
      }
      document.querySelector(`[name='general_average_${i}']`).value = count ? (total / count).toFixed(2) : '';
    }

    // Initialize event listeners for all scholastic records
    const numScholasticRecords = <?= $num_scholastic_records; ?>;
    const numSubjects = <?= $num_subjects; ?>;

    for (let i = 1; i <= numScholasticRecords; i++) {
      // Add event listeners to quarterly inputs for each scholastic record
      let qInputs = document.querySelectorAll(
        `[name^='q1_${i}'],[name^='q2_${i}'],[name^='q3_${i}'],[name^='q4_${i}']`
      );
      qInputs.forEach(input => {
        input.addEventListener('input', () => recalc(i));
      });
      
      // Initialize calculations for each scholastic record
      recalc(i);
    }
    
    // Initialize tab click listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Load initial remedial records - try to find any school year input
      let schoolYearInput = null;
      for (let i = 1; i <= numScholasticRecords; i++) {
        schoolYearInput = document.getElementById(`school_year_${i}`);
        if (schoolYearInput) {
          console.log(`Found school_year_${i} with value:`, schoolYearInput.value);
          fetchRemedial(schoolYearInput.value);
          break;
        }
      }
      
      if (!schoolYearInput) {
        console.warn('No school year input found. numScholasticRecords:', numScholasticRecords);
      }
      
      // Add event listeners to all scholastic tabs
      const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
      tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(e) {
          // Extract scholastic index from button ID (e.g., 'tab1' -> 1)
          const tabId = this.getAttribute('id');
          const scholasticIndex = parseInt(tabId.replace('tab', ''));
          
          // Get the school year for this scholastic record
          const schoolYearElement = document.getElementById(`school_year_${scholasticIndex}`);
          if (schoolYearElement) {
            const schoolYear = schoolYearElement.value;
            // Reload remedial records for this school year
            fetchRemedial(schoolYear);
          }
        });
      });
    })
  </script>
</body>

</html>