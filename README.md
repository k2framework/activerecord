ActiveRecord 2
==============

Nuevo Active Record para la nueva versión de KumbiaPHP que trabaja con PHP 5.3 ó superior, trabaja con PDO.

Aunque es una librería realizada con la finalidad de ofrecer una capa de abstracción a base de datos para el
framework KumbiaPHP, esta nueva versión puede ser usada en cualquier proyecto PHP.

Actualmente no tiene su propio autoloader, por lo que para usarlo se debe registrar la ruta hacia lib en
cualquier autoloader que cumpla las especificaciones PSR-0.

Configuración
-------------
Configuración de la conexión a la base de datos:

```php

<?php

use ActiveRecord\Config\Config;
use ActiveRecord\Config\Parameters;

Config::add(new Parameters("default", array(
    'username' => 'root',
    'password' => 'contraseña',
    'host' => 'localhost', //por defecto localhost
    'type' => 'mysql',
    'port' => '3306', //si no se especifica se usa el puerto por defecto del gestor de base de datos usado.
    'name' => 'nombre_base_de_datos',
)));

?>
```

Con estos sencillos pasos ya tenemos configurada nuestra conexión a la base de datos.

Creando un Modelo
-----------------

```php

<?php

use ActiveRecord\Model;

class Usuarios extends Model
{

}
?>
```

Ahora nuestra clase usuario posee todos los métodos básicos para el acceso y comunicación con nuestra base de datos.
por defecto el nombre de la tabla es el nombre del módelo en notación small_case, sin embargo para casos donde no se
pueda cumpliar la conversión, podemos especificar el nombre de la tabla como un atributo de la clase, ejemplo:

```php

<?php

use ActiveRecord\Model;

class Usuarios extends Model
{
    //La tabla en la base de datos se llama users
    protected $table = 'users';
}

?>
```

Consultando registros
---------------------
La lib ofrece una serie de métodos para la realización de consultas a nuestra base de datos, veamos algunos ejemplos:

```php

<?php

//Consultando todos los registros en la tabla.
//Devuelve todos los registros de la tabla en la base de datos

$usuarios = Usuarios::findAll();
foreach($usuarios as $usuario){
    //Cada elemento iterado en el foreach es un objeto Usuarios
    echo $usuario->nombres;
}

//Obteniendo el resultados como una matriz
//Devuelve todos los registros de la tabla en la base de datos como un arreglo.

$usuarios = Usuarios::findAll("array");
foreach($usuarios as $usuario){
    //Cada elemento iterado en el foreach es un arreglo
    echo $e["nombres"];
}

?>
```

Filtrando Consultas
-------------------

Para filtrar consultas el active record nos ofrece una clase DbQuery que nos permitirá construir
consultas SQL de manera orientada a Objetos.

```php

<?php

//El metodo createQuery() crea y nos devuelve una instancia de DbQuery

Usuarios::createQuery()
    ->where("nombres = :nom")
    ->bindValue("nom", "Manuel José");

//ya que el active record trabaja con PDO, y este permite crear consultas preparadas, es decir, los valores
//de variables no se colocan directamente en la cadena de consulta, sino que se pasan a traves de métodos
//de la clase PDO, que se encargan de filtrar y sanitizar los valores de la consulta, el DbQuery permite establecer
//estos valores directamente en su clase a través de los métodos bindValue($param,$value) y bind($params).

$usuarios = Usuarios::findAll(); //aunque llamemos al mismo metodo findAll, esté va a filtrar los datos por medio de
                                //las especificaciones indicadas en la instancia del DbQuery.

//mostramos los registros que nos devolvió la consulta:
foreach($usuarios as $usuario){
    //Cada elemento iterado en el foreach es un objeto Usuarios
    echo $usuario->nombres;
}

?>
```
