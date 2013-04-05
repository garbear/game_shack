<?php
/**
 * GameFile model.
 *
 * @copyright     Copyright (c) 2013 Garrett Brown
 * @link          http://kineticthings.com
 * @license       GPLv2 <http://www.gnu.org/licenses/>
 */

App::uses('AppModel', 'Model');

class GameFile extends AppModel {
    public $belongsTo = array(
        'Property' => array(
            'counterCache' => true,
        ),
    );

    public $hasAndBelongsToMany = array(
        'Username' => array(
            'className' => 'Username',
            'joinTable' => 'gamefileslinkusernames',
        ),
    );
}
