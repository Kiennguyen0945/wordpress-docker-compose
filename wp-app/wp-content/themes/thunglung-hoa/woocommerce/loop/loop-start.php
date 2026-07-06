<?php
/**
 * Product loop start - grid wrapper
 *
 * @package ThungLungHoa
 */

$columns = wc_get_loop_prop('columns');
$grid_class = 'grid-3';
if ($columns === 4) {
    $grid_class = 'grid-4';
} elseif ($columns === 2) {
    $grid_class = 'grid-2';
}
?>
<div class="<?php echo esc_attr($grid_class); ?>">
