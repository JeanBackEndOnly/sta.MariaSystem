<?php
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
  include 'eror.php';
  exit;
}
$user_id = $_SESSION['user_id'];
/* verify_init($user_id); */
    $query = "SELECT * FROM users 
    WHERE user_id = :user_id;";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':user_id'=>$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="justify-content-between d-flex flex-row col-md-12 col-12  m-0" style="padding: .2rem !important; padding-top: .3rem !important; padding-bottom: .3rem !important; border-bottom: solid 1px rgba(0,0,0,.2);">
  <div class="m-0 p-0 col-md-8 col-10 py-2 d-flex flex-row buttonUpdateProfile">
      <div class="burderDiv" style="display: none;">
        <button class="buttonHide" id="burgerButton"><i class="burger fa-solid fa-bars fs-4 me-3"></i></button>
      </div>
      <div class="row d-flex flex-row col-md-12 col-12 ps-3 transformMedia">
        <img src="../../assets/image/system_logo/ZPPSU.png" alt="" style="width: 70px; height: auto; border-radius: 50%;">
        <h4 class="card-title text-black text-start p-0 m-0 w-75 d-flex align-items-center">TEMPLATE</h4>
      </div>
  </div>
  <div class="justify-content-end paddingMedia transformMedia d-flex col-md-4 col-4 pe-4">
    <div class="h-100 align-items-center registerNoneDes" style="display: flex;">
      <!-- <i class="fas fa-user-shield me-2"></i> -->
      <span><?php echo ucwords(strtolower($result["lastname"]. ', ' . $result["firstname"])); ?></span>
    </div>

    <button type="button" onclick="LogoutButton()" style="border: none; background: none;">
        <i class="fas fa-sign-out-alt text-black ms-1" style="font-size: 17px !important;"></i> 
    </button>
    <div class="logoutDomain transformMedia col-md-3 col-8 h-auto shadow rounded-1 flex-column border" id="logoutDomain" style="display:none; background-color: #fff !important;">
      <div class="header-logout modalBG p-3 d-flex align-items-start justify-content-start w-100 rounded-top">
          <strong class="text-white">Logout Confirmation</strong>
      </div>
      <div class="body-logout py-4 px-4">
        <span class="fw-bold text-muted" style="color: #000 !important;">Are you sure you want to logout?</span>
      </div>
      <div class="footer-logout w-100 d-flex align-items-end justify-content-end gap-3 pb-3 pe-3 pt-2" style="border-top: solid .5px #0e0e0e4f !important;">
        <form action="../../authentication/auth.php" method="post">
          <input type="hidden" name="Logout" value="true">
          <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
          <button class="m-0 btn btn-dark" class="logout" style="cursor: pointer;">
              yes
          </button>
        </form>
        <button class="m-0 btn btn-primary" type="button" onclick="cancel()">Cancel</button>
      </div>
    </div>
  </div>
</div>


<script>
  $(document).ready(function () {
    $('html').css('scroll-behavior', 'smooth');
  });
  function LogoutButton(){
    document.getElementById("logoutDomain").style.display = 'flex';
  }
  function cancel(){
    document.getElementById("logoutDomain").style.display = 'none';
  }
</script>