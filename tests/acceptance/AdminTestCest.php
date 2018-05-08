<?php

class AdminTestCest  {


	public function TestPluginsPage( AcceptanceTester $I ) {

		$I->wantTo( 'See if WP PHP Console is listed in Plugins page' );
		$I->amOnPluginsPage();
		$I->seePluginActivated( 'wp-php-console' );
	}


}
