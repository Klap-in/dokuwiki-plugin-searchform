<?php
/**
 * Plugin Search Form: Inserts a search form in any page
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Adolfo González Blázquez <code@infinicode.org>
 */
 
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_searchform extends DokuWiki_Syntax_Plugin {


    function getType() { return 'substition'; }
    function getSort() { return 138; }

    function connectTo($mode) {
		$this->Lexer->addSpecialPattern('\{searchform\}',$mode,'plugin_searchform');
    }

    function handle($match, $state, $pos, &$handler) {  
    	return array($match, $state, $pos);
    }

    function render($mode, &$renderer, $data) {
 		
 		global $lang, $INFO, $ACT, $QUERY;
 		
		if ($mode == 'xhtml') {
            // don't print the search form if search action has been disabled
            if(!actionOK('search')) return true;

			$ns = $INFO['namespace'];
            /** based on  tpl_searchform() */
			$renderer->doc .= '<div id="searchform_plugin">'."\n";
			$renderer->doc .= '<form action="'.wl().'" accept-charset="utf-8" class="search" id="dw__search2" method="get" role="search"><div class="no">'."\n";
			$renderer->doc .= '<input type="hidden" name="do" value="search" />'."\n";
			$renderer->doc .= '<input type="hidden" id="dw__ns" name="ns" value="' . $ns .'" />';
			$renderer->doc .= '<input type="text" ';
            if($ACT == 'search') $renderer->doc .= 'value="'.htmlspecialchars($QUERY).'" ';
			$renderer->doc .= 'id="qsearch2__in" name="id" class="edit" />'."\n";
            $renderer->doc .= '<input type="submit" value="'.$lang['btn_search'].'" class="button" title="'.$lang['btn_search'].'" />'."\n";
			$renderer->doc .= '<div id="qsearch2__out" class="ajax_qsearch JSpopup"></div>'."\n";
			$renderer->doc .= '</div></form>'."\n";
			$renderer->doc .= '</div>'."\n";

			return true;
		}
		return false;
	}
}
