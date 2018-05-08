<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('perform actions and see result');
$I->amOnPluginsPage();
$I->seePluginActivated( 'wp-php-console' );
