<?php

require_once "Character.php";

class Wizard extends Character {

    /**
     * Gathers the informations required for the character creation by asking the user.
     *
     * @static
     * @return array Gathered data
     **/
    public static function askForData() :array {
        $data = parent::askForData();
        $data["class"] = "Wizard";

        println("Magic damage :");
        $data["magicdamage"] = intval(trim(fgets(STDIN)));

        return $data;
    }

    /**
     * @var int Magic damage amount.
     */
    protected $magicdamage;

    /**
     * Constructor
     *
     * @param string $name       Character name.
     * @param int    $damage     Damage dealt per attack.
     * @param int    $health     Current health points.
     * @param int    $initiative Initiative points.
     * @param int    $magicdamage     magicdamage points.
     *
     */
    public function __construct($name, $damage, $health, $initiative, $magicdamage){
        parent::__construct($name, $damage, $health, $initiative);
        $this->magicdamage = $magicdamage;
    }

    /**
     * Object to string conversion
     *
     * @return string
     */
    public function __toString() :string {
        $valueWidth = max(strlen($this->name), strlen("Wizard"));
        return implode(PHP_EOL, [
            "-----------------".str_repeat("-", $valueWidth) . "--",
            "| Name         | ".str_pad($this->name, $valueWidth, " ") . " |",
            "| Class        | ".str_pad("Wizard", $valueWidth, " ") . " |",
            "-----------------".str_repeat("-", $valueWidth) . "--",
            "| Damage       | ".str_pad($this->damage, $valueWidth, " ") . " |",
            "| Health       | ".str_pad($this->health, $valueWidth, " ") . " |",
            "| Initiative   | ".str_pad($this->initiative, $valueWidth, " ") . " |",
            "| Magic damage | ".str_pad($this->magicdamage, $valueWidth, " ") . " |",
            "-----------------".str_repeat("-", $valueWidth) . "--",
        ]);
    }

    /**
     * Getter for magicdamage property
     *
     * @return int
     */
    public function getMagicDamage() :int {
        $magicdamage = $this->magicdamage;
        $this->magicdamage = round($this->magicdamage / 2);
        return $magicdamage;
    }

    /**
     * Return the current total attack value
     *
     * @return int
     */
    public function getAttackValue() :int {
        $total = $this->getMagicDamage() + parent::getAttackValue();
        return $total;
    }

    /**
     * Computes the attack value and attacks the given opponent
     *
     * @param Character $opponent The opponent being attacked
     */
    public function attack(Character $opponent) {
        $damage = $this->getDamage();
        $magicdamage = $this->getMagicDamage();
        $total = $damage + $magicdamage;
        println(sprintf(
            "\t%s attacks for %d damage (%d base + %d magic damage)",
            $this->getName(),
            $total,
            $damage,
            $magicdamage
        ));
        $opponent->defend($total);
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
        // Random enemy
        return $enemies[array_rand($enemies)];
    }
}