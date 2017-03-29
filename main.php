<!DOCTYPE html>
<html>

<head>
	<title>sans titre</title>
	<meta charset="utf-8" />
	<meta name="generator" content="Geany 1.27" />
	<style>#zone{border:1px solid red;}</style>
</head>

<body>
  <p id="joueur"></p>
  <button onclick="nextTurn()">fin du tour</button> <br/>
  <canvas id="zone" width="1000" height="500"></canvas>
  <p id="info"></p>
</body>

<script src="model/Biomass.js" ></script>
<script src="model/Player.js" ></script>
<script src="model/Fleet.js" ></script>
<script src="model/Planet.js" ></script>
<script src="model/Galaxy.js" ></script>

<script>

  var a = new Player(new String("mi"), "blue");
  var b = new Player(new String("mi2"), "green");
  var c = new Planet("azer1", 5, 150, 50, 50, a);
  var d = new Planet("azer2", 10, 100, 500, 400, a);
  var e = new Planet("azer3", 0, 100, 200, 300, b);
  var f = new Planet("azer4", 3, 100, 600, 200, b);
  
  a.addBiomass(c);
  a.addBiomass(d);
  b.addBiomass(e);
  b.addBiomass(f);
  
  var maZone = document.getElementById("zone");
  var info = document.getElementById("info");
  
  var g = new Galaxy([c, d, e, f, new Planet("azer5", 5, 100, 600, 100), new Planet("azer6", 5, 100, 700, 400, null)], 
    [a, b], maZone);
  document.getElementById("joueur").innerHTML = g.getCurrentPlayer().name;
    
  function tmp() {document.getElementById("info").innerHTML = "salut";}
  
  
  function findPos(obj){
    var curleft = 0;
    var curtop = 0;
    if (obj.offsetParent) {
        curleft = obj.offsetLeft
        curtop = obj.offsetTop
        while (obj = obj.offsetParent) {
            curleft += obj.offsetLeft
            curtop += obj.offsetTop
        }
    }
    return [curleft,curtop];
  }
  
  
  function firstClick(evt) {
    var zonePos = findPos(maZone);
    var tab = g.getCurrentPlayer().biomass;
    var i = 0;
    while (i < tab.length) {
      if (tab[i] instanceof Planet && tab[i].isOn(evt.clientX - zonePos[0], evt.clientY - zonePos[1])) {
        function secondClick(evt) {
          var j = 0;
          while (j < g.planets.length) {
            if (g.planets[j].isOn(evt.clientX - zonePos[0], evt.clientY- zonePos[1])) {
              var rep = prompt("Quel est le nombre de vaisseau que vous souhaitez envoyer.");
              if (rep === null) {
                break;;
              }
              if (isNaN(rep)) {
                info.innerHTML += rep + " ne peut pas représenter la puissance d'une flotte.<br />";
              } else {
                var str;
                if (rep == "1") {
                  str = "Un de vos vaisseaux";
                } else {
                  str = "Une flotte de " + rep + " de vos vaisseaux"
                }
                if (g.planets[j].faction === g.getCurrentPlayer()) {
                  info.innerHTML += str + " a été envoyé" + (rep == "1"? "" : "e") + " en renfort.<br />";
                } else {
                  info.innerHTML += str + " est parti" + (rep == "1"? "" : "e") + " soummettre vos ennemis.<br />";
                }
                tab[i].launch(parseInt(rep), g.planets[j]);
              }
              break;
            }
            ++j;
          }
          maZone.removeEventListener("click", secondClick);
          maZone.addEventListener("click", firstClick);
        }
        
        maZone.removeEventListener("click", firstClick);
        maZone.addEventListener("click", secondClick);
        break;
      }
      ++i;
    }
  }
  
  document.addEventListener(Planet.CHANGE_FACTION_EVENT, function (evt) {
    if (evt.detail.src.faction === null) {
      document.getElementById("info").innerHTML += evt.detail.src.name + " est devenue neutre. <br/>";
    } else {
      document.getElementById("info").innerHTML += evt.detail.src.name + " a été prise par " + evt.detail.src.faction.name + ".<br/>";
    }
  });
  maZone.addEventListener("click", firstClick);
  
  function nextTurn() {
    g.nextTurn();
    g.draw();
    document.getElementById("joueur").innerHTML = g.getCurrentPlayer().name;
  }
</script>
