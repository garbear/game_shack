<?php
/**
 * GameFiles view.
 *
 * @copyright     Copyright (c) 2013 Garrett Brown
 * @link          http://kineticthings.com
 * @license       GPLv2 <http://www.gnu.org/licenses/>
 */
?>
<h2>Welcome to the Game Shack</h2>

Sign in

<form id="loginform" action="<?php echo $this->Html->url(array(
    'controller' => 'games',
    'action' => 'login',
)); ?>" method="post">

<div class="username-div">
    <label for="Username">
        <strong class="username-label">Username</strong>
    </label>
    <input spellcheck="false" name="Username" id="Username" value="">
</div>

<div class="passwd-div">
    <label for="Passwd">
        <strong class="passwd-label">Password</strong>
    </label>
    <input name="Passwd" id="Passwd" value="0000000000000000"><?php /* We only show the first 16 of 32 chars */ ?>

</div>

<input type="submit" class="shack-button shack-button-submit" name="signIn" id="signIn" value="Sign in">

</form>

<div class="email-div">
    <label for="Email">
        <strong class="email-label">Email:</strong>
    </label>
    <input name="Email" id="Email" value="">
</div>

<p>
    The Game Shack is a data mining project geared toward developing advanced game search heuristics. The project currently relies on supervised machine learning, ROM header parsing, and probabilistic text classifiers.
</p>
