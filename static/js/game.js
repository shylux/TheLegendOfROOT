var TLOR = {};
/***
 * Parameter:
 * jq_element: The jQuery element in which the dungeon is displayed.
 * initial_dungeon_data: The json file with the terrain and entities.
 * options: The default value is in the example.
    {
        editor_mode: false,
        display_controls: true
    }
  ***/

TLOR.setup = function(jq_element, initial_dungeon_data, options) {
  TLOR.el = jq_element;
  TLOR = $.extend(TLOR, initial_dungeon_data);

  TLOR.el.append('<table><tbody></tbody></table>');
  var tbody = TLOR.el.find('tbody');
  for (y = 0; y < TLOR.height; y++) {
    var row = $('<tr>');
    for (x = 0; x < TLOR.width; x++) {
      var cell = $('<td>');
      cell.attr('data-y', y).attr('data-x', x);
      cell.attr('data-terr', TLOR.terrain[y][x]);
      row.append(cell);
    }
    tbody.append(row);

    // resize
    var table = TLOR.el.find('table');
    table.height(table.width()/TLOR.width*TLOR.height);
  }
};

TLOR.getTerrainFor = function(x, y) {
  return TLOR.el.find(sprintf('td[data-x="%i"][data-y="%i"]', x, y)).attr('data-terr');
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
