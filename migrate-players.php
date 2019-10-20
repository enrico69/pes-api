<?php
function initconnection(&$connection, $host, $base, $dbUser, $dbPassword)
{
    try {
        echo "Connecting to DB '{$base}'...";
        $connection = new PDO("mysql:host=$host;dbname=$base;charset=utf8", $dbUser, $dbPassword);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (\Exception $e) {
        echo "Impossible to connect to the database!";
        echo $e->getMessage();
        exit;
    }
}

echo PHP_EOL . 'Initializing connections' . PHP_EOL;
$v3connection = null;
initconnection($v3connection, '0.0.0.0', 'pesv3', 'root', 'asdgl');

$files = [
  '/Users/eric/Desktop/export-pulse/pes-joueurs.csv',
  '/Users/eric/Desktop/export-pulse/pes-joueurs2.csv'
];

$now = new \DateTime();
$now->setTimezone(new DateTimeZone("UTC"));
$now = $now->format('Y-m-d H:i:s');

foreach ($files as $file) {
    $row = 1;
    if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $num = count($data);
            echo "$num fields in line $row: " . PHP_EOL;
            $row++;
            for ($c=0; $c < $num; $c++) {
                echo $data[$c] . PHP_EOL;
            }

            echo "Inserting ... " .  $data[0] . $data[1] . PHP_EOL;
            var_dump($data);

            if (empty($data[1])){ $data[1] = 'X';}
            $request = "INSERT INTO player (first_name, last_name, creation_date, update_date) VALUES (:last_name, :first_name, :creation_date, :update_date)";
            $sql = $v3connection->prepare($request);
            $data[0] = str_replace('é', 'e', $data[0]);
            $data[0] = trim($data[0]);
            $data[0] = strtolower($data[0]);
            $data[0] = ucfirst($data[0]);
            $sql->bindValue('last_name', $data[1], PDO::PARAM_STR);
            $sql->bindValue('first_name', $data[0], PDO::PARAM_STR);
            $sql->bindValue('creation_date', $now, PDO::PARAM_STR);
            $sql->bindValue('update_date', $now, PDO::PARAM_STR);
            $sql->execute();

            $lastID = $v3connection->lastInsertId();
            $updateQuery = "INSERT INTO player_team (player_id, team_id) VALUES (" . $lastID . ',' . $data[2] . ')';
            $v3connection->exec($updateQuery);
        }
        fclose($handle);
    }
}
?>