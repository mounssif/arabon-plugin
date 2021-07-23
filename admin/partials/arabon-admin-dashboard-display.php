<?php
/**
 * Provide a dashboard area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.arabon.be
 * @since      1.0.0
 * @package    Arabon
 * @subpackage Arabon/admin/partials
 * @author     Aranere <arabon@aranere.be>
 */
?>

<div class="wrap" id="arabon-start-screen">
    <h2>Arabon Import</h2>
    <?php echo $message_value; ?>
    <textarea id="messages" disabled="disabled"></textarea>
    <input type="submit" class="button-primary" id="import_all" name="import_all" value="Importeren"/>
</div>