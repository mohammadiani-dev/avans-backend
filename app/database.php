<?php namespace avansdp;

class database extends singleton {

    const tables = [
        'points_transactions',
        'notifications'
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

    public function get_sql_create_points_transactions_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            action varchar(50) NOT NULL,
            score int(11) NOT NULL,
            total int(11) NOT NULL,
            status tinyint(1) NOT NULL,
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
            message_type varchar(50) NOT NULL,
            message TEXT NOT NULL,
            message_id bigint(20) NULL,
            parent_id bigint(20) NULL,
            status tinyint(1) NOT NULL,
            extera TEXT NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            scheduled_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        )";
    }

}