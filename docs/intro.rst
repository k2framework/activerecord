Introducción
============
En esta sección a nivel de introducción explicaremos porqué debería usarse un ORM como ActiveRecord
y las ventajas que representa este para una aplicación profesional en PHP.

¿Qué es?
--------
ActiveRecord es un componente que implementa el patrón ORM (Object Relational-Mapping), gracias a este,
podemos trabajar con bases de datos de manera orientada a objetos, encapsulando muchos detalles de bajo nivel,
facilitando la manutención del código y haciendo las aplicaciones menos propensas a errores.

¿Por qué usar ActiveRecord?
---------------------------

En el pasado era normal usar código como el siguiente para acceder a bases de datos:

.. code-block:: php

	<?php

	mysql_query("SELECT * FROM usuarios LIMIT 10");
	while ($usuario = mysql_fetch_array()) {
		echo $usuario['nombre'];
	}

Y si queriamos actualizar los registros consultados haciamos algo como esto:

.. code-block:: php

	<?php

	mysql_query("SELECT * FROM usuarios LIMIT 10");
	while ($usuario = mysql_fetch_array()) {
		mysql_query("UPDATE usuarios SET fecha = '".date('Y-m-d').'" WHERE id = '.$usuario['id']);
	}

Esta forma de trabajar representa muchas desventajas para nuestra aplicación:

* Usamos funciones que están atadas a un motor especifico (MySQL en este caso) haciendo dificil usar la aplicación en otros motores
* Escribir SQL nos lleva a usar extensiones que puede que no sean compatibles en otros motores (por ejemplo LIMIT)
* Escribir SQL requiere de cuidado pues al olvidar una simple comilla podriamos hacer fallar la aplicación
* Si nuestra aplicación es orientada a objetos estaríamos mezclandola con programación estructurada lo cual es una mala práctica
* El código podría ser potencialmente suceptible a ataques de inyección de SQL si no se toman las debidas precauciones

¿Qué es un modelo?
------------------
Las bases de datos relacionales almacenan los datos en tablas de esta manera se organiza la naturaleza de la información.
Las tablas tienen columnas, cada columna representa un atributo de una entidad. La siguiente tabla "personas" nos muestra esto:

.. code-block:: bash

	mysql> desc personas;
	+------------------+------------------+------+-----+---------+----------------+
	| Field            | Type             | Null | Key | Default | Extra          |
	+------------------+------------------+------+-----+---------+----------------+
	| id               | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
	| apellidos        | varchar(120)     | NO   |     | NULL    |                |
	| nombres          | varchar(120)     | NO   |     | NULL    |                |
	| fecha_nacimiento | date             | YES  |     | NULL    |                |
	| estado_civil     | varchar(24)      | YES  |     | NULL    |                |
	| nacionalidad     | varchar(50)      | YES  |     | NULL    |                |
	+------------------+------------------+------+-----+---------+----------------+
	6 rows in set (0.00 sec)

Esta tabla tiene algunos atributos para representar a una persona, algunos datos en esta tabla podrían ser los siguientes:

.. code-block:: bash

	mysql> select * from personas;
	+----+-----------+---------+------------------+--------------+--------------+
	| id | apellidos | nombres | fecha_nacimiento | estado_civil | nacionalidad |
	+----+-----------+---------+------------------+--------------+--------------+
	|  1 | Perez     | Juan    | 1979-05-17       | Casado       | Mexicano     |
	|  2 | Martinez  | Rosario | 1985-11-10       | Soltero      | Peruano      |
	|  3 | Arenas    | Manuel  | 1963-08-24       | Viudo        | Boliviano    |
	+----+-----------+---------+------------------+--------------+--------------+
	3 rows in set (0.00 sec)

En ActiveRecord para representar una tabla creamos una clase, una clase asociada a una tabla es un modelo. La siguiente clase
"Personas" es una clase que "mapea" la tabla "personas":

.. code-block:: php

	<?php

	class Personas extends \ActiveRecord\Model
	{

	}

Un modelo no requiere una implementación muy sofisticada inicialemente, solamente extender la clase "\\ActiveRecord\\Model".
Las tablas son clases y las filas son objectos. Las columnas son atributos de las clases. De la siguiente manera entendemos que:

* Crear una instancia de una clase es crear una fila
* Actualizar los atributos de un objeto es actualizar los valores de sus columnas
* Eliminar un objeto es eliminar la fila

Veámolo en código:

.. code-block:: php

	<?php

	//Creamos una nueva persona
	$persona = new Personas();

	//Asignamos valores a sus atributos
	$persona->apellidos = 'Alvarez';
	$persona->nombre = 'Carolina';
	$persona->fecha_nacimiento = '1991-10-14';
	$persona->estado_civil = 'Soltera';
	$persona->nacionalidad = 'Uruguaya';

	//Guardamos el nuevo registro
	$persona->save();

El anterior código agregaría un nuevo registro a la tabla "personas":

.. code-block:: bash

	mysql> select * from personas;
	+----+-----------+----------+------------------+--------------+--------------+
	| id | apellidos | nombres  | fecha_nacimiento | estado_civil | nacionalidad |
	+----+-----------+----------+------------------+--------------+--------------+
	|  1 | Perez     | Juan     | 1979-05-17       | Casado       | Mexicano     |
	|  2 | Martinez  | Rosario  | 1985-11-10       | Soltero      | Peruano      |
	|  3 | Arenas    | Manuel   | 1963-11-10       | Viudo        | Boliviano    |
	|  4 | Alvarez   | Carolina | 1991-10-14       | Soltera      | Uruguaya     |
	+----+-----------+----------+------------------+--------------+--------------+
	4 rows in set (0.00 sec)

Como pudimos ver el registro es insertado sin requerir escribir SQL tan solo usando programación orientada a objetos.
ActiveRecord también nos permite consutlar registros existentes, actualizarlos y eliminarlos si es necesario:

.. code-block:: php

	<?php

	//Consultar la persona con id=1
	$persona = Personas::first(1);

	//Cambiar su estado civil
	$persona->estado_civil = 'Soltero';

	//Guardar los cambios
	$persona->save();

	//Consultar todas las personas Bolivianas
	foreach (Personas::findAllBy('nacionalidad', 'Boliviano') as $persona) {
		echo $persona->nombres;
	}

	//Eliminar todas las personas Casadas
	Personas::createQuery()
				->where('estado_civil = :edo_civil')
				->bindValue('edo_civil', 'Casado');
	
	Personas::deleteAll();

