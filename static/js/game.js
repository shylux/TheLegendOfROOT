var TLOR = {};
/***
 * Parameter:
 * jq_element: The jQuery element in which the dungeon is displayed.
 * initial_dungeon_data: The json file with the terrain and entities.
  ***/

TLOR.setup = function(jq_element, initial_dungeon_data) {
  TLOR.el = jq_element;
  TLOR = $.extend(TLOR, initial_dungeon_data);

  TLOR.el.append('<table><tbody></tbody></table>');
  if ('terrain' in TLOR)
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
      var cell = $('<td>');
      cell.attr('data-y', y).attr('data-x', x);
      cell.attr('data-terr', terrain_data[y][x]);
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
      var cell = $('<td>');
      cell.attr('data-y', y).attr('data-x', x);
      cell.attr('data-terr', 0);
      row.append(cell);
    }
    tbody.append(row);
  }
};

TLOR.getTerrainFor = function(x, y) {
  return parseInt(TLOR.el.find(sprintf('td[data-x="%i"][data-y="%i"]', x, y)).attr('data-terr'));
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
