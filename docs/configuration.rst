Configuración Inicial
=====================

Esta librería permite comunicarnos con una ó varias bases de datos en una aplicación mediante una capa de abstracción
de base de datos, pero para lograr ello debemos indicarle a la misma cual ó cuales son las configuraciones de
conexión de la base de datos.

Para configurar una ó varias conexiones a Base de Datos debemos hacer uso de dos clases principales,
una es la clase Parameters y la otra la clase Config.

La clase Parameters
-------------------

Esta clase será la que contenga los valores de configuración de una base de datos en especifico, dichos
valores pueden ser establicidos en el constructor de la clase al instanciarla, ó con el uso de métodos setters,
veamos un ejemplo:

.. code-block:: php

	<?php

	use ActiveRecord\Config\Parameters;

	//en el siguiente ejemplo crearemos la configuración en el constructor de la clase:

	$mysql = new Parameters("config_mysql", array(
		"type"     => "mysql"
		'username' => "root"
		"password" => "123456"
		"host"     => "localhost"
		"name"     => "mi_base_de_datos"
	));

	//Ahora configuraremos otra conexión, pero a traves de los métodos setters de la clase:

	$postgres = new Parameters("config_postgres"); //no pasamos el arreglo con la configuración.

	$postgres->setUsername("administrador")
		->setPassword("admin123Admin")
		->setHost("192.168.1.3")
		->setPort("2020")
		->setDbName("clinicas")
		->setType("pgsql");

Como se puede ver, esas son las dos formas de crear la configuración de acceso a una base de datos.

NOTA: en el constructor el indice para el nombre de la base de datos debe ser "name" aunque realmente dicho valor
se almacena en la propiedad $dbName de la clase Parameters, por eso el método para establecer el nombre de la
base de datos es "setDbName()".

Es importante conocer el nombre de la configuración, que para el ejemplo la config de mysql se llama "config_mysql" y
la de postgres "config_postgres", ya que si queremos que un modelo se conecte a una ú otra debemos especificarlo
en el atributo $conection del módelo, ejemplo:

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

La clase Config
---------------

Aunque en la clase Parameters se establescan los parámetros de configuración de acceso a nuestra base de datos,
es en la clase Config donde el ActiveRecord busca esas configuraciones, por lo tanto para que la libreria
reconozca y pueda usar los parámetros de nuestra BD, debemos pasarlos a la clase Config, esto
se logra de la siguiente manera:

.. code-block:: php

	<?php

	use ActiveRecord\Config\Parameters;
	use ActiveRecord\Config\Config;

	//creamos los objetos Parameters necesarios, como en el ejemplo anterior.
	.....
	.....

	//ahora introduciremos esos parametros de configuración en la clase Config:

	Config::add($mysql);
	Config::add($postgres);

	//con esto ya tenemos configuradas das conexiones a bases de datos, una para mysql y otra para postgres.

Como se puede ver, es muy sencillo configurar una ó varias conexiones a bases de datos en la librería.