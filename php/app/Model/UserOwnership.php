<?php
/**
 * Username model.
 *
 * @copyright     Copyright (c) 2013 Garrett Brown
 * @link          http://kineticthings.com
 * @license       GPLv2 <http://www.gnu.org/licenses/>
 */

App::uses('AppModel', 'Model');

class UserOwnership extends AppModel {

    public $useTable = 'gamefileslinkusernames';

    public $belongsTo = array(
        'Gamefile',
        'Username' => array(
            'counterCache' => 'hoarded',
        ),
    );

    public $actsAs = array('Containable');
    
    public $recursive = -1;
}
