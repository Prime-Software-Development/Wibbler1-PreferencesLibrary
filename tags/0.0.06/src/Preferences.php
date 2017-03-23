<?php
namespace Trunk\PreferencesLibrary\Modules;
/**
 * Created by PhpStorm.
 * User: nas
 * Date: 07/07/15
 * Time: 09:46
 */
class Preferences extends \Trunk\Wibbler\Modules\base {
	/**
	 * Array of preferences
	 * @var null
	 */
	protected $preferences = null;

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
		$preferences = $query_function::create()
			->select( [ 'Code', 'Value' ] )
			->find();

		$this->preferences = $preferences->toKeyValue( 'Code', 'Value' );
	}

	/**
	 * Return Preference value by code
	 * @param $code
	 * @return null
	 */
	public function get( $code ) {
		return isset( $this->preferences[ $code ] ) ? $this->preferences[ $code ] : null;
	}

	/**
	 * Set the preference to the given value
	 * @param $code
	 * @param $value
	 */
	public function set( $code, $value ) {

		$query_function = "\\" . $this->namespace . "\\PreferencesQuery";
		$preference = $query_function::create()
			->filterByCode( $code )
			->findOne();
		$preference->setValue( $value );
		$preference->save();
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