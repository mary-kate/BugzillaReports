<?php
/**
 * Mock Parser for standalone use of Bugzilla Reports
 */

/**
 * Copyright (C) 2008 - Ian Homer & bemoko
 */

class BOutput {
	public $head = '';

	public function addHeadItem( $markup ) {
		$this->head .= $markup;
	}
}

class BParser {
	public $mOutput;

	function BParser() {
		$this->mOutput = new BOutput();
	}
}
