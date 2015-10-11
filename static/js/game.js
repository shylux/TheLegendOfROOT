var TLOR = {};
var TERRAIN = {
  GRASS: 0, ROCK: 1, WATER: 2, BRIDGE: 3, TALL_GRASS: 4, ROAD: 5, SAND: 6
};
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
TLOR.isHidden = function(entity) {
  if (["message", "monster"].indexOf(entity.type) > -1) return true;
  if (TLOR.getTerrainFor(entity.x, entity.y) == TERRAIN.TALL_GRASS) return true;
  return false;
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
    if (entity.x && entity.y && !TLOR.isHidden(entity)) {
      var tile = TLOR.newTile(entity.x, entity.y);
      tile.addClass(entity.type);
      if (entity.type == "movePlayer")
        tile.addClass(entity.direction);
    }
  }
  // setup dialog
  TLOR.dialog = $('<div id="dialog">This is a message</div>');
  TLOR.el.append(TLOR.dialog);
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
      case 13:
        if (TLOR.dialog.is(':visible')) {
          TLOR.confirmDialog();
          break;
        }
        return;
      default:
        return;
    }
    e.preventDefault();

    if (TLOR.requestInProgress || TLOR.actionQueue.length > 0 || TLOR.dialog.is(':visible')) return; // avoid walking too far
    $.getJSON('api.php', {id:TLOR.id, action: action}, TLOR.handleActions);
  });

  $(document).ajaxStart(function() {
    TLOR.requestInProgress = true;
  });
  $(document).ajaxStop(function() {
    TLOR.requestInProgress = false;
  });

  $(document).click(TLOR.confirmDialog);
};

TLOR.requestInProgress = false;

TLOR.actionQueue = [];
// executes commands given by server
TLOR.handleActions = function(commands) {
  // start executing if queue is not running
  var startExecuting = (TLOR.actionQueue.length == 0);
  TLOR.actionQueue = TLOR.actionQueue.concat(commands);
  if (startExecuting) TLOR.executeActions();
}
TLOR.executeActions = function() {
  if (TLOR.actionQueue.length == 0) return;
  command = TLOR.actionQueue.shift();
  switch (command.action) {
    case "movePlayer":
      TLOR.el.find('.player').remove();
      TLOR.newTile(command.x, command.y).addClass('player');
      break;
    case "message":
      TLOR.showMessage(command.message);
      return;
    case "refreshBrowser":
      location.reload();
      return;
    default:
      console.error(sprintf("Unknown action: %s", command.action));
  }
  if (TLOR.actionQueue.length > 0) setTimeout(TLOR.executeActions, 400);
}

TLOR.showMessage = function(message) {
  message = message.replace(/\n/g, '<br />');
  TLOR.dialog.html(message);
  TLOR.dialog.show();
}
TLOR.confirmDialog = function() {
  if (TLOR.dialog.is(':visible')) {
    TLOR.dialog.hide();
    TLOR.executeActions();
  }
}
