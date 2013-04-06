<?php
/**
 * GameFile model.
 *
 * @copyright     Copyright (c) 2013 Garrett Brown
 * @link          http://kineticthings.com
 * @license       GPLv2 <http://www.gnu.org/licenses/>
 */

App::uses('AppModel', 'Model');

class Gamefile extends AppModel {

    public $belongsTo = array(
        'Property' => array(
            'counterCache' => true,
        ),
    );

    public $hasMany = 'UserOwnership';

    public $actsAs = array('Containable');
    
    public $recursive = -1;
}
