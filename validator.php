<?php
function trimData($tab)
{
    if (is_array($tab)) {
        foreach ($tab as $key => $value) {
            $value = trim($value);
            $value = strip_tags($value);
            $tab[$key] = htmlspecialchars($value);
        }
        return $tab;
    }
}

function checkData($tab)
{
    $tab = trimData($tab);
    $result = array();
    foreach ($tab as $key => $value) {
        $value = trim($value);
        //GENERAL
        if ($value === "") {
            $result[$key] = "Le champs " . $key . "  ne peut pas être vide";
        }

        //SPECIFIQUE  
        if ($key == "title" && !isset($result[$key])) {
            if (strlen($value) < 5) {
                $result[$key] = "Le titre doit faire au moins 5 caratères !";
            }
            if (strlen($value) > 50) {
                $result[$key] = "Le titre doit faire au maxium 50 caratères !";
            }
        }

        if ($key == "article" && !isset($result[$key])) {
            if (strlen($value) < 500) {
                $result[$key] = "L'article doit faire au moins 500 caratères !";
            }
        }

        if ($key == "fileExtension" && !isset($result[$key])) {
            $extension_autorise = ["jpeg", "png", "jpg"];
            if (!in_array($value, $extension_autorise)) {
                $result[$key] = "extension d'images autorisées : jpeg, jpg, png !";
            }
        }

        if ($key == "size" && !isset($result[$key])) {
            if ($result[$key] > 5000000) {
                $result[$key] = "taille maximal autorisée 5Mo !";
            }
        }
    }
    return $result;
}

//rename fonction
function generate_reference()
{
    $message_name = "";
    $code = "az12345678MWXC9ertyuiUIOPQSDFGHJopqsdfgh123456789jklmwxcvbn123456789AZERTYKLVBN";

    $index = 1;
    while ($index <= 20) {
        $message_name .= $code[rand(0, 78)];
        $index++;
    }
    return $message_name;
}
