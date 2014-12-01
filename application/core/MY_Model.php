<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * A simple model to agilize our work, 
 * this class model uses CodeIgniter Active Record to query build
 *
 * @link https://github.com/wallacesilva/ci-simple-model
 * @copyright Copyright (c) 2014, Wallace Silva <http://wallacesilva.com>
 */

class MY_Model extends CI_Model 
{   
    /**
     * This model's default database table name. Set on extend.
     */
    public $_tablename;

    /**
     * Auto change table to get methods from $CI->db
     */
    public $_auto_requiretable = true;

    /**
     * Methods to auto set tablename
     */
    public $_requiretable = array(  'get', 'get_where', 'count_all_results', 'count_all', 
                                    'insert', 'insert_batch', 'update', 'update_batch', 
                                    'delete', 'empty_table', 'truncate'
                                ); // possible 'from'
    
    /**
     * Initialise the model, this use the default database $this->db.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function __call($method, $args)
    {       

        if (is_callable( array($this, $method) )){ // call method on this class first (for overwrite methods)

            return call_user_func_array( array($this, $method), $args );

        } elseif (is_callable( array($this->db, $method) )) { // call method from db

            $method_args = $args;

            if ($this->_auto_requiretable && in_array($method, $this->_requiretable)) {

                $method_args = array($this->_tablename);
                $method_args = array_merge($method_args, $args);

            }

            return call_user_func_array( array($this->db, $method), $method_args );

        } else {

            throw new Exception("Error on call method (".$method.") with arguments (".(string)$args.")", 1);
            return false;
            
        }

    }

    /**
     * Check if item/id exists on database
     * @param integer $id Id item from table
     * @return boolean 
     */
    public function check_exist($id)
    {
        // verifica sem tem mensagem
        if (empty($message_error))
            $message_error = 'Ops! Erro ao processar dados.';

        // if not valid :false
        if ($id < 1) 
            return false;

        $item = $this->find($id);

        return (bool)(isset($item->id) && $item->id == $id);

    }

    public function check_exist_redirect($id, $message_error=null, $redirect_to=false)
    {   
        if (!$this->check_exist($id)) {

            $this->session->set_flashdata('msg_error', $message_error);
            redirect($redirect_to);

        } 
        
    }

    /**
     * Count all records with where
     * @param array/string $where Where to count all records
     */
    public function countAll( $where=null )
    {

        if( is_array($where) )
          $this->db->where( $where );

        $total_results = $this->db->count_all_results( $this->_tablename );

        return $total_results;

    }

    /**
     * Get a Row/record from the database table
     * @param integer $id Id to get record
     */
    public function find( $id )
    {

        if( $id < 1 )
          return false;

        $this->db->where('id', $id);
        return $this->db->get( $this->_tablename )->result();

    }

    /**
     * findAll - Get records with where, order, offset and limit params
     * @param array $options Get options to find rows 
     * @example $options =  array('where' => array('id'   => 1), 
     *                            'order' => array('name' => 'asc'), 
     *                            'offset' => 0, 
     *                            'limit' => 10)
     * @return array Return array with rows
     * @author Wallace Silva
     */
    public function findAll( $options = null )
    {

        if( !is_array($options) )
          $options = (array)$options;

        $default = array('where' => null, 'order' => null, 'offset' => 0, 'limit' => 0, 'group' => null);

        $options = array_merge($default, $options);

        if( is_array($options['where']) )
          $this->db->where( $options['where'] );

        if( is_array($options['order']) )
          foreach ($options['order'] as $order_key => $order_value) 
            $this->db->order_by( $order_key, $order_value );

        if( is_array($options['group']) )
          foreach ($options['group'] as $group) 
            $this->db->group_by( $group );

        if( $options['limit'] > 0 && $options['offset'] >= 0 )
          $this->db->limit($options['limit'], $options['offset']);

        $result = $this->db->get( $this->_tablename );

        return $result->result();

    }

    /**
     * Get All records without where's only offset and limit
     * @param integer $offset Define number to start get/return records
     * @param integer $limite Define number to records to return
     */
    public function getAll( $offset=0, $limit=10 )
    {

        $this->db->limit($limit, $offset);

        $result = $this->db->get( $this->_tablename );

        return $result->result();    

    }

    /**
     * Insert/Update row(s) from the database table
     * @param array $data Array with data to inserted/update, if set 'id' this update
     * @param bool $get_last_id_inserted Define if want return last id inserted
     */
    public function save( $data, $get_last_id_inserted=false )
    {

        if( !is_array($data) )
          return false;

        if( isset($data['id']) ){

          $id = $data['id'];
          $this->db->where('id', $id); // set id
          unset($data['id']);

          return $this->db->update( $this->_tablename, $data );
          
        } else {

          $inserted = $this->db->insert( $this->_tablename, $data );
          
          if ($get_last_id_inserted)
            return $this->db->insert_id();
          else
            return $inserted;

        }

    }

    /**
     * Delete a row from the database table
     * @param integer $id Id to be removed
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete( $this->_tablename );
    }

    /**
     * Delete multiple rows from the database table
     * @param integer $id Id to be removed
     */
    public function deleteAll($items)
    {
        $this->db->where($items);
        return $this->db->delete( $this->_tablename );        
    }

}