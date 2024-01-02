// Show/hide options on Use settings from select
function useSettingsFromChange(el, content_type)
{
    content_type = content_type.replace(/\./, '\\.');

    if (jQuery(el).val()) {
        jQuery("#use_settings_from_container_"+content_type).hide();
    } else {
        jQuery("#use_settings_from_container_"+content_type).show();
    }
}

// Show/hide content type options
function contentTypeShowChange(el, content_type, content_id)
{
    content_type = content_type.replace(/\./, '\\.');

    if (jQuery(el).val() == '1') {
        jQuery("#content_type_container_"+content_type).show();
        jQuery("#likebtnContentTypeButtonTabs a[href='#content_type_pane_"+content_id+"'] span").addClass('icon-save');
    } else {
        jQuery("#content_type_container_"+content_type).hide();
        jQuery("#likebtnContentTypeButtonTabs a[href='#content_type_pane_"+content_id+"'] span").removeClass('icon-save');
    }
}

function themeChange(el, content_type, content_id)
{
    content_type = content_type.replace(/\./, '\\.');

    if (jQuery(el).val() == 'custom') {
        jQuery("#custom_theme_container_"+content_type).show();
    } else {
        jQuery("#custom_theme_container_"+content_type).hide();
    }
}

// Toggle upgrade website instructions
function toggleToUpgrade()
{
    jQuery("#likebtn_to_upgrade").toggle();
}

// Load jQuery if needed
function loadJQuery()
{
    if (typeof(jQuery) == "undefined") {
        var jq = document.createElement('script');
        jq.type = 'text/javascript';
        jq.async = false;
        jq.src = '/plugins/content/likebtn/assets/js/jquery.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(jq, s);
    }
}

// Toggle upgrade website instructions
function toggleStatToUpgrade()
{
    jQuery("#likebtn_stat_to_upgrade").toggle();
}

// Show statistics
function statisticsShow()
{
    jQuery("#statistics_loader").show();
    jQuery("#statistics_error").hide();
    jQuery('#statistics_wrapper').hide();

    jQuery.ajax({
        type: 'POST',
        dataType: "json",
        url: window.location.href,
        data: {
            action: 'ajax',
            method: 'statistics',
            params: {
                'content_type': jQuery("#likebtn_statistics_param_content_type").val(),
                'order_by': jQuery("#likebtn_statistics_param_order_by").val(),
                'page_size': jQuery("#likebtn_statistics_param_page_size").val(),
                'id': jQuery("#likebtn_statistics_param_id").val(),
                'content_title': jQuery("#likebtn_statistics_param_content_title").val()
            }
        },
        success: function(response) {

            jQuery("#statistics_loader").hide();
            if (!response.success) {
                jQuery("#statistics_error").show();
                return false;
            }
            jQuery("#statistics_container tbody").html('');
            if (response.data.rows.length > 0) {
                var rows_html = '';
                for (row_index in response.data.rows) {
                    if (isNaN(parseInt(row_index))) {
                        continue;
                    }
                    row = response.data.rows[row_index];
                    rows_html += '<tr>';
                    rows_html += '<td><input type="checkbox" class="item_checkbox" value="'+row.id+'" /></td>';
                    rows_html += '<td class="small">'+row.id+'</td>';
                    //rows_html += '<td>'+row.thumbnail+'</td>';
                    rows_html += '<td><a href="'+row.link+'" target="_blank">'+row.title+'</a></td>';
                    rows_html += '<td>'+row.likes+'</td>';
                    rows_html += '<td>'+row.dislikes+'</td>';
                    rows_html += '<td>'+row.likes_minus_dislikes+'</td>';
                    rows_html += '</tr>';
                }
                jQuery("#statistics_container tbody").html(rows_html);

                jQuery('#statistics_total').text(response.data.total_found);
                jQuery('#statistics_container').show();
                jQuery('#statistics_wrapper').show();
            } else {
                jQuery('#statistics_total').text('0');
                jQuery('#statistics_wrapper').show();
                jQuery('#statistics_container').hide();
            }

            console.log(response);
        },
        error: function(response) {
            jQuery("#statistics_loader").hide();
            jQuery("#statistics_error").show();
        }
    });
}

// select/unselect items
function statisticsItemsCheckbox(el)
{
    if (jQuery(el).is(':checked')) {
        jQuery("#statistics_container .item_checkbox").attr("checked", "checked");
    } else {
        jQuery("#statistics_container .item_checkbox").removeAttr("checked");
    }
}

function likebtnIconFormatSelect(state)
{
    return '<i class="lb-fi lb-fi-'+state.id+'"></i>';
}

function settingsScript()
{
    jQuery(document).ready(function(jQuery) {
        // Color picker        
        jQuery('.cp-group').colorpicker({
            customClass: 'colorpicker-2x',
            sliders: {
                saturation: {
                    maxLeft: 200,
                    maxTop: 200
                },
                hue: {
                    maxTop: 200
                },
                alpha: {
                    maxTop: 200
                }
            }
        });
        jQuery(".likebtn_icon_list").chosen("destroy");
        jQuery("select.likebtn_icon_list").select2({
            dropdownCssClass: 'select2-celled',
            minimumResultsForSearch: -1,
            formatResult: likebtnIconFormatSelect,
            formatSelection: likebtnIconFormatSelect,
            escapeMarkup: function(m) { return m; }
        });
    });

    (function(d, e, s) {a = d.createElement(e);m = d.getElementsByTagName(e)[0];a.async = 1;a.src = s;m.parentNode.insertBefore(a, m)})(document, 'script', '//likebtn.com/en/js/donate_generator.js');
}