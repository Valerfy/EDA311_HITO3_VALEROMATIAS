# EDA311_HITO3_ApellidoNombre

Aplicacion web en PHP y MySQL con patron MVC para gestionar las tablas clientes, pedidos y productos (CRUD). Los registros se cargan en una lista doblemente enlazada propia y se ordenan con Bubble Sort directamente sobre la lista.

## Requisitos de software

- PHP 8.0 o superior con la extension mysqli habilitada
- Git
- Navegador web
- Acceso de red al servidor MySQL del docente (host, puerto, usuario y contrasena)

## Descargar el proyecto

```
git clone https://github.com/TU_USUARIO/EDA311_HITO3_ApellidoNombre.git
cd EDA311_HITO3_ApellidoNombre
```

## Configuracion del entorno PHP

1. Verificar la version: `php -v`
2. Verificar mysqli: `php -m | grep mysqli` (en Windows: `php -m | findstr mysqli`)
3. Si mysqli no aparece, abrir `php.ini` y quitar el punto y coma de la linea `extension=mysqli`.

No hace falta Apache ni XAMPP: se usa el servidor integrado de PHP.

## Configuracion de la conexion a MySQL

1. Copiar el archivo de ejemplo:
   - Linux/Mac: `cp config/config.example.php config/config.php`
   - Windows: `copy config\config.example.php config\config.php`
2. Editar `config/config.php` con los datos entregados por el docente:

| Clave | Que escribir |
|---|---|
| host | IP o dominio del servidor de la base de datos |
| puerto | Puerto entregado por el docente |
| usuario | Usuario entregado por el docente |
| clave | Contrasena entregada por el docente |
| nombre | Nombre de la base de datos |

3. En el arreglo `tablas`, el valor de cada elemento debe ser el nombre real de la tabla en la base de datos (por ejemplo, si la tabla se llama `cliente`, cambiar `'clientes' => 'cliente'`).

`config/config.php` esta en `.gitignore`, por lo que las credenciales reales no se suben a GitHub. En el repositorio solo existe `config/config.example.php`.

## Estructura del proyecto

```
EDA311_HITO3_ApellidoNombre/
├── config/
│   ├── config.example.php
│   └── config.php
├── models/
│   ├── Configuracion.php
│   ├── Conexion.php
│   ├── Tabla.php
│   ├── Nodo.php
│   └── ListaDoblementeEnlazada.php
├── controllers/
│   ├── Controlador.php
│   ├── InicioController.php
│   └── TablaController.php
├── views/
│   ├── ayudas.php
│   ├── inicio.php
│   ├── explorar.php
│   ├── listar.php
│   ├── formulario.php
│   ├── layout/
│   │   ├── encabezado.php
│   │   └── pie.php
│   └── parciales/
│       └── tabla.php
├── styles/
│   └── estilos.css
├── index.php
├── .gitignore
└── README.md
```

| Carpeta o archivo | Funcion |
|---|---|
| `index.php` | Punto de entrada: recibe el parametro `accion` y llama al controlador correspondiente |
| `config/` | Configuracion de la conexion y de los nombres de las tablas |
| `models/Conexion.php` | Conexion a MySQL con mysqli |
| `models/Tabla.php` | Lee la estructura de una tabla y ejecuta consultar, insertar, actualizar y eliminar con consultas preparadas; valida los datos |
| `models/Nodo.php` | Nodo de la lista doblemente enlazada |
| `models/ListaDoblementeEnlazada.php` | Lista doblemente enlazada y Bubble Sort |
| `controllers/TablaController.php` | Acciones listar, formulario, guardar y eliminar |
| `controllers/InicioController.php` | Menu principal y exploracion de la estructura de la BD |
| `views/` | Paginas HTML que se muestran al usuario |
| `styles/estilos.css` | Estilos de la interfaz |

## Ejecutar la aplicacion

1. Abrir una terminal en la carpeta del proyecto.
2. Ejecutar: `php -S localhost:8000`
3. Abrir en el navegador: `http://localhost:8000`
4. Entrar a **Estructura BD** para comprobar que la conexion funciona y ver las tablas.

## Exploracion de la base de datos

La aplicacion no asume una estructura fija: al entrar a **Estructura BD** lee con `SHOW COLUMNS` y `information_schema` los campos, tipos, claves primarias y claves foraneas de las tres tablas. Con esos datos construye los formularios y las consultas preparadas, valida enteros, decimales, fechas, campos obligatorios y longitud maxima de texto, y detecta la clave primaria de cada tabla.

## Operaciones CRUD

| Operacion | Como se usa |
|---|---|
| Registrar | Boton **Registrar nuevo** en la pagina de cada tabla |
| Consultar | Pagina de cada tabla (registros cargados en la lista doblemente enlazada) |
| Modificar | Boton **Editar** en cada fila; al guardar, la fila modificada se resalta en amarillo |
| Eliminar | Boton **Eliminar** en cada fila (pide confirmacion) |

## Solucion de problemas

| Mensaje o sintoma | Causa probable |
|---|---|
| `No se pudo conectar o consultar la base de datos` | Datos incorrectos en `config/config.php` o sin acceso de red al servidor |
| `Class "mysqli" not found` | Falta habilitar `extension=mysqli` en `php.ini` |
| `Table ... doesn't exist` | El nombre real de la tabla no coincide con el valor del arreglo `tablas` |
| Error de clave foranea al registrar o eliminar | El valor referenciado no existe o el registro tiene dependientes |

## Lista doblemente enlazada

- `Nodo` guarda los datos de un registro (`datos`) y dos referencias: `anterior` y `siguiente`.
- `ListaDoblementeEnlazada` administra la `cabeza`, la `cola` y el `tamano`.
- `insertarAlInicio` e `insertarAlFinal` crean un nodo y actualizan los enlaces en ambos sentidos.
- `Tabla::cargarLista` ejecuta `SELECT *` y por cada fila llama a `insertarAlFinal`.
- `recorrer()` recorre la lista desde `cabeza` avanzando con `siguiente` hasta llegar a `null`; `recorrerAtras()` la recorre desde `cola` retrocediendo con `anterior`. Las vistas usan `recorrer()` para dibujar las tablas.

## Bubble Sort

El metodo `ordenarBurbuja($campo, $ascendente)` trabaja directamente sobre los nodos, sin convertir la lista en arreglo:

1. Se recorre la lista comparando cada nodo con su siguiente.
2. Si estan en el orden incorrecto, se intercambian los datos de ambos nodos.
3. Al terminar cada pasada, el ultimo nodo comparado queda en su posicion final y se usa como limite de la siguiente pasada.
4. El proceso termina cuando una pasada no hace intercambios.

Criterio de comparacion: el usuario elige el campo y la direccion (ascendente o descendente) en la vista de cada tabla. Si ambos valores son numericos se comparan como numeros; en caso contrario se comparan como texto sin distinguir mayusculas. La pantalla muestra tambien el numero de comparaciones e intercambios.
