<?php
/**
 * Default page template
 *
 * @package ThungLungHoa
 */

get_header();
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
get_footer();
