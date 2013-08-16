<?php

class Spark extends Eloquent {
	private $model_name;
	private $hasRelationships;
	public function __construct($attributes = array())
	{
	  parent::__construct($attributes);
	  $this->model_name = $this->fetch_model_name();
	  $this->hasRelationships = $this->checkRelationships();
	}
	protected $relationships = null;

	public function ember() {
		$model_arr = array();
		$model_arr[$this->model_name] = $this->toArray();
		foreach ($model_arr[$this->model_name] as $attr => $value)
		{
			$new_attr = camel_case($attr);
			$model_arr[$this->model_name][$new_attr] = $value;
			unset($model_arr[$this->model_name][$attr]);
		};
		if($this->hasRelationships)
		{
			$relationships = $this->buildRelationships();
			foreach($relationships as $key=>$value)
			{
				$model_arr[$this->model_name][$key] = $value;
			}
		};
		return $model_arr;
	}

	private function fetch_model_name() {
		$model_name = get_class($this);
		$model_name = strtolower($model_name);
		return $model_name;
	}
	private function checkRelationships()
	{
		if(!empty($this->relationships))
		{
			return true;
		}
		else
		{
			return false;
		};
	}

	private function buildRelationships()
	{
		$relationships = $this->relationships;
		$relationships_arr = array();
		foreach($relationships as $key=>$value)
		{
			$id;
			if($value)
			{
				$id = 'ids';
			}else
			{
				$id = 'id';
			}
			$relationships_arr[str_singular($key).'_'.$id] = $this->$key->lists('id');
			
		}
		return $relationships_arr;
	}

}