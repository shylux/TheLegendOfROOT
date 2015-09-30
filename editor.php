<?php require "header.php" ?>

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
    <input type="submit" value="New" onclick="javascript:newDungeon();" disabled="disabled" />
  </div>
  <div id="export">
    <input type="submit" value="Export terrain data" onclick="javascript:exportTerrain();" />
  </div>
  <div id="game-container"></div>
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
function exportTerrain() {
  var data = TLOR.generateTerrainJSON();
  window.open("data:text/json," + encodeURIComponent(data));
}
</script>
<?php require "footer.php" ?>
