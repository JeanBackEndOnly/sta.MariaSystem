<?php 
session_start();
require_once 'C:/xampp/htdocs/sta.MariaSystem/vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\IOFactory;

$templatePath = 'C:/xampp/htdocs/sta.MariaSystem/src/UI-Admin/contents/sf5/sf5.xlsx';
$saveDir = 'C:/xampp/htdocs/sta.MariaSystem/sf5_files';
if (!is_dir($saveDir)) mkdir($saveDir, 0777, true);

$totalRows = 59;
$skipRow = 33;

$formData = $_SESSION['sf5_form'] ?? [];
$downloadLink = $_SESSION['sf5_download'] ?? '';

/* -------- DOWNLOAD -------- */
if (isset($_GET['download'])) {
    if (!empty($_SESSION['sf5_download'])) {
        $file = $saveDir . DIRECTORY_SEPARATOR . $_SESSION['sf5_download'];
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        } else die("File not found!");
    } else die("No file to download.");
}

/* -------- Save -------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // School Info
    $formData['school_year'] = $_POST['school_year'] ?? '';
    $formData['curriculum'] = $_POST['curriculum'] ?? '';
    $formData['grade_level'] = $_POST['grade_level'] ?? '';
    $formData['section'] = $_POST['section'] ?? '';

    // learners
    for ($r = 13; $r <= $totalRows; $r++) {
        if ($r == $skipRow) continue;
        $formData['lrn'][$r] = $_POST['lrn'][$r] ?? '';
        $formData['name'][$r] = $_POST['name'][$r] ?? '';
        $formData['average'][$r] = $_POST['average'][$r] ?? '';
        $formData['action'][$r] = $_POST['action'][$r] ?? '';
        $formData['did_not_meet'][$r] = $_POST['did_not_meet'][$r] ?? '';
    }

    // male/female totals
    $formData['male_total'] = $_POST['male_total'] ?? 0;
    $formData['female_total'] = $_POST['female_total'] ?? 0;
    $formData['combined_total'] = (int)$formData['male_total'] + (int)$formData['female_total'];

    // Summary
    $summaryRows = ['promoted','conditional','retained'];
    foreach ($summaryRows as $status) {
        $formData['summary'][$status]['male'] = $_POST['summary'][$status]['male'] ?? '';
        $formData['summary'][$status]['female'] = $_POST['summary'][$status]['female'] ?? '';
        $formData['summary'][$status]['total'] = $_POST['summary'][$status]['total'] ?? '';
    }

    // learning progress
    $progressRows = [
        'did_not_meet'=>'Did Not Meet Expectations (74 and below)',
        'fairly_satisfactory'=>'Fairly Satisfactory (75-79)',
        'satisfactory'=>'Satisfactory (80-84)',
        'very_satisfactory'=>'Very Satisfactory (85-89)',
        'outstanding'=>'Outstanding (90-100)'
    ];
    foreach ($progressRows as $key=>$label) {
        $formData['progress'][$key]['male'] = $_POST['progress'][$key]['male'] ?? '';
        $formData['progress'][$key]['female'] = $_POST['progress'][$key]['female'] ?? '';
        $formData['progress'][$key]['total'] = $_POST['progress'][$key]['total'] ?? '';
    }

    // Sidebar
    $formData['prepared_by'] = $_POST['prepared_by'] ?? '';
    $formData['certified_by'] = $_POST['certified_by'] ?? '';
    $formData['reviewed_by'] = $_POST['reviewed_by'] ?? '';

    $_SESSION['sf5_form'] = $formData;

    // Write to Excel
    $spreadsheet = IOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('G5', $formData['school_year']);
    $sheet->setCellValue('J5', $formData['curriculum']);
    $sheet->setCellValue('J7', $formData['grade_level']);
    $sheet->setCellValue('M7', $formData['section']);

    for ($r = 13; $r <= $totalRows; $r++) {
        if ($r == $skipRow) continue;
        $sheet->setCellValue("A{$r}", $formData['lrn'][$r] ?? '');
        $sheet->setCellValue("B{$r}", $formData['name'][$r] ?? '');
        $sheet->setCellValue("F{$r}", $formData['average'][$r] ?? '');
        $sheet->setCellValue("G{$r}", $formData['action'][$r] ?? '');
        $sheet->setCellValue("I{$r}", $formData['did_not_meet'][$r] ?? '');
    }

    // Write totals to Excel
    $sheet->setCellValue('F33', $formData['male_total']);
    $sheet->setCellValue('F60', $formData['female_total']);
    $sheet->setCellValue('F61', $formData['combined_total']);

    // Summary → Excel
    $summaryMap = [
        'promoted' => ['M15','N15','O15'],
        'conditional' => ['M17','N17','O17'],
        'retained' => ['M19','N19','O19']
    ];
    foreach ($summaryMap as $status => $cells) {
        $sheet->setCellValue($cells[0], $formData['summary'][$status]['male']);
        $sheet->setCellValue($cells[1], $formData['summary'][$status]['female']);
        $sheet->setCellValue($cells[2], $formData['summary'][$status]['total']);
    }

    // Learning Progress → Excel
    $progressMap = [
        'did_not_meet' => ['M24','N24','O24'],
        'fairly_satisfactory' => ['M26','N26','O26'],
        'satisfactory' => ['M28','N28','O28'],
        'very_satisfactory' => ['M30','N30','O30'],
        'outstanding' => ['M32','N32','O32']
    ];
    foreach ($progressMap as $status => $cells) {
        $sheet->setCellValue($cells[0], $formData['progress'][$status]['male']);
        $sheet->setCellValue($cells[1], $formData['progress'][$status]['female']);
        $sheet->setCellValue($cells[2], $formData['progress'][$status]['total']);
    }

    // Sidebar → Excel
    $sheet->setCellValue('N36', $formData['prepared_by']);
    $sheet->setCellValue('N41', $formData['certified_by']);
    $sheet->setCellValue('N46', $formData['reviewed_by']);

    // Save Excel with custom name
    $schoolYear = preg_replace('/[^A-Za-z0-9_-]/', '', $formData['school_year']);
    $gradeLevel = preg_replace('/[^A-Za-z0-9_-]/', '', $formData['grade_level']);
    $section = preg_replace('/[^A-Za-z0-9_-]/', '', $formData['section']);
    $filename = "{$schoolYear}_{$gradeLevel}_{$section}.xlsx";

    $savePath = $saveDir . DIRECTORY_SEPARATOR . $filename;
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($savePath);

    $_SESSION['sf5_download'] = $filename;

    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SF5</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family:'Poppins',sans-serif; background:#f4f5f7; }
.header { background:#E75480; color:white; padding:12px 20px; display:flex; align-items:center; }
.header img { width:60px; height:60px; border-radius:50%; margin-right:10px; }
.scrollable-table { max-height:350px; overflow-y:auto; }
.scrollable-table thead th { position:sticky; top:0; background:#f8f9fa; }
</style>
</head>
<body>

<div class="header">
  <img src="/sta.MariaSystem/assets/image/logo2.png" alt="Logo">
  <h4>STA. MARIA WEB SYSTEM</h4>
</div>

<div class="container-fluid mt-4">
<form method="post">
<div class="row">

<!-- LEFT -->
<div class="col-md-8">

<div class="card p-3 mb-3">
  <h5>School Info</h5>
  <div class="row">
    <div class="col-md-3"><input type="text" name="school_year" class="form-control" placeholder="School Year" value="<?= $formData['school_year'] ?? '' ?>"></div>
    <div class="col-md-3"><input type="text" name="curriculum" class="form-control" placeholder="Curriculum" value="<?= $formData['curriculum'] ?? '' ?>"></div>
    <div class="col-md-3"><input type="text" name="grade_level" class="form-control" placeholder="Grade Level" value="<?= $formData['grade_level'] ?? '' ?>"></div>
    <div class="col-md-3"><input type="text" name="section" class="form-control" placeholder="Section" value="<?= $formData['section'] ?? '' ?>"></div>
  </div>
</div>

<!-- Learners Table -->
<div class="card p-3 mb-3">
  <h5>Learners Table</h5>
  <div class="scrollable-table">
    <table class="table table-bordered table-sm text-center align-middle">
      <thead><tr><th>LRN</th><th>Learner Name</th><th>General Average</th><th>Action Taken</th><th>Did Not Meet</th></tr></thead>
      <tbody>
      <?php for ($r=13;$r<=59;$r++): if ($r==33) continue; ?>
      <tr>
        <td><input type="text" name="lrn[<?= $r ?>]" value="<?= $formData['lrn'][$r] ?? '' ?>" class="form-control form-control-sm"></td>
        <td><input type="text" name="name[<?= $r ?>]" value="<?= $formData['name'][$r] ?? '' ?>" class="form-control form-control-sm"></td>
        <td><input type="text" name="average[<?= $r ?>]" value="<?= $formData['average'][$r] ?? '' ?>" class="form-control form-control-sm"></td>
        <td><input type="text" name="action[<?= $r ?>]" value="<?= $formData['action'][$r] ?? '' ?>" class="form-control form-control-sm"></td>
        <td><input type="text" name="did_not_meet[<?= $r ?>]" value="<?= $formData['did_not_meet'][$r] ?? '' ?>" class="form-control form-control-sm"></td>
      </tr>
      <?php endfor; ?>
      </tbody>
    </table>
  </div>

  <div class="row mt-3 text-center">
    <div class="col">
      <label>Male Total (F33)</label>
      <input type="number" name="male_total" id="male_total" value="<?= $formData['male_total'] ?? '' ?>" class="form-control text-center">
    </div>
    <div class="col">
      <label>Female Total (F60)</label>
      <input type="number" name="female_total" id="female_total" value="<?= $formData['female_total'] ?? '' ?>" class="form-control text-center">
    </div>
    <div class="col">
      <label>Combined (F61)</label>
      <input type="number" readonly id="combined_total" name="combined_total" value="<?= $formData['combined_total'] ?? '' ?>" class="form-control text-center">
    </div>
  </div>
</div>

<script>
document.getElementById('male_total').addEventListener('input', updateTotal);
document.getElementById('female_total').addEventListener('input', updateTotal);
function updateTotal() {
  const male = parseInt(document.getElementById('male_total').value || 0);
  const female = parseInt(document.getElementById('female_total').value || 0);
  document.getElementById('combined_total').value = male + female;
}
</script>

<!-- Summary Table -->
<div class="card p-3 mb-3">
  <h5>Summary Table</h5>
  <table class="table table-bordered table-sm text-center">
    <thead><tr><th>Status</th><th>Male</th><th>Female</th><th>Total</th></tr></thead>
    <tbody>
    <?php $summaryRows=['promoted'=>'Promoted','conditional'=>'Conditional','retained'=>'Retained'];
    foreach($summaryRows as $k=>$label): ?>
    <tr>
      <td><?= $label ?></td>
      <td><input name="summary[<?= $k ?>][male]" value="<?= $formData['summary'][$k]['male'] ?? '' ?>" class="form-control form-control-sm"></td>
      <td><input name="summary[<?= $k ?>][female]" value="<?= $formData['summary'][$k]['female'] ?? '' ?>" class="form-control form-control-sm"></td>
      <td><input name="summary[<?= $k ?>][total]" value="<?= $formData['summary'][$k]['total'] ?? '' ?>" class="form-control form-control-sm"></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- learning progress table -->
<div class="card p-3 mb-3">
  <h5>Learning Progress & Achievement</h5>
  <table class="table table-bordered table-sm text-center">
    <thead><tr><th>Descriptors & Grading Scale</th><th>Male</th><th>Female</th><th>Total</th></tr></thead>
    <tbody>
    <?php 
    $progress=[
      'did_not_meet'=>'Did Not Meet Expectations (74 and below)',
      'fairly_satisfactory'=>'Fairly Satisfactory (75-79)',
      'satisfactory'=>'Satisfactory (80-84)',
      'very_satisfactory'=>'Very Satisfactory (85-89)',
      'outstanding'=>'Outstanding (90-100)'
    ];
    foreach($progress as $k=>$label): ?>
    <tr>
      <td class="text-start"><?= $label ?></td>
      <td><input name="progress[<?= $k ?>][male]" value="<?= $formData['progress'][$k]['male'] ?? '' ?>" class="form-control form-control-sm"></td>
      <td><input name="progress[<?= $k ?>][female]" value="<?= $formData['progress'][$k]['female'] ?? '' ?>" class="form-control form-control-sm"></td>
      <td><input name="progress[<?= $k ?>][total]" value="<?= $formData['progress'][$k]['total'] ?? '' ?>" class="form-control form-control-sm"></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

</div>

<!--  (Sidebar) yung sa right-->
<div class="col-md-4">
  <div class="card p-3">
    <h5>Prepared / Certified / Reviewed</h5>
    <div class="mb-3">
      <label>Prepared By:</label>
      <input name="prepared_by" value="<?= $formData['prepared_by'] ?? '' ?>" class="form-control form-control-sm">
      <small>Class Adviser</small>
    </div>
    <div class="mb-3">
      <label>Certified Correct & Submitted:</label>
      <input name="certified_by" value="<?= $formData['certified_by'] ?? '' ?>" class="form-control form-control-sm">
      <small>School Head</small>
    </div>
    <div class="mb-3">
      <label>Reviewed By:</label>
      <input name="reviewed_by" value="<?= $formData['reviewed_by'] ?? '' ?>" class="form-control form-control-sm">
      <small>Division Representative</small>
    </div>

    <div class="text-center mt-4">
      <button type="button" class="btn btn-secondary me-2" onclick="history.back()">Back</button>
      <button type="submit" class="btn btn-primary me-2">Save SF5</button>
      <a href="?download=1" class="btn btn-success">Download Latest SF5</a>
    </div>
  </div>
</div>

</div>
</form>
</div>

</body>
</html>
