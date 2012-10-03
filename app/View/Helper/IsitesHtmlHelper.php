<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

App::uses('HtmlHelper', 'View/Helper');
App::uses('IsitesRequest', 'Lib');

class IsitesHtmlHelper extends HtmlHelper {
 
	/**
	 * Override Helper::assetUrl() to generate full, non-iSites URLs
	 * because assets such as JS and CSS aren't delivered via iSites.
	 * 
	 * Note: the overrides include the following changes:
	 * 	- sets the fullBase option to include the base URL
	 * 	- calls parent::url() instead of $this->url()
	 */   
	public function assetUrl($path, $options = array()) {
		$options['fullBase'] = true;
		if (is_array($path)) {
			$path = parent::url($path, !empty($options['fullBase']));
		} elseif (strpos($path, '://') === false) {
			if (!array_key_exists('plugin', $options) || $options['plugin'] !== false) {
				list($plugin, $path) = $this->_View->pluginSplit($path, false);
			}
			if (!empty($options['pathPrefix']) && $path[0] !== '/') {
				$path = $options['pathPrefix'] . $path;
			}
			if (
				!empty($options['ext']) &&
				strpos($path, '?') === false &&
				substr($path, -strlen($options['ext'])) !== $options['ext']
			) {
				$path .= $options['ext'];
			}
			if (isset($plugin)) {
				$path = Inflector::underscore($plugin) . '/' . $path;
			}
			$path = $this->assetTimestamp($this->webroot($path));

			if (!empty($options['fullBase'])) {
				$path = parent::url('/', true) . $path;
			}
		}

		return $path;
	}
	
    /**
     * Override Helper::url() to generate iSites URLs. 
     */
    public function url($url = null, $full = false) {
		return $this->isiteUrl($url, $full);
	}
	
	/**
	 * Creates a URL for iSites.
	 */
	public function isiteUrl($url = null, $full = false) {
		$url = Router::url($url, false);
		if($this->_isFullUrl($url)) {
			return $url;
		}
        
		$parts = parse_url($url);
		if(isset($parts['path'])) {
			$path = $this->_stripLeadingSlash($parts['path']);
		} else {
			$path = '';
		}
		
		$query = array();
		$fragment = isset($parts['fragment']) ? $parts['fragment'] : null;
		if(isset($parts['query'])) {
			parse_str($parts['query'], $query);
		}
        
		return $this->_constructIsiteUrl($path, $query, $fragment);		
	}
    
	/**
	 * Constructs an iSites url.
	 */
    protected function _constructIsiteUrl($viewPath = null, $viewQuery = array(), $viewFragment = null) {
        $host = $this->request->isites->getParam('urlRoot');
        $keyword = $this->request->isites->getParam('keyword');
        $page_id = $this->request->isites->getParam('pageid');
        $page_content_id = $this->request->isites->getParam('pageContentId');
        $topic_id = $this->request->isites->getParam('topicId');
        $state = $this->request->isites->getParam('state');
        
        $parts = array(
            'scheme' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http',
            'host' => isset($host) ? $host : 'localhost',
            'path' => 'icb/icb.do',
            'query' => '',
            'fragment' => $viewFragment,
        );
    
        $mergeQuery = array(
            'state' => $state,
            'keyword' => $keyword
        );

        if($state === 'popup') {
                $viewParams = $this->_queryAsViewParams($viewQuery);
                $mergeQuery = array_merge($mergeQuery, array(
                    'topicid' => $topic_id, // Note the spelling: topicid, NOT topicId
                    'view' => $viewPath));
                $mergeQuery = array_merge($mergeQuery, $viewParams);
        } else {
            // pass view params back to our app via the "panel" query
            $panelView = $viewPath;
            $panelParams = array();
            if(!empty($viewQuery)) {
                foreach($viewQuery as $queryKey => $queryVal) {
                    $panelParams[] = "$queryKey=$queryVal";
                }
                $panelView .= '?' . implode('&', $panelParams);
            }
            
            $mergeQuery = array_merge($mergeQuery, array(
                'topicId' => $topic_id,
                'pageContentId' => $page_content_id,
                'pageid' => $page_id,
                'panel' => $page_content_id.':r'.$panelView
            ));
        }

        $parts['query'] = $mergeQuery;
        
        $full_url = $parts['scheme'] . '://' . $parts['host'] . '/' . $parts['path'] . '?' . http_build_query($mergeQuery);
        if(isset($parts['fragment'])) {
            $full_url .= '#' . $parts['fragment'];
        }
        
        return $full_url;
    }

    protected function _queryAsViewParams($query = array()) {
        $viewParams = array();
        
        foreach($query as $queryKey => $queryVal) {
            if(is_array($queryVal)) {
                foreach($queryVal as $queryValItem) {
                    $viewParams['viewParam_' .$queryKey.'[]'] = $queryValItem;
                }
            } else {
                $viewParams['viewParam_'.$queryKey] = $queryVal;
            }
        }
        
        return $viewParams;
    }
    
    protected function _isFullUrl($url = '') {
        if((strpos($url, '://') !== false) ||
            (strpos($url, 'javascript:') === 0) ||
            (strpos($url, 'mailto:') === 0)) {
            return true;
        }
        return false;   
    }
	
	protected function _stripLeadingSlash($path) {
		if(substr($path, 0, 1) === '/') {
			return substr($path, 1);
		}
		return $path;
	}
}

