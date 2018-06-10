<?php

/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel-Gearman
 * @version    0.5
 * @author     Miura Daisuke
 * @license    MIT License
 * @link
 */

namespace Gearman;


class Gearman {


    protected static $try       = 3;
    protected static $cluster   = false;


    /**
     * 開始処理
     *
     * @param  string $name   confing name
     * @return array  $config config
     * @throws GearmanException
     * @access public static
     */
    protected static function _init($name = null) {
        if (!extension_loaded('gearman')) {
            throw new GearmanException('You need install PECL Gearman extension. see http://php.net/manual/en/book.gearman.php');
        }

        \Config::load('gearman', true);
        if ( $name === null ) {
            $name = \Config::get('gearman.active');
        }
        $config = \Config::get('gearman.'.$name, []);
        if ( empty( $config['connection']['host'] ) ) {   unset( $config['connection']['host'] );   }
        if ( empty( $config['connection']['port'] ) ) {   unset( $config['connection']['port'] );   }

        return  $config;
    }

    public static function client( string $job_name, string $name = null ) {
        $config = self::_init($name);
        $server = self::_connection_conf($config['connection']);
        $geaman = new GearmanClient($config, $job_name, self::$try);
        unset( $config );
        self::_server_connect( $server, $geaman );

        return  $geaman;
    }

    /**
     * Create a Gearman Job server
     *
     * @param  string $name confing name
     * @return \Gearman\GearmanWorker
     * @throws GearmanException
     * @access public static
     */
    public static function worker($name = null) {
        $config = self::_init($name);
        $server = self::_connection_conf($config['connection']);
        $geaman = new GearmanWorker();
        unset( $config );

        self::_server_connect( $server, $geaman );

        return  $geaman;
    }

    /**
     * Connect to the server
     *
     * @param  array   $server server configuration
     * @param  object &$geaman gearman class
     * @throws GearmanException
     * @access protected static
     */
    protected static function _server_connect(array $server, &$geaman) {
        $try    = self::$try;

        do {
            try {
                $try--;
                if      ( self::$cluster )           {  $geaman->addServers($server['host']);   }
                else if ( isset( $server['port'] ) ) {  $geaman->addServer($server['host'], $server['port']);   }
                else                                 {  $geaman->addServer($server['host']);    }
                $try    = 0;
            } catch (\GearmanException $e) {
                if ( $try === 0 ) {
                    throw new GearmanException('Failed to connect.', $e->getCode(), $e->getPrevious(), $e->getFile(), $e->getLine());
                }
                sleep( 3 );
            }
        } while ( $try );
    }

    /**
     * 接続先の調整
     *
     * @param  array $connection config connection
     * @return array
     * @access protected static
     */
    protected static function _connection_conf($connection) {
        if ( is_numeric( key( $connection ) ) ) {
            foreach ( $connection as $key => $value ) {
                if ( is_array( $value ) ) {
                    $connection[$key]   = $value['host'];
                    if ( isset( $value['port'] ) ) {
                        $connection[$key]  .= ':'.$value['port'];
                    }
                }
            }
            $server = ['host' => implode(',', array_unique( $connection ))];
            self::$cluster      = true;
        }
        else {
            $host   = $connection['host'];
            $port   = isset( $connection['port'] ) ? $connection['port'] : 4730;
            $server = ['host' => $host, 'port' => $port];
        }

        return  $server;
    }


}