<aside class="col-lg-3 order-lg-first">
	<div class="sidebar sidebar-shop">
		<div class="widget widget-clean">
			<label>Filters:</label>
			<?php if($orderby != "" || $order != "" || $min != "" || $max != "" || $cat != "") { ?>
				<a  href="<?php echo BASE_URL.''.$page_name.''.SUFFIX; ?>"  class="">Clean All</a>
			<?php }?>
		</div><!-- End .widget widget-clean -->

		<div class="widget widget-collapsible">
            <?php
                $rightcategoryResult = $db->view("*", "rb_categories", "categoryid", "and status='active'", "order_custom desc", "15");
                if($rightcategoryResult['num_rows'] >= 1)
                {
            ?>
			<h3 class="widget-title">
			    <a data-toggle="collapse" href="#widget-1" role="button" aria-expanded="true" aria-controls="widget-1">
			        Category
			    </a>
			</h3><!-- End .widget-title -->

			<div class="collapse show" id="widget-1">
				<div class="widget-body">
					<div class="filter-items filter-items-count">
						<?php
						foreach($rightcategoryResult['result'] as $rightcategoryRow)
						{
							if($rightcategoryRow['url'] == "#")
							{
								$rightcategory_url = "#";
								$rightcategory_url_target = "";
							}
							else if($rightcategoryRow['url'] != "http://www." and $rightcategoryRow['url'] != "https://www." and $rightcategoryRow['url'] != "")
							{
								if(substr($rightcategoryRow['url'], 0, 7) == 'http://' || substr($rightcategoryRow['url'], 0, 8) == 'https://')
								{
									$rightcategory_url = $validation->db_field_validate($rightcategoryRow['url']);
									$rightcategory_url_target = $validation->db_field_validate($rightcategoryRow['url_target']);
								}
								else
								{
									$rightcategory_url = BASE_URL."".$validation->db_field_validate($rightcategoryRow['url']);
									$rightcategory_url_target = $validation->db_field_validate($rightcategoryRow['url_target']);
								}
							}
							else
							{
								$rightcategory_url = BASE_URL.'products/?cat='.$validation->db_field_validate($rightcategoryRow['title_id'])."";
								$rightcategory_url_target = "";
							}
						?>
						<div class="filter-item">
							<div class="custom-control custom-checkbox">
								
								<a href="<?php echo $rightcategory_url; ?>" target="<?php echo $rightcategory_url_target; ?>" class=""><?php echo $validation->db_field_validate($rightcategoryRow['title']); ?></a>
							</div><!-- End .custom-checkbox -->
							<!-- <span class="item-count">3</span> -->
						</div><!-- End .filter-item -->
						<?php
							}
						?>

					</div><!-- End .filter-items -->
				</div><!-- End .widget-body -->
			</div><!-- End .collapse -->
		</div><!-- End .widget -->
<?php
}
?>
		<div class="widget widget-collapsible">
			<h3 class="widget-title">
			    <a data-toggle="collapse" href="#widget-2" role="button" aria-expanded="true" aria-controls="widget-2">
			        Sort by
			    </a>
			</h3><!-- End .widget-title -->

			<div class="collapse show" id="widget-2">
				<div class="widget-body">
					<div class="filter-items">
						<div class="filter-item">
							<div class="custom-control custom-checkbox">
								<a href="<?php echo BASE_URL.''.$page_name.''.SUFFIX.'?orderby=title&order=asc'.$url_parameters_order; ?>">
									<input type="checkbox" <?php if($orderby == "title" and $order == "asc") echo "checked"; ?> class="custom-control-input" id="size-1">
									<p class="custom-control-label" >A-Z</p>
								</a>
							</div><!-- End .custom-checkbox -->
						</div><!-- End .filter-item -->

						<div class="filter-item">
							<div class="custom-control custom-checkbox">
								<a href="<?php echo BASE_URL.''.$page_name.''.SUFFIX.'?orderby=title&order=desc'.$url_parameters_order; ?>">
									<input type="checkbox" class="custom-control-input" <?php if($orderby == "title" and $order == "desc") echo "checked"; ?> id="size-2">
									<p class="custom-control-label">Z-A</p>
								</a>
							</div><!-- End .custom-checkbox -->
						</div><!-- End .filter-item -->

						<div class="filter-item">
							<div class="custom-control custom-checkbox">
								<a href="<?php echo BASE_URL.''.$page_name.''.SUFFIX.'?orderby=price&order=asc'.$url_parameters_order; ?>" class="anchor-tag fs-black">
									<input type="checkbox" class="custom-control-input" <?php if($orderby == "price" and $order == "asc") echo "checked"; ?> id="size-3">
									<p class="custom-control-label">Price - Low to High</p>
								</a>
							</div><!-- End .custom-checkbox -->
						</div><!-- End .filter-item -->

						<div class="filter-item">
							<div class="custom-control custom-checkbox">
								<a href="<?php echo BASE_URL.''.$page_name.''.SUFFIX.'?orderby=price&order=desc'.$url_parameters_order; ?>">
									<input type="checkbox" class="custom-control-input" <?php if($orderby == "price" and $order == "desc") echo "checked"; ?> id="size-4">
									<p class="custom-control-label" >Price - High to Low</p>
								</a>
							</div><!-- End .custom-checkbox -->
						</div><!-- End .filter-item -->

						<div class="filter-item">
							<div class="custom-control custom-checkbox">
								<a href="<?php echo BASE_URL.''.$page_name.''.SUFFIX.'?orderby=createdate&order=desc'.$url_parameters_order; ?>">
									<input type="checkbox"  <?php if($orderby == "createdate" and $order == "desc") echo "checked"; ?> class="custom-control-input" id="size-5">
									<p class="custom-control-label" >Newest First</p>
								</a>
							</div><!-- End .custom-checkbox -->
						</div><!-- End .filter-item -->

						<div class="filter-item">
							<div class="custom-control custom-checkbox">
								<a href="<?php echo BASE_URL.''.$page_name.''.SUFFIX.'?orderby=views&order=desc'.$url_parameters_order; ?>" class="anchor-tag fs-black">
									<input type="checkbox" <?php if($orderby == "views" and $order == "desc") echo "checked"; ?> class="custom-control-input" id="size-6">
									<p class="custom-control-label">Popularity</p>
								</a>
							</div><!-- End .custom-checkbox -->
						</div><!-- End .filter-item -->
					</div><!-- End .filter-items -->
				</div><!-- End .widget-body -->
			</div><!-- End .collapse -->
		</div><!-- End .widget -->

		<div class="widget widget-collapsible">
			<h3 class="widget-title">
			    <a data-toggle="collapse" href="#widget-5" role="button" aria-expanded="true" aria-controls="widget-5">
			        Price
			    </a>
			</h3><!-- End .widget-title -->

			<div class="collapse show" id="widget-5">
				<div class="widget-body">
                    <div class="filter-price">
                        <div class="filter-price-text">
                            Price Range:
                            <span id="filter-price-range"></span>
                        </div><!-- End .filter-price-text -->

                        <div class="filter_con filter-widget">
							<div class="filter-range-wrap">
								<div class="drag_value  d-flex justify-content-between align-items-center">
									<div class="min_value"><span class="">₹</span> <span class="min_value_class"><?php if($min != "") echo number_format($min); else echo "0"; ?></span></div>
									<div class="max_value"><span class="">₹</span> <span class="max_value_class"><?php if($max != "") echo number_format($max); else echo number_format($max_price); ?></span></div>
								</div>
								<div class="dragable">
									<div class="price-range ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content" data-min="0" data-max="<?php echo $max_price; ?>">
										<div class="ui-slider-range ui-corner-all ui-widget-header"></div>
										<span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
										<span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
									</div>
								</div>
							</div>
						</div><!-- End #price-slider -->
                    </div><!-- End .filter-price -->
				</div><!-- End .widget-body -->
			</div><!-- End .collapse -->
		</div><!-- End .widget -->
	</div><!-- End .sidebar sidebar-shop -->
</aside><!-- End .col-lg-3 -->