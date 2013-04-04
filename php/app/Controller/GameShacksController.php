<?php
/**
 * GameShacks controller.
 *
 * @copyright     Copyright (c) 2013 Garrett Brown
 * @link          http://kineticthings.com
 * @license       GPLv2 <http://www.gnu.org/licenses/>
 */
App::uses('AppController', 'Controller');

/**
 * GameShacks controller.
 */
class GameShacksController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
    public $name = 'GameShacks';

/**
 * This controller does not use a model
 *
 * @var array
 */
    #public $uses = array();
    public $uses = false;

    public function index() {
        // Grab all gameshacks and pass them to the view:
        #$gameshacks = $this->GameShack->find('all');
        #$this->set('gameshacks', $gameshacks);
    }

    public function hoard() {
        if (!$this->request->isPost())
        {
            $this->redirect(array('action' => 'index'));
            return;
        }

        # Pre-initialize our return statuses
        $i = 1;
        $success = array( "result" => "success" );
        $error = array( "error" => array( "code" => $i, "message" => NULL ) );

        $gameList = $this->request->input('json_decode', true);

        if (!is_array($gameList))
        {
            $error['error']['code'] = $i;
            $error['error']['message'] = 'POST data is not a json object';
            return new CakeResponse(array('body' => json_encode($error)));
        }
        $i++;

        foreach (array('username', 'site', 'platform', 'directory') as $var)
        {
            if (!array_key_exists($var, $gameList))
            {
                $error['error']['code'] = $i;
                $error['error']['message'] = "POST data doesn't contain a $var field";
                return new CakeResponse(array('body' => json_encode($error)));
            }
            ${$var} = $gameList[$var];
            $i++;
        }

        

        return new CakeResponse(array('body' => json_encode($success)));
    }

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 */
    public function display() {
        $path = func_get_args();

        $count = count($path);
        if (!$count) {
            $this->redirect('/');
        }
        $page = $subpage = $title_for_layout = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        if (!empty($path[$count - 1])) {
            $title_for_layout = Inflector::humanize($path[$count - 1]);
        }
        $this->set(compact('page', 'subpage', 'title_for_layout'));
        $this->render(implode('/', $path));
    }
}
