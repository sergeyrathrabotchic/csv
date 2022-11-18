<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

<ul class="nav nav-pills nav-fill">
  <li class="nav-item">
    <a class="nav-link"  href="index.php">Ввод файла</a>
  </li>
  <li class="nav-item">
    <a class="nav-link active" href="#">Вывод файлов</a>
  </li>
</ul>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>

<?php

function kama_parse_csv_file( $file_path, $file_encodings = ['cp1251','UTF-8'], $col_delimiter = '', $row_delimiter = '' ){

    if( ! file_exists( $file_path ) ){
        // return false;
        return 'work';
    }
    
    $cont = trim( file_get_contents( $file_path ) );
    
    $encoded_cont = mb_convert_encoding( $cont, 'UTF-8', mb_detect_encoding( $cont, $file_encodings ) );
    
    unset( $cont );
    
    // определим разделитель
    if( ! $row_delimiter ){
        $row_delimiter = "\r\n";
        if( false === strpos($encoded_cont, "\r\n") )
            $row_delimiter = "\n";
    }
    
    $lines = explode( $row_delimiter, trim($encoded_cont) );
    $lines = array_filter( $lines );
    $lines = array_map( 'trim', $lines );
    
    // авто-определим разделитель из двух возможных: ';' или ','.
    // для расчета берем не больше 30 строк
    if( ! $col_delimiter ){
        $lines10 = array_slice( $lines, 0, 30 );
    
        // если в строке нет одного из разделителей, то значит другой точно он...
        foreach( $lines10 as $line ){
            if( ! strpos( $line, ',') ) $col_delimiter = ';';
            if( ! strpos( $line, ';') ) $col_delimiter = ',';
    
            if( $col_delimiter ) break;
        }
    
        // если первый способ не дал результатов, то погружаемся в задачу и считаем кол разделителей в каждой строке.
        // где больше одинаковых количеств найденного разделителя, тот и разделитель...
        if( ! $col_delimiter ){
            $delim_counts = array( ';'=>array(), ','=>array() );
            foreach( $lines10 as $line ){
                $delim_counts[','][] = substr_count( $line, ',' );
                $delim_counts[';'][] = substr_count( $line, ';' );
            }
    
            $delim_counts = array_map( 'array_filter', $delim_counts ); // уберем нули
    
            // кол-во одинаковых значений массива - это потенциальный разделитель
            $delim_counts = array_map( 'array_count_values', $delim_counts );
    
            $delim_counts = array_map( 'max', $delim_counts ); // берем только макс. значения вхождений
    
            if( $delim_counts[';'] === $delim_counts[','] )
                return array('Не удалось определить разделитель колонок.');
    
            $col_delimiter = array_search( max($delim_counts), $delim_counts );
        }
    
    }
    
    $data = [];
    foreach( $lines as $key => $line ){
        $data[] = str_getcsv( $line, $col_delimiter ); // linedata
        unset( $lines[$key] );
    }
    
    return $data;
    }

// $pdo = new PDO( $dsn = "mysql:host=localhost;dbname=workbd;charset=utf8mb4",'root', '');
$pdo = new PDO( $dsn = "mysql:host=localhost;dbname=a0743778_work;charset=utf8mb4",'a0743778_work', 'work');
$stmt = $pdo->prepare( "SELECT * FROM `path`");
$stmt->execute(  []);
$filePaths = $stmt->fetchAll();

foreach ($filePaths as $filePath) {
    // $data = kama_parse_csv_file( "$_SERVER[DOCUMENT_ROOT]/upload/tets.csv" );
    // print_r( "$_SERVER[DOCUMENT_ROOT]/" .  $filePath['path'] );
    $data = kama_parse_csv_file( "$_SERVER[DOCUMENT_ROOT]/" .  $filePath['path'] );
    print_r( $data );
}

?>