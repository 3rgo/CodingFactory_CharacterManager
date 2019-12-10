<?php

require_once "Character.php";

interface FighterInterface {

    /**
     * Function called when a character attack another
     *
     * @param Character $opponent The opponent being attacked
     */
    public function attack(Character $opponent);

    /**
     * Function called when a character is being attacked by another
     *
     * @param int $incomingDamage The damage dealt by the opponent
     */
    public function defend(int $incomingDamage);

    /**
     * Function called to select an enemy
     *
     * @param array $enemies Array containing all the enemy characters
     *
     * @return Character Targeted enemy
     */
    public function targetEnemy(array $enemies) :Character;

    /**
     * Function called to heal self or an ally
     *
     * @param array $allies Array containing all allies (including self)
     */
    public function healAlly(array $allies);
}