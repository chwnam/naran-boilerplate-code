<?php
/**
 * NBPC: Activation reg.
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NBPC_Reg_Activation' ) ) {
	class NBPC_Reg_Activation implements NBPC_Reg {
		/** @var Closure|array|string */
		public $callback;

		public array $args;

		public bool $error_log;

		/**
		 * @param Closure|array|string $callback
		 * @param array                $args
		 * @param bool                 $error_log
		 */
		public function __construct( $callback, array $args = [], bool $error_log = false ) {
			$this->callback  = $callback;
			$this->args      = $args;
			$this->error_log = $error_log;
		}

		/**
		 * Method name can mislead, but it does its activation callback job.
		 *
		 * @param null $dispatch
		 */
		public function register( $dispatch = null ) {
			try {
				$callback = nbpc_parse_callback( $this->callback );
			} catch (Exception $e) {
				$error = new WP_Error();
				$error->add(
					'nbpc_activation_error',
					sprintf(
						'Activation callback handler `%s` is invalid. Please check your activation register items.',
						$this->callback
					)
				);
				wp_die( $error );
			}

			if ( $callback ) {
				if ( $this->error_log ) {
					error_log( error_log( sprintf( 'Activation callback started: %s', $this->callback ) ) );
				}

				call_user_func( $callback, $this->args );

				if ( $this->error_log ) {
					error_log( sprintf( 'Activation callback finished: %s', $this->callback ) );
				}
			}
		}
	}
}
