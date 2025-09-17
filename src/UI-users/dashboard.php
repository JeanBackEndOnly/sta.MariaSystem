<body>
    <section class="">
        <div class="mb-4">
            <div class="mx-2 marginToMedia sideAnimation">
                <h4><i class="fa fa-tv mx-2"></i>Student Dashboard</h4>
                <small class="text-muted">Knowledge is power</small>
            </div>
        </div>

        <!-- Grid Row -->
        <div class="row text-center d-flex align-items-center justify-content-evenly  ">

            <div class="booksAvailable sideAnimation d-flex flex-row col-md-3 col-11 rounded-2 shadow p-0 m-2">
                <div class="icon h-100 d-flex align-items-center justify-content-evenly col-md-3 col-3">
                    <i
                        class="fa-solid fa-book-open-reader m-0 p-0 fs-1"><?php echo get_book_by('COUNT(*)', 'lost_by', $user_id); ?></i>
                </div>
                <div class="description col-md-9 col-8 d-flex flex-column mt-0 align-items-center ">
                    <h5 class="desDashboard text-ccenter mb-0 w-100">BOOKS AVAILABLE</h5>
                    <h2 class="h2Dashboard"><?php echo get_book_by("COUNT(*)", "borrowed_by", null, true);
                                       ?></h2>
                </div>
            </div>

            <div class="booksAvailable sideAnimation d-flex flex-row col-md-3 col-11 rounded-2 shadow px-0 m-2"
                data-bs-toggle="modal" data-bs-target="#borrowModal">
                <div class="icon h-100 d-flex align-items-center justify-content-center col-md-3 col-3">
                    <i class="fa-solid fa-book-open-reader m-0 p-0 fs-1"></i>
                </div>
                <div class="description col-md-9 col-8 d-flex flex-column mt-0">
                    <h5 class="desDashboard text-ccenter mb-0 w-100">BORROWED BOOKS</h5>
                    <h2 class="h2Dashboard"><?php echo get_book_by('COUNT(*)', 'borrowed_by', $user_id); ?></h2>
                </div>
            </div>

            <div class="booksAvailable sideAnimation d-flex flex-row col-md-3 col-11 rounded-2 shadow p-0 m-2"
                data-bs-toggle="modal" data-bs-target="#retainedModal">
                <div class="icon h-100 d-flex align-items-center justify-content-center col-md-3 col-3">
                    <i class="fa-solid fa-book-open-reader m-0 p-0 fs-1"></i>
                </div>
                <div class="description col-md-9 col-8 d-flex flex-column mt-0">
                    <h5 class="desDashboard text-ccenter mb-0 w-100">RETAINED BOOKS</h5>
                    <h2 class="h2Dashboard"><?php echo get_book_by('COUNT(*)', 'lost_by', $user_id); ?></h2>
                </div>
            </div>

            <!-- ================================== borrowed book s modal ====================================== -->
           <!--  <div class="modal fade" id="borrowModal" tabindex="-1" aria-labelledby="borrowModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <?php 
                     $query = "SELECT * FROM books_data 
                     WHERE borrowed_by = :borrowed_by";
                     $stmt = $pdo->prepare($query);
                     $stmt->bindParam(':borrowed_by', $user_id);
                     $stmt->execute();
                     $booksBorrowed = $stmt->fetchAll(PDO::FETCH_ASSOC);
                  ?>
                        <div class="modal-header modalBG">
                            <h5 class="modal-title font-color-white" id="borrowModalLabel">BORROWED BOOK DETAILS</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="booksDiv w-100 flex-wrap d-flex justify-content-center modalHomeHeight">
                        <?php foreach($booksBorrowed as $books) : ?>
                        
                            <div class="bookImage sideAnimation  flex-column rounded-2 shadow p-2 m-2 col-md-3 col-5 heightMedia"
                                style="height: 35vh;">
                                <div class="imgBooksHome w-100">
                                    <img src="../../authentication/<?= $books["book_images"] ?>" class="my-2 mediaImg"
                                        style="height: 150px; width: 150px;" alt="hehe">
                                </div>
                                <label for=""><?= $books["book_title"] ?></label>
                                <label for=""><?= $books["book_status"] ?></label>
                            </div>
                        
                        <?php endforeach; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Exit</button>
                        </div>

                    </div>
                </div>
            </div> -->
            <!-- =================================================== RETAINED BOOKS MODAL ===================================== -->
            <!-- <div class="modal fade" id="retainedModal" tabindex="-1" aria-labelledby="borrowModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content ">

                        <div class="modal-header modalBG">
                            <h5 class="modal-title font-color-white" id="borrowModalLabel">RETAINED BOOK DETAILS</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="booksDiv w-100 flex-wrap d-flex flex-row justify-content-center modalHomeHeight">

                            <div class="bookImage d-flex flex-column rounded-2 shadow p-2 m-2 col-md-5 col-5 heightMedia"
                                style="height: 35vh;">
                                <div class="imgBooksHome d-flex justify-content-center align-items-center w-100">
                                    <img src="../../assets/image/heheBook.jpg" class="my-2 mediaImg"
                                        style="height: 150px; width: 150px;" alt="hehe">
                                </div>
                                <label for="">NOLI ME TANGERE</label>
                            </div>
                            <div class="bookImage d-flex flex-column rounded-2 shadow p-2 m-2 col-md-5 col-5"
                                style="height: 35vh;">
                                <div class="imgBooksHome d-flex justify-content-center align-items-center w-100">
                                    <img src="../../assets/image/heheBook.jpg" class="my-2"
                                        style="height: 150px; width: 150px;" alt="hehe">
                                </div>
                                <label for="">NOLI ME TANGERE</label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Exit</button>
                        </div>

                    </div>
                </div>
            </div> -->

        </div>
    </section>
</body>