<?php
/**
 * Default page template
 *
 * @package ThungLungHoa
 */

get_header();
?>

<!-- DEBUG: Page template loaded -->
<?php
$post = get_post();
$slug = $post->post_name;

// Log for debugging
error_log('PAGE DEBUG: slug=' . $slug);

if ($slug === 'dang-nhap') {
    error_log('PAGE DEBUG: rendering login form');
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
          get_template_part('template-parts/user/login');
      }
      ?>
    </div>
    <?php
} elseif ($slug === 'dang-ky') {
    error_log('PAGE DEBUG: rendering register form');
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
} else {
    error_log('PAGE DEBUG: rendering normal page content');
    ?>
    <div class="container" style="padding:80px 32px; min-height:60vh;">
      <?php
      while (have_posts()) : the_post();
          the_title('<h1>', '</h1>');
          the_content();
      endwhile;
      ?>
    </div>
    <?php
}
?>

<?php
get_footer();
