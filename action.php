<?php
/**
 * DokuWiki Plugin searchform (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Gerrit Uitslag <klapinklapin@gmail.com>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * Class action_plugin_searchform
 */
class action_plugin_searchform extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller) {

        $controller->register_hook('SEARCH_QUERY_FULLPAGE', 'BEFORE', $this, '_search_query_fullpage');
        $controller->register_hook('SEARCH_QUERY_PAGELOOKUP', 'BEFORE', $this, '_search_query_pagelookup');
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this,'_ajax_call');

    }

    /**
     * Restrict fullpage search to namespace given as url parameter
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */
    public function _search_query_fullpage(Doku_Event &$event, $param) {
        $this->_addNamespace2query($event->data['query']);
    }

    /**
     * Restrict page lookup search to namespace given as url parameter
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */
    public function _search_query_pagelookup(Doku_Event &$event, $param) {
        $this->_addNamespace2query($event->data['id']);
    }

    /**
     * Extend query string with namespace, if it doesn't contain a namespace expression
     *
     * @param string &$query (reference) search query string
     */
    private function _addNamespace2query(&$query) {
        global $INPUT;

        $ns = cleanID($INPUT->str('ns'));
        if($ns) {
            //add namespace if user hasn't already provide one
            if(!preg_match('/(?:^| )(?:@|ns:)[\w:]+/u', $query, $matches)) {
                $query .= ' @' . $ns;
            }
        }
    }
    
    
    # Adds the Ajax-Call "quickfullseach"
	public function _ajax_call(Doku_Event $event, $param) {
		if ($event->data !== 'quickfullsearch') {
			return;
		}
    
		# No other ajax call handlers needed
		$event->stopPropagation();
		$event->preventDefault();
 
 
		# Perform Fullsearch
		global $INPUT;

		$query = $INPUT->post->str('q');
		if(empty($query)) $query = $INPUT->get->str('q');
		if(empty($query)) return;

		$query = urldecode($query);
        
        # Add * to each term (excepting for namespaces) for real fulltext search
        $terms = explode(' ',$query);
        foreach ($terms as &$t) {
			if (strpos($t,'*')===false && strpos($t,'@')===false) {				
				$t="*$t*";
				$search[] = $t;
			}
		}
		$query=implode(' ',$terms);
        
		$regex = Array();
		$data = ft_pageSearch($query,$regex);
		
		if(!count($data)) return;
		
		# display number of results
		echo '<div class="qfs_result_num">' . count($data) . ' ' . $this->getLang('results') . '</div>';

		# display results
		foreach ($data as $id => $counts) {
			echo '<div class="qfs_result">';
			echo html_wikilink(':' . $id, $name,$search).'<br><div>' ;
			
			# Could not get ft_snippet to work properly, so uses rawWiki instead
			echo substr(strip_tags(p_render('xhtml', p_get_instructions(rawWiki($id)))),0,300);
			
			echo '</div></div>';
		}
	
				
	}

}

// vim:ts=4:sw=4:et:
