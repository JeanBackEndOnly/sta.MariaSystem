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

    $stmt = $pdo->prepare("SELECT * FROM student ORDER BY lname, fname");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = 1;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="mx-2">
        <h4><i class="fa-solid fa-file-archive me-2"></i>SF10 Learner's Permanent Academic Record</h4>
    </div>
</div>

<div class="row g-3 scroll-sf10">
    <!-- Search and Filters -->
    <div class="row mb-3 justify-content-between align-items-center">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" id="searchInput" name="search" class="form-control"
                    placeholder="Search by LRN, name, grade level, or enrolment status...">
            </div>
        </div>
        <div class="col-md-4">
            <select id="categoryFilter" name="statusCategory" class="form-select">
                <option value="">All Grade Levels</option>
                <option value="Grade 1">Grade 1</option>
                <option value="Grade 2">Grade 2</option>
                <option value="Grade 3">Grade 3</option>
                <option value="Grade 4">Grade 4</option>
                <option value="Grade 5">Grade 5</option>
                <option value="Grade 6">Grade 6</option>
            </select>
        </div>
    </div>


    <!-- Students Table -->
    <div class="table-container-wrapper p-0">
        <!-- Fixed Header -->
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover" style="font-size: 0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">LRN</th>
                        <th width="25%">Name</th>
                        <th width="15%">Grade Level</th>
                        <th width="10%">Sex</th>
                        <th width="15%">Enrolment Status</th>
                        <th width="15%">Action</th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- Scrollable Body -->
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-sm table-bordered table-hover mb-0" style="font-size: 0.875rem;">
                <tbody id="studentsTableBody">
                    <?php foreach($students as $stu) : ?>
                    <tr class="student-row" 
                        data-lrn="<?= htmlspecialchars(strtolower($stu["lrn"])) ?>"
                        data-name="<?= htmlspecialchars(strtolower($stu["fname"] . ' ' . $stu["lname"])) ?>"
                        data-grade="<?= htmlspecialchars(strtolower($stu["gradeLevel"])) ?>"
                        data-sex="<?= htmlspecialchars(strtolower($stu["sex"])) ?>"
                        data-status="<?= htmlspecialchars(strtolower($stu["enrolment_status"])) ?>">
                        <td width="5%"><?= $count++ ?></td>
                        <td width="15%">
                            <strong><?= htmlspecialchars($stu["lrn"]) ?></strong>
                        </td>
                        <td width="25%" class="student-name">
                            <div class="d-flex align-items-center">
                                <div class="avatar-placeholder me-2">
                                    <i class="fa-solid fa-graduation-cap text-dark"></i>
                                </div>
                                <div>
                                    <strong><?= htmlspecialchars($stu["lname"] . ", " . $stu["fname"]) ?></strong>
                                    <?php if(!empty($stu["mname"])): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($stu["mname"]) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td width="15%">
                            <span class="badge bg-info"><?= htmlspecialchars($stu["gradeLevel"]) ?></span>
                        </td>
                        <td width="10%">
                            <span class="badge bg-<?= $stu["sex"] == 'Male' ? 'info' : 'dark' ?>">
                                <?= htmlspecialchars($stu["sex"]) ?>
                            </span>
                        </td>
                        <td width="15%">
                            <?php
                            $status = $stu["enrolment_status"] ?? '';
                            $statusText = '';
                            $badgeClass = '';
                            
                            if ($status == 'active') {
                                $statusText = 'Enrolled';
                                $badgeClass = 'success';
                            } elseif ($status == 'rejected') {
                                $statusText = 'Rejected';
                                $badgeClass = 'danger';
                            } else {
                                $statusText = 'Pending';
                                $badgeClass = 'secondary';
                            }
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>">
                                <i class="fa-solid fa-circle fa-xs me-1"></i>
                                <?= $statusText ?>
                            </span>
                        </td>
                        <td width="15%">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="/sta.MariaSystem/src/UI-Admin/contents/schoolform10.php?student_id=<?= htmlspecialchars($stu["student_id"]) ?>"
                                   class="btn btn-sm btn-info" title="View SF10 Academic Record">
                                    <i class="fa-solid fa-eye me-1"></i> View
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div id="noResults" class="text-center py-5 d-none">
            <div class="empty-state">
                <i class="fa-solid fa-graduation-cap fa-3x text-muted mb-3"></i>
                <h5>No students found</h5>
                <p class="text-muted">Try adjusting your search</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const studentRows = document.querySelectorAll('.student-row');
    const studentsTableBody = document.getElementById('studentsTableBody');
    const noResultsDiv = document.getElementById('noResults');
    
    // Search and filter functionality
    function filterStudents() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const filterValue = categoryFilter.value.toLowerCase();
        
        let visibleCount = 0;

        studentRows.forEach(row => {
            const lrn = row.getAttribute('data-lrn');
            const name = row.getAttribute('data-name');
            const grade = row.getAttribute('data-grade');
            const sex = row.getAttribute('data-sex');
            const status = row.getAttribute('data-status');
            
            let matchesSearch = true;
            let matchesFilter = true;
            
            // Apply search filter
            if (searchTerm) {
                matchesSearch = lrn.includes(searchTerm) || 
                               name.includes(searchTerm) || 
                               grade.includes(searchTerm) || 
                               sex.includes(searchTerm) || 
                               status.includes(searchTerm);
            }
            
            // Apply grade level filter
            if (filterValue) {
                matchesFilter = grade.includes(filterValue);
            }
            
            // Show/hide row based on filters
            if (matchesSearch && matchesFilter) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0) {
            studentsTableBody.style.display = 'none';
            noResultsDiv.classList.remove('d-none');
        } else {
            studentsTableBody.style.display = '';
            noResultsDiv.classList.add('d-none');
        }
        
        // Update row numbers
        updateRowNumbers();
    }
    
    // Function to update row numbers
    function updateRowNumbers() {
        let counter = 1;
        studentRows.forEach(row => {
            if (row.style.display !== 'none') {
                const firstCell = row.querySelector('td:first-child');
                if (firstCell) {
                    firstCell.textContent = counter++;
                }
            }
        });
    }
    
    // Event listeners
    searchInput.addEventListener('input', filterStudents);
    categoryFilter.addEventListener('change', filterStudents);
    
    // Add Enter key support for search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            filterStudents();
        }
    });
    
    // Add some styling
    searchInput.addEventListener('focus', function() {
        this.parentElement.classList.add('border-primary', 'border-2');
    });
    
    searchInput.addEventListener('blur', function() {
        this.parentElement.classList.remove('border-primary', 'border-2');
    });
    
    categoryFilter.addEventListener('focus', function() {
        this.parentElement.classList.add('border-primary', 'border-2');
    });
    
    categoryFilter.addEventListener('blur', function() {
        this.parentElement.classList.remove('border-primary', 'border-2');
    });
    
    // Initialize
    filterStudents();
});
</script>

<style>
.scroll-sf10 {
    height: 80vh;
    overflow-y: scroll;
    overflow-x: hidden;
}

.table-container-wrapper {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
}

.table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.avatar-placeholder {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.empty-state {
    padding: 3rem 1rem;
}

.empty-state i {
    opacity: 0.5;
}

.badge {
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 600;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.input-group-text {
    border-right: none;
}

#searchInput:focus {
    box-shadow: none;
    border-color: #86b7fe;
}

.btn:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

/* Custom scrollbar for main container */
.scroll-sf10::-webkit-scrollbar {
    width: 8px;
}

.scroll-sf10::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.scroll-sf10::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.scroll-sf10::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>