<?php
/**
 * Author Avatars List
 * User: Paul Bearne
 * Date: 2014-10-25
 * Time: 11:02 AM
 */


class BuddyPressSupport {
	/**
     * @var array
     */
    protected static $profiles_field_list = array();

	/**
     * @return array
     */
    protected static function list_profiles_fields(){
        if( ! empty( self::$profiles_field_list ) ){
            return self::$profiles_field_list;
        }

        global $wpdb;

        //https://buddypress.org/support/topic/how-to-get-list-of-xprofile-filds/#post-227700

        $profile_groups = BP_XProfile_Group::get( array( 'fetch_fields' => true	) );

        $fields2 = array();
        if ( !empty( $profile_groups ) ) {
            foreach ( $profile_groups as $profile_group ) {
                if ( !empty( $profile_group->fields ) ) {
                    $group_name = $profile_group->name;
                    foreach ( $profile_group->fields as $field ) {
                      //  echo $field->id . ' - ' . $field->name . '<br/>';
                        $id = 'bp_' . str_replace(" ", "_", $field->name);
                        $fields2[$id] = sprintf(__('BP %s profile: %s', 'author-avatars' ),$group_name, $field->name);
                    }
                }
            }
        }

        self::$profiles_field_list = $fields2;
        return  self::$profiles_field_list;

    }

	/**
     * @param $fields
     *
     * @return array
     */
    public static function filter_profiles_fields( $fields ){
         return array_merge( $fields, self::list_profiles_fields() );
     }

	/**
     * @param $display_extra
     * @param $user
     *
     * @return string
     */
    public static function get_profile_outputs( $display_extra, $user ){

        $out = '';
        foreach( $display_extra as $name ){
            $args = array(
                'field'   => str_replace( '_', ' ', str_replace('bp_', '', $name ) ), // Field name or ID.
                'user_id' => $user->user_id
            );
         //   $out .=   bp_get_profile_field_data( $args  );
            $profile_field_data = bp_get_profile_field_data( $args  );
            $css = ( false == $profile_field_data )? $name . ' aa_missing' : $name;
            $out .=  sprintf( apply_filters( 'aa_user_display_extra_template', '<div class="extra %s">%s</div>', $args ,$profile_field_data ), $css, $profile_field_data );
            //$out .=  '<div class="extra '. $name . '">' . $profile_field_data . '</div>';
        }

        return $out;
    }

}

add_filter( 'AA_render_field_display_options', 'BuddyPressSupport::filter_profiles_fields' );
add_filter( 'aa_user_display_extra', 'BuddyPressSupport::get_profile_outputs', 10 , 2 );



