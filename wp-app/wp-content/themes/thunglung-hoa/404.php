<?php
/**
 * 404 template
 *
 * @package ThungLungHoa
 */

// Check if this is auth page
$slug = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Check if URL contains dang-nhap or dang-ky
if (strpos($request_uri, 'dang-nhap') !== false) {
    get_header();
    ?>
    <div style="min-height: calc(100vh - 140px); display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="width: 100%; max-width: 480px;">
            <?php get_template_part('template-parts/user/login'); ?>
        </div>
    </div>
    <?php
    get_footer();
    return;
}

if (strpos($request_uri, 'dang-ky') !== false) {
    get_header();
    ?>
    <div style="min-height: calc(100vh - 140px); display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="width: 100%; max-width: 480px;">
            <?php get_template_part('template-parts/user/register'); ?>
        </div>
    </div>
    <?php
    get_footer();
    return;
}

// Normal 404
get_header();
?>

<div class="container" style="padding:100px 32px; text-align:center; min-height:60vh;">
  <h1 style="font-size:4rem; color:var(--accent); margin-bottom:20px;">404</h1>
  <p class="lede" style="margin:0 auto 32px;">Trang bạn tìm kiếm không tồn tại hoặc đã bị di chuyển.</p>
  <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-accent btn-lg">Về trang chủ</a>
</div>

<?php
get_footer();
