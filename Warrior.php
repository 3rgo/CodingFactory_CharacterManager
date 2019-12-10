<?php

require_once "Character.php";

class Warrior extends Character {

    /**
     * Gathers the informations required for the character creation by asking the user.
     *
     * @static
     * @return array Gathered data
     **/
    public static function askForData() :array {
        $data = parent::askForData();
        $data["class"] = "Warrior";

        println("Shield points :");
        $data["shield"] = intval(trim(fgets(STDIN)));

        return $data;
    }

    /**
     * @var int Shield amount.
     */
    protected $shield;

    /**
     * Constructor
     *
     * @param string $name       Character name.
     * @param int    $damage     Damage dealt per attack.
     * @param int    $health     Current health points.
     * @param int    $initiative Initiative points.
     * @param int    $shield     Shield points.
     *
     */
    public function __construct($name, $damage, $health, $initiative, $shield){
        parent::__construct($name, $damage, $health, $initiative);
        $this->shield = $shield;
    }

    /**
     * Object to string conversion
     *
     * @return string
     */
    public function __toString() :string {
        $valueWidth = max(strlen($this->name), strlen("Warrior"));
        return implode(PHP_EOL, [
            "---------------".str_repeat("-", $valueWidth) . "--",
            "| Name       | ".str_pad($this->name, $valueWidth, " ") . " |",
            "| Class      | ".str_pad("Warrior", $valueWidth, " ") . " |",
            "---------------".str_repeat("-", $valueWidth) . "--",
            "| Damage     | ".str_pad($this->damage, $valueWidth, " ") . " |",
            "| Health     | ".str_pad($this->health, $valueWidth, " ") . " |",
            "| Initiative | ".str_pad($this->initiative, $valueWidth, " ") . " |",
            "| Shield     | ".str_pad($this->shield, $valueWidth, " ") . " |",
            "---------------".str_repeat("-", $valueWidth) . "--",
        ]);
    }

    /**
     * Getter for shield property
     *
     * @return int
     */
    public function getShield() :int {
        return $this->shield;
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
        $shielded = min($this->getShield(), $incomingDamage);
        println(sprintf(
            "\t%s shields %d damage",
            $this->getName(),
            $shielded
        ));
        $this->health -= ($incomingDamage - $shielded);
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
        // Random enemy
        return $enemies[array_rand($enemies)];
    }
}