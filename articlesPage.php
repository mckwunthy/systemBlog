<?php
require_once("validator.php");

if (isset($_GET["idToDisplay"])) {
    $data = [];
    $data["idToDisplay"] = $_GET["idToDisplay"];
    $idToDisplay = trimData($data);
    $idToDisplay = intval($idToDisplay["idToDisplay"]);

    //get list from taskList.json
    $listPath = "taskList.json";
    $file = file_get_contents($listPath, true);
    $fileList = json_decode($file, true);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TodoList</title>
    <link rel="stylesheet" href="/style.css">
</head>

<body>
    <div id="app">
        <h1>ARTICLES</h1>
        <div class="list">
            <div class="image">
                <img src="<?php echo isset($fileList) && !empty($fileList) ? $fileList[$idToDisplay]["imgUrl"] : null; ?>" width="300" alt="illustration">
            </div>
            <div class="title-article">
                <div class="title">
                    <?php echo isset($fileList) && !empty($fileList) ? $fileList[$idToDisplay]["title"] : null; ?>
                </div>
                <div class="article">
                    <?php echo isset($fileList) && !empty($fileList) ? $fileList[$idToDisplay]["article"] : null; ?>
                </div>
            </div>
        </div>
        <div class="back"><a href="index.php">retour</a></div>
    </div>
</body>

</html>