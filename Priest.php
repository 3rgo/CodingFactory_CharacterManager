<?php

require_once "Character.php";

class Priest extends Character {

    /**
     * Gathers the informations required for the character creation by asking the user.
     *
     * @static
     * @return array Gathered data
     **/
    public static function askForData() :array {
        $data = parent::askForData();
        $data["class"] = "Priest";

        println("Heal amount :");
        $data["heal"] = intval(trim(fgets(STDIN)));

        return $data;
    }

    /**
     * @var int Heal amount.
     */
    protected $heal;

    /**
     * Constructor
     *
     * @param string $name       Character name.
     * @param int    $damage     Damage dealt per attack.
     * @param int    $health     Current health points.
     * @param int    $initiative Initiative points.
     * @param int    $heal       Heal points.
     *
     */
    public function __construct($name, $damage, $health, $initiative, $heal){
        parent::__construct($name, $damage, $health, $initiative);
        $this->heal = $heal;
        $this->maxhealth = $health;
    }

    /**
     * Object to string conversion
     *
     * @return string
     */
    public function __toString() :string {
        $valueWidth = max(strlen($this->name), strlen("Priest"));
        return implode(PHP_EOL, [
            "---------------".str_repeat("-", $valueWidth) . "--",
            "| Name       | ".str_pad($this->name, $valueWidth, " ") . " |",
            "| Class      | ".str_pad("Priest", $valueWidth, " ") . " |",
            "---------------".str_repeat("-", $valueWidth) . "--",
            "| Damage     | ".str_pad($this->damage, $valueWidth, " ") . " |",
            "| Health     | ".str_pad($this->health, $valueWidth, " ") . " |",
            "| Initiative | ".str_pad($this->initiative, $valueWidth, " ") . " |",
            "| Heal       | ".str_pad($this->heal, $valueWidth, " ") . " |",
            "---------------".str_repeat("-", $valueWidth) . "--",
        ]);
    }

    /**
     * Getter for heal property
     *
     * @return int
     */
    public function getHeal() :int {
        return $this->heal;
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
     * Heal self or an ally
     *
     * @param array $allies Array containing all allies (including self)
     */
    public function healAlly(array $allies) {
        // Get the ally with the lowest HP
        $lowestHp = PHP_INT_MAX;
        $target = null;
        foreach($allies as $ally){
            if($ally->getHealth() < $lowestHp){
                $lowestHp = $ally->getHealth();
                $target = $ally;
            }
        }

        // Compute heal amount (to ensure we don't heal more than the max health)
        $healAmount = min($target->getMaxHealth() - $target->getHealth(), $this->heal);
        if($healAmount > 0) {
            $target->setHealth($target->getHealth() + $healAmount);

            println(sprintf(
                "\t%s heals %s for %d health, now has %d health points",
                $this->getName(),
                ($target->getName() === $this->getName() ? "self" : $target->getName()),
                $healAmount,
                $this->health
            ));
        }
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