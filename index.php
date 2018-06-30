<?php
session_start();

require_once __DIR__.'/class/DirectoryList.php';
require_once __DIR__.'/class/TreeBuilder.php';

$dl = new DirectoryList();
$baseDirs = $dl->getBaseDirs();

if ($_REQUEST['basedir']) {
    $baseDir = isset($_REQUEST['basedir']) ? $baseDirs[$_REQUEST['basedir']] : '';
    $tree = $dl->getLsByPath($baseDir);
} else {
    $baseDir = getcwd();
    $tree = $dl->getLsByPath($baseDir);
}
$_SESSION['baseDir'] = $baseDir;


if ($_REQUEST['metrics']) {
    // TODO: wrong!
    $path = $baseDir;
    $outputDir = $path . '/metrics-report';
    `php phpmetrics --report-html={$outputDir} {$path}`;
}

const STATUS_NOT_FOUND = 1;
const STATUS_NOT_SYNCED = 2;
const STATUS_SYNCED = 4;

$colors = [
    STATUS_NOT_FOUND => 'not-found',
    STATUS_NOT_SYNCED => 'not-synced',
    STATUS_SYNCED => 'synced',
];

?>
<!doctype html>
<html>
<head>
<title>PHP metrics</title>

<link rel="stylesheet" href="/styles.css">
<link rel="stylesheet" href="/js/dist/themes/default/style.css" />
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/ui-lightness/jquery-ui.css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.1/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script src="/js/dist/jstree.min.js"></script>

<script>
$(function () {
    $('#tree').jstree({
      core: {
        themes: { stripes: true }
      }
    });
    var instance = $('#tree').jstree(true)
    var baseDir = '<?= $baseDir; ?>'

    $('#basedir').on('change', function(e) {
      $('#form').submit();
    });

    $('#tree').on('activate_node.jstree', function(e, data) {
//        console.log(data);
    });
/*
    $('#tree').on("select_node.jstree", function (e, data) {
        instance.toggle_node(data.node);
    });
*/
    $('#tree').on("select_node.jstree", function (e, data) {
      var pathChunks = instance.get_path(data.node, '/', true).split('/')
      pathChunks.shift()
//      console.log('path', path)
      var path = baseDir + '/' + pathChunks.map(function(nodeId) {
          return instance.get_node(nodeId, false)['data']['text']
      }).join('/')
      //var node = instance.get_node(path[0], false)
      //var name = node['data']['text']
      //console.log(node)
      console.log(path)
    });

    instance.open_node($('#tree li:first'));
});
</script>

</head>

<body>

    <form id="form" action="index.php" method="post">
        <select id="basedir" name="basedir" size="1">
            <option value="" disabled selected>Select folder to build tree</option>
            <?php
                foreach ($baseDirs as $dir => $path) {
                    $selected = $baseDir == $dir ? 'selected' : '';
                    echo '<option value="'.$dir.'" '.$selected.'>'.str_pad($dir.' ', 15, '-').' ('.$path.')</option>';
                }
            ?>
        </select>
        <br /><br />
        <hr />

        <div id="tree">
        <?php
            if ($tree) {
                echo TreeBuilder::buildHTMLTree($tree);
            }
        ?>
        </div> <!-- tree -->
    </form>

</body>

</html>
