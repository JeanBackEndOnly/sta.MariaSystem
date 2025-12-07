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

    $stmt = $pdo->prepare("SELECT * FROM sections ORDER BY section_grade_level, section_name LIMIT 20");
            $stmt->execute();
            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = 1;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="mx-2">
        <h4><i class="fa-solid fa-file-alt me-2"></i>SF5 - Report on Promotion and Level of Proficiency</h4>
    </div>
</div>

<div class="row g-3 scroll-sf5">
    <!-- Search and Filters -->
    <div class="row mb-3 justify-content-between align-items-center">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" id="searchInput" name="search" class="form-control"
                    placeholder="Search sections by name or grade level...">
              
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

    <!-- Sections Table -->
    <div class="table-container-wrapper p-0">

        <!-- Fixed Header -->
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover" style="font-size: 0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="40%">Section</th>
                        <th width="30%">Grade Level</th>
                        <th width="25%">Action</th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- Scrollable Body -->
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-sm table-bordered table-hover mb-0 p-0" style="font-size: 0.875rem;">
                <tbody id="sectionsTableBody">
                    <?php foreach($sections as $sec) : ?>
                    <tr class="section-row" 
                        data-name="<?= htmlspecialchars(strtolower($sec["section_name"])) ?>"
                        data-grade="<?= htmlspecialchars(strtolower($sec["section_grade_level"])) ?>">
                        <td width="5%"><?= $count++ ?></td>
                        <td width="40%" class="section-name">
                            <div class="d-flex align-items-center">
                                <div class="avatar-placeholder me-2">
                                    <i class="fa-solid fa-users text-primary"></i>
                                </div>
                                <div>
                                    <strong><?= htmlspecialchars($sec["section_name"]) ?></strong>
                                </div>
                            </div>
                        </td>
                        <td width="30%">
                            <span class="badge bg-info"><?= htmlspecialchars($sec["section_grade_level"]) ?></span>
                        </td>
                        <td width="25%">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="/sta.MariaSystem/src/UI-Admin/contents/schoolform5.php?section_id=<?= htmlspecialchars($sec["section_id"]) ?>&grade=<?= htmlspecialchars($sec["section_grade_level"]) ?>&section=<?= htmlspecialchars($sec["section_name"]) ?>"
                                   class="btn btn-sm btn-info" title="View SF5 Report">
                                    <i class="fa-solid fa-eye me-1"></i> View Report
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
                <i class="fa-solid fa-file-alt fa-3x text-muted mb-3"></i>
                <h5>No sections found</h5>
                <p class="text-muted">Try adjusting your search</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const sectionRows = document.querySelectorAll('.section-row');
    const sectionsTableBody = document.getElementById('sectionsTableBody');
    const noResultsDiv = document.getElementById('noResults');
    
    // Search and filter functionality
    function filterSections() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const filterValue = categoryFilter.value.toLowerCase();
        
        let visibleCount = 0;

        sectionRows.forEach(row => {
            const name = row.getAttribute('data-name');
            const grade = row.getAttribute('data-grade');
            
            let matchesSearch = true;
            let matchesFilter = true;
            
            // Apply search filter
            if (searchTerm) {
                matchesSearch = name.includes(searchTerm) || grade.includes(searchTerm);
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
            sectionsTableBody.style.display = 'none';
            noResultsDiv.classList.remove('d-none');
        } else {
            sectionsTableBody.style.display = '';
            noResultsDiv.classList.add('d-none');
        }
        
        // Update row numbers
        updateRowNumbers();
    }
    
    // Function to update row numbers
    function updateRowNumbers() {
        let counter = 1;
        sectionRows.forEach(row => {
            if (row.style.display !== 'none') {
                const firstCell = row.querySelector('td:first-child');
                if (firstCell) {
                    firstCell.textContent = counter++;
                }
            }
        });
    }
    
    // Event listeners
    searchInput.addEventListener('input', filterSections);
    categoryFilter.addEventListener('change', filterSections);
    
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        categoryFilter.value = '';
        filterSections();
        searchInput.focus();
    });
    
    // Add Enter key support for search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            filterSections();
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
    filterSections();
});
</script>

<style>
.scroll-sf5 {
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

#clearSearchBtn:hover {
    background-color: #e9ecef;
}

.btn:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

/* Custom scrollbar for main container */
.scroll-sf5::-webkit-scrollbar {
    width: 8px;
}

.scroll-sf5::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.scroll-sf5::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.scroll-sf5::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>