<?php

$key = $this->get_api_key();
global $current_user;
$name = trim($current_user->user_firstname . ' ' . $current_user->user_lastname);
$email = trim($current_user->user_email);
$link = '<a href="https://tinypng.com/developers" target="_blank">' . esc_html__('TinyPNG developer section', 'tiny-compress-images') . '</a>';

?>
<div class='tiny-account-container' class='wp-core-ui'>
    <div class='tiny-update-account-step1'>
        <h4><?php echo esc_html_e('Register new account', 'tiny-compress-images'); ?></h4>

        <p><?php echo esc_html__('Provide your name and email address to start optimizing images.', 'tiny-compress-images'); ?></p>

        <input class='tinypng-api-key-input' type='text' id='tinypng_api_key_name' name='tinypng_api_key_name' placeholder="Your full name" value="<?php echo htmlspecialchars($name); ?>" />
        <input class='tinypng-api-key-input' type='text' id='tinypng_api_key_email' name='tinypng_api_key_email' placeholder="Your email address" value="<?php echo htmlspecialchars($email); ?>" />
        <p class="tiny-create-account-message error" style="display: none"></p>
        <button class='tiny-account-create-key button button-primary'>
            <?php echo esc_html__('Register account', 'tiny-compress-images') ?>
        </button>

    </div>
    <div class='tiny-update-account-step2'>
        <h4><?php echo esc_html__('Already have an account?', 'tiny-compress-images'); ?></h4>

        <p><?php printf(esc_html__('Enter your API key. Go to the %s to retrieve it.', 'tiny-compress-images'), $link); ?></p>

        <input class='tinypng-api-key-input' type='text' id='<?php echo self::get_prefixed_name('api_key'); ?>' name='<?php echo self::get_prefixed_name('api_key'); ?>' />
        <p class="tiny-update-account-message error" style="display: none"></p>
        <button class='tiny-account-update-key button button-primary'>
            <?php echo esc_html__('Save', 'tiny-compress-images'); ?>
        </button>
    </div>
</div>
