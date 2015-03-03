<?php

class Gcm extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'gcms';

	protected $fillable = ['name', 'email', 'regid'];

}
