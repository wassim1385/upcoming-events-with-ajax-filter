<?php
    $event_sdate = get_post_meta( $post->ID, 'wassim_events_sdate', true );
    $event_edate = get_post_meta( $post->ID, 'wassim_events_edate', true );
?>

<table class="form-table wassim-events-metabox">
<input type="hidden" name="wassim_events_nonce" value="<?php echo wp_create_nonce( "wassim_events_nonce" ); ?>">
    <tr>
        <th>
            <label for="wassim_events_sdate">Start Date</label>
        </th>
        <td>
            <input 
                type="date" 
                name="wassim_events_sdate" 
                id="wassim_events_sdate" 
                class="regular-text link-text"
                value="<?php echo esc_attr( $event_sdate ); ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="wassim_events_edate">End Date</label>
        </th>
        <td>
            <input 
                type="date" 
                name="wassim_events_edate" 
                id="wassim_events_edate" 
                class="regular-text link-text"
                value="<?php echo esc_attr( $event_edate ); ?>"
                required
            >
        </td>
    </tr>
</table>