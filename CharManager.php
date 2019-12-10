<?php

require_once "Character.php";
require_once "Warrior.php";
require_once "Wizard.php";
require_once "Thief.php";
require_once "Priest.php";


class CharManager {

    /**
     * @var array List of created characters
     */
    private $characterList;

    /**
     * Constructor
     */
    public function __construct(){
        // Init character list
        $this->characterList = [];

        // Look for a save file. If it exists, read it and decode it
        if(file_exists('./save.json')){
            $json = file_get_contents('./save.json');
            $decoded = json_decode($json, true);
            if($decoded !== null && is_array($decoded)){
                $this->characterList = $decoded;
            }
        }
    }

    /**
     * Destructor
     */
    public function __destruct(){
        // On program close, save character list to save file
        file_put_contents('save.json', json_encode($this->characterList));
    }

    /**
     * Displays application header
     */
    private function header() {
        cls();
        println("------------------------------------------");
        println("------------ CHARACTER MANAGER------------");
        println("------------------------------------------");
    }

    /**
     * Main application loop
     */
    public function start() {
        while(true){
            $this->header();
            while(true) {
                echo("What do you want to do ? ");
                $input = trim(fgets(STDIN));
                try {
                    switch($input){
                        case "list":
                            $this->listCharacters();
                            break 2; // Breaks the switch and the inner while
                        case "show":
                            $this->showCharacter();
                            break 2; // Breaks the switch and the inner while
                        case "new":
                            $this->newCharacter();
                            break 2; // Breaks the switch and the inner while
                        case "delete":
                            $this->deleteCharacter();
                            break 2; // Breaks the switch and the inner while
                        case "fight":
                            $this->fight();
                            break 2; // Breaks the switch and the inner while
                        case "teamfight":
                            $this->teamfight();
                            break 2; // Breaks the switch and the inner while
                        case "help":
                            $this->help();
                            break 2; // Breaks the switch and the inner while
                        case "exit":
                            println("Bye !");
                            return; // Breaks all loops and exits the program
                        default:
                            println("Unknown command, try again !");
                    }
                } catch(\Exception $e) {
                    println("An error occured : " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Summons a character based on its index in the list.
     *
     * @param int $characterId Character index.
     * @param int $color Color number for name display.
     *
     * @return Character
     **/
    private function summon(int $characterId, int $color = null)
    {
        $c = $this->characterList[$characterId];
        // Class property does not exist if character is basic (no specific class)
        switch($c["class"] ?? ""){
            case "Warrior":
                $char = new Warrior($c["name"], $c["damage"], $c["health"], $c["initiative"], $c["shield"]);
                break;
            case "Wizard":
                $char = new Wizard($c["name"], $c["damage"], $c["health"], $c["initiative"], $c["magicdamage"]);
                break;
            case "Thief":
                $char = new Thief($c["name"], $c["damage"], $c["health"], $c["initiative"], $c["dodgechance"], $c["criticalchance"]);
                break;
            case "Priest":
                $char = new Priest($c["name"], $c["damage"], $c["health"], $c["initiative"], $c["heal"]);
                break;
            default:
                $char = new Character($c["name"], $c["damage"], $c["health"], $c["initiative"]);
                break;
        }
        $char->setColor($color);
        return $char;
    }

    /**
     * Displays the list of characters
     */
    private function listCharacters() {
        if(empty($this->characterList)){
            throw new \Exception("No characters to display");
        }
        $this->header();
        println("Character list :");
        foreach($this->characterList as $index => $character){
            echo sprintf(
                "\t%s\t%s\t%s".PHP_EOL,
                $index+1,
                $character["name"],
                array_key_exists("class", $character) ? "(" . $character["class"] . ")" : ""
            );
        }
        println("Press Enter to return to the menu");
        fgets(STDIN);
    }

    /**
     * Asks for a character number and displays its data
     */
    private function showCharacter() {
        if(empty($this->characterList)){
            throw new \Exception("No characters to display");
        }
        $this->header();
        println("Character number :");
        $characterId = intval(trim(fgets(STDIN)));
        if(!in_array($characterId, range(1, count($this->characterList)))){
            throw new \Exception("Given number ($characterId) matches no character");
        }

        println($this->summon($characterId-1));
        println("");
        println("Press Enter to return to the menu");
        fgets(STDIN);
    }

    /**
     * Ask for data to create a character
     */
    private function newCharacter() {
        $this->header();

        println("What class do you want to use (Warrior, Wizard, Thief, Priest or empty) : ");
        $cls = trim(fgets(STDIN));
        if(!in_array($cls, ["Warrior", "Wizard", "Thief", "Priest"])){
            // If given class does not match any known ones, defaults to basic Character
            $cls = "Character";
        }

        // Delegates the creation form to the class
        $data = $cls::askForData();
        $index = array_push($this->characterList, $data);

        println("Character #$index ({$data["name"]}) has been created !");
        println("Press Enter to return to the menu");
        fgets(STDIN);
    }

    /**
     * Deletes a character
     */
    private function deleteCharacter() {
        if(empty($this->characterList)){
            throw new \Exception("No characters to delete");
        }
        $this->header();
        println("Character number :");
        $characterId = intval(trim(fgets(STDIN)));
        if(!in_array($characterId, range(1, count($this->characterList)))){
            throw new \Exception("Given number ($characterId) matches no character");
        }
        $char = $this->characterList[$characterId-1];
        // Ask confirmation
        println("Are you sure you want to delete the character " . $char["name"] . "?");
        echo "Type [yes] to confirm : ";
        $confirm = trim(fgets(STDIN));
        if($confirm === "yes"){
            // Removes entry from array and resets the keys
            unset($this->characterList[$characterId-1]);
            $this->characterList = array_values($this->characterList);
            println("Character #$characterId deleted !");
        } else {
            println("Cancelled.");
        }
        println("Press Enter to return to the menu");
        fgets(STDIN);
    }

    /**
     * Starts a fight
     */
    private function fight() {
        if(count($this->characterList) < 2){
            throw new \Exception("Not enough characters to fight");
        }
        $this->header();
        println("1st character number :");
        $char1 = intval(trim(fgets(STDIN)));
        if(!in_array($char1, range(1, count($this->characterList)))){
            throw new \Exception("Given number ($char1) matches no character");
        }
        println("2nd character number :");
        $char2 = intval(trim(fgets(STDIN)));
        if(!in_array($char2, range(1, count($this->characterList)))){
            throw new \Exception("Given number ($char2) matches no character");
        }
        if($char2 == $char1){
            throw new \Exception("Please select 2 different characters");
        }

        // Create characters instances
        $chars = [
            $this->summon($char1-1, 32),
            $this->summon($char2-1, 34)
        ];

        // Sort by initiative
        usort($chars, function($a, $b){
            return $b->getInitiative() <=> $a->getInitiative();
        });
        println("Starting fight between ".$chars[0]->getName()." and " . $chars[1]->getName());
        println($chars[0]->getName()." will attack first because of higher initiative");
        $turn = 0;
        // Repeat until a characters wins by killing the other
        do {
            println("TURN #" . ++$turn . " : ");
            $chars[0]->attack($chars[1]);
            // checks if we killed the other character
            if($chars[1]->getHealth() <= 0){
                println(sprintf(
                    "\n%s is dead. %s has won !\n\n",
                    $chars[1]->getName(),
                    $chars[0]->getName(),
                ));
            } else {
                // Do the healing (will do nothing if the character is not a priest)
                $chars[0]->healAlly([$chars[0]]);
                $chars[1]->attack($chars[0]);
                // checks if we killed the other character
                if($chars[0]->getHealth() <= 0){
                    println(sprintf(
                        "\n%s is dead. %s has won !\n\n",
                        $chars[0]->getName(),
                        $chars[1]->getName(),
                    ));
                } else {
                    // Do the healing (will do nothing if the character is not a priest)
                    $chars[1]->healAlly([$chars[1]]);
                }
            }
        } while($chars[0]->getHealth() > 0 && $chars[1]->getHealth() > 0);

        println("Press Enter to return to the menu");
        fgets(STDIN);
    }

    /**
     * Starts a team fight (2v2)
     */
    private function teamfight() {
        // Ask the number of characters per team
        println("How many characters par team ? ");
        $teamSize = intval(trim(fgets(STDIN)));
        // Check that we have enough characters to do the fight
        if(count($this->characterList) < $teamSize*2){
            throw new \Exception("Not enough characters to fight");
        }
        $this->header();
        $chars = [];
        $i = 1;
        do {
            // Ask for the character number, and affects it to the team
            // We do team A first, then team B
            $team = $i <= 2 ? "A" : "B";
            println("TEAM $team - Character $i number :");
            try {
                $c = intval(trim(fgets(STDIN)));
                if(!in_array($c, range(1, count($this->characterList)))){
                    throw new \Exception("Given number ($c) matches no character");
                }
                if(in_array($c, $chars)){
                    throw new \Exception("Character $i was already selected. Please select unique characters");
                }
                $chars[] = $c;
                $i++;
            } catch (\Exception $e) {
                println("Error : " . $e->getMessage());
            }
        } while(count($chars) < $teamSize*2);

        // Team A will be green (32) and Team B will be blue (34)
        $colors = array_merge(
            array_fill(0, $teamSize, 32),
            array_fill(0, $teamSize, 34),
        );

        // Summon the characters with the color of their team
        $chars = array_map(function($charIndex, $color){
            return $this->summon($charIndex-1, $color);
        }, $chars, $colors);

        // Split the characters array per team, and use the letter as key
        $chars = array_chunk($chars, $teamSize);
        $chars = array_combine(["A", "B"], $chars);

        // Sort the teams by combined initiative value
        uasort($chars, function($a, $b){
            $initA = array_map(function($c){ return $c->getInitiative(); }, $a);
            $initB = array_map(function($c){ return $c->getInitiative(); }, $b);
            return array_sum($initB) <=> array_sum($initA);
        });

        // Displays the teams
        println("Starting teamfight :");
        println("Team A : " . implode(' + ', array_map(function($c){ return $c->getName(); }, $chars["A"])));
        println("Team B : " . implode(' + ', array_map(function($c){ return $c->getName(); }, $chars["B"])));
        list($firstTeam, $secondTeam) = array_keys($chars);
        println("Team $firstTeam will attack first because of higher initiative");
        $turn = 0;
        while(true) {
            println("TURN #" . ++$turn . " : ");
            foreach($chars[$firstTeam] as $char){
                // For each character of the team, choose a target and attack
                $target = $char->targetEnemy($chars[$secondTeam]);
                $char->attack($target);
                // Checks if the target died
                if($target->getHealth() <= 0){
                    println("\t\t".$target->getName() . " is dead");
                    // If it did, remove it from the opponent team
                    $targetIndex = array_search($target, $chars[$secondTeam]);
                    unset($chars[$secondTeam][$targetIndex]);
                    // If no opponents are alive, end the fight
                    if(empty($chars[$secondTeam])){
                        println(sprintf(
                            "\nAll members of team %s are dead. Team %s has won !\n\n",
                            $secondTeam,
                            $firstTeam,
                        ));
                        break 2;
                    }
                }
                // Do the healing
                $char->healAlly($chars[$firstTeam]);
            }
            foreach($chars[$secondTeam] as $char){
                // For each character of the team, choose a target and attack
                $target = $char->targetEnemy($chars[$firstTeam]);
                $char->attack($target);
                // Checks if the target died
                if($target->getHealth() <= 0){
                    println("\t\t".$target->getName() . " is dead");
                    // If it did, remove it from the opponent team
                    $targetIndex = array_search($target, $chars[$firstTeam]);
                    unset($chars[$firstTeam][$targetIndex]);
                    // If no opponents are alive, end the fight
                    if(empty($chars[$firstTeam])){
                        println(sprintf(
                            "\nAll members of team %s are dead. Team %s has won !\n\n",
                            $firstTeam,
                            $secondTeam,
                        ));
                        break 2;
                    }
                }
                // Do the healing
                $char->healAlly($chars[$secondTeam]);
            }
        }

        println("Press Enter to return to the menu");
        fgets(STDIN);
    }

    private function help() {
        $this->header();
        println("Available commands : ");
        println("\tlist\t\t=> List your characters");
        println("\tshow\t\t=> Show a character's informations");
        println("\tnew\t\t=> Create a new character");
        println("\tdelete\t\t=> Delete a character");
        println("\tfight\t\t=> Start a fight");
        println("\tteamfight\t=> Start a team fight");
        println("\thelp\t\t=> Show this help");
        println("\texit, quit\t=> Exit the application");
        println("");
        println("Press Enter to return to the menu");
        fgets(STDIN);
    }
}