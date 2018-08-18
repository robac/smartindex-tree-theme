<?php



if(!defined('DOKU_INC')) die();


class action_plugin_smartindextree extends DokuWiki_Action_Plugin {

    private $theme_info = array(
        'syntaxRenderer' => '\Smartindex\Renderer\SyntaxRenderer',
        'indexRenderer' => 'TreeIndexRenderer',
        'indexRendererPath' => __DIR__ . '/TreeIndexRenderer.php',
        'css-class' => 'smartindex-simple-theme',
    );

    /**
     * Registers a callback functions
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('PLUGIN_SMARTINDEX_GET_THEME_TREE', 'BEFORE', $this, 'handle_get_theme');
    }

    public function handle_get_theme(Doku_Event &$event, $param) {
        if ($event->data['theme'] !== 'tree') {
            return;
        }

        $event->stoppropagation();
        $event->data['theme-info'] = $this->theme_info;
    }

}

// vim:ts=4:sw=4:et:
