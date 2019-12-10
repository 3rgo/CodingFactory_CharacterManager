<?php

require_once "Character.php";

class Thief extends Character {

    /**
     * Gathers the informations required for the character creation by asking the user.
     *
     * @static
     * @return array Gathered data
     **/
    public static function askForData() :array {
        $data = parent::askForData();
        $data["class"] = "Thief";

        println("Dodge chance [0-100] :");
        $input = intval(trim(fgets(STDIN)));
        if($input < 0 || $input > 100){
            throw new \Exception("Invalid dodge chance value");
        }
        $data["dodgechance"] = $input;

        println("Critical chance [0-100] :");
        $input = intval(trim(fgets(STDIN)));
        if($input < 0 || $input > 100){
            throw new \Exception("Invalid critical chance value");
        }
        $data["criticalchance"] = $input;

        return $data;
    }

    /**
     * @var int Dodge chance.
     */
    protected $dodgechance;

    /**
     * @var int Critical chance.
     */
    protected $criticalchance;

    /**
     * Constructor
     *
     * @param string $name           Character name.
     * @param int    $damage         Damage dealt per attack.
     * @param int    $health         Current health points.
     * @param int    $initiative     Initiative points.
     * @param int    $dodgechance    Dodge chance.
     * @param int    $criticalchance Critical chance.
     *
     */
    public function __construct($name, $damage, $health, $initiative, $dodgechance, $criticalchance){
        parent::__construct($name, $damage, $health, $initiative);
        $this->dodgechance    = $dodgechance;
        $this->criticalchance = $criticalchance;
    }

    /**
     * Object to string conversion
     *
     * @return string
     */
    public function __toString() :string {
        $valueWidth = max(strlen($this->name), strlen("Thief"));
        return implode(PHP_EOL, [
            "-----------------".str_repeat("-", $valueWidth) . "--",
            "| Name         | ".str_pad($this->name, $valueWidth, " ") . " |",
            "| Class        | ".str_pad("Thief", $valueWidth, " ") . " |",
            "-----------------".str_repeat("-", $valueWidth) . "--",
            "| Damage       | ".str_pad($this->damage, $valueWidth, " ") . " |",
            "| Health       | ".str_pad($this->health, $valueWidth, " ") . " |",
            "| Initiative   | ".str_pad($this->initiative, $valueWidth, " ") . " |",
            "| Dodge (%)    | ".str_pad($this->dodgechance, $valueWidth, " ") . " |",
            "| Critical (%) | ".str_pad($this->criticalchance, $valueWidth, " ") . " |",
            "----------------".str_repeat("-", $valueWidth) . "--",
        ]);
    }

    /**
     * Getter for dodgechance property
     *
     * @return int
     */
    public function getDodgeChance() :int {
        return $this->dodgechance;
    }

    /**
     * Getter for criticalchance property
     *
     * @return int
     */
    public function getCriticalChance() :int {
        return $this->criticalchance;
    }

    /**
     * Diminishes current health with the given amount (except if dodge), and returns the new value
     *
     * @param int $amount Amount of the hit
     *
     * @return int Current health after being hit
     */
    public function hit(int $amount) :int {
        if($this->getDodgeChance() < random_int(1, 100)){
            // Dodge successful
            return $this->getHealth();
        }
        return parent::hit($amount);
    }


    /**
     * Computes the attack value and attacks the given opponent
     *
     * @param Character $opponent The opponent being attacked
     */
    public function attack(Character $opponent) {
        $damage = $this->getDamage();
        // Compute critical hits
        $isCritical = $this->getCriticalChance() < random_int(1, 100);
        if($isCritical){
            $damage *= 2;
        }
        println(sprintf(
            "\t%s attacks for %d damage%s",
            $this->getName(),
            $damage,
            ($isCritical ? " (CRITICAL !)" : "")
        ));
        $opponent->defend($damage);
    }

    /**
     * Computes new health points
     *
     * @param int $incomingDamage The damage dealt by the opponent
     */
    public function defend(int $incomingDamage) {
        // Compute dodge chance
        $isDodged = $this->getDodgeChance() < random_int(1, 100);
        if($isDodged){
            println(sprintf(
                "\t%s dodges the attack, still has %d health left",
                $this->getName(),
                $this->health
            ));
        } else {
            $this->health -= $incomingDamage;
            println(sprintf(
                "\t%s has %d health left",
                $this->getName(),
                max(0, $this->health)
            ));
        }
    }

    /**
     * Chooses the enemy to target
     *
     * @param array $enemies Array containing all the enemy characters
     *
     * @return Character Targeted enemy
     */
    public function targetEnemy(array $enemies) :Character {
        // Get the enemy with the lowest HP
        $lowestHp = PHP_INT_MAX;
        $target = null;
        foreach($enemies as $enemy){
            if($enemy->getHealth() < $lowestHp){
                $lowestHp = $enemy->getHealth();
                $target = $enemy;
            }
        }
        return $target;
    }
}