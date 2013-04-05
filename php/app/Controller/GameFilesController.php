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
        # For some reason, Gamefile::$useTable isn't working...
        $this->Gamefile->useTable = 'gamefiles';

        if (!$this->request->isPost())
        {
            $this->redirect(array('action' => 'index'));
            return;
        }

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
            'site' => 'thegamesdb.net',
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

        $validSites = array('thegamesdb.net');
        if (!in_array($site, $validSites))
        {
            $error['error']['code'] = $i;
            $error['error']['message'] = "Invalid site: $site. Valid sites are: " . implode(", ", $validSites);
            return new CakeResponse(array('body' => json_encode($error)));
        }
        $i++;

        if (!$platform)
        {
            $error['error']['code'] = $i;
            $error['error']['message'] = 'Invalid platform';
            return new CakeResponse(array('body' => json_encode($error)));
        }
        $i++;
        
        # Query the user, and create a new user if one doesn't exist
        $user = $this->Username->find('first', array(
            'conditions' => array(
                'username' => $username,
            ),
        ));
        if (!count($user))
        {
            $this->Username->create(array('username' => $username));
            $user = $this->Username->save();
        }

        $gamefiles = $this->UserOwnership->find('all', array(
            'conditions' => array(
                'username_id' => $user['Username']['id'],
                'Gamefile.platform' => $platform,
            ),
            'contain' => 'Gamefile',
        ));

        foreach ($gamefiles as $g)
        {
            $gamefile = $g['Gamefile'];
            foreach ($directory as $key => $file)
            {
                if (!array_key_exists('filename', $file))
                {
                    unset($directory[$key]);
                }
                else if ($gamefile['filename'] == $file['filename'])
                {
                    # The file already exists in the database
                    # If new properties arrived, add them now
                    if ($gamefile['property_id'] === null &&
                        array_key_exists('properties', $file))
                    {
                        $properties = $this->addProperties($file['properties']);
                        if (count($properties))
                        {
                            $gamefile['property_id'] = $properties['Property']['id'];
                            $g['Gamefile'] = $gamefile;
                            $this->Gamefile->save($g);
                        }
                    }
                    unset($directory[$key]);
                }
            }
        }

        if (!count($directory))
        {
            $error['error']['code'] = $i;
            $error['error']['message'] = 'No new files have been hoarded by the server';
            return new CakeResponse(array('body' => json_encode($error)));
        }
        $i++;

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
                    $gamefile['property_id'] = $properties['Property']['id'];
            }
            $this->UserOwnership->create(array(
                'gamefile_id' => $this->addGamefile($gamefile),
                'username_id' => $user['Username']['id'],
            ));
            $this->UserOwnership->save();
        }

        return new CakeResponse(array('body' => json_encode($success)));
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

    private function addGamefile($data)
    {
        $this->Gamefile->create($data);
        $gamefile = $this->Gamefile->save();
        return $gamefile["Gamefile"]["id"];
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
