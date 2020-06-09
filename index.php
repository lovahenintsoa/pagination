<?php
    define("ROW_PER_PAGE",2);
    require_once('db.php');
 
    $search_keyword = '';
    if(!empty($_POST['search'])) {
        $search_keyword = $_POST['search'];
    }
    
    $sql = 'SELECT * FROM poste WHERE post_title LIKE :keyword ';
    $sql.= 'OR description LIKE :keyword OR post_at LIKE :keyword ORDER BY id DESC ';
 
    $stmt = $cnx->prepare($sql);
    $stmt->bindValue(':keyword', '%' . $search_keyword . '%', PDO::PARAM_STR);
    $stmt->execute();
 
    // PAGINATION
    $page = 1;
    $start = 0;
    if(!empty($_POST["page"])) {
      $page = $_POST["page"];
      $start = ($page-1) * ROW_PER_PAGE;
    }
    $limit=" limit " . $start . "," . ROW_PER_PAGE;
 
    $per_page_html = '';
    $row_count = $stmt->rowCount();
    if(!empty($row_count)){
      $per_page_html .= "<div style='text-align:center;margin:20px 0px;'>";
      $page_count = ceil($row_count/ROW_PER_PAGE);
      if($page_count > 1) {
        for($i=1 ; $i <= $page_count ; $i++){
          if($i == $page){
            $per_page_html .= '<input type="submit" name="page" value="' . $i . '" class="btn-page current" />';
        } else {
            $per_page_html .= '<input type="submit" name="page" value="' . $i . '" class="btn-page" />';
        }
        }
      }
    $per_page_html .= "</div>";
  }
 
  // jeu d'enregistrement à afficher
  $query = $sql . $limit;
  $stmt = $cnx->prepare($query);
  $stmt->bindValue(':keyword', '%' . $search_keyword . '%', PDO::PARAM_STR);
  $stmt->execute();
  $result = $stmt->fetchAll();
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <form name='frmSearch' action='' method='post'>
    <div style='text-align:right;margin:20px 0px;'><input type='text' name='search' value="<?php echo $search_keyword; ?>" id='keyword' maxlength='25'></div>
    <table class='tbl-qa'>
      <thead>
            <tr>
              <th class='table-header' width='20%'>Title</th>
              <th class='table-header' width='40%'>Description</th>
              <th class='table-header' width='20%'>Date</th>
            </tr>
      </thead>
      <tbody id='table-body'>
      <?php
            if(!empty($result)) { 
                foreach($result as $row) {
            ?>
                    <tr class='table-row'>
                        <td><?php echo $row["post_title"]; ?></td>
                        <td><?php echo $row["description"]; ?></td>
                        <td><?php echo $row["post_at"]; ?></td>
                    </tr>
            <?php
                }
            }
            ?>
      </tbody>
    </table>
    <!-- afficher ici bouton de pagination -->
    <?php echo $per_page_html; ?>
    </form>
</body>
</html>