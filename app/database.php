<?php namespace avansdp;

class database extends singleton {

    const tables = [
        'points_transactions',
        'points_transactions_meta',
        'notifications',
    ];

    public function init(): void
    {
        if( version_compare( get_option( AVANS_PREFIX . 'db_version' ), AVANS_DB_VERSION , '!=' ) ){
            global $wpdb;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $charset_collate = $wpdb->get_charset_collate();

            $tables = apply_filters( AVANS_PREFIX . 'database_tables' , self::tables );

            foreach ($tables as $table){
                $table_name = $wpdb->prefix . AVANS_PREFIX . $table;

                if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
                    $sql = call_user_func([$this, 'get_sql_create_' . $table . '_tbl'], $table_name);
                    dbDelta($sql . " " . $charset_collate);
                }

            }
            update_option( AVANS_PREFIX . 'db_version' ,  AVANS_DB_VERSION );
        }
    }

    public function get_sql_create_points_transactions_meta_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            log_id bigint(20) NOT NULL,
            meta_key varchar(250) NOT NULL,
            meta_value TEXT NOT NULL,
            PRIMARY KEY  (id)
        )";
    }

    public function get_sql_create_points_transactions_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            action varchar(50) NOT NULL,
            score int(11) NOT NULL,
            total int(11) NOT NULL,
            status tinyint(1) NOT NULL DEFAULT 1,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expire_date datetime NULL,
            PRIMARY KEY  (id)
        )";
    }

    public function get_sql_create_notifications_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            type ENUM('toast', 'email', 'sms')  NOT NULL,
            template_id bigint(20) NULL,
            status ENUM('pending' , 'success' , 'failed') DEFAULT 'pending' NOT NULL,
            data json NULL,
            response varchar(256) NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            scheduled_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            sended_at timestamp NULL DEFAULT NULL,
            PRIMARY KEY  (id),
            INDEX idx_user_id (user_id), 
            INDEX idx_scheduled_at (scheduled_at),
            INDEX idx_status (status),
            INDEX idx_type (type)
        )";
    }

}