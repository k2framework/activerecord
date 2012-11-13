Configuración Inicial
=====================

ActiveRecord permite administrar varias configuraciones de bases de datos que podrán ser usadas por
nuestros modelos.

Estableciendo parámetros de conexión
------------------------------------

La clase Parameters nos permitirá describir conexiones, esta clase nos da la libertad de definir
los parámetros de varias formas:

.. code-block:: php

	<?php

	use ActiveRecord\Config\Parameters;

	//en el siguiente ejemplo crearemos la configuración en el constructor de la clase:

	$mysql = new Parameters("configMysql", array(
		"type"     => "mysql"
		'username' => "root"
		"password" => "123456"
		"host"     => "localhost"
		"name"     => "mi_base_de_datos"
	));

	//Ahora configuraremos otra conexión, pero a traves de los métodos setters de la clase:

	$postgres = new Parameters("configPostgres"); //no pasamos el arreglo con la configuración.

	$postgres->setUsername("administrador")
		->setPassword("admin123Admin")
		->setHost("192.168.1.3")
		->setPort("2020")
		->setDbName("clinicas")
		->setType("pgsql");

Como se puede ver, esas son las dos formas de crear la configuración de acceso a una base de datos.

.. highlights::

	NOTA: en el constructor el indice para el nombre de la base de datos debe ser "name" aunque realmente dicho valor
	se almacena en la propiedad $dbName de la clase Parameters, por eso el método para establecer el nombre de la
	base de datos es "setDbName()".

Anteriormente hemos usado los nombres "configMysql" y "configPostgres", con estos nombres podremos identificar
la conexión y asignarla al modelo:

.. code-block:: php

	<?php

	use ActiveRecord\Model;

	class Clientes extends Model
	{
		protected $connection = "config_postgres"
	}

	class Clientes extends Model
	{
		protected $connection = "config_mysql"
	}

	class Clientes extends Model
	{
		//si no establecemos la conexión a usar, la libreria
		//tomará la primera configuración establecida en la clase Config
	}

Administrador de Conexiones
---------------------------

De acuerdo al nombre de conexión que asignemos al modelo, ActiveRecord solicitará a la clase Config
estos parámetros cuando los requiera. Config actúa como un administrador de conexiones, por esto
debemos agregar los parametros a esta para que estén disponibles para nuestros modelos:

.. code-block:: php

	<?php

	use ActiveRecord\Config\Parameters;
	use ActiveRecord\Config\Config;

	//creamos los objetos Parameters necesarios, como en el ejemplo anterior.
	$mysql = new Parameters("configMysql", array(
		"type"     => "mysql"
		'username' => "root"
		"password" => "123456"
		"host"     => "localhost"
		"name"     => "mi_base_de_datos"
	));

	//luego agregamos los parametros a la configuración
	Config::add($mysql);

	//multiples conexiones pueden ser agregadas a Config
	Config::add($postgres);
