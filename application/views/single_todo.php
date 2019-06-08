<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<section>
	<!-- Form -->
	<div>
		<form method="post" action="<?php echo site_url('app/upd_todo'); ?>">
		<input type="hidden" name="id" value="<?php echo $todo->id; ?>">
			<input type="text" name="todo" value="<?php echo $todo->text; ?>">
			<button type="submit">Update</button>
		</form>
	</div>
</section>