<?php
namespace Trunk\PreferencesLibrary\Modules;

class Preferences extends \Trunk\Wibbler\Modules\base {
	/**
	 * Array of preferences
	 * @var null
	 */
	protected $preferences = null;

	/**
	 * @var Prefs[]
	 */
	protected $preferencesArray = null;

	/**
	 * Namespace the preferences table is part of
	 * @var string
	 */
	private $namespace = null;

	/**
	 * Name of the preferences table
	 * @var string
	 */
	private $table_name = "Preferences";

	public function __construct(array $options = null) {
		parent::__construct();

		if ( $options ) {
			if ( isset( $options[ 'namespace' ] ) ) {
				$this->set_namespace($options['namespace'], false );
			}
			if ( isset( $options[ 'table_name' ] ) ) {
				$this->set_table_name($options['table_name']);
			}

			$this->retrieve_preferences();
		}
	}

	/**
	 * Sets the namespace for the database queries
	 * @param string $namespace
	 * @param bool   $retrieve_preferences Whether to retrieve the data now
	 */
	public function set_namespace( $namespace, $retrieve_preferences = true ) {
		$this->namespace = $namespace;
		if ( $retrieve_preferences ) {
			$this->retrieve_preferences();
		}
	}

	/**
	 * Set the name of the table holding the preferences
	 * @param string $table_name
	 * @param bool   $retrieve_preferences Whether to retrieve the data now
	 */
	public function set_table_name( $table_name, $retrieve_preferences = true ) {
		$this->table_name = $table_name;
		if ( $retrieve_preferences ) {
			$this->retrieve_preferences();
		}
	}

	/**
	 * Actually retrieve the preferences
	 */
	private function retrieve_preferences() {
		$query_function = "\\" . $this->namespace . "\\" . $this->table_name . "Query";
		$preferences = $query_function::create()->find();

		// Iterate over the preferences
		foreach( $preferences as $pref ) {
			// Get the code for the preference
			$code = $pref->getCode();
			// Fill the simple key => value array
			$this->preferences[ $code ] = $pref->getValue();
			// Fill the full object array
			$this->preferencesArray[ $code ] = $pref;
		}
	}

	/**
	 * Return the preference by code
	 * @param $code
	 * @return Prefs|null
	 */
	public function getPref( $code ) {
		return isset( $this->preferencesArray[ $code ] ) ? $this->preferencesArray[ $code ] : null;
	}

	/**
	 * Return Preference value by code
	 * @param $code
	 * @return null
	 */
	public function get( $code ) {
		$pref = $this->getPref( $code );
		return $pref ? $pref->getValue() : null;
	}

	/**
	 * Set the preference to the given value
	 * @param $code
	 * @param $value
	 */
	public function set( $code, $value ) {

		// Get the preference
		$preference = $this->getPref( $code );

		if ( $preference === null )
			return $this;

		$preference->setValue( $value );
		$preference->save();

		return $this;
		/*$query_function = "\\" . $this->namespace . "\\" . $this->table_name . "Query";
		$preference = $query_function::create()
			->filterByCode( $code )
			->findOne();
		$preference->setValue( $value );
		$preference->save();*/
	}

	/**
	 * Returns all preferences as array ( can be filtered by array of preference codes )
	 * @param null $codes
	 * @return array|null
	 */
	public function toArray( $codes = null ) {
		$preferences = $this->preferences;
		if ( ( is_array( $codes ) ) or ( $codes instanceof Traversable ) ) {
			$preferences = [ ];
			foreach ( $codes as $key => $code ) {
				if ( isset( $this->preferences[ $code ] ) ) {
					$preferences[ $code ] = $this->preferences[ $code ];
				}
			}
		}
		return $preferences;
	}
}
