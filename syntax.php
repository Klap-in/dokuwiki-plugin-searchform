<?php
/**
 * Plugin Search Form: Inserts a search form in any page
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Adolfo González Blázquez <code@infinicode.org>
 */

// must be run within Dokuwiki
use dokuwiki\Extension\Event;

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_searchform extends DokuWiki_Syntax_Plugin {

    /**
     * Syntax Type
     *
     * Needs to return one of the mode types defined in $PARSER_MODES in parser.php
     * @return string
     */
    public function getType() {
        return 'substition';
    }

    /**
     * Sort order when overlapping syntax
     * @return int
     */
    public function getSort() {
        return 138;
    }

    /**
     * @param $mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{searchform\b.*?\}', $mode, 'plugin_searchform');
    }

    /**
     * Handler to prepare matched data for the rendering process
     *
     * @param   string $match   The text matched by the patterns
     * @param   int $state   The lexer state for the match
     * @param   int $pos     The character position of the matched text
     * @param   Doku_Handler $handler Reference to the Doku_Handler object
     * @return  array Return an array with all data you want to use in render
     */
    public function handle($match, $state, $pos, Doku_Handler $handler) {
        $match = trim(substr($match,11,-1)); //strip {searchform from start and } from end
        list($key,$value) = explode('=', $match, 2);

        $options['namespace'] = null;

        if(isset($key) && $key == 'ns') {
            $options['namespace'] = cleanID($value);
        }
        return array($options, $state, $pos);
    }

    /**
     * The actual output creation.
     *
     * @param   $format   string        output format being rendered
     * @param   $renderer Doku_Renderer reference to the current renderer object
     * @param   $data     array         data created by handler()
     * @return  boolean                 rendered correctly?
     */
    public function render($format, Doku_Renderer $renderer, $data) {
        global $lang, $INFO, $ACT, $QUERY, $ID;

        if($format == 'xhtml') {
            list($options,,) = $data;

            // don't print the search form if search action has been disabled
            if(!actionOK('search')) return true;

            $ns = $options['namespace'];
            if($ns === null) {
                $ns = $INFO['namespace'];
            }

            /** based on  tpl_searchform() */
            $autocomplete=true;
            $ajax=true;

            $searchForm = new dokuwiki\Form\Form([
                                                     'action' => wl(),
                                                     'method' => 'get',
                                                     'role' => 'search',
                                                     'class' => 'search',
                                                 ], true);
            $searchForm->addTagOpen('div')->addClass('no');
            $searchForm->setHiddenField('do', 'search');
            $searchForm->setHiddenField('id', $ID);
            $searchForm->setHiddenField('ns', $ns)->addClass('searchform__ns');
            $searchForm->addTextInput('q')
                       ->addClass('edit searchform__qsearch_in')
                       ->attrs([
                                   'title' => '[F]',
                                   'accesskey' => 'f',
                                   'placeholder' => $lang['btn_search'],
                                   'autocomplete' => $autocomplete ? 'on' : 'off',
                               ])
                       ->val($ACT === 'search' ? $QUERY : '')
                       ->useInput(false)
            ;
            $searchForm->addButton('', $lang['btn_search'])->attrs([
                                                                       'type' => 'submit',
                                                                       'title' => $lang['btn_search'],
                                                                   ]);
            if ($ajax) {
                $searchForm->addTagOpen('div')->addClass('ajax_qsearch JSpopup searchform__qsearch_out');
                $searchForm->addTagClose('div');
            }
            $searchForm->addTagClose('div');
            // TODO for 2021 release: update to the new event
            Event::createAndTrigger('FORM_QUICKSEARCH_OUTPUT', $searchForm);

            $renderer->doc .= '<div class="searchform__form">';
            $renderer->doc .= $searchForm->toHTML();
            $renderer->doc .= '</div>';

            return true;
        }
        return false;
    }
}
