<?php
/**
 * Single post template
 *
 * @package ThungLungHoa
 */

get_header();
?>

<div class="container" style="padding:80px 32px; min-height:60vh; max-width:800px;">
  <?php
  while (have_posts()) : the_post();
      the_title('<h1>', '</h1>');
      echo '<div style="color:#8a7f75; font-size:.85rem; margin-bottom:24px;">';
      echo get_the_date();
      echo '</div>';
      the_content();
  endwhile;
  ?>
</div>

<?php
get_footer();
