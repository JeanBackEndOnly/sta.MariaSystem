<main>
    <section class="">
        <div class="mb-4">
            <div class="mx-2 marginToMedia sideAnimation">
                <h4><i class="fa-solid fa-message me-2"></i>Feedback</h4>
            </div>
        </div>

        <?php
            $stmt = $pdo->prepare("SELECT * FROM feeback");
            $stmt->execute();
            $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="row text-center d-flex align-items-center justify-content-evenly ">
            <div class="row align-items-center justify-content-center gap-4">
                <strong clas="w-100 text-center">Recent Feedbacks</strong>
                <?php foreach($feedbacks as $feedback) : ?>
                    <div class="col-md-5 shadow border rounded-3 p-3 d-flex flex-column" style="height: 150px !important;">
                        <div class="d-flex">
                            <span class="mb-2 w-100 text-start">Title: <strong><?= htmlSpecialChars($feedback["title"]) ?></strong></span>
                        </div>
                        <div class="d-flex">
                            <span class="mb-2 w-100 text-start">Desctiption: <strong><?= htmlSpecialChars($feedback["description"]) ?></strong></span>
                        </div>
                        
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- create feedback modal -->
        <div class="modal fade" id="AddNewAccount" tabindex="-1" aria-labelledby="AddNewAccountLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white mb-4">
                        <h5 class="modal-title text-white" id="AddNewAccountLabel">Create Feedback</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close" onclick="location.reload()"></button>
                    </div>
                    <div class="modal-body">
                        <form class="row g-3" id="feedback-form">
                            <input type="hidden" name="parent_id" value="<?= $user_id ?>">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label">Feedback Title</label>
                                    <input type="text" required class="form-control" name="title" placeholder="Title or concern, ex. cleanliness">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" required id="" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="col-12 text-center mt-3">
                                <button type="submit" class="btn btn-primary px-5">
                                    submit feeback
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>