<?php
/*
	Spark

	Goal - To be able to call Model::find($id)->ember() & Model::all()->ember()
		   and return Ember loving JSON
*/

class Spark extends Eloquent {
	private $model_name;
	// Contains an array that follows array(relationship_function=>false if belongs_to, else true,...)
	private $hasRelationships;

	public function __construct($attributes = array())
	{
	  parent::__construct($attributes);
	  // get the model name and make sure its lowercase 
	  $this->model_name = $this->fetch_model_name();

	  //check if there are relationships for that model
	  $this->hasRelationships = $this->checkRelationships();
	}
	protected $relationships = null;

	/*
		ember()

		main function that creates the ember friendly JSON
	*/
	public function ember() {
		// array that will be returned
		$model_arr = array();
		//convert it to array so it can be manipulated easier
		$model_arr[$this->model_name] = $this->toArray();

		// converts all of the keys of the array to camel case
		foreach ($model_arr[$this->model_name] as $attr => $value)
		{
			$new_attr = camel_case($attr);
			$model_arr[$this->model_name][$new_attr] = $value;
			unset($model_arr[$this->model_name][$attr]);
		};

		if($this->hasRelationships)
		{
			// build the relationships in the form of relationship_ids: [id,id,id]
			$relationships = $this->buildRelationships();

			// add them to the array to be returned
			foreach($relationships as $key=>$value)
			{
				$model_arr[$this->model_name][$key] = $value;
			}
		};

		return $model_arr;
	}

	/*
		fetch_model_name()

		returns the model name that is Ember friendly (lowercase)
	*/
	private function fetch_model_name() {
		$model_name = get_class($this);
		$model_name = strtolower($model_name);
		return $model_name;
	}
	/*
		checkRelationships()

		returns true if there are relationships for that model
	*/
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

	// builds the relationship arrays
	private function buildRelationships()
	{
		$relationships = $this->relationships;
		//array to be returned
		$relationships_arr = array();

		foreach($relationships as $key=>$value)
		{
			//$id is set to ids if it is a many to many or one to many, else id
			$id;
			if($value)
			{
				$id = 'ids';
			}else
			{
				$id = 'id';
			}

			// Converts the key to be singular and adds the _$id to it
			// gets a list of the ids for that related model
			// adds it to the array to be returned

			$relationships_arr[str_singular($key).'_'.$id] = $this->$key->lists('id');
			
		}
		return $relationships_arr;
	}

}