<?php
echo $this->Session->flash('auth');
echo $this->Form->create(false, array('type' => 'post', 'action' => 'proxy'));
echo $this->Form->input('User.id', array(
    'type' => 'text',
    'label' => 'Proxy User ID'
));
echo $this->Form->end(array('label' => 'Submit', 'class' => 'btn'));
