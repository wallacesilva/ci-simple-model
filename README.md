CodeIgniter Simple Model
===============

A simple model to extend CodeIgniter Model.

Installation
----

Place `MY_Model.php` into the application/core folder. Make sure your models extend MY_Model and you are ready to go!

Usage
----

Create a new model class with an appropriate name, that extends MY_Model. You are advised to fill in the following attributes:

    <?php defined('BASEPATH') OR exit('No direct script access allowed');

    class Departments_model extends MY_Model 
    {   
        public $_tablename = 'department';
    }

- **$_tablename:** the name of the database table; *Required*

Methods
----

- **countAll( $where = array() ):** Count all records with where
- **find( $id = 0 ):** Get a Row/record from the database table
- **findAll( $options = array() ):** Get records with where, order, offset and limit params
- **getAll( $offset=0, $limit=10 ):** Get All records without where's only offset and limit
- **save( $data = array(), $get_last_id_inserted=false ):** Insert/Update row(s) from the database table
- **delete( $id = 0 ):** Delete a row from the database table
- **deleteAll( $where = array() ):** Delete multiple rows from the database table

Database Connection
-------------------

The class will automatically use the default database connection, and even load it for you if you haven't yet.

You can specify a database connection calling `$this->db->database('db_group_name', TRUE)`. The CodeIgniter default is 'db'.

See ["Connecting to your Database"](http://ellislab.com/codeigniter/user-guide/database/connecting.html) for more information.

Example:
----
    <?php
    
    //...
    $this->load->model('departments_model'); // departments example
    $options = array(   'where'  => array('id'   => 1), 
                        'order'  => array('name' => 'asc'), 
                        'offset' => 0, 
                        'limit'  => 10
                    );
    $departments = $this->departments_model->findAll( $options );
    foreach ($departments as $item) 
    {
        echo $item->name;
    }
    //...

    ?>

Changelog
----

**Version 0.1.0**

- Initial Releases