<?php
echo $this->Session->flash('auth');
echo $this->Form->create(false, array('type' => 'post', 'action' => 'login'));
echo $this->Form->input('User.id', array('type' => 'string', 'label' => 'Enter user ID'));
echo $this->Form->end('Login');
