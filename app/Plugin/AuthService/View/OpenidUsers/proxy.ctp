<?php
echo $this->Session->flash('auth');
echo $this->Form->create(false, array('type' => 'post', 'action' => 'proxy'));
echo $this->Form->input('OpenidUser.claimed_id', array(
    'type' => 'text',
    'label' => 'User Identity'
));
echo $this->Form->end('Go');
