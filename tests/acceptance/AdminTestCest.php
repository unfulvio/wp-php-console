<?php

class AdminTestCest  {


	public function TestPluginActivationDeactivation( AcceptanceTester $I ) {

		$I->wantTo( 'See if WP PHP Console is listed in Plugins page' );

		$I->amOnPluginsPage();

		$I->seePluginActivated( 'wp-php-console' );

		$I->deactivatePlugin( 'wp-php-console' );
		$I->activatePlugin( 'wp-php-console' );
	}


}
