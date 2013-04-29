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
class GamesController extends AppController {

    #public $uses = array('Gamefile', 'Property', 'Username', 'UserOwnership');
    public $helpers = array('Html');

    public $components = array(
        'GameShackAuth' => array(
            'loginAction' => array(
                'controller' => 'games',
                'action' => 'login',
                'plugin' => 'users',
            ),
            'authError' => 'Did you really think you are allowed to see that?',
            'authenticate' => array(
                AuthComponent::ALL => array(
                    'userModel' => 'User',
                    'fields' => array(
                        'username' => 'username',
                        'password' => 'email', # TODO: Need to use the md5-64 email hash as password field
                    ),
                ),
                'Form',
            ),
        ),
    );

    public function beforeFilter() {
        #$this->GameShackAuth->authorize = array('controller');
        #$this->GameShackAuth->loginAction = array('controller' => 'users', 'action' => 'login');
        #$this->Cookie->name = 'CookieMonster';
    }

    public function index() {
        $path = func_get_args();
        /*
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
        */
        // Grab all gameshacks and pass them to the view:
        #$gameshacks = $this->GameShack->find('all');
        #$this->set('gameshacks', $gameshacks);
    }

    public function login() {
        if ($this->request->isPost())
        {
            if ($this->GameShackAuth->login())
            {
                return $this->redirect($this->GameShackAuth->redirectUrl());
                // Prior to 2.3 use `return $this->redirect($this->GameShackAuth->redirect());`
            }
            else
            {
                $this->Session->setFlash(__('Username or password is incorrect'), 'default', array(), 'auth');
            }
        }
    }
}
