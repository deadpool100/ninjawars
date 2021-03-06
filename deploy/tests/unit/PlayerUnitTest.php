<?php
use NinjaWars\core\data\PlayerVO;
use NinjaWars\core\data\Player;

class PlayerUnitTest extends PHPUnit_Framework_TestCase {
    private $player;
    private $data;

    public function __construct() {
        $this->data = new PlayerVO();
        $this->data->uname = 'User1';
        $this->data->player_id = 1;
        $this->data->level = 20;
        $this->data->health = Player::maxHealthByLevel($this->data->level);
    }

	protected function setUp() {
        $this->player = new Player();
        $this->player->uname = $this->data->uname;
        $this->player->player_id = $this->data->player_id;
        $this->player->level = $this->data->level;
        $this->player->health = $this->data->health;
        $this->player->stamina = Player::baseStaminaByLevel($this->data->level);
        $this->player->strength = Player::baseStrengthByLevel($this->data->level);
        $this->player->speed = Player::baseSpeedByLevel($this->data->level);
    }

	protected function tearDown() {
        unset($this->player);
    }

    public function testPlayerConstructor() {
        $this->assertInstanceOf(Player::class, $this->player);
    }

    public function testToString() {
        $this->assertEquals((string)$this->player, $this->data->uname);
    }

    public function testAccessor_magic() {
        $this->assertEquals($this->player->level, $this->data->level);
    }

    public function testAccessor_id() {
        $this->assertEquals($this->player->id(), $this->data->player_id);
    }

    public function testAccessor_level() {
        $this->assertEquals($this->player->level, $this->data->level);
    }

    public function testIsHurtBy() {
        $this->assertGreaterThanOrEqual(0, $this->player->is_hurt_by());
    }

    public function testHealthPercent() {
        $this->assertEquals(100, $this->player->health_percent());
    }

    public function testInitialPlayerHealthIsAtLeastBaseConstant(){
        $this->assertGreaterThanOrEqual(NEW_PLAYER_INITIAL_HEALTH, $this->player->health);
    }

    public function testInitialPlayerStaminaConformsToSettings(){
        $expected_stamina = NEW_PLAYER_INITIAL_STATS + (LEVEL_UP_STAT_RAISE * ($this->player->level -1));
        $this->assertGreaterThanOrEqual(NEW_PLAYER_INITIAL_STATS, $expected_stamina);
    }

    public function testInitialPlayerHealthConformsToSettings(){
        $expected_stamina = NEW_PLAYER_INITIAL_STATS + (LEVEL_UP_STAT_RAISE * ($this->player->level -1));
        $expected_health = NEW_PLAYER_INITIAL_HEALTH+($expected_stamina*Player::HEALTH_PER_STAMINA);
        $expected_stamina = NEW_PLAYER_INITIAL_STATS;
        $this->assertEquals($expected_health, $this->player->getMaxHealth());
        $this->assertEquals($expected_health, $this->player->health);
    }

    public function testHealAPlayer(){
        $max_health = $this->player->getMaxHealth();
        $max_harm = 10;
        $max_heal = 5;
        if($max_harm > $max_health){
            throw new LogicException('The max harm is greater than the total max health of players currently!');
        }

        $this->player->harm($max_harm);
        $this->player->heal($max_heal);
        $this->assertEquals($this->player->getMaxHealth()-5, $this->player->health);
    }

    public function testHarmAPlayer(){
        $this->player->harm(7);
        $this->assertEquals($this->player->getMaxHealth()-7, $this->player->health);
    }

    public function testHarmAPlayerWithMoreHealthThanTheyHave() {
        $this->player->harm(9999);
        $this->assertEquals(0, $this->player->health);
    }

}
