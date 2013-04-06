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

    public $uses = array('Gamefile', 'Property', 'Username', 'UserOwnership');

    public function hoard() {
        # For some reason, Gamefile::$useTable isn't working...
        $this->Gamefile->useTable = 'gamefiles';

        /*
        if (!$this->request->isPost())
        {
            $this->redirect(array('controller' => 'games', 'action' => 'index'));
            return;
        }
        /*
        # TODO: Verify user agent
        if (!$this->request->isPost())
        {
            $this->redirect(array('controller' => 'games', 'action' => 'index'));
            return;
        }
        */
        # Initialize our return status error codes
        $i = 1;

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
            return $this->makeError($i, 'POST data is not a json object');
        $i++;

        # Flatten object into raw variables
        foreach (array('username', 'site', 'platform', 'directory') as $var)
        {
            if (!array_key_exists($var, $gameFiles))
                return $this->makeError($i, "POST data doesn't contain a $var field");
            ${$var} = $gameFiles[$var];
            $i++;
        }

        if (!$platform)
            return $this->makeError($i, 'Invalid platform');
        $i++;

        if (!$username)
            return $this->makeError($i, 'Invalid username');
        $i++;

        $validSites = array('thegamesdb.net');
        if (!in_array($site, $validSites))
            return $this->makeError($i, "Invalid site: $site. Valid sites are: " . implode(', ', $validSites));
        $i++;

        $user = $this->loadUser($username);
        $gamefiles = $this->getUserGames($user['Username']['id'], $platform);

        $processed = false;
        foreach ($directory as $file)
        {
            if (!array_key_exists('filename', $file))
                continue;

            if (!$processed)
                $processed = true;

            # Check to see if the file exists in the database. $gamefiles is
            # ordered, exploit this to reduce O(n*m) to O(n*logm).
            if (array_key_exists('properties', $file))
            {
                $index = $this->updateProperties($gamefiles, $file, 0, count($gamefiles) - 1);
                if ($index !== false)
                {
                    if (is_int($index))
                    {
                        $properties = $this->addProperties($file['properties']);
                        if (count($properties))
                            $gamefiles[$index]['Gamefile']['property_id'] = $properties['Property']['id'];

                        # Indirect 'Gamefile' array, because a 'UserOwnership' is
                        # stored parallel to it in $gamefiles[$index]
                        $this->Gamefile->save(array(
                            'Gamefile' => $gamefiles[$index]['Gamefile'],
                        ));
                    }
                    continue;
                }
                
            }

            # Build our new Gamefile object and save it to the database
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
            $this->addGamefile($user['Username']['id'], $gamefile);
        }

        if (!$processed)
            return $this->makeError($i, 'No new files have been hoarded by the server');
        $i++;

        return new CakeResponse(array(
            'body' => json_encode(array(
                'result' => 'success',
            )),
        ));
    }

    /**
     * Query the user, and create a new user if one doesn't exist.
     */
    private function loadUser($username)
    {
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
        return $user;
    }

    private function getUserGames($user_id, $platform)
    {
        return $this->UserOwnership->find('all', array(
            'conditions' => array(
                'username_id' => $user_id,
                'Gamefile.platform' => $platform,
            ),
            'contain' => 'Gamefile',
            'order' => 'Gamefile.filename',
        ));
    }

    private function updateProperties($gamefiles, $file, $lower, $upper)
    {
        if ($lower > $upper)
            return false;

        # PHP doesn't do integer division, and casting is SLOW, so bit-shift
        $mid = ($lower + $upper) >> 1;

        # Check for key in lower subset first, then upper subset
        if ($gamefiles[$mid]['Gamefile']['filename'] > $file['filename'])
            return $this->updateProperties($gamefiles, $file, $lower, $mid - 1);
        if ($gamefiles[$mid]['Gamefile']['filename'] < $file['filename'])
            return $this->updateProperties($gamefiles, $file, $mid + 1, $upper);

        # The file already exists in the database. If properties are needed, add them now
        if ($gamefiles[$mid]['Gamefile']['property_id'] === null)
        {
            $properties = $this->addProperties($file['properties']);
            if (count($properties))
            {
                $gamefiles[$mid]['Gamefile']['property_id'] = $properties['Property']['id'];
                return $mid;
            }
        }

        return true;
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

    private function addGamefile($user_id, $data)
    {
        # First create the Gamefile object
        $this->Gamefile->create($data);
        $gamefile = $this->Gamefile->save();

        # Next map it to the correct Username
        $this->UserOwnership->create(array(
            'gamefile_id' => $gamefile["Gamefile"]["id"],
            'username_id' => $user_id,
        ));
        $this->UserOwnership->save();
    }

    private function makeError($i, $msg)
    {
        $error = array(
            'error' => array(
                'code' => $i,
                'message' => $msg,
            ),
        );
        return new CakeResponse(array(
                'body' => json_encode($error),
        ));
    }
}
