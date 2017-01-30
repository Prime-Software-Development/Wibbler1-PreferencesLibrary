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

	public function __construct(array $options = array()) {
		parent::__construct();

		if(!isset($options['namespace'])) {
			$namespace = $options['namespace'];

			$this->set_namespace($namespace);
		}
	}

	public function set_namespace( $namespace ) {
		$this->namespace = $namespace;
		$query_function = "\\" . $this->namespace . "\\PreferencesQuery";
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