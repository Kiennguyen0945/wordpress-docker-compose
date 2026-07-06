<?php
/**
 * Front Page — Homepage of Thung Lũng Hoa
 *
 * @package ThungLungHoa
 */

get_header();
?>

<!-- ============ HERO ============ -->
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <span class="eyebrow">Hoa tươi mỗi sớm mai</span>
      <h1>Gửi trọn yêu thương,<br>qua từng <span class="italic">cánh hoa</span></h1>
      <p class="lede">Thung Lũng Hoa tuyển hoa mỗi sáng và cắm theo từng câu chuyện riêng của bạn — từ lời chúc mừng rộn rã đến lời an ủi lặng thầm, đều được gói ghém bằng sự tinh tế.</p>
      <div class="hero-actions">
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="btn btn-accent btn-lg">Đặt hoa ngay</a>
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="btn btn-outline btn-lg">Xem bộ sưu tập</a>
      </div>
      <div class="hero-stats">
        <div class="stat-chip">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.2 2"/></svg>
          <span><span class="stat-num">2 giờ</span><span class="stat-label">giao hoa nội thành</span></span>
        </div>
        <div class="stat-chip">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c3 3.5 6 7 6 10.5A6 6 0 0 1 6 13.5C6 10 9 6.5 12 3Z"/></svg>
          <span><span class="stat-num">Mỗi ngày</span><span class="stat-label">hoa nhập tươi từ vườn</span></span>
        </div>
        <div class="stat-chip">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-7-4.4-9.5-9A5 5 0 0 1 12 6a5 5 0 0 1 9.5 5c-2.5 4.6-9.5 9-9.5 9Z"/></svg>
          <span><span class="stat-num">8.000+</span><span class="stat-label">bó hoa đã gửi trao</span></span>
        </div>
      </div>
    </div>

    <div class="hero-art blob blob-a blob-frame">
      <svg viewBox="0 0 400 480" width="86%" xmlns="http://www.w3.org/2000/svg">
        <path d="M200,458 C172,404 132,344 129,280 C128,266 142,258 162,258 L238,258 C258,258 272,266 271,280 C268,344 228,404 200,458 Z" class="fill-bg" stroke="var(--line)" stroke-width="2"/>
        <path d="M200,270 L200,450" stroke="var(--line)" stroke-width="1.5"/>
        <path d="M150,418 C150,408 250,408 250,418 L250,438 C250,448 150,448 150,438 Z" class="fill-accent"/>
        <path d="M200,415 C185,395 155,392 148,410 C142,425 165,432 200,420 Z" class="fill-accent" opacity="0.92"/>
        <path d="M200,415 C215,395 245,392 252,410 C258,425 235,432 200,420 Z" class="fill-accent" opacity="0.92"/>
        <path d="M196,428 L182,462 L196,455 Z" class="fill-accent"/>
        <path d="M204,428 L218,462 L204,455 Z" class="fill-accent"/>
        <line x1="188" y1="456" x2="188" y2="476" stroke="var(--text)" stroke-width="2" opacity="0.55"/>
        <line x1="200" y1="459" x2="200" y2="480" stroke="var(--text)" stroke-width="2" opacity="0.55"/>
        <line x1="212" y1="456" x2="212" y2="476" stroke="var(--text)" stroke-width="2" opacity="0.55"/>
        <path d="M115,210 C95,205 85,225 95,245 C110,235 118,222 115,210 Z" class="fill-secondary" stroke="var(--text)" stroke-width="1" opacity="0.7"/>
        <path d="M285,215 C305,210 315,230 305,250 C290,240 282,227 285,215 Z" class="fill-secondary" stroke="var(--text)" stroke-width="1" opacity="0.7"/>
        <g transform="translate(200,192) scale(1.35)">
          <g class="fill-primary" opacity="0.92">
            <path transform="rotate(0)"   d="M0,0 C-9,-7 -9,-24 0,-32 C9,-24 9,-7 0,0 Z"/>
            <path transform="rotate(45)"  d="M0,0 C-9,-7 -9,-24 0,-32 C9,-24 9,-7 0,0 Z"/>
            <path transform="rotate(90)"  d="M0,0 C-9,-7 -9,-24 0,-32 C9,-24 9,-7 0,0 Z"/>
            <path transform="rotate(135)" d="M0,0 C-9,-7 -9,-24 0,-32 C9,-24 9,-7 0,0 Z"/>
            <path transform="rotate(180)" d="M0,0 C-9,-7 -9,-24 0,-32 C9,-24 9,-7 0,0 Z"/>
            <path transform="rotate(225)" d="M0,0 C-9,-7 -9,-24 0,-32 C9,-24 9,-7 0,0 Z"/>
            <path transform="rotate(270)" d="M0,0 C-9,-7 -9,-24 0,-32 C9,-24 9,-7 0,0 Z"/>
            <path transform="rotate(315)" d="M0,0 C-9,-7 -9,-24 0,-32 C9,-24 9,-7 0,0 Z"/>
          </g>
          <g class="fill-accent" opacity="0.85">
            <path transform="rotate(22)"  d="M0,0 C-5,-4 -5,-14 0,-18 C5,-14 5,-4 0,0 Z"/>
            <path transform="rotate(94)"  d="M0,0 C-5,-4 -5,-14 0,-18 C5,-14 5,-4 0,0 Z"/>
            <path transform="rotate(166)" d="M0,0 C-5,-4 -5,-14 0,-18 C5,-14 5,-4 0,0 Z"/>
            <path transform="rotate(238)" d="M0,0 C-5,-4 -5,-14 0,-18 C5,-14 5,-4 0,0 Z"/>
            <path transform="rotate(310)" d="M0,0 C-5,-4 -5,-14 0,-18 C5,-14 5,-4 0,0 Z"/>
          </g>
        </g>
        <g transform="translate(122,232) scale(0.85)">
          <g class="fill-accent" opacity="0.5">
            <path transform="rotate(0)"   d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(72)"  d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(144)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(216)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(288)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
          </g>
          <circle r="6" class="fill-primary"/>
        </g>
        <g transform="translate(280,222) scale(0.9)">
          <g class="fill-primary" opacity="0.85">
            <path transform="rotate(15)"  d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(75)"  d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(135)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(195)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(255)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(315)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
          </g>
          <circle r="5.5" class="fill-accent"/>
        </g>
        <g transform="translate(155,130) scale(0.55)">
          <g class="fill-accent" opacity="0.7">
            <path transform="rotate(0)"   d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(72)"  d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(144)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(216)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(288)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
          </g>
        </g>
        <g transform="translate(252,142) scale(0.5)">
          <g class="fill-primary" opacity="0.8">
            <path transform="rotate(20)"   d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(92)"  d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(164)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(236)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            <path transform="rotate(308)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
          </g>
        </g>
      </svg>
    </div>
  </div>
</section>

<?php echo tlh_wave_divider(); ?>

<!-- ============ OCCASION STRIP ============ -->
<section class="occasion-strip">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Dịp lễ sắp tới</span>
      <h2>Chọn hoa cho khoảnh khắc quan trọng</h2>
    </div>
    <div class="occasion-grid">

      <div class="occasion-card">
        <span class="occasion-tag">14/2</span>
        <svg class="occasion-icon" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
          <g transform="translate(30,32) scale(0.85)">
            <g class="fill-primary" opacity="0.9">
              <path transform="rotate(0)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(45)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(90)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(135)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(180)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(225)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(270)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(315)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            </g>
            <circle r="6" class="fill-accent"/>
          </g>
        </svg>
        <h3>Valentine</h3>
        <p>Một bó hồng chỉnh chu, gói trọn lời yêu chưa kịp nói.</p>
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="link-accent">Khám phá →</a>
      </div>

      <div class="occasion-card">
        <span class="occasion-tag">8/3</span>
        <svg class="occasion-icon" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
          <path d="M30,14 C21,14 16,24 16,32 C16,40 22,47 30,58 C38,47 44,40 44,32 C44,24 39,14 30,14 Z" class="fill-primary" opacity="0.9"/>
          <line x1="30" y1="24" x2="30" y2="52" class="stroke-accent" stroke-width="1.3" opacity="0.5"/>
        </svg>
        <h3>Quốc tế Phụ nữ</h3>
        <p>Tulip pastel dịu dàng — món quà tinh tế cho phái đẹp.</p>
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="link-accent">Khám phá →</a>
      </div>

      <div class="occasion-card">
        <span class="occasion-tag">20/11</span>
        <svg class="occasion-icon" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
          <g transform="translate(24,36) scale(0.7)">
            <g class="fill-accent" opacity="0.75">
              <path transform="rotate(0)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(72)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(144)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(216)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(288)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            </g>
          </g>
          <g transform="translate(38,32) scale(0.55)">
            <g class="fill-primary" opacity="0.9">
              <path transform="rotate(20)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(92)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(164)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(236)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
              <path transform="rotate(308)" d="M0,0 C-8,-6 -8,-20 0,-27 C8,-20 8,-6 0,0 Z"/>
            </g>
          </g>
        </svg>
        <h3>Ngày Nhà giáo</h3>
        <p>Bó hoa thanh lịch, gửi lời tri ân đến người đưa đò thầm lặng.</p>
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="link-accent">Khám phá →</a>
      </div>

    </div>
  </div>
</section>

<!-- ============ BEST SELLER ============ -->
<section class="best-seller">
  <div class="container">
    <div class="section-head" style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px;">
      <div>
        <span class="eyebrow">Được yêu thích nhất</span>
        <h2>Best Seller tuần này</h2>
      </div>
      <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="link-accent">Xem tất cả sản phẩm →</a>
    </div>

    <div class="grid-4">
      <?php
      $best_sellers = wc_get_products([
          'meta_key' => 'total_sales',
          'orderby'  => 'meta_value_num',
          'order'    => 'DESC',
          'limit'    => 4,
      ]);

      if (empty($best_sellers)) {
          // Fallback: show latest products
          $best_sellers = wc_get_products(['limit' => 4]);
      }

      foreach ($best_sellers as $product) :
          setup_postdata($product->get_id());
          $pid = $product->get_id();
      ?>
        <a href="<?php echo esc_url(get_permalink($pid)); ?>" class="product-card">
          <div class="product-media blob blob-a blob-frame">
            <?php if ($product->is_on_sale()) : ?>
              <span class="badge">Bán chạy</span>
            <?php endif; ?>
            <span class="wish" aria-label="Yêu thích">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20s-7-4.4-9.5-9A5 5 0 0 1 12 6a5 5 0 0 1 9.5 5c-2.5 4.6-9.5 9-9.5 9Z"/></svg>
            </span>
            <?php echo $product->get_image('woocommerce_thumbnail', ['width' => '100%', 'height' => 'auto', 'style' => 'object-fit:contain;width:100%;height:100%;']); ?>
          </div>
          <div class="product-name"><?php echo esc_html($product->get_name()); ?></div>
          <p style="font-size:.85rem; color:#8a7f75; margin-bottom:8px;"><?php echo wc_get_product_category_list($pid, ', '); ?></p>
          <div class="product-meta">
            <span class="product-price"><?php echo $product->get_price_html(); ?></span>
            <?php if ($rating = $product->get_average_rating()) : ?>
              <span class="stars"><?php echo str_repeat('★', round($rating)) . str_repeat('☆', 5 - round($rating)); ?></span>
            <?php endif; ?>
          </div>
        </a>
      <?php
      endforeach;
      wp_reset_postdata();
      ?>
    </div>
  </div>
</section>

<!-- ============ WHY US ============ -->
<section class="why-us" id="why">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Vì sao chọn chúng tôi</span>
      <h2>Sự khác biệt nằm ở từng chi tiết nhỏ</h2>
    </div>
    <div class="why-grid">
      <div class="why-item">
        <svg class="why-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.2 2"/></svg>
        <h3>Giao nhanh 2 giờ</h3>
        <p>Đặt trước 17h, nhận hoa trong ngày — miễn phí giao trong bán kính 5km nội thành.</p>
      </div>
      <div class="why-item">
        <svg class="why-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c3 3.5 6 7 6 10.5A6 6 0 0 1 6 13.5C6 10 9 6.5 12 3Z"/></svg>
        <h3>Hoa tươi mỗi ngày</h3>
        <p>Hoa được nhập trực tiếp từ vườn mỗi sáng, không lưu kho quá 24 giờ.</p>
      </div>
      <div class="why-item">
        <svg class="why-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 9h18"/><path d="M7 15h4"/></svg>
        <h3>Thiệp & lời nhắn miễn phí</h3>
        <p>Viết trọn tâm tình của bạn lên thiệp đi kèm mỗi bó hoa, không mất thêm phí.</p>
      </div>
      <div class="why-item">
        <svg class="why-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6Z"/><path d="M9 12l2 2 4-4"/></svg>
        <h3>Đảm bảo tươi 5 ngày</h3>
        <p>Đổi mới miễn phí nếu hoa héo trước thời hạn cam kết — không cần hỏi lý do.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ CTA BAND ============ -->
<div class="cta-band">
  <h2>Không biết chọn hoa gì? Để chúng tôi gợi ý theo dịp và ngân sách của bạn.</h2>
  <div>
    <a href="#footer" class="btn btn-accent btn-lg">Nhận tư vấn miễn phí</a>
  </div>
</div>

<?php
get_footer();
