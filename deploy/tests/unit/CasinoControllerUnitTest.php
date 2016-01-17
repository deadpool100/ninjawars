<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\SessionFactory;
use NinjaWars\core\control\CasinoController;

class CasinoControllerUnitTest extends PHPUnit_Framework_TestCase {
    private $controller;

    public function __construct() {
        $this->controller = new CasinoController();
    }

	protected function setUp() {
		SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
		SessionFactory::getSession()->set('player_id', $char_id);
    }

	protected function tearDown() {
        RequestWrapper::destroy();
    }

    public function testIndex() {
        $response = $this->controller->index();

        $this->assertArrayHasKey('template', $response);
    }
}