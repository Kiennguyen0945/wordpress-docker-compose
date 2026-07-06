<?php
/**
 * Fallback index template
 *
 * @package ThungLungHoa
 */

get_header();
?>

<div class="container" style="padding:80px 32px; min-height:60vh;">
  <?php
  if (have_posts()) :
      while (have_posts()) : the_post();
          the_title('<h1>', '</h1>');
          the_content();
      endwhile;
      the_posts_pagination();
  else :
      echo '<p>Chưa có nội dung nào.</p>';
  endif;
  ?>
</div>

<?php
get_footer();
