<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.arabon.be
 * @since      1.0.0
 * @package    Arabon
 * @subpackage Arabon/admin
 * @author     Aranere <arabon@aranere.be>
 */

$message_value = isset( $message ) ? $message : '';

$api_key_value                      = isset( $arabon_options['api_key'] ) ? $arabon_options['api_key'] : '';
$api_host_value                     = isset ( $arabon_options['api_host'] ) ? $arabon_options['api_host'] : '';
$portfolio_slug_value               = isset ( $arabon_options['portfolio_slug'] ) ? $arabon_options['portfolio_slug'] : '';
$portfolio_item_slug_value          = isset ( $arabon_options['portfolio_item_slug'] ) ? $arabon_options['portfolio_item_slug'] : '';
$register_gf_form_id                = isset ( $arabon_options['register_gf_form_id'] ) ? $arabon_options['register_gf_form_id'] : '';
$register_gf_form_default_values    = isset ( $arabon_options['register_gf_form_default_values'] ) ? $arabon_options['register_gf_form_default_values'] : '';
?>

<div class="wrap">

    <h2>Arabon</h2>
    <?php echo $message_value; ?>

    <p>Here you can set the default options for the Arabon integration.</p>

    <form action="" method="post">

        <table class="form-table">

            <tr valign="top">
                <th><label for="api_host">API host</label></th>
                <td>
                    <input name="api_host" id="api_host" type="text" value="<?=$api_host_value;?>" class="regular-text" /><br>
                    <span class="description">Please fill in the HOSTNAME to connect with the API.</span>
                </td>
            </tr>

            <tr valign="top">
                <th><label for="key">API key</label></th>
                <td>
                    <input name="api_key" id="key" type="text" value="<?=$api_key_value;?>" class="regular-text"/><br/>
                    <span class="description">Please fill in the API key.</span>
                </td>
            </tr>

            <tr valign="top">
                <th><label for="key">Winkel overzicht slug</label></th>
                <td>
                    <input name="portfolio_slug" id="portfolio_slug" type="text" value="<?=$portfolio_slug_value;?>" class="regular-text"/><br/>
                    <span class="description">Please fill in the portfolio slug.</span>
                </td>
            </tr>

            <tr valign="top">
                <th><label for="key">Winkel item slug</label></th>
                <td>
                    <input name="portfolio_item_slug" id="key" type="text" value="<?=$portfolio_item_slug_value;?>" class="regular-text"/><br/>
                    <span class="description">Please fill in the portfolio item slug.</span>
                </td>
            </tr>

            <tr valign="top">
                <th><label for="key">Inschrijf formulier Gravity form Id</label></th>
                <td>
                    <input name="register_gf_form_id" id="register_gf_form_id" type="text" value="<?=$register_gf_form_id;?>" class="regular-text"/><br/>
                    <span class="description">Please fill in the Gravity Forms id of the register form.</span>
                </td>
            </tr>

            <tr valign="top">
                <th><label for="key">Inschrijf formulier default values</label></th>
                <td>
                    <input name="register_gf_form_default_values" id="register_gf_form_default_values" type="text" value="<?=$register_gf_form_default_values;?>" class="regular-text"/><br/>
                    <span class="description">Please fill in the Gravity Forms default values (CSV) to <u>overwrite</u>: address_postal_code=,address_city=,address_country=</span>
                </td>
            </tr>

        </table><!-- form-table -->

        <p class="submit">
            <input type="submit" class="button-primary" name="submit" value="Save changes"/><br><br>
        </p>

    </form>

</div><!-- wrap -->
