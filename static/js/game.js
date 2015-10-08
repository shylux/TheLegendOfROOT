var TLOR = {};
/***
 * Parameter:
 * jq_element: The jQuery element in which the dungeon is displayed.
 * initial_dungeon_data: The json file with the terrain and entities.
  ***/

TLOR.setup = function(jq_element, initial_dungeon_data) {
  TLOR.el = jq_element;
  TLOR = $.extend(TLOR, initial_dungeon_data);

  TLOR.el.append('<table cellspacing="0"><tbody></tbody></table>');
  if ('terrain' in TLOR) // create new terrain?
    TLOR.buildTerrain(TLOR.terrain);
  else
    TLOR.newTerrain();

  // resize
  var table = TLOR.el.find('table');
  table.height(table.width()/TLOR.width*TLOR.height);
  //table.find('tr').height(table.height()/TLOR.height);
};
TLOR.buildTerrain = function(terrain_data) {
  var tbody = TLOR.el.find('tbody');
  for (y = 0; y < TLOR.height; y++) {
    var row = $('<tr>');
    for (x = 0; x < TLOR.width; x++) {
      var cell = $('<td><img></td>');
      cell.attr('data-y', y).attr('data-x', x);
      cell.find('img').attr('data-terr', terrain_data[y][x]);
      row.append(cell);
    }
    tbody.append(row);
  }
};
TLOR.newTerrain = function() {
  var tbody = TLOR.el.find('tbody');
  for (y = 0; y < TLOR.height; y++) {
    var row = $('<tr>');
    for (x = 0; x < TLOR.width; x++) {
      var cell = $('<td><img></td>');
      cell.attr('data-y', y).attr('data-x', x);
      cell.find('img').attr('data-terr', 0);
      row.append(cell);
    }
    tbody.append(row);
  }
};

TLOR.getCell = function(x, y) {
  return TLOR.el.find(sprintf('td[data-x="%i"][data-y="%i"]', x, y));
};
TLOR.getTerrainFor = function(x, y) {
  return parseInt(TLOR.getCell(x, y).find('img').attr('data-terr'));
};
TLOR.newTile = function (x, y) {
  var tile = $('<img>');
  TLOR.getCell(x, y).append(tile);
  return tile;
};

TLOR.generateTerrainJSON = function() {
  var terr = new Array(TLOR.height);
  for (y = 0; y < TLOR.height; y++) {
    var row = new Array(TLOR.width);
    for (x = 0; x < TLOR.width; x++) {
      row[x] = TLOR.getTerrainFor(x, y);
    }
    terr[y] = row;
  }
  return JSON.stringify(terr);
};

var controls = `
  <div id="game-controls">
    <button>Up</button>
    <button>Down</button>
    <button>Left</button>
    <button>Right</button>
  </div>
`;
TLOR.play = function() {
  // setup entities
  for (var i in TLOR.entities) {
    var entity = TLOR.entities[i];
    if (entity.x && entity.y) {
      var tile = TLOR.newTile(entity.x, entity.y);
      tile.addClass(entity.type);
      if (entity.type == "movePlayer")
        tile.addClass(entity.direction);
    }
  }
  // place player
  TLOR.newTile(TLOR.stats.x, TLOR.stats.y).addClass('player');

  // add controls
  TLOR.el.append(controls);
  $(TLOR.el).find('#game-controls button').on('click', function() {
    var action = $(this).text().toLowerCase();
    $.getJSON('api.php', {id:TLOR.id, action: action}, TLOR.handleActions);
  });
  $(document).on('keydown keypress', function(e) {
    var action;
    switch (e.keyCode) {
      case 37:
        action = 'left';
        break;
      case 38:
        action = 'up';
        break;
      case 39:
        action = 'right';
        break;
      case 40:
        action = 'down';
        break;
      default:
        return;
    }
    e.preventDefault();

    if (TLOR.requestInProgress || TLOR.actionQueue.length > 0) return; // avoid walking too far
    $.getJSON('api.php', {id:TLOR.id, action: action}, TLOR.handleActions);
  });

  $(document).ajaxStart(function() {
    TLOR.requestInProgress = true;
  });
  $(document).ajaxStop(function() {
    TLOR.requestInProgress = false;
  });
};

TLOR.requestInProgress = false;

TLOR.actionQueue = [];
// executes commands given by server
TLOR.handleActions = function(commands) {
  console.log(commands);
  // start executing if queue is not running
  var startExecuting = (TLOR.actionQueue.length == 0);
  TLOR.actionQueue = TLOR.actionQueue.concat(commands);
  if (startExecuting) TLOR.executeActions();
}
TLOR.executeActions = function() {
  command = TLOR.actionQueue.shift();
  switch (command.action) {
    case "movePlayer":
      TLOR.el.find('.player').remove();
      TLOR.newTile(command.x, command.y).addClass('player');
      break;
    case "message":
      alert(command.message);
      break;
    default:
      console.error(sprintf("Unknown action: %s", command.action));
  }
  if (TLOR.actionQueue.length > 0) setTimeout(TLOR.executeActions, 400);
}
