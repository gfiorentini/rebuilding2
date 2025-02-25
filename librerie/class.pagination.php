<?php

class Pagination {
	 
	 /*
		$paging=new Pagination($records_count, 100, $page, 10);
		$sqlEnte=$mySicurezza->get_sql();			
	 */
	 
	public $pages_count;			//totale pagine da visualizzare
	public $record_each_page;		//numero di record da visualizzare ogni pagina
	
	public $page;					//numero pagina attuale
	public $start_page;				//numero prima pagina da visualizzare
	public $end_page;				//numero ultima pagina da visualizzare
	public $pages_element_count;	//numero di tab da visualizzare
	public $number_of_pages;

	
	public function __construct($records_count, $record_each_page, $page, $pages_element_count) {

		$this->pages_element_count=$pages_element_count;
		$this->record_each_page=$record_each_page;
		
		if(empty($page))
			$page=1;
		$this->page=$page;
		
		$this->pages_count = ceil($records_count / $this->record_each_page);	//10
		
		$this->start_page = $this->page - ($this->pages_element_count / 2);
		if($this->start_page < 1)
		{
			$this->start_page = 1;
			$this->end_page = 1 + $this->pages_element_count;
			if($this->end_page > $this->pages_count)
				$this->end_page = $this->pages_count;
		}
		else
		{
			$this->end_page = $this->page + ($this->pages_element_count / 2);
			if($this->end_page > $this->pages_count)
			{
				$this->end_page = $this->pages_count;
				$this->start_page = $this->pages_count - $this->pages_element_count;
				if($this->start_page < 1)
					$this->start_page = 1;
			}
		}
		
		$this->number_of_pages=$this->end_page-$this->start_page+1;
	}

	public function get_pagination(){
		if($this->number_of_pages<=1)
			return "";
		
		$return='<center><ul class="pagination pagination-sm" style="margin: 10px;">
					<li class="page-item"><a class="page-link" href="javascript: void(0);" aria-label="Precedente" onclick="load_data(1)" ><span aria-hidden="true">&laquo;</span></a></li>';
		
		for($this->start_page; $this->start_page<=$this->end_page; $this->start_page++)
		{
			if($this->page==$this->start_page)
				$return.='<li class="page-item active"><a class="page-link" href="javascript: void(0);" onclick="load_data('.$this->start_page.')">'.$this->start_page.'</a></li>';
			else
				$return.='<li class="page-item"><a class="page-link" href="javascript: void(0);" onclick="load_data('.$this->start_page.')">'.$this->start_page.'</a></li>';
		}
		
		$return.='<li class="page-item"><a class="page-link" href="javascript: void(0);" aria-label="Successivo" onclick="load_data('.$this->pages_count.')" ><span aria-hidden="true">&raquo;</span></a></li>
				</ul></center>';

		return $return;
	}


	public function get_sql(){
		$first_row=($this->page-1)*$this->record_each_page;
			return " LIMIT $first_row,$this->record_each_page;";		
	}

	
	public function get_counter(){
		return (($this->page-1)*$this->record_each_page)+1;
	}

}
?>
