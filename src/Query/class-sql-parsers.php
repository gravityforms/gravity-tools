<?php

namespace Gravity_Forms\Gravity_Tools\Query;

class SQL_Parsers {

	/**
	 * @var Select_Parser
	 */
	public $select;

	/**
	 * @var Search_Parser
	 */
	public $search;

	/**
	 * @var Where_Parser
	 */
	public $where;

	/**
	 * @var Join_Parser
	 */
	public $join;

	/**
	 * @var Order_Parser
	 */
	public $order;

	/**
	 * @var Limit_Parser
	 */
	public $limit;

	public function __construct( $select, $search, $where, $join, $order, $limit ) {
		$this->select = $select;
		$this->search = $search;
		$this->where  = $where;
		$this->join   = $join;
		$this->order  = $order;
		$this->limit  = $limit;
	}

}