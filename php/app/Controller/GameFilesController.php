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
    public $uses = array('GameFile', 'Property', 'Username');

    public function index() {
        // Grab all gameshacks and pass them to the view:
        #$gameshacks = $this->GameShack->find('all');
        #$this->set('gameshacks', $gameshacks);
    }

    public function hoard() {
        /*
        if (!$this->request->isPost())
        {
            $this->redirect(array('action' => 'index'));
            return;
        }
        */

        # Pre-initialize our return statuses
        $i = 1;
        $success = array( "result" => "success" );
        $error = array( "error" => array( "code" => $i, "message" => NULL ) );

        #$gameFiles = $this->request->input('json_decode', true);
        $gameFiles = array(
            'directory' => array(
                array(
                    'properties' => array (
                        'publisher' => '01',
                        'code' => 'TETRIS',
                        'extension' => 'gb',
                    ),
                    'filename' => 'Tetris.gb',
                ),
                array(
                    'properties' => array (
                        'publisher' => '01',
                        'code' => 'AZLE',
                        'extension' => 'gba',
                        'title' => 'GBAZELDA',
                    ),
                    'filename' => 'The Legend of Zelda - A Link to the Past & Four Swords.gba',
                ),
            ),
            'platform' => 'Nintendo Game Boy',
            'username' => 'testuser',
            'site' => 'thegamesdb.org',
        );

        if (!is_array($gameFiles))
        {
            $error['error']['code'] = $i;
            $error['error']['message'] = 'POST data is not a json object';
            return new CakeResponse(array('body' => json_encode($error)));
        }
        $i++;

        foreach (array('username', 'site', 'platform', 'directory') as $var)
        {
            if (!array_key_exists($var, $gameFiles))
            {
                $error['error']['code'] = $i;
                $error['error']['message'] = "POST data doesn't contain a $var field";
                return new CakeResponse(array('body' => json_encode($error)));
            }
            ${$var} = $gameFiles[$var];
            $i++;
        }

        # Query the user, and create a new user if one doesn't exist
        $user = $this->Username->find('first', array(
            'conditions' => array(
                'Username.username' => $username,
            ),
            'contain' => 'GameFile',
            'recursive' => 2,
        ));
        if (count($user) == 0)
        {
            $this->Username->create(array('username' => $username));
            $user = $this->Username->save();
        }

        if (array_key_exists('GameFile', $user) && count($user['GameFile']))
        {
            $error['error']['code'] = $i;
            $error['error']['message'] = '$user["GameFile"]: ' . json_encode($user['GameFile']);
            return new CakeResponse(array('body' => json_encode($error)));
            unset($user['GameFile']);
        }

        foreach ($directory as $file)
        {
            if (!array_key_exists('filename', $file))
                continue;
            $gamefile = array(
                'filename' => $file['filename'],
                'site' => $site,
                'platform' => $platform,
            );
            if (array_key_exists('properties', $file))
            {
                $properties = $this->addProperties($file['properties']);
                if (count($properties))
                    $gamefile['property_id'] = $properties["Property"]["id"];
            }
            $gamefile_id = $this->addGameFile($gamefile);
            $user['GameFile']['GameFile'][] = $gamefile_id;
        }

        $result = $this->Username->saveAll($user);

        $error['error']['code'] = $i;
        $error['error']['message'] = "Result: " . $result . ", Username: " . json_encode($user);
        return new CakeResponse(array('body' => json_encode($error)));
        
        #return new CakeResponse(array('body' => json_encode($success)));
    }

    private function addProperties($properties)
    {
        $search = array();
        $data = array();
        $property = array();
        foreach (array('extension', 'code', 'title', 'publisher') as $var)
        {
            if (array_key_exists($var, $properties) && $properties[$var])
                $search['Property.' . $var] = $data[$var] = $properties[$var];
        }
        if (count($search))
        {
            $property = $this->Property->find('first', array('conditions' => $search));
            if (!count($property))
            {
                $this->Property->create($data);
                $property = $this->Property->save();
            }
        }
        return $property;
    }

    private function addGameFile($data)
    {
        $this->GameFile->create($data);
        $gamefile = $this->GameFile->save();
        return $gamefile["GameFile"]["id"];
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
