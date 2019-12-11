<?php

$settings = WPMT()->settings->get_setting();
$advanced_settings = (bool) WPMT()->settings->get_setting( 'advanced_settings', true );

?>
<div id="post-body-content">
    <div class="postbox">
        <div class="inside">
            <fieldset>
                <table class="form-table">
                    <?php foreach( $settings as $setting_name => $setting ) :
                    $hide_main_layer = '';

                    if( ! $advanced_settings && isset( $setting['advanced'] ) && $setting['advanced'] === true ){
                        $hide_main_layer = 'style="display:none !important;"';
                    }

                    //Validate settings value
                    $main_settings_value = '';
                    if( isset( $setting['value'] ) ){
                        $main_settings_value = $setting['value'];
                    }

                    $is_checked = ( $setting['type'] == 'checkbox' && ( $main_settings_value === 1 || $main_settings_value === '1' ) ) ? 'checked' : '';
                    $value = ( $setting['type'] != 'checkbox' && $setting['type'] != 'multi-input' ) ? $main_settings_value : '1';

                    ?>
                        <tr valign="top" <?php echo $hide_main_layer; ?>>
                            <th scope="row">
                                <?php echo $setting['title']; ?>
                            </th>
                            <td scope="row" valign="top">
                                <p>
                                    <?php if( $setting['type'] === 'multi-input' ) : ?>
                                        <?php foreach( $setting['inputs'] as $si_key => $data ) : 
                                            $hide_sub_layer = '';

                                            if( ! $advanced_settings && isset( $data['advanced'] ) && $data['advanced'] === true ){
                                                $hide_sub_layer = 'style="display:none !important;"';
                                            }

                                            //Always set the radio value of single inputs to their key
                                            if( $setting['input-type'] === 'radio' ){
                                                $data['value'] = $si_key;
                                            }

                                            $mi_is_checked = ( $setting['input-type'] == 'checkbox' && ( isset( $data['value'] ) && ( $data['value'] === 1 || $data['value'] === '1' ) ) ) ? 'checked' : '';
                                            $mi_value = ( $setting['input-type'] != 'checkbox' ) ? $data['value'] : '1';
                                            $si_name = $si_key;

                                            //Re-validate for radio inputs
                                            if( $setting['input-type'] == 'radio' ){
                                                $si_name = $setting_name;

                                                //Check radio button
                                                if( (string) $main_settings_value === (string) $data['value'] ){
                                                    $mi_is_checked = 'checked';
                                                }
                                            }
                                            ?>
                                            <p <?php echo $hide_sub_layer; ?>>
                                                <input id="<?php echo $si_name . '_' . $si_key; ?>" name="<?php echo $this->settings_key; ?>[<?php echo $si_name; ?>]" type="<?php echo $setting['input-type']; ?>" class="regular-text" value="<?php echo $mi_value; ?>" <?php echo $mi_is_checked; ?> />
                                                <label for="<?php echo $si_name . '_' . $si_key; ?>">
                                                    <?php echo $data['label']; ?>
                                                </label>
                                            </p>
                                            <?php if( isset( $data['description'] ) ) : ?>
                                            <p class="description" <?php echo $hide_sub_layer; ?>>
                                                <?php if( in_array( $setting['input-type'], array( 'checkbox', 'radio' ) ) ) : ?>
                                                    <input name="wp-mailto-links-hidden-margin" type="radio" class="regular-text" value="" style="visibility:hidden !important;pointer-events:none !important;"/>
                                                <?php endif; ?>
                                                <?php echo $data['description']; ?>
                                            </p>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <input id="<?php echo $setting['id']; ?>" name="<?php echo $this->settings_key; ?>[<?php echo $setting_name; ?>]" type="<?php echo $setting['type']; ?>" class="regular-text" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />
                                        <?php if( isset( $setting['label'] ) ) : ?>
                                            <label for="<?php echo $setting_name; ?>">
                                                <?php echo $setting['label']; ?>
                                            </label>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </p>
                                <?php if( isset( $setting['description'] ) ) : ?>
                                    <p class="description">
                                        <?php echo $setting['description']; ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td>
                                
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </fieldset>

            <p>
                <?php submit_button( WPMT()->helpers->translate( 'Save all', 'admin-settings' ) ); ?>
            </p>
        </div>
    </div>
</div>