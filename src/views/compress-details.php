<?php
$error = $tiny_metadata->get_latest_error();
$total = $tiny_metadata->get_count(array('modified', 'missing', 'has_been_compressed', 'compressed'));
$active = $tiny_metadata->get_count(array('uncompressed', 'never_compressed'), $active_tinify_sizes);
$size_before = $tiny_metadata->get_total_size_before_optimization();
$size_after = $tiny_metadata->get_total_size_after_optimization();

$size_active = array_fill_keys($active_tinify_sizes, true);
$size_exists = array_fill_keys($available_sizes, true);
ksort($size_exists);

?><div class="details-container">
    <div class="details" id="tiny-compress-details">
        <?php if ($tiny_metadata->can_be_compressed()) { ?>
            <?php if ($error) { ?>
                <span class="icon dashicons dashicons-warning error"></span>
            <?php } else if ($total['missing'] > 0 || $total['modified'] > 0) { ?>
                <span class="icon dashicons dashicons-yes alert"></span>
            <?php } else if ($total['compressed'] > 0 && $active['uncompressed'] > 0) { ?>
                <span class="icon dashicons dashicons-yes alert"></span>
            <?php } else if ($total['compressed'] > 0) { ?>
                <span class="icon dashicons dashicons-yes success"></span>
            <?php } ?>
            <span class="icon spinner hidden"></span>

            <?php if ($total['has_been_compressed'] > 0 || ($total['has_been_compressed'] == 0 && $active['uncompressed'] == 0)) { ?>
                <span class="message">
                    <strong><?php echo $total['has_been_compressed'] ?></strong>
                    <span>
                        <?php echo htmlspecialchars(_n('size compressed', 'sizes compressed', $total['has_been_compressed'], 'tiny-compress-images')) ?>
                    </span>
                </span>
                <br/>
            <?php } ?>

            <?php if ($active['never_compressed'] > 0) { ?>
                <span class="message">
                    <?php echo htmlspecialchars(sprintf(_n('%d size not compressed', '%d sizes not compressed', $active['never_compressed'], 'tiny-compress-images'), $active['never_compressed'])) ?>
                </span>
                <br />
            <?php } ?>

            <?php if ($total['missing'] > 0) { ?>
                <span class="message">
                    <?php echo htmlspecialchars(sprintf(_n('%d file removed', '%d files removed', $total['missing'], 'tiny-compress-images'), $total['missing'])) ?>
                </span>
                <br />
            <?php } ?>

            <?php if ($total['modified'] > 0) { ?>
                <span class="message">
                    <?php echo htmlspecialchars(sprintf(_n('%d file modified after compression', '%d files modified after compression', $total['modified'], 'tiny-compress-images'), $total['modified'])) ?>
                </span>
                <br />
            <?php } ?>

            <?php if ($size_before - $size_after) { ?>
                <span class="message">
                    <?php printf(esc_html__('Total savings %s', 'tiny-compress-images'), str_replace(" ", "&nbsp;", size_format($size_before - $size_after, 1))) ?>
                </span>
                <br />
            <?php } ?>

            <?php if ($error) { ?>
                <span class="message error_message">
                    <?php echo esc_html__('Latest error', 'tiny-compress-images') . ': '. esc_html__($error, 'tiny-compress-images') ?>
                </span>
                <br/>
            <?php } ?>

            <a class="thickbox message" href="#TB_inline?width=700&amp;height=500&amp;inlineId=modal_<?php echo $tiny_metadata->get_id() ?>">Details</a>
        <?php } ?>
    </div>

    <?php if ($tiny_metadata->can_be_compressed() && $active['uncompressed'] > 0) { ?>
        <button type="button" class="tiny-compress button button-small button-primary" data-id="<?php echo $tiny_metadata->get_id() ?>">
            <?php echo esc_html__('Compress', 'tiny-compress-images') ?>
        </button>
    <?php } ?>
</div>

<div class="modal" id="modal_<?php echo $tiny_metadata->get_id() ?>">
    <div class="tiny-compression-details">
        <h3>
            <?php printf(esc_html__('Compression details for %s', 'tiny-compress-images'), $tiny_metadata->get_name()) ?>
        </h3>
        <table>
            <tr>
                <th><?php esc_html_e('Size', 'tiny-compress-images') ?></th>
                <th><?php esc_html_e('Original', 'tiny-compress-images') ?></th>
                <th><?php esc_html_e('Compressed', 'tiny-compress-images') ?></th>
                <th><?php esc_html_e('Date', 'tiny-compress-images') ?></th>
            </tr>
            <?php $i = 0 ?>
            <?php
                $images = $tiny_metadata->get_image_sizes() + $size_exists;
                foreach ($images as $size => $image) {
                    if (!is_object($image)) $image = new Tiny_Image_Size();
                    ?>
                <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd' ?>">
                    <td><?php
                        echo (Tiny_Image::is_original($size) ? esc_html__('original', 'tiny-compress-images') : $size ) . ' ';
                        if (!array_key_exists( $size, $active_sizes )) {
                            echo '<em>' . esc_html__('(not in use)', 'tiny-compress-images') . '</em>';
                        } else if ($image->missing()) {
                            echo '<em>' . esc_html__('(file removed)', 'tiny-compress-images') . '</em>';
                        } else if ($image->modified()) {
                            echo '<em>' . esc_html__('(modified after compression)', 'tiny-compress-images') . '</em>';
                        } else if ($image->resized()) {
                            printf('<em>' . esc_html__('(resized to %dx%d)', 'tiny-compress-images') . '</em>', $image->meta['output']['width'], $image->meta['output']['height']);
                        }
                    ?></td>
                    <td><?php
                        if ($image->has_been_compressed()) {
                            echo size_format($image->meta["input"]["size"], 1);
                        } else if ($image->exists()) {
                            echo size_format($image->filesize(), 1);
                        } else {
                            echo '-';
                        }
                    ?></td>
                    <?php
                        if ($image->has_been_compressed()) {
                            echo '<td>';
                            echo size_format($image->meta["output"]["size"], 1);
                            echo '</td>';
                            echo '<td>' . human_time_diff($image->end_time($size)) . ' ' . esc_html__('ago', 'tiny-compress-images') .'</td>';
                        } else if (!$image->exists()) {
                            echo '<td colspan=2><em>' . esc_html__('Not present or duplicate', 'tiny-compress-images') . '</em></td>';
                        } else if (isset($size_active[$size])) {
                            echo '<td colspan=2><em>' . esc_html__('Not compressed', 'tiny-compress-images') . '</em></td>';
                        } else if (isset($size_exists[$size])) {
                            echo '<td colspan=2><em>' . esc_html__('Not configured to be compressed', 'tiny-compress-images') . '</em></td>';
                        } else if (!array_key_exists( $size, $active_sizes )) {
                            echo '<td colspan=2><em>' . esc_html__('Size is not in use', 'tiny-compress-images') . '</em></td>';
                        } else {
                            echo '<td>-</td>';
                        }
                     ?>
                </tr>
                <?php $i++ ?>
            <?php } ?>
            <?php if ($tiny_metadata->get_image_sizes_optimized() > 0) { ?>
            <tfoot>
                <tr>
                    <td><?php esc_html_e('Combined', 'tiny-compress-images') ?></td>
                    <td><?php echo size_format($size_before, 1) ?></td>
                    <td><?php echo size_format($size_after, 1) ?></td>
                    <td></td>
                </tr>
            </tfoot>
            <?php } ?>
        </table>
        <?php if ($size_before && $size_after) { ?>
            <p><strong>
                <?php printf(esc_html__('Total savings %s (%.0f%%)', 'tiny-compress-images'),
                size_format($size_before - $size_after, 1),
                (1 - $size_after / floatval($size_before)) * 100) ?>
            </strong></p>
        <?php } ?>
    </div>
</div>
