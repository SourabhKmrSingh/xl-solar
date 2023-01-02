<?php
include_once("inc_config.php");
include_once("login_user_check.php");

$_SESSION['active_menu'] = "product";

echo $validation->read_permission();

@$orderby = $validation->input_validate($_GET['orderby']);
@$order = $validation->input_validate($_GET['order']);

$where_query = " and stock_quantity <= 5 and productid IN (select productid from rb_products)";

if($orderby != "" and $order != "")
{
	$orderby_final = "{$orderby} {$order}";
	if($orderby == "createdate")
	{
		$orderby_final .= ", createtime {$order}";
	}
}
else
{
	$orderby_final = "stock_quantity asc";
}

$param1 = "title";
$param2 = "order_custom";
$param3 = "createdate";
$param4 = "stock_quantity";
include_once("inc_sorting.php");

$table = "rb_products_variants";
$id = "variantid";
$url_parameters = "";

$data = $pagination->main($table, $url_parameters, $where_query, $id, $orderby_final);

echo $validation->search_filter_enable();
?>
<!DOCTYPE html>
<html LANG="en">
<head>
<?php include_once("inc_title.php"); ?>
<?php include_once("inc_files.php"); ?>
</head>
<body>
<div ID="wrapper">
<?php include_once("inc_header.php"); ?>
<div ID="page-wrapper">
<div CLASS="container-fluid">
<div CLASS="row">
	<div CLASS="col-lg-12">
		<h1 CLASS="page-header">Products Stock Alerts</h1>
	</div>
</div>

<form name="form_actions" method="POST" action="product_actions.php" ENCTYPE="MULTIPART/FORM-DATA">
<div class="table-responsive">
<table class="table table-striped table-view" cellspacing="0" width="100%">
	<thead>
	<tr>
		<th class="<?php echo $th_sort1." ".$th_order_cls1; ?>"><a href="product_stock_view.php?orderby=title&order=<?php echo $th_order1; echo $url_parameters; ?>"><span>Title</span> <span class="sorting-indicator"></span></a></th>
		<th>Variant</th>
		<th>Price</th>
		<th class="<?php echo $th_sort4." ".$th_order_cls4; ?>"><a href="product_stock_view.php?orderby=stock_quantity&order=<?php echo $th_order4.''.$url_parameters; ?>"><span>Stock</span> <span class="sorting-indicator"></span></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($data['num_rows'] >= 1)
	{
		foreach($data['result'] as $variantRow)
		{
			$productid = $variantRow['productid'];
			$productResult = $db->view("title", "rb_products", "productid", "and productid='{$productid}'");
			$productRow = $productResult['result'][0];
		?>
		<tr class="text-center has-row-actions">
			<td data-label="Title - ">
				<a href="product_form.php?mode=edit&productid=<?php echo $validation->db_field_validate($variantRow['productid']); ?>" class="fw-500"><?php echo $validation->db_field_validate($productRow['title']); ?></a>
				
				<div class="row row-actions">
					<div class="col-sm-12">
						<?php if($_SESSION['per_update'] == "1") { ?>
							<a href="product_form.php?mode=edit&productid=<?php echo $validation->db_field_validate($variantRow['productid']); ?>" class="delete">Update</a>
						<?php } ?>
					</div>
				</div>
			</td>
			<td data-label="Variant - "><?php echo $validation->db_field_validate($variantRow['variant']); ?></td>
			<td data-label="Price - "><?php if($variantRow['currency_code'] == 'INR') echo '&#8377;'; else $validation->db_field_validate($variantRow['currency_code']); ?><?php echo $validation->price_format($variantRow['price']); ?></td>
			<td data-label="Stock - "><?php echo $validation->db_field_validate($variantRow['stock_quantity']); ?></td>
		</tr>
		<?php
		}
	}
	else
	{
	?>
		<tr class="text-center">
			<td class="text-center" colspan="4">No Record is Available!</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>
</div>
</form>

<hr />
<?php echo $data['content']; ?>
<hr />
</div>
</div>
</div>
</body>
</html>