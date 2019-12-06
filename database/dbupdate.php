<?php
require_once('../util/env.php');

$host = env('DB_HOST', 'localhost');
$name = env('DB_DATABASE', 'jikkenb');
$user = env('DB_USERNAME', 'root');
$pass = env('DB_PASSWORD', 'jikken2019');

$dsn = "mysql:host=$host;dbname=$name";

try {
    $dbh = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    echo $e;
    exit();
}

$help = '
help: show help
add: add change
apply: apply changes
delete <id>: delete change
status: show status
';

function load_dbupdate_data () {
    $json = file_get_contents('./log.json');
    $json = $json ? $json : json_encode(['applied' => []]);
    return json_decode($json, true);
}

function save_dbupdate_data (array $data) {
    $json = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    file_put_contents('./log.json', $json);
}

function get_unapplied () {
    $data = load_dbupdate_data();

    $unapplied = [];
    foreach (glob("./patchstack/*.sql") as $filename) {
        $filename = basename($filename);
        $filename = (int)substr($filename, 0, strlen($filename)-4);
        if (!in_array($filename, $data['applied'], true)) $unapplied[] = $filename;
    }
    sort($unapplied);

    return $unapplied;
}

if (count($argv) == 1) {
    echo $help;
}
if ($argv[1] == 'help') {
    echo $help;
}
if ($argv[1] == 'add') {
    $sql = file_get_contents('./patch.sql');
    $dbh->query($sql);
    $time = time();
    file_put_contents("./patchstack/$time.sql", $sql);
    $log = load_dbupdate_data();
    array_push($log['applied'], $time);
    save_dbupdate_data($log);
    echo "\033[32madded:\033[0m ".$time;
}
if ($argv[1] == 'status') {
    echo "\033[31m未適用:\033[0m\n";
    foreach (get_unapplied() as $val) {
        echo $val."\n";
    }
    echo "\033[32m適用済:\033[0m\n";
    foreach (load_dbupdate_data()['applied'] as $val) {
        echo $val."\n";
    }
}
if ($argv[1] == 'apply') {
    foreach (get_unapplied() as $id) {
        $sql = file_get_contents("./patchstack/".$id.".sql");
        $dbh->query($sql);
        $log = load_dbupdate_data();
        array_push($log['applied'], $id);
        save_dbupdate_data($log);
        echo "\033[32mapplied:\033[0m ".$id."\n";
    }
}
if ($argv[1] == 'delete') {
    $data = load_dbupdate_data();
    foreach ($data['applied'] as $key => $val) {
        $test = $argv[2];
        if (preg_match("/$test/", $val)) {
            unlink("./patchstack/$val.sql");
            array_splice($data['applied'], $key, 1);
            echo "\033[31mdeleted:\033[0m ".$val."\n";
        }
    }
    save_dbupdate_data($data);
}
