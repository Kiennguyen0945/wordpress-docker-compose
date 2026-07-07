<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
if (function_exists('wp_body_open')) {
    wp_body_open();
}
?>

<!-- ============ HEADER ============ -->
<header class="site-header">
  <div class="header-row container">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
      <?php echo tlh_logo_svg(); ?>
      <?php bloginfo('name'); ?>
    </a>

    <?php get_template_part('template-parts/header/nav'); ?>
    <?php get_template_part('template-parts/header/actions'); ?>
  </div>
</header>
