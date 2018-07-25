<?php

class Xport{

private $servers;

public $db_ignore_a = array(
    'information_schema',
    'innodb',
    'mysql',
    'performance_schema',
    'mydb',
    'tmp',
    'test',
    'tester',
    'sys',
    'phpmyadmin'
);

function __construct( $config)
{
    $this->servers = $config['servers'];
    foreach($this->servers as $name => $server)
    {
        $this->servers[$name]['databases']		= null;
        $this->servers[$name]['connection'] 	= null;
    }
    
    $this->setup();
    $this->list();
    //print_r($this->servers);
}

function list()
{
    echo "Configured Servers:\n";
    foreach($this->servers as $s)
        echo " - {$s['user']}@{$s['host']}\n" ;
}

function setup()
{
    echo "Configured Servers:\n";
    foreach($this->servers as $name => $s)
    {
        echo " - {$s['user']}@{$s['host']}: \n" ;

        try{
            $mysqli = new mysqli($s['host'], $s['user'], $s['password'], "mysql");	
        } catch (Exception $e ) {
            //echo "message: " . $e->message; 
        }
        
        if ( !$mysqli->connect_errno )
        {
            $this->servers[$name]['connection'] = $mysqli;
            $this->get_databases( $name );

            $server_dir = dirname(__FILE__) . "/" . $name;
            if ( !file_exists($server_dir) )
                mkdir( $server_dir );
        }
    }

    //print_r($this->servers);
}

function import( $dir, $import_server)
{
    $d = dir( dirname(__FILE__) . "/" . $dir );
    while ( false !== ($entry = $d->read()) )
    {
        if ( strpos($entry, ".sql") !== false )
        {
               echo "SQL: ". $entry."\n";

               $s = $this->servers[$import_server];

               $filename = dirname(__FILE__) . "/" . $dir . "/". $entry;

               $command = "mysql -h {$s['host']} -u {$s['user']} -p{$s['password']} < $filename ";
               echo "mysql -h {$s['host']} -u {$s['user']} -pPASSWORD < $filename " . PHP_EOL;
               exec($command);	
        }
    }
}

function get_databases( $name )
{
    $this->servers[$name]['databases'] = [];
    $res = $this->servers[$name]['connection']->query("SHOW DATABASES");
    while ($row = $res->fetch_assoc())
    {
        if ( array_search($row['Database'], $this->db_ignore_a) === FALSE )
        {
            $this->servers[$name]['databases'][] = $row['Database'];
        }
    }
}

function backup( $servername = '')
{
    foreach($this->servers as $name => $s)
    {
        
        if ( !empty($s['databases']) && ( empty($servername) || $servername == $name ) )
        {
            echo "{$name} [{$s['user']}@{$s['host']}]: Export all DBs :\n";

            foreach( $s['databases'] as $database)
            {
                // --add-drop-database
                $command = "mysqldump --single-transaction --skip-lock-tables  --opt -h {$s['host']} -u {$s['user']} -p{$s['password']} --databases {$database} > {$name}/{$database}.sql";
                echo "mysqldump --single-transaction --skip-lock-tables --opt -h {$s['host']} -u {$s['user']} -pPASSWORD --databases {$database} > {$name}/{$database}.sql" . PHP_EOL;
                exec($command);	
            }
        }
        echo PHP_EOL;
    }
    
}
}