<div class="my_meta_control">
 
	<p>Specify details about your artwork here.</p>

	<label>Title</label>
 
	<p>
		<?php $mb->the_field('title'); ?>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
		<span></span> <!--here you can add descriptive text for the field; chose not to-->
	</p>
 
	<label>Dimensions</label>
 
	<p>
		<?php $mb->the_field('dimen'); ?>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
		<span></span> <!--here you can add descriptive text for the field; chose not to-->
	</p>
 
	<label>Collaborators</label>
 
	<p>
		<?php $mb->the_field('collabs'); ?>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
		<span></span> <!--can add text here; chose not to-->
	</p>
 
	<label>Additional info</label>
 
	<p>
		<?php $mb->the_field('additional'); ?>
		<textarea name="<?php $mb->the_name(); ?>" rows="3"><?php $mb->the_value(); ?></textarea>
		<span></span> <!--can add text here; chose not to-->
	</p>

	<p>Note:
	<br />
	All fields are optional and will not display if empty.</a>

</div>