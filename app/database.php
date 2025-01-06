<?php namespace avansdp;

class database extends singleton {

    private string $prefix;
    private string $avans_prefix;

    const tables = [
        'segments',
        'scenarios',
        'scenario_schedules',
        'point_transactions',
        'wallet_transactions',
        'notifications',
        'user_segment_relationships',
        'group_relationships',
        'scenario_segment_relationships',
        'point_transactions_meta',
        'segment_meta'
    ];

    public function init(): void
    {
        if( version_compare( get_option( AVANS_PREFIX . 'db_version' ), AVANS_DB_VERSION , '!=' ) ){
            global $wpdb;

            $this->prefix = $wpdb->prefix;
            $this->avans_prefix = $wpdb->prefix .  AVANS_PREFIX;

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $charset_collate = $wpdb->get_charset_collate();

            $tables = apply_filters( AVANS_PREFIX . 'database_tables' , self::tables );

            foreach ($tables as $table){
                $table_name = $wpdb->prefix . AVANS_PREFIX . $table;

                if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                    $sql = call_user_func([$this, 'get_sql_create_' . $table . '_tbl'], $table_name);
                    dbDelta($sql . " " . $charset_collate);
                }

            }
            update_option( AVANS_PREFIX . 'db_version' ,  AVANS_DB_VERSION );
        }
    }


    public function get_sql_create_point_transactions_meta_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            log_id bigint UNSIGNED NOT NULL,
            meta_key varchar(256) NOT NULL,
            meta_value TEXT NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (log_id) REFERENCES `{$this->avans_prefix}point_transactions`(id) ON DELETE CASCADE,
            INDEX idx_log_id (log_id),
            INDEX idx_meta_key (meta_key)
        )";
    }

    public function get_sql_create_point_transactions_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint UNSIGNED NOT NULL,
            type varchar(50) NOT NULL,
            score int(11) NOT NULL,
            balance int(11) NOT NULL,
            total int(11) NOT NULL,
            object_id bigint UNSIGNED NOT NULL,
            status tinyint(1) NOT NULL DEFAULT 1,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expired_at timestamp NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (user_id) REFERENCES `{$this->prefix}users`(ID) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_type (type),
            INDEX idx_score (score),
            INDEX idx_status (status),
            INDEX idx_expired_at (expired_at)
        )";
    }

    public function get_sql_create_scenario_schedules_tbl( $table_name ): string
    {

        return "CREATE TABLE $table_name (
            id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint UNSIGNED  NOT NULL,
            scenario_id bigint UNSIGNED NOT NULL,
            status tinyint(1) NOT NULL,
            scheduled_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            FOREIGN KEY (user_id) REFERENCES `{$this->prefix}users`(ID) ON DELETE CASCADE,
            FOREIGN KEY (scenario_id) REFERENCES `{$this->avans_prefix}scenarios`(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_status (status),
            INDEX idx_scheduled_at (scheduled_at)
        )";
    }
    public function get_sql_create_scenarios_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            type varchar(80)  NOT NULL,
            name varchar(512) NOT NULL,
            hook varchar(512) NOT NULL,
            run_time int(11) NULL,
            usage_time int(11) NULL,
            next_time int(11) NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            modified_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            INDEX idx_hook (hook)
        )";
    }

    public function get_sql_create_scenario_segment_relationships_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            scenario_id bigint UNSIGNED NOT NULL,
            segment_id bigint NOT NULL,
            PRIMARY KEY  (scenario_id),
            FOREIGN KEY (scenario_id) REFERENCES `{$this->avans_prefix}scenarios`(id) ON DELETE CASCADE,
            FOREIGN KEY (segment_id) REFERENCES `{$this->avans_prefix}segments`(id) ON DELETE CASCADE,
            INDEX idx_segment_id (segment_id)
        )";
    }

    public function get_sql_create_segments_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            id bigint NOT NULL AUTO_INCREMENT,
            type varchar(80) NOT NULL,
            name varchar(512) NOT NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            modified_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            INDEX idx_type (type)
        )";
    }

    public function get_sql_create_segment_meta_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            segment_id bigint NOT NULL,
            meta_key varchar(250) NOT NULL,
            meta_value TEXT NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (segment_id) REFERENCES `{$this->avans_prefix}segments`(id) ON DELETE CASCADE,
            INDEX idx_segment_id (segment_id),
            INDEX idx_meta_key (meta_key)
        )";
    }


    public function get_sql_create_group_relationships_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            group_id bigint NOT NULL,
            segment_id bigint NOT NULL,
            PRIMARY KEY (group_id),
            FOREIGN KEY (group_id) REFERENCES `{$this->avans_prefix}segments`(id) ON DELETE CASCADE,
            FOREIGN KEY (segment_id) REFERENCES `{$this->avans_prefix}segments`(id) ON DELETE CASCADE,
            INDEX idx_segment_id (segment_id)
        )";
    }


    public function get_sql_create_user_segment_relationships_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            user_id bigint UNSIGNED NOT NULL,
            segment_id bigint NOT NULL,
            PRIMARY KEY  (user_id),
            FOREIGN KEY (user_id) REFERENCES `{$this->prefix}users`(ID) ON DELETE CASCADE,
            FOREIGN KEY (segment_id) REFERENCES `{$this->avans_prefix}segments`(id) ON DELETE CASCADE,
            INDEX idx_segment_id (segment_id)
        )";
    }



    public function get_sql_create_notifications_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint UNSIGNED NOT NULL,
            type ENUM('toast', 'email', 'sms')  NOT NULL,
            template_id bigint UNSIGNED NULL,
            status ENUM('pending' , 'success' , 'failed') DEFAULT 'pending' NOT NULL,
            data json NULL,
            response varchar(256) NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            scheduled_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            sended_at timestamp NULL DEFAULT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (user_id) REFERENCES `{$this->prefix}users`(ID) ON DELETE CASCADE,
            FOREIGN KEY (template_id) REFERENCES `{$this->prefix}posts`(ID) ON DELETE CASCADE,
            INDEX idx_user_id (user_id), 
            INDEX idx_scheduled_at (scheduled_at),
            INDEX idx_status (status),
            INDEX idx_type (type)
        )";
    }


    public function get_sql_create_wallet_transactions_tbl( $table_name ): string
    {
        return "CREATE TABLE $table_name (
            id bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint UNSIGNED NOT NULL,
            type varchar(50) NOT NULL,
            amount int(11) NOT NULL,
            balance int(11) NOT NULL,
            total int(11) NOT NULL,
            curency varchar(20) NOT NULL,
            object_id bigint UNSIGNED NOT NULL,
            status tinyint(1) NOT NULL DEFAULT 1,
            description varchar(512) NULL,
            extera json NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expired_at timestamp NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (user_id) REFERENCES `{$this->prefix}users`(ID) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_type (type),
            INDEX idx_amount (amount),
            INDEX idx_status (status),
            INDEX idx_expired_at (expired_at)
        )";
    }


}