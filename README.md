# O2.PHP

## Intro

O2 stands for "Object Oriented" and it aims at making easier to save, recover, update and delete data from databases.

O2.PHP version supports MySQL, and is a working in progress. And O2 have a [.NET version](https://github.com/leandroguimaraes/O2.NET) too, with a very similar syntax.

## How to

Pre-requisite: having PDO and PDO MySQL, as stated here: http://php.net/manual/pdo.installation.php

Start by creating a config.php (or any other name you wish) file, with the following content:

```php
$o2_db_type = 'mysql';

$o2_db_host = 'dbhost';
$o2_db_name = 'dbname';
$o2_db_user = 'dbuser';
$o2_db_psw = 'dbpassword';

$o2_tbprefix = 'o2_'; //tables' prefix
```

Copy O2.PHP files to your project and include previously created config.php file and O2.PHP load.php file on it, at this order.

```php
require_once('config.php');
require_once('O2.PHP/load.php');
```

Create a database table (MySQL sample below):

```sql
CREATE TABLE `o2_clients` ( /* "o2_" will be a common prefix for all database tables on your project scope */
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT, /* all tables must have an "id" autoincrement column */
  `given_name` varchar(100) DEFAULT NULL,
  `surname` varchar(150) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `sample_integer` int(11) DEFAULT NULL,
  `sample_decimal` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

Create a class:

```php
class Client extends SysObject {
	protected $table = 'clients'; //all classes must reference its correspondent table this way, without table prefix

	//you don't need to declare an "id" property because it's inherited from SysObject parent class
	protected $given_name;
    protected $surname;
    protected $date_of_birth;
    protected $sample_integer;
    protected $sample_decimal;

    public function __set($property, $value) {
    	if (property_exists($this, $property))
			$this->$property = $value;

		return $this;
    }

    public function __get($property) {
    	if (property_exists($this, $property))
    		return $this->$property;
    }
}
```
And now you're ready for the CRUD show!

## CREATE (Insert)

```php
$client = new Client();

$client->given_name = 'Homer';
$client->surname = 'Simpson';
$client->date_of_birth = '1956-05-12';
$client->sample_integer = 123;
$client->sample_decimal = 123.45;

$client->Insert();

echo $client->id; //database autoincrement gift
```

## READ (Select / Load)

```php
$client = new Client();

$client->Load(1); //or any other id you may need

echo $client->given_name; //you can read all object loaded data this way
```

## UPDATE (Update)

```php
$client = new Client();
$client->Load(1); //or any other id you may need
$client->given_name = 'Bart';
$client->surname = 'Simpson';
$client->date_of_birth = '1980-04-01';
$client->sample_integer = 456;
$client->sample_decimal = 456.78;

$client->Update();
```

## DELETE (Delete)

```php
$client = new Client();
$client->Load(1); //or any other id you may need

$client->Delete();
```

## CUSTOM QUERYs

And finally, you can execute general purpose SQL querys with O2 as shown below.

### SELECT

```php
$client = new Client();

$query = new Query();
$query->AddParameter('@given_name', '%Bart%', PDO::PARAM_STR); // for PDO::PARAM_* constants options, check: http://php.net/manual/pdo.constants.php
$reader = $query->ExecuteReader('SELECT * FROM '.$client->get_table().' WHERE given_name LIKE @given_name');
while ($row = $reader->fetch()) {
  $client = new Client();
  //load database info into a object
  $client->LoadBy_array($row);

  //read data straigth from reader
  echo '<br />'.$row['id'].' - '.$row['given_name'];
  //read data from object
  echo '<br />'.$client->id.' - '.$client->given_name;
}
```

### UPDATE, INSERT or DELETE

```php
$client = new Client();

$query = new Query();

$query->AddParameter('@id', 11, PDO::PARAM_INT); // for PDO::PARAM_* constants options, check: http://php.net/manual/pdo.constants.php
$query->ExecuteNonQuery('DELETE FROM '.$client->get_table().' WHERE id = @id');
```

Enjoy! :)
