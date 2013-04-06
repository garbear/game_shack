<?php
/**
 * GameFiles controller.
 *
 * @copyright     Copyright (c) 2013 Garrett Brown
 * @link          http://kineticthings.com
 * @license       GPLv2 <http://www.gnu.org/licenses/>
 */
App::uses('AppController', 'Controller');

/**
 * GameFiles controller.
 */
class GameFilesController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
    public $name = 'GameFiles';

/**
 * This controller does not use a model
 *
 * @var array
 */
    public $uses = array('Gamefile', 'Property', 'Username', 'UserOwnership');

    public function index() {
        // Grab all gameshacks and pass them to the view:
        #$gameshacks = $this->GameShack->find('all');
        #$this->set('gameshacks', $gameshacks);
    }

    public function hoard() {
        
    }
}
