<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
<?php




function can_upload($file){
    // если имя пустое, значит файл не выбран
      if($file['name'] == '')
      return 'Вы не выбрали файл.';
    
    /* если размер файла 0, значит его не пропустили настройки 
    сервера из-за того, что он слишком большой */
    if($file['size'] == 0)
      return 'Файл слишком большой.';
    
    // разбиваем имя файла по точке и получаем массив
    $getMime = explode('.', $file['name']);
    // нас интересует последний элемент массива - расширение
    $mime = strtolower(end($getMime));
    // объявим массив допустимых расширений
    $types = array('csv');
    
    // если расширение не входит в список допустимых - return
    if(!in_array($mime, $types))
      return 'Недопустимый тип файла.';
    
    return true;
}



$check = can_upload($_FILES['image'] );



function make_upload($file){
    // $pdo = new PDO( $dsn = "mysql:host=localhost;dbname=workbd;charset=utf8mb4",'root', '');
    $pdo = new PDO( $dsn = "mysql:host=localhost;dbname=a0743778_work;charset=utf8mb4",'a0743778_work', 'work');
  
    /*echo "<pre>"; print_r( $file); echo "</pre>";
    echo "<pre>"; print_r( $file2); echo "</pre>";*/
   //echo "<pre>"; print_r( $file); echo "</pre>";
    // формируем уникальное имя картинки: случайное число и name
    // if (is_array($file)) {
      $name = mt_rand(10000, 999999) . 1 . $file['name'];
      copy($file['tmp_name'],"$_SERVER[DOCUMENT_ROOT]/upload/" . $name);
      $name = "upload/" . $name;
    // } else {
    //   $name = $file;
    // }
    $stmt = $pdo->prepare( "INSERT INTO `path` ( `path`) VALUE( ?)");
    $stmt->execute(  [$name]);

                  
  }

if( isset( $_POST['Опубликовать'] )  ) {
    // echo "work";
    // проверяем, можно ли загружать изображение
    $check = can_upload($_FILES['csv'] );
    //echo "<pre>"; print_r( $_FILES['image8'] == 0); echo "</pre>";
    //echo "<pre>"; print_r( $_POST["prise"]); echo "</pre>";
    // echo $check;
    if($check === true ){
      // загружаем изображение на сервер
      
      make_upload($_FILES['csv']);
      
      
      /*echo "<strong>Файл успешно загружен!</strong>";*/
    }
    else{
      // выводим сообщение об ошибке
      echo "<strong>$check</strong>";  
    }
  }



?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

<ul class="nav nav-pills nav-fill">
<li class="nav-item">
    <a class="nav-link active"  href="#">Ввод файла</a>
  </li>
  <li class="nav-item">
    <a class="nav-link " href="output.php">Вывод файлов</a>
  </li>
</ul>


<form enctype="multipart/form-data" method="post" action="">
  <div class="form-group">
    <label for="exampleFormControlFile1">Загрузите файл</label>
    <input  type="file" name="csv" class="form-control-file" id="exampleFormControlFile1" required>
  </div>
  <button type="submit" class="btn btn-primary" value="Опубликовать" name="Опубликовать">Загрузить</button>
</form>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
