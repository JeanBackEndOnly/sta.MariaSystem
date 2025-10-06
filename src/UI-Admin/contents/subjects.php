<main style="width: 82vw !important;">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="mx-2">
            <h4><i class="fa-solid fa-school me-2"></i>Subjects Management</h4>
        </div>
    </div>
    <div class="col-md-12 col-12 d-flex justify-content-between mb-2">
        <div class="col-md-4">
            <input type="text" class="form-control" name="search" placeholder="Search....">
        </div>
        <div class="col-md-2">
            <button class="btn btn-danger m-0" data-bs-toggle="modal" data-bs-target="#createSubjects"
                id="createSubjectsBtn">Create Subjects</button>
        </div>
    </div>
    <!-- add Subjects -->
    <div class="modal fade" id="createSubjects" tabindex="-1" aria-labelledby="createSubjectsLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="createSubjectsLabel">Create New Subject</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        onclick="location.reload()"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="subjects-form" method="post">
                        <div class="my-2">
                            <label class="form-label">Subject Name</label>
                            <input type="text" name="subject_name" class="form-control" placeholder="ex. Mathematics">
                        </div>
                        <div class="my-2">
                            <label class="form-label">Subject Code</label>
                            <input type="text" name="subject_code" class="form-control" placeholder="ex. Math">
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
                            <label class="form-label">Subject Units</label>
                            <input type="text" name="subject_units" class="form-control" placeholder="ex. 6">
                        </div>
                        <div class="my-2">
                            <label class="form-label">Subject Status</label>
                            <select name="subjects_status" id="" class="form-select">
                                <option value="">Select Status</option>
                                <option value="Available">Available</option>
                                <option value="Unavailable">Unavailable</option>
                            </select>
                        </div>
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary px-5">
                                Create S.Y
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="subjectsDisplays">
        <div class="col-md-12 mt-3">
            <h4><strong>Subjects</strong></h4>
        </div>
        <div class="table-container-wrapper">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM subjects ORDER BY created_date DESC");
            $stmt->execute();
            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = 1;
        ?>

            <!-- Fixed Header -->
            <div class="table-header">
                <table class="table table-bordered table-sm text-center mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Subject Name</th>
                            <th width="15%">Subject Units</th>
                            <th width="15%">Subject Status</th>
                            <th width="15%">Grade Level</th>
                            <th width="15%">Created </th>
                            <th width="15%">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Scrollable Body -->
            <div class="table-body-scroll">
                <table class="table table-bordered table-sm text-center mb-0">
                    <tbody>
                        <?php foreach($subjects as $subject) : ?>
                        <tr>
                            <td width="5%"><?= $count++ ?></td>
                            <td width="15%">
                                <?= htmlspecialchars($subject["subject_name"])?>
                            </td>
                            <td width="15%">
                                <?= htmlspecialchars($subject["subject_units"])?>
                            </td>
                            <td width="15%">
                                <span
                                    class="badge bg-<?= ($subject["subjects_status"] == 'Available') ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars($subject["subjects_status"] ?? 'Inactive') ?>
                                </span>
                            </td>
                            <td width="15%">
                                <?= htmlspecialchars($subject["grade_level"])?>
                            </td>
                            <td width="15%"><?= htmlspecialchars($subject["created_date"]) ?></td>
                            <td width="15%">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button" data-id="<?= $subject["subject_id"] ?>"
                                        class="btn btn-info btn-sm editSubjectBtn">Edit</button>
                                    <button type="button" data-id="<?= $subject["subject_id"] ?>"
                                        class="btn btn-danger btn-sm deleteSubjectBtn">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- subjects -->
    <div class="modal fade" id="deleteSubject" tabindex="-1" aria-labelledby="deleteSubjectLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="deleteSubjectLabel">Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        onclick="location.reload()"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="deleteSubject-form" method="post">
                        <input type="hidden" name="subject_id" id="subject_id_delete">
                        <span class="m-2">Are you Sure you want to <strong>Delete</strong> this Subject?</span>
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
    <div class="modal fade" id="editSubjects" tabindex="-1" aria-labelledby="editSubjectsLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="editSubjectsLabel">Deactivation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        onclick="location.reload()"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="editSubjects-form" method="post">
                        <input type="hidden" name="subject_id" id="subject_id_edit">
                        <div class="my-2">
                            <label class="form-label">Subject Name</label>
                            <input type="text" id="subject_name" name="subject_name" class="form-control"
                                placeholder="ex. Jupiter">
                        </div>
                        <div class="my-2">
                            <label class="form-label">Subject Code</label>
                            <input type="text" id="subject_code" name="subject_code" class="form-control"
                                placeholder="ex. Jupiter">
                        </div>
                        <div class="my-2">
                            <label class="form-label">Grade Level</label>
                            <select name="grade_level" id="grade_level" class="form-select">
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
                            <label class="form-label">Subject Units</label>
                            <input type="text" id="subject_units" name="subject_units" class="form-control"
                                placeholder="ex. Jupiter">
                        </div>
                        <div class="my-2">
                            <label class="form-label">Subject status</label>
                            <select name="subjects_status" id="subjects_status" class="form-control">
                                <option value="">Select Subject status</option>
                                <option value="Available">Available</option>
                                <option value="Unavailable">Unavailable</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary px-5">
                            edit
                        </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</main>
<script>
// Delete Subject Modal
document.querySelectorAll('.deleteSubjectBtn').forEach(button => {
    button.addEventListener('click', function() {
        const subjectId = this.getAttribute('data-id');
        document.getElementById('subject_id_delete').value = subjectId;
        
        // Show the delete modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteSubject'));
        deleteModal.show();
    });
});

// Edit Subject Modal
document.querySelectorAll('.editSubjectBtn').forEach(button => {
    button.addEventListener('click', function() {
        const subjectId = this.getAttribute('data-id');
        document.getElementById('subject_id_edit').value = subjectId;
        
        // You would typically fetch the subject data here and populate the form
        // For now, just show the modal
        const editModal = new bootstrap.Modal(document.getElementById('editSubjects'));
        editModal.show();
    });
});
</script>