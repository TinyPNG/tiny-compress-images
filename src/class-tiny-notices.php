<?php
/*
* Tiny Compress Images - WordPress plugin.
* Copyright (C) 2015 Voormedia B.V.
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the Free
* Software Foundation; either version 2 of the License, or (at your option)
* any later version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT
* ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
* FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
* more details.
*
* You should have received a copy of the GNU General Public License along
* with this program; if not, write to the Free Software Foundation, Inc., 51
* Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

class Tiny_Notices extends Tiny_WP_Base {

    private $notices;
    private $dismissals;

    private static function get_option_key() {
        return self::get_prefixed_name('admin_notices');
    }

    private static function get_user_meta_key() {
        return self::get_prefixed_name('admin_notice_dismissals');
    }

    public function admin_init() {
        add_action('wp_ajax_tiny_dismiss_notice', $this->get_method('dismiss'));
        if (current_user_can('manage_options')) {
            $this->show_stored();
        }
    }

    private function load_notices() {
        if (is_array($this->notices)) {
            return;
        }
        $option = get_option(self::get_option_key());
        $this->notices = is_array($option) ? $option : array();
    }

    private function save_notices() {
        update_option(self::get_option_key(), $this->notices);
    }

    private function load() {
        $this->load_notices();
        $this->load_dismissals();
    }

    private function load_dismissals() {
        if (is_array($this->dismissals)) {
            return;
        }
        $meta = get_user_meta($this->get_user_id(), $this->get_user_meta_key(), true);
        $this->dismissals = is_array($meta) ? $meta : array();
    }

    private function save_dismissals() {
        update_user_meta($this->get_user_id(), $this->get_user_meta_key(), $this->dismissals);
    }

    private function show_stored() {
        $this->load();
        foreach ($this->notices as $name => $message) {
            if (empty($this->dismissals[$name])) {
                $this->show($name, $message);
            }
        }
    }

    public function add($name, $message) {
        $this->load_notices();
        $this->notices[$name] = $message;
        $this->save_notices();
    }

    public function remove($name) {
        $this->load();
        if (isset($this->notices[$name])) {
            unset($this->notices[$name]);
            $this->save_notices();
        }
        if (isset($this->dismissals[$name])) {
            unset($this->dismissals[$name]);
            $this->save_dismissals();
        }
    }

    public function dismiss() {
        check_ajax_referer('tiny-compress');

        if (empty($_POST['name'])) {
            echo json_encode(false);
            exit();
        }
        $this->load_dismissals();
        $this->dismissals[$_POST['name']] = true;
        $this->save_dismissals();
        echo json_encode(true);
        exit();
    }

    public function show($name, $message, $dismissable=true) {
        $link = $dismissable ? "&nbsp;<a href=\"#\" data-name=\"$name\" class=\"tiny-dismiss\">" . self::translate_escape('Dismiss') . '</a>' : '';
        add_action('admin_notices', create_function('', "echo '<div class=\"updated\"><p>Compress JPEG & PNG images: $message$link</p></div>';"));
    }
}