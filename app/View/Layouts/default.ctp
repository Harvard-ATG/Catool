<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $title_for_layout; ?>
    </title>

    <?php
        //---- META
        echo $this->Html->meta('icon');
        echo $this->Html->meta(null, null, array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0'));
        echo $this->fetch('meta');

        //---- CSS
        echo $this->Html->css(array(
            'bootstrap',
            'bootstrap-responsive',
            'video-js',
            'video-js.rangeslider',
            'jquery.dataTables'
        ));
        echo $this->fetch('css');
    ?>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <?php
        //---- JAVASCRIPT
        if(Configure::read('debug') > 1) {
            // vendor libraries
            echo $this->Html->script(array(
                'lib/jquery',
                'lib/jquery.dataTables',
                'lib/bootstrap',
                'lib/underscore',
                'lib/backbone',
                'lib/moment',
                'lib/video',
                'lib/video.rangeslider'
            ));

            // custom app libraries
            echo $this->Html->script(array(
                'app/core',
                'app/utils',
                'app/models',
                'app/views',
                'app/scripts'
            ));
		} else {
            echo $this->Html->script(array(
                'build/lib.min', 
                'build/app.min'
            ));
        }
        echo $this->fetch('script');
    ?>

    <script>
      _V_.options.flash.swf = '<?php echo $this->Html->url('/swf/video-js.swf', true); ?>'; // for video-js
    </script>	
</head>
<body>
    <div class="catool catool-content">

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?php echo $this->Html->url('/'); ?>">CATool</a>
          <div class="nav-collapse">
          	<?php echo $this->element('primary_nav', array('hasProxyPermission' => $this->get('hasProxyPermission'))); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
        <?php echo $this->Session->flash('auth', array('params' => array('class' => 'alert alert-error'))); ?>
        <?php echo $this->Session->flash('flash',  array('params' => array('class' => 'alert'))); ?>
        <?php echo $this->fetch('content'); ?>

        <footer>
            <p>
            <?php 
            echo $this->Html->link($this->Html->image('cake.power.gif',
                array('alt' => $cakeDescription, 'border' => '0')),
                'http://www.cakephp.org/',
                array('target' => '_blank', 'escape' => false)
            );
            ?>
            </p>
        </footer>

        <div class="row">
            <div class="span12">
                <?php echo $this->element('sql_dump'); ?>
            </div>
        </div>
    </div>
  
    </div> <!-- /end catool-content -->
</body>
</html>