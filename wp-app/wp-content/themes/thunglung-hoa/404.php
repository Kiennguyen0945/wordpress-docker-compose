<?php
/**
 * 404 template
 *
 * @package ThungLungHoa
 */

get_header();
?>

<div class="container" style="padding:100px 32px; text-align:center; min-height:60vh;">
  <h1 style="font-size:4rem; color:var(--accent); margin-bottom:20px;">404</h1>
  <p class="lede" style="margin:0 auto 32px;">Trang bạn tìm kiếm không tồn tại hoặc đã bị di chuyển.</p>
  <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-accent btn-lg">Về trang chủ</a>
</div>

<?php
get_footer();
