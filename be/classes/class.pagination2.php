<?php
class pagination2
{
	protected $table;
	protected $url_parameters;
	protected $where_query;
	protected $id;
	protected $limit;
	protected $page;
	protected $pagination;
	protected $content;
	protected $pageQueryResult;
	protected $orderby;
	
	public function main($table, $url_parameters, $where_query, $id, $orderby, $url='', $groupby='')
	{
		global $connect;
		$db = new db;
		$validation = new validation;
		
		$total_pages_row = $db->view($id, $table, $id, $where_query, $orderby, '', $groupby);
		$total_pages = $total_pages_row['num_rows'];
		
		$limit = $validation->urlstring_validate(@$_GET['pagesize']);
		if($limit!='')
		{
			$limit = $limit;
		}
		else
		{
			$limit = 20;
		}
		
		if($url != "")
		{
			$targetpage = $url;
		}
		else
		{
			$targetpage = $_SERVER['PHP_SELF'];
		}
		$targetpage = str_replace(".php","/",$targetpage);
		
		$page = $validation->urlstring_validate(@$_GET['page']);
		if($page)
			$start = ($page - 1) * $limit;
		else
			$start = 0;
		
		$pageQueryResult = $db->view("*", $table, $id, $where_query, $orderby, "{$start}, {$limit}", $groupby);
		$result = $pageQueryResult['result'];
		
		if ($page == 0) $page = 1;
		$prev = $page - 1;
		$next = $page + 1;
		$lastpage = ceil($total_pages/$limit);
		$lpm1 = $lastpage - 1;

		$pagination = "";
		if($lastpage > 1)
		{	
			$pagination .= "<div class=\"pagination\">";
			//previous button
			if ($page > 1)
			{
				$pagination.= "<a href=\"$targetpage?page=$prev$url_parameters\"><i class='fa fa-chevron-left'></i></a>";
			}
			
			//For display 1st page and dots
			if($page != "1" and $page != "2")
			{
				$pagination.= "<a href=\"$targetpage?page=1$url_parameters\">1</a>";
				if($page != "3")
				{
					$pagination.= " ... ";
				}
			}
			
			//For all pages with the help of for loop
			for($counter=1; $counter<=$lastpage;$counter++)
			{
				//For display 3 pages from lastpage when lastpage is active
				if($page == $lastpage)
				{
					if($counter <= ($page - 3) || $counter >= ($page + 2))
					{
						continue;
					}
				}
				//For hide one extra record when 3rd page is active
				else if($page == "3")
				{
					if($counter <= ($page - 2) || $counter >= ($page + 1))
					{
						continue;
					}
				}
				//For hide one page from last 3 pages when last 3rd page is active 
				else if($page == ($lastpage - 2))
				{
					if($counter <= ($page - 1) || $counter >= ($page + 2))
					{
						continue;
					}
				}
				//when all validation done this query runs
				else
				{
					if($counter <= ($page - 2) || $counter >= ($page + 2))
					{
						continue;
					}
				}
				
				//For display active page unique
				if($counter == $page)
				{
					$pagination.= "<span class=\"current\">$counter</span>";
				}
				else
				{
					$pagination.= "<a href=\"$targetpage?page=$counter$url_parameters\">$counter</a>";
				}
			}
			
			//For display lastpage and dots
			if($page != $lastpage and $page != ($lastpage - 1))
			{
				if($page != ($lastpage - 2))
				{
					$pagination.= " ... ";
				}
				$pagination.= "<a href=\"$targetpage?page=$lastpage$url_parameters\">$lastpage</a>";
			}
			
			//next button
			if ($page != $lastpage)
			{
				$pagination.= "<a href=\"$targetpage?page=$next$url_parameters\"><i class='fa fa-chevron-right'></i></a>";
			}
			$pagination.= "</div>\n";
		}
		
		$content = 'Showing  '.$page.' of Total '.$lastpage.' Page(s)';
		
		return array("pagination"=>$pagination, "content"=>$content, "result"=>$result, "num_rows"=>$total_pages);
	}
}

$pagination2 = new pagination2;
?>