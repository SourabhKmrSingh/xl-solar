<?php
if($orderby == $param1)
{
	if($order == "asc")
	{
		$th_order1 = "desc";
		$th_order_cls1 = "asc";
	}
	else
	{
		$th_order1 = "asc";
		$th_order_cls1 = "desc";
	}
	$th_sort1 = "sorted";
}
else
{
	$th_order1 = "asc";
	$th_order_cls1 = "desc";
	$th_sort1 = "sortable";
}

if($orderby == $param2)
{
	if($order == "asc")
	{
		$th_order2 = "desc";
		$th_order_cls2 = "asc";
	}
	else
	{
		$th_order2 = "asc";
		$th_order_cls2 = "desc";
	}
	$th_sort2 = "sorted";
}
else
{
	$th_order2 = "asc";
	$th_order_cls2 = "desc";
	$th_sort2 = "sortable";
}

if($orderby == $param3)
{
	if($order == "asc")
	{
		$th_order3 = "desc";
		$th_order_cls3 = "asc";
	}
	else
	{
		$th_order3 = "asc";
		$th_order_cls3 = "desc";
	}
	$th_sort3 = "sorted";
}
else
{
	$th_order3 = "asc";
	$th_order_cls3 = "desc";
	$th_sort3 = "sortable";
}

if($orderby == $param4)
{
	if($order == "asc")
	{
		$th_order4 = "desc";
		$th_order_cls4 = "asc";
	}
	else
	{
		$th_order4 = "asc";
		$th_order_cls4 = "desc";
	}
	$th_sort4 = "sorted";
}
else
{
	$th_order4 = "asc";
	$th_order_cls4 = "desc";
	$th_sort4 = "sortable";
}
?>