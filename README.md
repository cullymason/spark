spark
=====

Extending Laravel/Eloquent to return Ember friendly JSON

> Original Gist: [explaination link](https://gist.github.com/cullymason/6198667)

## Basic Explaination

It would be nice if we had a plugin that allows Laravel to play nice with Ember. Put simply I would like to create an easy way for Laravel to output JSON strings in a way that Ember likes.

### What Laravel Does


```
return Model::find($id);

// OR

return Model::all();
```

You are returned a raw json object like so:

```
//One Model
{"id": 1, "other attribute": "something"}

// Array of Models
[{"id": 2, "other attribute": "something"},{"id": 3, "other attribute": "something"},{"id": 1, "other attribute": "something"}]
```



### What Ember expects


```
{ "model": {"id": 1, "otherAttribute": "something"} }

// or

{ "models": [{"id": 2, "otherAttribute": "something"},{"id": 3, "otherAttribute": "something"},{"id": 1, "otherAttribute": "something"}] }
```

The code to make this work really is not that bad:

```
return $a['model'] = Model::find($id)->toArray();
```

***

### Relationship Overload

But it gets increasingly cumbersome and repetitive when relationships are added into the mix. 

For example, if a post has many comments, Ember wants JSON to to be returned like so:

```
{
	"post": {'id': 1,'post_title':'This is a title', 'text':'post text','comment_ids': [1,2,3]}},
	"comments":{[
		{'id':1,'post_id:'1','text':'test'},
		{'id':2,'post_id:'1','text':'test'},
		{'id':3,'post_id:'1','text':'test'}]
}
```

It would be nice if there was a way to type something like: 

```
return Post::find($id)->ember();
```
or maybe...

```
$post = Post::find($id);
return Response::ember($post);
```

And it automatically does a few things:

- creates a json string in the format of "{model_name: {json representation of that model}}
- somehow distinguish if there are any relationships associated with that model
- if there are:
	- Add their ids to the Model's JSON string
	- create a json array of all of those relationships
	- and recursively add ids and json arrays if those relationships have them 
	_(For example,if a category has posts and those posts have comments)_

#### Thoughts?
