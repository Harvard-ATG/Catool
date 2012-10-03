<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	
/**
 * afterFind callback
 *
 * @param array $results
 * @return array
 */
    public function afterFind($results) {
        return $this->__afterFindConvertDateToTime($results);
    }

/**
 * __afterFindConvertDateToTime function
 *
 * @param array $results
 * @return array
 */
	private function __afterFindConvertDateToTime(&$results) {
    	$alias = $this->alias;
		$dateFields = array('created', 'updated', 'modified');
		
        foreach($results as $key => $val) {
        	foreach($dateFields as $dateField) {
             	if(isset($val[$alias][$dateField]) 
             		&& !empty($val[$alias][$dateField]) 
             		&& !isset($val[$alias][$dateField.'_unix'])) {
                	$results[$key][$alias][$dateField.'_unix'] = strtotime($val[$alias][$dateField]); 
				}
			}
		}
		
		return $results;
	}
}
