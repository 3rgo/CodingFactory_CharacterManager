<?php

require_once 'FighterInterface.php';

class Character implements FighterInterface {

    /**
     * Gathers the informations required for the character creation by asking the user.
     *
     * @static
     * @return array Gathered data
     **/
    public static function askForData() :array {
        println("Character name :");
        $name = trim(fgets(STDIN));
        if(empty($name)){
            throw new \Exception("Character name cannot be empty");
        }
        println("Damage :");
        $damage = intval(trim(fgets(STDIN)));

        println("Health points :");
        $health = intval(trim(fgets(STDIN)));

        println("Initiative points :");
        $initiative = intval(trim(fgets(STDIN)));

        return [
            "name" => $name,
            "damage" => $damage,
            "health" => $health,
            "initiative" => $initiative
        ];
    }

    /**
     * @var string Character name.
     */
    protected $name;

    /**
     * @var int Damage dealt per attack.
     */
    protected $damage;

    /**
     * @var int Current health points.
     */
    protected $health;

    /**
     * @var int Initiative points.
     */
    protected $initiative;

    /**
     * @var int Maximum health points
     */
    protected $maxhealth;

    /**
     * @var int Color number for name output
     */
    protected $color = null;

    /**
     * Constructor
     *
     * @param string $name       Character name.
     * @param int    $damage     Damage dealt per attack.
     * @param int    $health     Current health points.
     * @param int    $initiative Initiative points.
     *
     */
    public function __construct($name, $damage, $health, $initiative){
        $this->name       = $name;
        $this->damage     = $damage;
        $this->health     = $health;
        $this->maxhealth  = $health;
        $this->initiative = $initiative;
    }

    /**
     * Object to string conversion
     *
     * @return string
     */
    public function __toString() :string {
        $valueWidth = max(strlen($this->name), 4);
        return implode(PHP_EOL, [
            "---------------".str_repeat("-", $valueWidth) . "--",
            "| Name       | ".str_pad($this->name, $valueWidth, " ") . " |",
            "---------------".str_repeat("-", $valueWidth) . "--",
            "| Damage     | ".str_pad($this->damage, $valueWidth, " ") . " |",
            "| Health     | ".str_pad($this->health, $valueWidth, " ") . " |",
            "| Initiative | ".str_pad($this->initiative, $valueWidth, " ") . " |",
            "---------------".str_repeat("-", $valueWidth) . "--",
        ]);
    }

    /**
     * Getter for name property
     *
     * @return string
     */
    public function getName() :string {
        if(!is_null($this->color)){
            return sprintf(
                "\e[1m\e[%dm%s\e[0m",
                $this->color,
                $this->name
            );
        }
        return $this->name;
    }

    /**
     * Getter for damage property
     *
     * @return int
     */
    public function getDamage() :int {
        return $this->damage;
    }

    /**
     * Getter for health property
     *
     * @return int
     */
    public function getHealth() :int {
        return $this->health;
    }

    /**
     * Setter for health property
     *
     * @param int $health New health value
     */
    public function setHealth(int $health) {
        $this->health = $health;
    }

    /**
     * Getter for maxhealth property
     *
     * @return int
     */
    public function getMaxHealth() :int {
        return $this->maxhealth;
    }

    /**
     * Getter for initiative property
     *
     * @return int
     */
    public function getInitiative() :int {
        return $this->initiative;
    }

    /**
     * Getter for color
     *
     * @return int
     */
    public function getColor() :int {
        return $this->color;
    }

    /**
     * Setter for color
     *
     * @param int $color Color code
     */
    public function setColor(int $color = null) {
        $this->color = $color;
    }

    /**
     * Computes the attack value and attacks the given opponent
     *
     * @param Character $opponent The opponent being attacked
     */
    public function attack(Character $opponent) {
        $damage = $this->getDamage();
        println(sprintf(
            "\t%s attacks for %d damage",
            $this->getName(),
            $damage
        ));
        $opponent->defend($this->getDamage());
    }

    /**
     * Computes new health points
     *
     * @param int $incomingDamage The damage dealt by the opponent
     */
    public function defend(int $incomingDamage) {
        $this->health -= $incomingDamage;
        println(sprintf(
            "\t%s has %d health left",
            $this->getName(),
            max(0, $this->health)
        ));
    }

    /**
     * Chooses the enemy to target
     *
     * @param array $enemies Array containing all the enemy characters
     *
     * @return Character Targeted enemy
     */
    public function targetEnemy(array $enemies) :Character {
        // Random enemy (except for Thief who will choose the enemy with the lowest HP)
        return $enemies[array_rand($enemies)];
    }

    /**
     * Heal self or an ally
     *
     * @param array $allies Array containing all allies (including self)
     */
    public function healAlly(array $allies) {
        // Do nothing (only Priest subclass will do something)
    }
}