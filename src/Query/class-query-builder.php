<?php

namespace Gravity_Forms\Gravity_Tools\Query;

class Query_Builder {

	/**
	 * @var SQL_Parsers
	 */
	protected $parsers;

	/**
	 * @var string
	 */
	protected $table_name;

	/**
	 * @var array
	 */
	protected $selects = array();

	/**
	 * @var array
	 */
	protected $search = array();

	/**
	 * @var array
	 */
	protected $filters = array();

	/**
	 * @var array
	 */
	protected $joins = array();

	/**
	 * @var array
	 */
	protected $order = array();

	/**
	 * @var int
	 */
	protected $page;

	/**
	 * @var int
	 */
	protected $per_page;

	public function __construct( $parsers ) {
		$this->parsers = $parsers;
	}

	public function as_sql() {
		$select_sql = $this->parsers->select->process( $this->selects );
		$join_sql   = $this->parsers->join->process( $this->joins );
		$where_sql  = $this->parsers->where->process( $this->filters, ! empty( $this->search ) );
		$search_sql = $this->parsers->search->process( $this->search );
		$order_sql  = $this->parsers->order->process( $this->order );
		$limit_sql  = $this->parsers->limit->process( $this->page, $this->per_page );

		return sprintf( '%s FROM %s %s WHERE %s %s %s %s', $select_sql, $this->table_name, $join_sql, $where_sql, $search_sql, $order_sql, $limit_sql );
	}

	/**
	 * @param $table_name
	 *
	 * @return $this
	 */
	public function query( $table_name ) {
		$this->table_name = $table_name;

		return $this;
	}

	/**
	 * @param $selects
	 *
	 * @return $this
	 */
	public function select( $selects ) {
		$this->selects = array_merge( $this->selects, $selects );

		return $this;
	}

	/**
	 * @param $joins
	 *
	 * @return $this
	 */
	public function join( $joins ) {
		$this->joins = array_merge( $this->joins, $joins );

		return $this;
	}

	/**
	 * @param $filters
	 *
	 * @return $this
	 */
	public function where( $filters ) {
		$this->filters = array_merge( $this->filters, $filters );

		return $this;
	}

	/**
	 * @param $search
	 *
	 * @return $this
	 */
	public function search( $search ) {
		$this->search = array_merge( $this->search, $search );

		return $this;
	}

	/**
	 * @param $order
	 *
	 * @return $this
	 */
	public function order( $order ) {
		$this->order = array_merge( $this->order, $order );

		return $this;
	}

	/**
	 * @param $page
	 * @param $per_page
	 *
	 * @return $this
	 */
	public function paginate( $page, $per_page ) {
		$this->page     = (int) $page;
		$this->per_page = (int) $per_page;

		return $this;
	}

}