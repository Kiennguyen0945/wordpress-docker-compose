<?php
/**
 * Register page template
 *
 * @package ThungLungHoa
 */

get_header();
?>

<div style="min-height:calc(100vh - 140px); display:flex; align-items:center; justify-content:center; padding:40px 20px; width:100%;">
  <?php
  if (function_exists('tlh_is_customer_logged_in') && tlh_is_customer_logged_in()) {
      ?>
      <div class="user-form-wrapper" style="text-align:center;">
        <h2>Bạn đã đăng nhập</h2>
        <p>Chuyển hướng đến <a href="<?php echo esc_url(home_url('/ho-so')); ?>">hồ sơ cá nhân</a></p>
        <script>
          setTimeout(() => {
            window.location.href = '<?php echo esc_js(home_url('/ho-so')); ?>';
          }, 2000);
        </script>
      </div>
      <?php
  } else {
      get_template_part('template-parts/user/register');
  }
  ?>
</div>

<?php
get_footer();
