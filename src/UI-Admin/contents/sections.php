<main style="width: 82vw !important;">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="mx-2">
            <h4><i class="fa-solid fa-school me-2"></i>Sections Management</h4>
        </div>
    </div>
    <div class="col-md-12 col-12 d-flex justify-content-between mb-2">
        <div class="col-md-4">
            <input type="text" class="form-control" name="search" placeholder="Search....">
        </div>
        <div class="col-md-2">
            <button class="btn btn-danger m-0 w-100" data-bs-toggle="modal" data-bs-target="#createSection"
                id="createSectionBtn">+ Create Section</button>
        </div>
    </div>
    <div class="modal fade" id="createSection" tabindex="-1" aria-labelledby="createSectionLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="createSectionLabel">Create New Secton</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        onclick="location.reload()"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="section-form" method="post">
                        <div class="my-2">
                            <label class="form-label">Secton Name</label>
                            <input type="text" name="section_name" class="form-control" placeholder="ex. Jupiter">
                        </div>
                        <div class="my-2">
                            <label class="form-label">Grade Level</label>
                            <select name="grade_level" id="" class="form-select">
                                <option value="">Select Grade Level</option>
                                <option value="Grade 1">Grade 1</option>
                                <option value="Grade 2">Grade 2</option>
                                <option value="Grade 3">Grade 3</option>
                                <option value="Grade 4">Grade 4</option>
                                <option value="Grade 5">Grade 5</option>
                                <option value="Grade 6">Grade 6</option>
                            </select>
                        </div>
                        <div class="my-2">
                            <label class="form-label">Secton </label>
                            <input readonly type="text" name="section_status" value="Available" class="form-control">
                        </div>
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary px-5">
                                Create Section
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="sectionsDisplays mt-3">
        <div class="table-container-wrapper">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM sections ORDER BY created_date DESC");
            $stmt->execute();
            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = 1;
        ?>

            <!-- Fixed Header -->
            <div class="table-responsive-lg modern-table">
                <table class="table table-hover table-bordered align-middle text-center text-dark">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">Section Name</th>
                            <th width="15%">Grade Level</th>
                            <th width="15%">Section Status</th>
                            <th width="20%">Created at</th>
                            <th width="25%">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Scrollable Body -->
            <div class="table-responsive-lg modern-table">
                <table class="table table-hover table-bordered align-middle text-center text-dark">
                    <tbody>
                        <?php foreach($sections as $user) : ?>
                        <tr>
                            <td width="5%"><?= $count++ ?></td>
                            <td width="20%">
                                <?= htmlspecialchars($user["section_name"])?>
                            </td>
                            <td width="15%"><?= htmlspecialchars($user["section_grade_level"]) ?></td>
                            <td width="15%">
                                <span
                                    class="badge bg-<?= ($user["section_status"] == 'Available') ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars($user["section_status"] ?? 'Inactive') ?>
                                </span>
                            </td>
                            <td width="20%"><?= htmlspecialchars($user["created_date"]) ?></td>
                            <td width="25%">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button" data-id="<?= $user["section_id"]?>"
                                        class="btn btn-info btn-sm editSectionBtn">Edit</button>
                                    <button type="button" data-id="<?= $user["section_id"]?>"
                                        class="btn btn-danger btn-sm deleteSectionBtn">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- sections -->
    <div class="modal fade" id="deleteSection" tabindex="-1" aria-labelledby="deleteSectionLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="deleteSectionLabel">Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        onclick="location.reload()"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="deleteSection-form" method="post">
                        <input type="hidden" name="section_id" id="section_id">
                        <span class="m-2">Are you Sure you want to <strong>Delete</strong> this Section?</span>
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary px-5">
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editSections" tabindex="-1" aria-labelledby="editSectionsLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="editSectionsLabel">Update Section</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        onclick="location.reload()"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="editSection-form" method="post">
                        <input type="hidden" name="section_id" id="section_ids">
                        <div class="my-2">
                            <label class="form-label">Section Status</label>
                            <select name="section_status" id="section_status" class="form-control">
                                <option value="">Select room status</option>
                                <option value="Available">Available</option>
                                <option value="Inavailable">Unavailable</option>
                            </select>
                        </div>
                        <div class="my-2">
                            <label class="form-label">Section Name</label>
                            <input type="text" id="section_name" name="section_name" class="form-control"
                                placeholder="ex. Jupiter">
                        </div>
                        <div class="my-2">
                            <label class="form-label">Grade Level</label>
                            <select name="section_grade_level" id="section_grade_level" class="form-select">
                                <option value="">Select Grade Level</option>
                                <option value="Grade 1">Grade 1</option>
                                <option value="Grade 2">Grade 2</option>
                                <option value="Grade 3">Grade 3</option>
                                <option value="Grade 4">Grade 4</option>
                                <option value="Grade 5">Grade 5</option>
                                <option value="Grade 6">Grade 6</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary px-5">
                            Update Section
                        </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</main>
<script>
    // Simplified Search for Sections Table
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    const tableBody = document.querySelector('table tbody');
    
    if (!searchInput || !tableBody) return;
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const rows = tableBody.querySelectorAll('tr');
        
        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(searchTerm) ? '' : 'none';
        });
    });
});
</script>