<?php
echo $this->Session->flash('auth', array('params' => array('class' => 'alert alert-error')));
echo $this->Form->create(false, array('type' => 'post', 'action' => 'login'));
echo $this->Form->input('is_login', array('type' => 'hidden', 'value' => 'yes'));
echo $this->Form->end(array('label' => 'Login with Google', 'class' => 'btn'));
