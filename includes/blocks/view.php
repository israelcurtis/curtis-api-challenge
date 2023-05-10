<?php
/**
 * Renders block output on front-end
 *
 * @since 1.0.0
 *
 * @var $attributes array is already defined and populated with the saved block attributes
 */

$table = new \Curtis\Table( $attributes );
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php $table->renderTable(); ?>
</div>
