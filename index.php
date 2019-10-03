<?php
if(isset($_GET["search"])){
    if($_GET["search"] != null){
        $search = strtolower($_GET["search"]);
    }
    else{
        $search = 1;
    }
}
else{
    $search = 1;
}
define('POKELINK',"https://pokeapi.co/api/v2/pokemon/");
$pokeApi = file_get_contents(POKELINK.$search);
$pokemon = json_decode($pokeApi, true);

$speciesApi = file_get_contents($pokemon["species"]["url"]);
$species = json_decode($speciesApi, true);

$evolutionApi = file_get_contents($species["evolution_chain"]["url"]);
$evolution = json_decode($evolutionApi, true);

$picture = $pokemon["sprites"]["front_default"];
$id = $pokemon["id"];
$name = $pokemon["forms"][0]["name"];

shuffle($pokemon["moves"]);
$allMoves = $pokemon["moves"];
$moves = array_slice($allMoves, 0, 4);

$firstSpecies = $evolution["chain"]["species"]["url"];
$firstEvoLink = str_replace("-species", "", $firstSpecies);

$firstEvoApi = file_get_contents($firstEvoLink);
$firstEvo = json_decode($firstEvoApi, true);


$secondSpecies = $evolution["chain"]["evolves_to"][0]["species"]["url"];
$secondEvoLink = str_replace("-species", "", $secondSpecies);

$secondEvoApi = file_get_contents($secondEvoLink);
$secondEvo = json_decode($secondEvoApi, true);


$thirdSpecies = $evolution["chain"]["evolves_to"][0]["evolves_to"][0]["species"]["url"];
$thirdEvoLink = str_replace("-species", "", $thirdSpecies);

$thirdEvoApi = file_get_contents($thirdEvoLink);
$thirdEvo = json_decode($thirdEvoApi, true);

if (count($moves) == 0) {
    $move1 = "";
    $move2 = "";
    $move3 = "";
    $move4 = "";
}

if (count($moves) == 1) {
    $move1 = $moves[0]["move"]["name"];
    $move2 = "";
    $move3 = "";
    $move4 = "";
}

if (count($moves) == 2) {
    $move1 = $moves[0]["move"]["name"];
    $move2 = $moves[1]["move"]["name"];
    $move3 = "";
    $move4 = "";
}

if (count($moves) == 3) {
    $move1 = $moves[0]["move"]["name"];
    $move2 = $moves[1]["move"]["name"];
    $move3 = $moves[2]["move"]["name"];
    $move4 = "";
}

if (count($moves) == 4) {
    $move1 = $moves[0]["move"]["name"];
    $move2 = $moves[1]["move"]["name"];
    $move3 = $moves[2]["move"]["name"];
    $move4 = $moves[3]["move"]["name"];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Pokedex</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" />
    <link rel="stylesheet" href="normalize.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<div id="zoinksButton"></div>
<iframe id="zoinks" class="gone zoinks" src="http://www.staggeringbeauty.com/" style="border: 1px inset #ddd" width="498" height="598"></iframe>
<div class="container">
    <img id="pokedex" src="pokedex.png" alt="PokeDex">
    <div class="picture" id="picture">
        <?php echo "<img src='".$picture."'>"; ?>
    </div>
    <div class="idnumber" id="id">
        <?php echo $id; ?>
    </div>
    <div class="cap description" id="name">
        <?php echo $name; ?>
    </div>
    <div class="cap move1" id="move1">
        <?php echo $move1; ?>
    </div>
    <div class="cap move2" id="move2">
        <?php echo $move2; ?>
    </div>
    <div class="cap move3" id="move3">
        <?php echo $move3; ?>
    </div>
    <div class="cap move4" id="move4">
        <?php echo $move4; ?>
    </div>
    <form class="search">
        <label for="search"></label><input id="search" name="search" placeholder="Pokémon name or ID" type="text">
        <div class="button"> <button type="submit" id="goSearch">Search Poké</button></div>
    </form>
    <?php
        echo "<a href='http://pokemon.local/?search=".($id-1)."'><button id='previous'>&larr;</button></a>";
        echo "<a href='http://pokemon.local/?search=".($id+1)."'><button id='next'>&rarr;</button></a>";
        echo "<a href='http://pokemon.local/?search=".($firstEvo['id'])."'><div id='first'><img alt='first evolution' src='".$firstEvo["sprites"]["front_default"]."'></div></a>";
        if ($secondEvo !== null) {
            echo "<a id='multiEvolution' href='http://pokemon.local/?search=".($secondEvo['id'])."'><div id='second'><img alt='second evolution' src='".$secondEvo["sprites"]["front_default"]."'></div></a>";
        }
        if ($thirdEvo !== null) {
            echo "<a href='http://pokemon.local/?search=".($thirdEvo['id'])."'><div id='third'><img alt='third evolution' src='".$thirdEvo["sprites"]["front_default"]."'></div></a>";
        }
    ?>
</div>
<script>
    let evolveLength = <?php echo count($evolution["chain"]["evolves_to"])?>;
    let i = 0;
    if(evolveLength > 1){
        let evolveData = <?php echo json_encode($evolution["chain"]["evolves_to"])?>;
        let evolutions = [];
        for (let x in evolveData) {
            evolutions.push(evolveData[x]);
        }
        slide = setInterval(function(){
            if(i < evolveLength-1){
                i++;
            }
            else{
                i=0;
            }
            let evoSlide = 'https://pokeapi.co/api/v2/pokemon/'+evolutions[i].species.name;
            fetch(evoSlide)
                .then(function(response) {
                    return response.json();
                })
                .then(function(secondEvo) {
                    document.getElementById("second").innerHTML = "<img alt='second evolution' src='" + secondEvo.sprites.front_default + "'>";
                    document.getElementById("multiEvolution").href = 'http://pokemon.local/?search='+evolutions[i].species.name;
                });
        }, 1500);
    }

    document.getElementById("zoinksButton").addEventListener("click", function() {
        document.getElementById("zoinks").classList.remove("gone");
    });
</script>
</body>
</html>


