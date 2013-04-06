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

    public $useTable = 'gamefileslinkusers';

    public $belongsTo = array(
        'Gamefile',
        'User' => array(
            'counterCache' => 'hoarded',
        ),
    );

    public $actsAs = array('Containable');
    
    public $recursive = -1;
}
