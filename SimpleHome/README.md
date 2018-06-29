# SimpleHome

_**The easiest plugin to create and edit virtual homes.**_

### Commands:

- Sethome Command:
    - Create home
    - permission: sh.cmd.sethome (no-op)
    - usage: /sethome <home>
- Home Command:
    - Displays list of homes or teleport to home
    - permission: sh.cmd.home (no-op)
    - usage: /home or /home <home>
- Delhome Command:
    - Remove home
    - permission: sh.cmd.delhome (no-op)
    - usage: /delhome <home>

### Permissions:

- sh.cmd:
    - permission for all commands
    - default: TRUE

### API:

- get SimpleHome instance

`$simpleHome = SimpleHome::getInstance();`


- get the player's home

`$home = $simpleHome->getPlayerHome(Player $player, string $homeName);`

- teleport player to his home

`$home->teleport($player);`

- create new home

`$simpleHome->setPlayerHome($player, $newHome = new Home($player, [$x, $y, $z, $levelName], $homeName));`

- delete home

`$simpleHome->deleteHome($player, $newHome);`

- get home list

`$simpleHome->getHomeList();`