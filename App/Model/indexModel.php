<?php

class indexModel extends Model
{
    public function index()
    {
    	echo 'this is a model test<br />';
    }
    
    public function test()
    {
    	//$info = $this->db->info();
    	//$list = $this->db->select('myblog_category','*');
    	//$list = $this->db->select('myblog_category','name');
    	//$list = $this->db->select('myblog_category',array('cid','name'));
    	//$list = $this->db->select('myblog_category',array('cid','name'),array('cid[>]'=>4));
    	//$list = $this->db->select('myblog_category',array('cid','name'),array('cid'=>4));
    	//$list = $this->db->select('myblog_category',array('cid','name'),array('cid[>=]'=>4));
    	//$list = $this->db->select('myblog_category',array('cid','name'),array('cid[!]'=>4));
    	//$list = $this->db->select('myblog_category',array('cid','name'),array('cid[<>]'=>array(3,6)));
    	//$list = $this->db->select('myblog_category',array('cid','name'),array("AND"=>array('cid[>]'=>4,'name'=>'工具源码')));
    	//$list = $this->db->select('myblog_category',array('cid','name'),array("OR"=>array('cid[>]'=>6,'name'=>'工具源码')));
    	
    	//echo $this->db->last_query();
    	//var_dump($list);
    }
}