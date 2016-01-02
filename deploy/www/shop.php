<?php
require_once(LIB_ROOT."control/lib_inventory.php");
require_once(LIB_ROOT."control/ShopController.php");

use app\Controller\ShopController;

if ($error = init(ShopController::PRIV, ShopController::ALIVE)) {
	display_error($error);
} else {
	$shop = new ShopController();

	if (in('command') === 'purchase') {
		$response = $shop->buy();
	} else {
		$response = $shop->index();
	}

	display_page(
		$response['template'],
		$response['title'],
		$response['parts'],
		$response['options']
	);
} // End of no display error.
