<?php
    $query = "SELECT classes.*, users.* FROM classes
    INNER JOIN users ON classes.adviser_id = users.user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $query = "SELECT * FROM school_year WHERE school_year_status = 'Active'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $schoolYear = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="mx-2">
        <h4><i class="fa-solid fa-folder me-2"></i>SF5 - Report on Promotion and Level of Proficiency</h4>
    </div>
</div>

<!-- Search and Filters -->

<div class="row g-2  justify-content-between">
    <div class="row mb-3  justify-content-start">
        <div class="col-md-4">
            <input type="text" id="searchInput" name="search" class="form-control"
                placeholder="Search by name, role, status, or date...">
        </div>
        <div class="col-md-4">
            <select id="categoryFilter" name="statusCategory" class="form-select">
                <option value="">Grade Level</option>
                <option value="Grade 1">Grade 1</option>
                <option value="Grade 2">Grade 2</option>
                <option value="Grade 3">Grade 3</option>
                <option value="Grade 4">Grade 4</option>
                <option value="Grade 5">Grade 5</option>
                <option value="Grade 6">Grade 6</option>
            </select>
        </div>
    </div>
    <!-- Accounts Displays -->
    <div class="table-container-wrapper">
        <?php
            $stmt = $pdo->prepare("SELECT * FROM sections ORDER BY section_grade_level, section_name LIMIT 20");
            $stmt->execute();
            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = 1;
        ?>

        <!-- Fixed Header -->
        <div class="table-header">
            <table class="table table-bordered table-sm text-center mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Section</th>
                        <th>Grade Level</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- Scrollable Body -->
        <div class="table-body-scroll">
            <table class="table table-bordered table-sm text-center mb-0">
                <tbody>
                    <?php foreach($sections as $sec) : ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td>
                            <?= htmlspecialchars($sec["section_name"])  ?>
                        </td>
                        <td><?= htmlspecialchars($sec["section_grade_level"]) ?></td>
                        <td>
                            <div class="d-flex gap-1 justify-content-center">
                                <a
                                    href="/sta.MariaSystem/src/UI-Admin/contents/schoolform5.php?section_id=<?= htmlspecialchars($sec["section_id"]) ?>&grade=<?= htmlspecialchars($sec["section_grade_level"]) ?>&section=<?= htmlspecialchars($sec["section_name"]) ?>"><button
                                        class="btn btn-sm m-0 px-4 py-2 btn-info">View</button></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const tableBody = document.querySelector('.table-body-scroll tbody');
    const tableRows = tableBody.querySelectorAll('tr');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const gradeFilterValue = categoryFilter.value; // This is the grade level filter
        
        tableRows.forEach(row => {
            let showRow = true;
            
            // Search filter (search by name/section)
            if (searchTerm) {
                const nameCell = row.querySelector('td:nth-child(2)'); // Section name column
                const gradeCell = row.querySelector('td:nth-child(3)'); // Grade level column
                
                const nameText = nameCell ? nameCell.textContent.toLowerCase() : '';
                const gradeText = gradeCell ? gradeCell.textContent.toLowerCase() : '';
                
                // Check if search term exists in section name or grade level
                if (!nameText.includes(searchTerm) && 
                    !gradeText.includes(searchTerm)) {
                    showRow = false;
                }
            }
            
            // Grade Level filter
            if (gradeFilterValue && showRow) {
                const gradeCell = row.querySelector('td:nth-child(3)'); // Grade level column (3rd column)
                if (gradeCell) {
                    const gradeText = gradeCell.textContent.trim();
                    if (gradeText !== gradeFilterValue) {
                        showRow = false;
                    }
                }
            }
            
            // Show/hide row
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    // Add event listeners
    searchInput.addEventListener('input', filterTable);
    categoryFilter.addEventListener('change', filterTable);
    
    // Initial filter
    filterTable();
});
</script>