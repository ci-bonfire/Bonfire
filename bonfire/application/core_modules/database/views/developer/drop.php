<h2><?php echo lang('db_drop_database_tables'); ?></h2>

<?php echo form_open(SITE_AREA .'/developer/database/drop'); ?>

	<?php if (isset($tables) && is_array($tables) && count($tables) > 0) : ?>
		<?php foreach ($tables as $table) : ?>
			<input type="hidden" name="tables[]" value="<?php echo $table ?>" />
		<?php endforeach; ?>


		<h3><?php echo lang('db_drop_confirm'); ?></h3>

		<ul>
		<?php foreach($tables as $file) : ?>
			<li><?php echo $file ?></li>
		<?php endforeach; ?>
		</ul>

		<div class="notification attention png_bg">
			<?php echo lang('db_drop_attention'); ?>
		</div>

		<div class="actions">
			<button type="submit" name="submit" class="btn btn-danger"><?php echo lang('db_action_delete_tables'); ?></button> <?php echo lang('bf_or'); ?>
			<?php echo anchor(SITE_AREA .'/developer/database', '<i class="icon-refresh icon-white">&nbsp;</i>&nbsp;' . lang('bf_action_cancel'), 'class="btn btn-warning"'); ?>
		</div>

	<?php endif; ?>

<?php echo form_close(); ?>
