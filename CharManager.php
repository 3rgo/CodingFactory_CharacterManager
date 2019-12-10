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
        $this->characterList = [];
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
                            break 2;
                        case "show":
                            $this->showCharacter();
                            break 2;
                        case "new":
                            $this->newCharacter();
                            break 2;
                        case "delete":
                            $this->deleteCharacter();
                            break 2;
                        case "fight":
                            $this->fight();
                            break 2;
                        case "teamfight":
                            $this->teamfight();
                            break 2;
                        case "help":
                            $this->help();
                            break 2;
                        case "exit":
                            println("Bye !");
                            return;
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
            $cls = "Character";
        }

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
        println("Are you sure you want to delete the character " . $char["name"] . "?");
        echo "Type [yes] to confirm : ";
        $confirm = trim(fgets(STDIN));
        if($confirm === "yes"){
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

        $chars = [
            $this->summon($char1-1, 32),
            $this->summon($char2-1, 34)
        ];


        usort($chars, function($a, $b){
            return $b->getInitiative() <=> $a->getInitiative();
        });
        println("Starting fight between ".$chars[0]->getName()." and " . $chars[1]->getName());
        println($chars[0]->getName()." will attack first because of higher initiative");
        $turn = 0;
        do {
            println("TURN #" . ++$turn . " : ");
            $chars[0]->attack($chars[1]);
            if($chars[1]->getHealth() <= 0){
                println(sprintf(
                    "\n%s is dead. %s has won !\n\n",
                    $chars[1]->getName(),
                    $chars[0]->getName(),
                ));
            } else {
                $chars[0]->healAlly([$chars[0]]);
                $chars[1]->attack($chars[0]);
                if($chars[0]->getHealth() <= 0){
                    println(sprintf(
                        "\n%s is dead. %s has won !\n\n",
                        $chars[0]->getName(),
                        $chars[1]->getName(),
                    ));
                } else {
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
        println("How many characters par team ? ");
        $teamSize = intval(trim(fgets(STDIN)));
        if(count($this->characterList) < $teamSize*2){
            throw new \Exception("Not enough characters to fight");
        }
        $this->header();
        $chars = [];
        $i = 1;
        do {
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

        $colors = array_merge(
            array_fill(0, $teamSize, 32),
            array_fill(0, $teamSize, 34),
        );

        $chars = array_map(function($charIndex, $color){
            return $this->summon($charIndex-1, $color);
        }, $chars, $colors);

        $chars = array_chunk($chars, $teamSize);
        $chars = array_combine(["A", "B"], $chars);
        uasort($chars, function($a, $b){
            $initA = array_map(function($c){ return $c->getInitiative(); }, $a);
            $initB = array_map(function($c){ return $c->getInitiative(); }, $b);
            return array_sum($initB) <=> array_sum($initA);
        });

        println("Starting teamfight :");
        println("Team A : " . implode(' + ', array_map(function($c){ return $c->getName(); }, $chars["A"])));
        println("Team B : " . implode(' + ', array_map(function($c){ return $c->getName(); }, $chars["B"])));
        list($firstTeam, $secondTeam) = array_keys($chars);
        println("Team $firstTeam will attack first because of higher initiative");
        $turn = 0;
        while(true) {
            println("TURN #" . ++$turn . " : ");
            foreach($chars[$firstTeam] as $char){
                $target = $char->targetEnemy($chars[$secondTeam]);
                $char->attack($target);
                if($target->getHealth() <= 0){
                    println("\t\t".$target->getName() . " is dead");
                    $targetIndex = array_search($target, $chars[$secondTeam]);
                    unset($chars[$secondTeam][$targetIndex]);
                    if(empty($chars[$secondTeam])){
                        println(sprintf(
                            "\nAll members of team %s are dead. Team %s has won !\n\n",
                            $secondTeam,
                            $firstTeam,
                        ));
                        break 2;
                    }
                }
                $char->healAlly($chars[$firstTeam]);
            }
            foreach($chars[$secondTeam] as $char){
                $target = $char->targetEnemy($chars[$firstTeam]);
                $char->attack($target);
                if($target->getHealth() <= 0){
                    println("\t\t".$target->getName() . " is dead");
                    $targetIndex = array_search($target, $chars[$firstTeam]);
                    unset($chars[$firstTeam][$targetIndex]);
                    if(empty($chars[$firstTeam])){
                        println(sprintf(
                            "\nAll members of team %s are dead. Team %s has won !\n\n",
                            $firstTeam,
                            $secondTeam,
                        ));
                        break 2;
                    }
                }
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