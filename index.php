<?php
require_once("validator.php");

if (isset($_POST) && !empty($_POST)) {
    //var_dump($_FILES);
    //validation
    $data = [];
    $data["title"] = $_POST["title"];
    $data["article"] = $_POST["article"];
    date_default_timezone_set('UTC');
    $data["datePub"] = date("F j, Y, g:i a");

    //traitement sur le fichier
    if (isset($_FILES) && !empty($_FILES)) {
        $file_name = $_FILES["file"]["name"];
        $table_explode = explode('.', $file_name);
        $data["fileExtension"] = $table_explode[count($table_explode) - 1];

        $data["file_size"] = $_FILES["file"]["size"];
        $data["file_error"] = $_FILES["file"]["error"];
        $data["file_tmp"] = $_FILES["file"]["tmp_name"];
    } else {
        $data["fileExtension"] = "png";
    }

    // var_dump($data);
    $error = checkData($data);
    // var_dump($error);
}

//save new list into taskList ou updat task
if (isset($_POST) && !empty($_POST) && empty($error)) {
    if (isset($_POST["idToUpdat"]) && $_POST["idToUpdat"] !== "") {
        //updat --> submitTaskForm
        $result = trimData($data);
        $list = [];
        $list["title"] = $result["title"];
        $list["article"] = $result["article"];

        // $index = trimData($_POST["idToUpdat"]);
        $index = $_POST["idToUpdat"];
        $index = intval($index);
        // var_dump($index);

        //get list from taskList.json
        $listPath = "taskList.json";
        $file = file_get_contents($listPath, true);
        $fileList = json_decode($file, true);



        $file_rename = $fileList[$index]["imgUrl"];
        $table_explode = explode('.', $file_rename);
        $file_root_name = $table_explode[0];

        $list["imgUrl"] = $file_root_name . '.' . $data["fileExtension"];
        if ($data["file_error"] == 0) {
            //copi du fichier sur serveur
            $result = copy($data["file_tmp"], $list["imgUrl"]);
        }

        //updat task
        $fileList[$index]["title"] = $list["title"];
        $fileList[$index]["article"] = $list["article"];
        $fileList[$index]["imgUrl"] = $list["imgUrl"];
        // var_dump($fileList);

        $fileList_json = json_encode($fileList);

        file_put_contents($listPath, $fileList_json);

        //redirection to avoid reload data
        // header("location: index.php");
    } else {
        //save new list
        //pas d'erreur --> on sauvegarde les infos
        $result = trimData($data);
        $list = [];
        $list["title"] = $result["title"];
        $list["article"] = $result["article"];
        $list["datePub"] = $result["datePub"];
        $list["fileExtension"] = $result["fileExtension"];

        //rename and transfert file
        $file_rename = generate_reference();
        $list["imgUrl"] = "img/" . $file_rename . '.' . $list["fileExtension"];
        if ($data["file_error"] == 0) {
            //copi du fichier sur serveur
            $result = copy($data["file_tmp"], $list["imgUrl"]);
        }

        //get list from taskList.json
        $listPath = "taskList.json";
        $file = file_get_contents($listPath, true);
        $fileList = json_decode($file, true);
        //supprimer la donnee extension
        unset($list["fileExtension"]);
        //add new task at the end tof array
        $taskCount = count($fileList);
        $fileList[$taskCount] = $list;
        $fileList_json = json_encode($fileList);

        file_put_contents($listPath, $fileList_json);

        //redirection to avoid reload data
        //header("location: index.php");
    }
}

//delete taskList
if (isset($_GET["deteleTask"])) {
    //pas d'erreur --> on mets à jours les infos
    $data = [];
    $data["id"] = $_GET["deteleTask"];
    $result = trimData($data);

    //get list from taskList.json
    $listPath = "taskList.json";
    $file = file_get_contents($listPath, true);
    $fileList = json_decode($file, true);

    // var_dump($fileList);
    //delete task who has id $result["id"]
    unset($fileList[$result["id"]]);

    //on reclasse les elements
    $i = 0;
    $newFileList = [];
    foreach ($fileList as $key => $value) {
        $newFileList[$i] = $value;
        $i++;
    }

    $newFileList_json = json_encode($newFileList);
    file_put_contents($listPath, $newFileList_json);

    //redirection to avoid reload data
    header("location: index.php");
}
//updat taskList
if (isset($_GET["updatTask"])) {
    //pas d'erreur --> on mets à jours les infos
    $data = [];
    $data["id"] = $_GET["updatTask"];
    $resultUpdat = trimData($data);
    $resultUpdat = intval($resultUpdat["id"]);

    //var_dump($resultUpdat);

    //get list from taskList.json
    $listPath = "taskList.json";
    $file = file_get_contents($listPath, true);
    $fileListToUpdat = json_decode($file, true);
    //upload data into form
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
        <div class="welcome">BLOG ARTICLES</div>
        <h1>ARTICLES</h1>
        <div class="todolist">
            <div class="form">
                <form action="index.php" method="POST" enctype="multipart/form-data">
                    <div class="first-line">
                        <input type="text" name="title" id="title" placeholder="title"
                            value="<?php echo isset($fileListToUpdat) && !empty($fileListToUpdat) ? $fileListToUpdat[$resultUpdat]["title"] : null; ?>"
                            required>
                    </div>
                    <div class="error error-title">
                        <?php
                        echo isset($error["title"]) && !empty($error["title"]) ? $error["title"] : null;
                        ?>
                    </div>
                    <div class="second-line">
                        <textarea name="article" id="article" placeholder="article"
                            required><?php echo isset($fileListToUpdat) && !empty($fileListToUpdat) ? $fileListToUpdat[$resultUpdat]["article"] : null; ?></textarea>
                    </div>
                    <div class="error error-article">
                        <?php
                        echo isset($error["article"]) && !empty($error["article"]) ? $error["article"] : null;
                        ?>
                    </div>
                    <div class="fird-line">
                        <input type="file" name="file" id="file" required>
                    </div>
                    <div class="error error-file">
                        <?php
                        echo isset($error["fileExtension"]) && !empty($error["fileExtension"]) ? $error["fileExtension"] : null;
                        echo isset($error["size"]) && !empty($error["size"]) ? $error["size"] : null;
                        ?>
                    </div>
                    <div class="forth-line">
                        <input type="hidden" name="idToUpdat"
                            <?php echo isset($fileListToUpdat) && !empty($fileListToUpdat) ? 'value=' . $resultUpdat : null; ?>>
                        <input type="submit" value="enregistrer" id="submit-task" name="submitTaskForm">
                    </div>
                </form>
            </div>
            <div class="list">
                <?php
                //get list from taskList.json and display it
                $listPath = "taskList.json";
                $file = file_get_contents($listPath, true);
                $fileList = json_decode($file, true);
                // var_dump($fileList[1]);
                if (isset($fileList) && !empty($fileList)) {
                    for ($i = 0; $i < count($fileList); $i++) {
                        // var_dump($fileList);
                ?>
                <div class="display-list">
                    <div class="display-image">
                        <?php echo '<img src="' . $fileList[$i]["imgUrl"] . '" width="50" alt="illustration">'; ?>
                    </div>
                    <div class="display-title-article">
                        <div class="display-title">
                            <?php echo  $fileList[$i]["title"] . ' ( <em>Publié le ' . $fileList[$i]["datePub"] . ')</em>'; ?>
                        </div>
                        <div class="display-article">
                            <?php
                                    $restArticle = substr($fileList[$i]["article"], 0, 300);
                                    echo $restArticle . '  <form action="articlesPage.php" method="GET">
                                    <input type="hidden" name="idToDisplay" id="idToDisplay" value="' . $i . '">
                            <input type="submit" id="submitToDysplay" value="lire la suite">
                            </form>';
                                    ?>
                        </div>
                    </div>
                    <div class="display-manage-bt">
                        <div class="updat-list">
                            <form action="index.php" method="GET">
                                <input type="hidden" name="updatTask" value="<?php echo $i; ?>">
                                <input type="submit" value="updat">
                            </form>
                        </div>
                        <div class="delete-list">
                            <form action="index.php" method="GET">
                                <input type="hidden" name="deteleTask" value="<?php echo $i; ?>">
                                <input type="submit" value="delete">
                            </form>
                        </div>
                    </div>
                </div>
                <?php

                    }
                } else {
                    echo '<div class="aucune-infos">
                    aucun article enregistré !
                    </div>';
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>