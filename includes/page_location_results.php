<h1>Results Page</h1>

<div class="sidebar-left">

		<div class="sidebar-background">

				<form class="store-finder-form" method="GET">

					<input type="text" id="sc-postcode" name="postcode" placeholder="Your Location" value="<?php echo $_GET['postcode'] ? $_GET['postcode'] : '' ; ?>" />

					<select id="sc-distance" name="distance">

							<option value="1000">Distance</option>

							<option value="5" <?php echo (int)$_GET['distance'] == 5 ? 'selected="selected"' : '' ; ?>>5 Miles</option>

							<option value="10" <?php echo (int)$_GET['distance'] == 10 ? 'selected="selected"' : '' ; ?>>10 Miles</option>

							<option value="20" <?php echo (int)$_GET['distance'] == 20 ? 'selected="selected"' : '' ; ?>>20 Miles</option>

							<option value="50" <?php echo (int)$_GET['distance'] == 50 ? 'selected="selected"' : '' ; ?>>50 Miles</option>

							<option value="100" <?php echo (int)$_GET['distance'] == 100 ? 'selected="selected"' : '' ; ?>>100 Miles</option>

					</select>

					<div class="clear"></div>

				</form>

				<ul id="sc-location-list">

						<li>
							Search for locations...
							<img class="sc-loading-spinner" style="width:50px; position:relative; margin:20px auto; display:block;" src="<?php echo SC_LOCATION_PLUGIN_URL.'/images/loading.gif'; ?>"/>
						</li>

						<script class="sc-location-result-template" type="text/template">
							<li class="sc-location-list-item">
								<strong class="sc-location-list-title"></strong>
								<br />
								<em class="sc-location-list-address"></em>
								<br />
								<em class="sc-location-list-distance"></em>
							</li>
						</script>

				</ul>

		</div>

</div>

<!-- Map -->
	<div class="content-right">

		<div>
			<div id="sc-location-map" style="height:300px;">
				<img class="sc-loading-spinner" style="width:50px; position:relative; margin:20px auto; display:block;" src="<?php echo SC_LOCATION_PLUGIN_URL.'/images/loading.gif'; ?>"/>
			</div>
		</div>

	</div>
<!-- /Map -->


<div class="clear"></div>
