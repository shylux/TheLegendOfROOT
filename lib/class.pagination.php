<?php

class Paginator {

	private $dbConnection; 
	private $paginationResultsPerPage;
	private $paginationBinding = false; 

	public function __construct( $amountOfResults = 10 )
	{
		if ( $amountOfResults < 2 || $amountOfResults > 30 )
		{
			return false;
		} 
		$this->paginationResultsPerPage = $amountOfResults;
	} 

	public function registerPagination( $table, $desired, $where )
	{  
		$fetchedData = $this->dbConnection->select($table, $desired, $where);
		
		$pagedData = array();
		$modulo = 1;
		$j = 0;
		for ( $i = 0; $i < count($fetchedData); $i++ ) {
			$modulo += ( $j %  $this->paginationResultsPerPage === 0 && $j != 0 ) ? 1 : 0 ;
			$pagedData[$modulo][] = $fetchedData[$i];
			$j++;
		}

		$this->paginationBinding = $pagedData;
	}

	public function getAmountOfPages()
	{
		if ( $this->paginationBinding === false )
		{ 
			return false;
		}

		return count($this->paginationBinding);
	}

	public function loadPage( $number )
	{
		if ( $this->paginationBinding === false || $number > count($this->paginationBinding) || $number < 1 )
		{
			return false;
		}
		return $this->paginationBinding[$number];
	}
}
