<main style="width: 82vw !important;">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="mx-2">
            <h4><i class="fa-solid fa-school me-2"></i>School Year Management</h4>
        </div>
    </div>
    <div class="col-md-12 col-12 d-flex justify-content-between mb-2">
        <div class="col-md-4">
            <input type="text" class="form-control" name="search" placeholder="Search....">
        </div>
        <div class="col-md-2">
            <button class="btn btn-danger m-0" data-bs-toggle="modal" data-bs-target="#createSchoolYear"
                id="createSchoolYearBtn">Create School Year</button>
        </div>
    </div>
    <!-- add School Year Modal -->
    <div class="modal fade" id="createSchoolYear" tabindex="-1" aria-labelledby="createSchoolYearLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="createSchoolYearLabel">Create New School Year</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        onclick="location.reload()"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="sy-form" method="post">
                        <div class="my-2">
                            <label class="form-label">School Year Name</label>
                            <input type="text" name="schoolYear_name" class="form-control"
                                placeholder="ex. 2025 - 2026">
                        </div>
                        <div class="my-2">
                            <label class="form-label">School Year Status</label>
                            <select name="status" id="" class="form-select">
                                <option value="">Select Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
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
    <div class="schoolYearDisplays">
        <div class="col-md-12 mt-3">
            <h4><strong>School Year</strong></h4>
        </div>
        <div class="table-container-wrapper">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM school_year ORDER BY created_date DESC");
            $stmt->execute();
            $school_year = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = 1;
        ?>

            <!-- Fixed Header -->
            <div class="table-header">
                <table class="table table-bordered table-sm text-center mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">School Year Name</th>
                            <th width="20%">School Year Status</th>
                            <th width="20%">Created at</th>
                            <th width="25%">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Scrollable Body -->
            <div class="table-body-scroll">
                <table class="table table-bordered table-sm text-center mb-0">
                    <tbody>
                        <?php foreach($school_year as $user) : ?>
                        <tr>
                            <td width="5%"><?= $count++ ?></td>
                            <td width="25%">
                                <?= htmlspecialchars($user["school_year_name"])?>
                            </td>
                            <td width="20%">
                                <span
                                    class="badge bg-<?= ($user["school_year_status"] == 'Active') ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars($user["school_year_status"] ?? 'Inactive') ?>
                                </span>
                            </td>
                            <td width="20%"><?= htmlspecialchars($user["created_date"]) ?></td>
                            <td width="25%">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button" id="activationBtn" data-id="<?= $user["school_year_id"] ?>"
                                        class="btn btn-success btn-sm">Activate</button>
                                    <button type="button" id="deactivationBtn" data-id="<?= $user["school_year_id"] ?>"
                                        class="btn btn-danger btn-sm">Deactivate</button>
                                    <button type="button" data-id="<?= $user["school_year_id"] ?>"
                                        class="btn btn-info btn-sm editSchoolyearBtn">Edit</button>
                                    <button type="button" data-id="<?= $user["school_year_id"] ?>"
                                        class="btn btn-danger btn-sm deleteSchoolyearBtn">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- activate School Year -->
    <div class="modal fade" id="activateSY" tabindex="-1" aria-labelledby="activateSYLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="activateSYLabel">Activation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        onclick="location.reload()"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="activateSY-form" method="post">
                        <input type="hidden" name="school_year_id" id="school_year_id">
                        <span class="m-2">Are you Sure you want to <strong>Activate</strong> this School year?</span>
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary px-5">
                                Activate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="DeactivateSY" tabindex="-1" aria-labelledby="DeactivateSYLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="DeactivateSYLabel">Deactivation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        onclick="location.reload()"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="DeactivateSY-form" method="post">
                        <input type="hidden" name="school_year_id" id="schoolyear_id">
                        <span class="m-2">Are you Sure you want to <strong>Dectivate</strong> this School year?</span>
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary px-5">
                                Deactivate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- school year -->
    <div class="modal fade" id="deleteSchoolYear" tabindex="-1" aria-labelledby="deleteSchoolYearLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="deleteSchoolYearLabel">Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        onclick="location.reload()"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="deleteSchoolyear-form" method="post">
                        <input type="hidden" name="school_year_id" id="school_year_id_delete">
                        <span class="m-2">Are you Sure you want to <strong>Delete</strong> this School Year?</span>
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
    <div class="modal fade" id="editSchoolyear" tabindex="-1" aria-labelledby="editSchoolyearLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="editSchoolyearLabel">Deactivation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        onclick="location.reload()"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="editSchoolyear-form" method="post">
                        <input type="hidden" name="school_year_id" id="school_year_id_edit">
                        <div class="my-2">
                            <label class="form-label">Section Status</label>
                            <select name="school_year_status" id="school_year_status" class="form-control">
                                <option value="">Select school year status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="my-2">
                            <label class="form-label">School Year Name</label>
                            <input type="text" id="school_year_name" name="school_year_name" class="form-control"
                                placeholder="ex. Jupiter">
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