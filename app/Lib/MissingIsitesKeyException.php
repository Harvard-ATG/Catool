<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * Used when an iSites producer key can't be found
 */
class MissingIsitesKeyException extends CakeException {

	protected $_messageTemplate = 'Missing isites producer key:  %s';	

}