<?php
    //
    // Yop poll meta functions
    //
    /**
     * Update yop poll meta field based on yop poll ID.
     *
     * Use the $prev_value parameter to differentiate between meta fields with the
     * same key and yop poll ID.
     *
     * If the meta field for the yop poll does not exist, it will be added.
     *
     * @param int    $yop_poll_id Yop Poll ID.
     * @param string $meta_key    Metadata key.
     * @param mixed  $meta_value  Metadata value.
     * @param mixed  $prev_value  Optional. Previous value to check before removing.
     *
     * @return bool False on failure, true if success.
     */

    function update_yop_poll_meta( $poll_id, $meta_key, $meta_value, $prev_value = '' ) {
        return update_metadata( 'yop_poll', $poll_id, $meta_key, $meta_value, $prev_value );
    }

    /**
     * Add yop poll data field to a yop poll.
     *
     * @param int    $yop_poll_id yop poll ID.
     * @param string $meta_key    Metadata name.
     * @param mixed  $meta_value  Metadata value.
     * @param bool   $unique      Optional, default is false. Whether the same key should not be added.
     *
     * @return bool False for failure. True for success.
     *
     */
function xss_clean($data)
{
// Fix &entity\n;
    $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

// Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

// Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

// Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do
    {
        // Remove really unwanted tags
        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    }
    while ($old_data !== $data);
    $data=filter_var($data, FILTER_SANITIZE_STRING);
// we are done...
    return $data;
}
    function add_yop_poll_meta( $poll_id, $meta_key, $meta_value, $unique = false ) {
        return add_metadata( 'yop_poll', $poll_id, $meta_key, $meta_value, $unique );
    }

    /**
     * Remove metadata matching criteria from a yop_poll.
     *
     * You can match based on the key, or key and value. Removing based on key and
     * value, will keep from removing duplicate metadata with the same key. It also
     * allows removing all metadata matching key, if needed.
     *
     * @param int    $poll_id    post ID
     * @param string $meta_key   Metadata name.
     * @param mixed  $meta_value Optional. Metadata value.
     *
     * @return bool False for failure. True for success.
     */
    function delete_yop_poll_meta( $poll_id, $meta_key, $meta_value = '' ) {
        return delete_metadata( 'yop_poll', $poll_id, $meta_key, $meta_value );
    }

    /**
     * Retrieve yop_poll meta field for a yop poll.
     *
     * @param int    $poll_id Yop poll ID.
     * @param string $key     Optional. The meta key to retrieve. By default, returns data for all keys.
     * @param bool   $single  Whether to return a single value.
     *
     * @return mixed Will be an array if $single is false. Will be value of meta data field if $single
     *  is true.
     */
    function get_yop_poll_meta( $poll_id, $key = '', $single = false ) {
        return get_metadata( 'yop_poll', $poll_id, $key, $single );
    }

    /**
     * Delete everything from yop_poll meta matching meta key.
     *
     * @param string $poll_meta_key Key to search for when deleting.
     *
     * @return bool Whether the poll meta key was deleted from the database
     */
    function delete_yop_poll_meta_by_key( $poll_meta_key ) {
        return delete_metadata( 'yop_poll', NULL, $poll_meta_key, '', true );
    }

    //
    // Yop poll answer meta functions
    //
    /**
     * Update yop poll answer meta field based on yop poll ID.
     *
     * Use the $prev_value parameter to differentiate between meta fields with the
     * same key and yop poll answer ID.
     *
     * If the meta field for the yop poll answer does not exist, it will be added.
     *
     * @param int    $yop_poll_answer_id Yop Poll ID.
     * @param string $meta_key           Metadata key.
     * @param mixed  $meta_value         Metadata value.
     * @param mixed  $prev_value         Optional. Previous value to check before removing.
     *
     * @return bool False on failure, true if success.
     */
    function update_yop_poll_answer_meta( $poll_answer_id, $meta_key, $meta_value, $prev_value = '' ) {
        return update_metadata( 'yop_poll_answer', $poll_answer_id, $meta_key, $meta_value, $prev_value );
    }

    /**
     * Add yop poll answer data field to a yop poll answer.
     *
     * @param int    $yop_poll_answer_id yop poll answer ID.
     * @param string $meta_key           Metadata name.
     * @param mixed  $meta_value         Metadata value.
     * @param bool   $unique             Optional, default is false. Whether the same key should not be added.
     *
     * @return bool False for failure. True for success.
     */
    function add_yop_poll_answer_meta( $poll_answer_id, $meta_key, $meta_value, $unique = false ) {
        return add_metadata( 'yop_poll_answer', $poll_answer_id, $meta_key, $meta_value, $unique );
    }

    /**
     * Remove metadata matching criteria from a yop_answer_poll.
     *
     * You can match based on the key, or key and value. Removing based on key and
     * value, will keep from removing duplicate metadata with the same key. It also
     * allows removing all metadata matching key, if needed.
     *
     * @param int    $poll_answer_id post ID
     * @param string $meta_key       Metadata name.
     * @param mixed  $meta_value     Optional. Metadata value.
     *
     * @return bool False for failure. True for success.
     */
    function delete_yop_poll_answer_meta( $poll_answer_id, $meta_key, $meta_value = '' ) {
        return delete_metadata( 'yop_poll_answer', $poll_answer_id, $meta_key, $meta_value );
    }

    /**
     * Retrieve yop_poll_answer meta field for a yop poll answer.
     *
     * @param int    $poll_answer_id Yop poll answer ID.
     * @param string $key            Optional. The meta key to retrieve. By default, returns data for all keys.
     * @param bool   $single         Whether to return a single value.
     *
     * @return mixed Will be an array if $single is false. Will be value of meta data field if $single
     *  is true.
     */
    function get_yop_poll_answer_meta( $poll_answer_id, $key = '', $single = false ) {
        return get_metadata( 'yop_poll_answer', $poll_answer_id, $key, $single );
    }

    /**
     * Delete everything from yop_poll_answer meta matching meta key.
     *
     * @param string $poll_meta_key Key to search for when deleting.
     *
     * @return bool Whether the poll meta key was deleted from the database
     */
    function delete_yop_poll_answer_meta_by_key( $poll_answer_meta_key ) {
        return delete_metadata( 'yop_poll_answer', NULL, $poll_answer_meta_key, '', true );
    }

    function get_human_time( $date ) {
        $t_time = get_the_time( __( 'Y/m/d g:i:s A' ) );
        $m_time = $date;
        $time   = get_post_time( 'G', true, $post );

        $time_diff = time() - $time;

        if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ){
            $h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
        }
        else {
            $h_time = mysql2date( __( 'Y/m/d' ), $m_time );
        }
    }

    function yop_poll_set_html_content_type() {
        return 'text/html';
    }

    function yop_poll_dump( $str ) {
        print "<pre>";
        print_r( $str );
        print "</pre>";
    }

    function yop_poll_kses( $string ) {
        $pt = array(
            'a'   => array(
                'href'   => array(),
                'title'  => array(),
                'target' => array()
            ),
            'img' => array(
                'src'   => array(),
                'title' => array(),
                'style' => array()
            ),
            'br'  => array()
        );
        return wp_kses( $string, $pt );
    }