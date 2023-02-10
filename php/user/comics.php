<?php
include_once '../inc/header.inc.php';
global $conection;
$limit = intval($_GET['limit']);
$offset = intval($_GET['offset']);

if (isset($_GET['checkboxChecked'])) {
    $search = explode(",", $_GET['checkboxChecked']);
    $search = array_map('trim', $search);
    $search = array_map('urldecode', $search);
    $search_count = count($search);

    if ($search_count == 1) {
        $where_clause = " WHERE nomGuionista LIKE '%" . $search[0] . "%' OR nomDibujante LIKE '%" . $search[0] . "%' OR nomEditorial = '" . $search[0] . "'";
    } else {
        $where_clauses = [];
        for ($i = 0; $i < $search_count; $i++) {
            $where_clauses[] = "(nomGuionista LIKE '%" . $search[$i] . "%' OR nomDibujante LIKE '%" . $search[$i] . "%' OR nomEditorial = '" . $search[$i] . "')";
        }
        $where_clause = " WHERE " . implode(' OR ', $where_clauses);
    }

    $comics = $conection->prepare("SELECT * FROM comics" . $where_clause);
    $comics->execute();
} else {
    $comics = return_comic_published($limit, $offset);
}
$contador = 0;
$total_comics = numComics();

while ($data_comic = $comics->fetch(PDO::FETCH_ASSOC)) {
    $id = $data_comic['IDcomic'];
    $numero = $data_comic['numComic'];
    $titulo = $data_comic['nomComic'];
    $numComic = $data_comic['numComic'];
    $variante = $data_comic['nomVariante'];
    $fecha = $data_comic['date_published'];

    echo "<li id='comicyXwd2' class='get-it'>
            <a href='infoComic.php?IDcomic=$id' title='$titulo - Variante: $variante / $numComic' class='title'>
              <span class='cover'>
                <img src='./assets/covers_img/$id.jpg' alt='$titulo - $variante / #$numComic'>
              </span>
              <strong>$titulo</strong>
              <span class='issue-number issue-number-l1'>$numComic</span>
            </a>
            <input type='hidden' name='id_grapa' id='id_grapa' value='$id'>
            <button data-item-id='yXwd2' class='add'>
              <span class='sp-icon' >Lo tengo</span>
            </button>
          </li>";
    $contador++;
    if ($contador % 8 === 0) {
        echo "<ul></ul>";
    }
}

?>

<script>
    (function() {
        const buttons = document.querySelectorAll('.add');
        buttons.forEach(function(button) {
            button.addEventListener('click', function() {
                if (button.classList.contains('add')) {
                    button.classList.remove('add');
                    button.classList.add('rem');
                    const id_comic = button.previousElementSibling.value;
                    console.log(id_comic);
                } else {
                    button.classList.remove('rem');
                    button.classList.add('add');
                }
            });
        });
    })();
</script>