<?php
class pagination
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
	
	public function main($table, $url_parameters, $where_query, $id, $orderby, $groupby='')
	{
		global $connect;
		$db = new db;
		$validation = new validation;
		
		$this->table = $table;
		$this->url_parameters = $url_parameters;
		$this->where_query = $where_query;
		$this->id = $id;
		$this->orderby = $orderby;
		
		$this->adjacents = 2;
		
		$total_pages_row = $db->view($this->id, $this->table, $this->id, $this->where_query, $this->orderby, '', $groupby);
		$this->total_pages = $total_pages_row['num_rows'];
		
		$configQueryResult = $db->view('records_perpage', 'rb_config', 'configid', "", "configid desc");
		$configRow = $configQueryResult['result'][0];
		
		$this->limit = $validation->urlstring_validate(@$_GET['pagesize']);
		if($this->limit!='')
		{
			$this->limit = $this->limit;
		}
		else
		{
			$this->limit = $configRow['records_perpage'];
		}

		$this->targetpage = $_SERVER['PHP_SELF'];
		
		$this->page = $validation->urlstring_validate(@$_GET['page']);
		if($this->page)
			$this->start = ($this->page - 1) * $this->limit;
		else
			$this->start = 0;
		
		$pageQueryResult = $db->view("*", $this->table, $this->id, $this->where_query, $this->orderby, "{$this->start}, {$this->limit}", $groupby);
		$result = $pageQueryResult['result'];
		
		if ($this->page == 0) $this->page = 1;
		$this->prev = $this->page - 1;
		$this->next = $this->page + 1;
		$this->lastpage = ceil($this->total_pages/$this->limit);
		$this->lpm1 = $this->lastpage - 1;

		$this->pagination = "";
		if($this->lastpage > 1)
		{
			$this->pagination .= "<div class=\"pagination\">";
			if ($this->page > 1)
				$this->pagination.= "<a href=\"$this->targetpage?page=$this->prev&pagesize=$this->limit$this->url_parameters\"><i class='fa fa-chevron-left'></i></a>";
			else
				$this->pagination.= "<span class=\"disabled\">previous</span>";

			if ($this->lastpage < 7 + ($this->adjacents * 2))
			{
				for ($this->counter = 1; $this->counter <= $this->lastpage; $this->counter++)
				{
					if ($this->counter == $this->page)
						$this->pagination.= "<span class=\"current\">$this->counter</span>";
					else
						$this->pagination.= "<a href=\"$this->targetpage?page=$this->counter&pagesize=$this->limit$this->url_parameters\">$this->counter</a>";
				}
			}
			elseif($this->lastpage > 5 + ($this->adjacents * 2))
			{
				if($this->page < 1 + ($this->adjacents * 2))		
				{
					for ($this->counter = 1; $this->counter < 4 + ($this->adjacents * 2); $this->counter++)
					{
						if ($this->counter == $this->page)
							$this->pagination.= "<span class=\"current\">$this->counter</span>";
						else
							$this->pagination.= "<a href=\"$this->targetpage?page=$this->counter&pagesize=$this->limit$this->url_parameters\">$this->counter</a>";
					}
					$this->pagination.= "...";
					$this->pagination.= "<a href=\"$this->targetpage?page=$this->lpm1&pagesize=$this->limit$this->url_parameters\">$this->lpm1</a>";
					$this->pagination.= "<a href=\"$this->targetpage?page=$this->lastpage&pagesize=$this->limit$this->url_parameters\">$this->lastpage</a>";
				}
				elseif($this->lastpage - ($this->adjacents * 2) > $this->page && $this->page > ($this->adjacents * 2))
				{
					$this->pagination.= "<a href=\"$this->targetpage?page=1\">1</a>";
					$this->pagination.= "<a href=\"$this->targetpage?page=2\">2</a>";
					$this->pagination.= "...";
					for ($this->counter = $this->page - $this->adjacents; $this->counter <= $this->page + $this->adjacents; $this->counter++)
					{
						if ($this->counter == $this->page)
							$this->pagination.= "<span class=\"current\">$this->counter</span>";
						else
							$this->pagination.= "<a href=\"$this->targetpage?page=$this->counter&pagesize=$this->limit$this->url_parameters\">$this->counter</a>";		
					}
					$this->pagination.= "...";

					$this->pagination.= "<a href=\"$this->targetpage?page=$this->lpm1&pagesize=$this->limit$this->url_parameters\">$this->lpm1</a>";
					$this->pagination.= "<a href=\"$this->targetpage?page=$this->lastpage&pagesize=$this->limit$this->url_parameters\">$this->lastpage</a>";
				}
				else
				{
					$this->pagination.= "<a href=\"$this->targetpage?page=1&pagesize=$this->limit$this->url_parameters\">1</a>";
					$this->pagination.= "<a href=\"$this->targetpage?page=2&pagesize=$this->limit$this->url_parameters\">2</a>";
					$this->pagination.= "...";
					for ($this->counter = $this->lastpage - (2 + ($this->adjacents * 2)); $this->counter <= $this->lastpage; $this->counter++)
					{
						if ($this->counter == $this->page)
							$this->pagination.= "<span class=\"current\">$this->counter</span>";
						else
							$this->pagination.= "<a href=\"$this->targetpage?page=$this->counter&pagesize=$this->limit$this->url_parameters\">$this->counter</a>";
					}
				}
			}
			
			if ($this->page < $this->counter - 1)
					$this->pagination.= "<a href=\"$this->targetpage?page=$this->next&pagesize=$this->limit$this->url_parameters\"><i class='fa fa-chevron-right'></i></a>";
			else
				$this->pagination.= "<span class=\"disabled\">next</span>";
			$this->pagination.= "</div>\n";
		}
		
		$this->content = '<div class="row fs-13">
			<div class="col-sm-12 mb-3 mb-md-3">
				<div class="row">
					<div class="col-sm-6 mb-0 mb-md-0">Total <strong class="pagination_design">'.$this->total_pages.'</strong> Record(s) Found</div>
					<div class="col-sm-6 mb-1 mb-md-0 text-sm-right d-none d-sm-block">Showing <strong class="pagination_design">'.$this->page.'</strong> of Total <strong  class="pagination_design">'.$this->lastpage.'</strong> Page(s)</div>
				</div>
			</div>
			<div class="col-sm-12">
				<div class="row">
					<div class="col-sm-6 d-none d-sm-block">
						<div class="d-inline-block">
							Records per Page: &nbsp;&nbsp;
						</div>
						<div class="d-inline-block">
							<select name="pagesize" id="pagesize" class="form-control" onChange="gotoURL(this.value);" >
								<option VALUE="'.$this->targetpage.'?pagesize=50'.$this->url_parameters.'" '. ($this->limit==50 ? 'selected' : '') .'>50</option>
								<option VALUE="'.$this->targetpage.'?pagesize=100'.$this->url_parameters.'" '.($this->limit==100 ? 'selected' : '') .'>100</option>
								<option VALUE="'.$this->targetpage.'?pagesize=200'.$this->url_parameters.'" '.($this->limit==200 ? 'selected' : '') .'>200</option>
								<option VALUE="'.$this->targetpage.'?pagesize=500'.$this->url_parameters.'" '.($this->limit==500 ? 'selected' : '') .'>500</option>
								<option VALUE="'.$this->targetpage.'?pagesize=1000'.$this->url_parameters.'" '.($this->limit==1000 ? 'selected' : '') .'>1000</option>
							</select>
						</div>
					</div>
					<div class="col-sm-6 mb-1 mb-md-0">
						<div class="float-sm-right">'.$this->pagination.'</div>
					</div>
				</div>
			</div>
		</div>';
		
		return array("content"=>$this->content, "result"=>$result, "num_rows"=>$this->total_pages);
	}
}

$pagination = new pagination;
?>