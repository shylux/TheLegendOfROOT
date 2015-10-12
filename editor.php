<h2>Dungeon Editor</h2>

<div id="editor">
  <div id="load">
    <select id="fs_load">
    <?php foreach (scandir("dungeons") as $file) {
            if (is_dir("dungeons/" . $file)) continue;
      ?>
      <option value="<?= $file ?>"><?= $file ?></option>
    <?php } ?>
    </select>
    <input type="submit" value="Load" onclick="javascript:loadFromFS();" />
    <br>
    <textarea id="text_load" type="textarea"></textarea>
    <input type="submit" value="Load" onclick="javascript:loadFromText();"/>
    <br>
    New Dungeon
    <label for="new-x">X</label><input id="new-x" type="number" name="new-x">
    <label for="new-y">Y</label><input id="new-y" type="number" name="new-y">
    <input type="submit" value="New" onclick="javascript:newDungeon();" />
  </div>
  <div id="export">
    <input type="submit" value="Export terrain data" onclick="javascript:exportTerrain();" />
  </div>
  <div id="game-container" class="game-tile"></div>
  <div id="position">
    <span>X: <code id="pos-x"></code></span>
    <span>Y: <code id="pos-y"></code></span>
  </div>
  <div id="terrain-select" class="game-tile">
    <div><b>Available Terrains</b> Hold <code>ctrl</code> and click to fill rects.</div>
    <table>
      <tr>
        <?php for ($i = 0; $i <= 15; $i++) { ?>
        <td><img data-terr="<?= $i ?>"></td>
        <?php } ?>
      </tr>
    </table>
  </div>
</div>
<script type="text/javascript">

function loadFromFS() {
  $.getJSON("/dungeons/"+$('#fs_load').val(), function( data ) {
    TLOR.setup($('#game-container'), data, {});
  });
}
function loadFromText() {
  TLOR.setup($('#game-container'), JSON.parse($('#text_load').val()), {});
}
function newDungeon() {
  TLOR.setup($('#game-container'), {height: $('#new-y').val(), width: $('#new-x').val()}, {});
}
function exportTerrain() {
  var data = TLOR.generateTerrainJSON();
  window.open("data:text/json," + encodeURIComponent(data));
}

$(function() {
  $('#terrain-select td:first').addClass('selected');
  $('#terrain-select td').click(function() {
    $('#terrain-select td').removeClass('selected');
    $(this).addClass('selected');
  });
  $('#game-container').on('mousedown mousemove', 'td', function(e) {
    $('#pos-x').text($(this).attr('data-x'));
    $('#pos-y').text($(this).attr('data-y'));
    if (e.buttons == 1) // left mouse button down
      $(this).find('img').attr('data-terr', $('#terrain-select td.selected img').attr('data-terr'));
  });
  var lastClickPos;
  $('#game-container').on('click', 'td', function(e) {
    if (e.shiftKey) {
      if (typeof lastClickPos === 'undefined') {
        lastClickPos = {x: parseInt($(this).attr('data-x')), y: parseInt($(this).attr('data-y'))};
      } else {
        newX = parseInt($(this).attr('data-x'));
        newY = parseInt($(this).attr('data-y'));
        for (x = Math.min(newX, lastClickPos.x); x <= Math.max(newX, lastClickPos.x); x++)
          for (y = Math.min(newY, lastClickPos.y); y <= Math.max(newY, lastClickPos.y); y++)
            TLOR.getCell(x, y).find('img').attr('data-terr', $('#terrain-select td.selected img').attr('data-terr'));
        lastClickPos = undefined;
      }
    }
  });
});
</script>
