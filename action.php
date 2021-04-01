<?php
/**
 * DokuWiki Plugin searchform (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Gerrit Uitslag <klapinklapin@gmail.com>
 */

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
        $controller->register_hook('DOKUWIKI_STARTED', 'AFTER',  $this, 'changeQuery');
    }

    /**
     * Restrict the global query to namespace given as url parameter
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */
    public function changeQuery(Doku_Event &$event, $param) {
        global $QUERY;
        global $ACT;

        if($ACT != 'search'){
            return;
        }
        $this->addNamespaceToQuery($QUERY);
    }

    /**
     * Extend query string with namespace, if it doesn't contain a namespace expression
     *
     * @param string &$query (reference) search query string
     */
    private function addNamespaceToQuery(&$query) {
        global $INPUT;

        $ns = cleanID($INPUT->str('ns'));
        if($ns) {
            //add namespace if user hasn't already provide one
            if(!preg_match('/(?:^| )(?:\^|@|-ns:|ns:)[\w:]+/u', $query, $matches)) {
                $query .= ' @' . $ns;
            }
        }
        $notns = cleanID($INPUT->str('-ns'));
        if($notns) {
            //add namespace if user hasn't already provide one
            if(!preg_match('/(?:^| )(?:\^|@|-ns:|ns:)[\w:]+/u', $query, $matches)) {
                $query .= ' ^' . $notns;
            }
        }
    }

}
