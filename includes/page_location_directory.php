<?php if( $locations = $this->get_locations(true) ) : ?>

	<h1>Directory Page</h1>

	<ul>

		<?php foreach( $locations as $location) : ?>

			<li><?php echo $location->post_title; ?></li>

		<?php endforeach; ?>

	</ul>

<?php endif; ?>