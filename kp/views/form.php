<link href="http://hayageek.github.io/jQuery-Upload-File/4.0.10/uploadfile.css" rel="stylesheet">
<script src="http://hayageek.github.io/jQuery-Upload-File/4.0.10/jquery.uploadfile.min.js"></script>

<h2><?php echo $title; ?> </h2>
<div class="well">
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="form-horizontal" enctype="multipart/form-data" id="form-kp">
        <div class="clearfix">
            <div class="span6">
                <div class="control-group">
                    <label class="control-label" for="site_name">Название сайта</label>
                    <div class="controls">
                        <input type="text" name="site_name" id="site_name" value="<?php if (isset($kp['site_name'])) echo $kp['site_name']; ?>" placeholder="example.com">
                    </div>
                </div>
                <div class="control-group">
                    <span class="control-label">Поисковые системы</span>
                    <div class="controls">
                        <label>
                            <span>Yandex</span>
                            <input type="checkbox" name="search_machine[]" value="yandex" <?php if (isset($kp['search_machine']) && in_array('yandex', json_decode($kp['search_machine']))) echo 'checked' ?>>
                        </label>
                        <label>
                            <span>Google</span>
                            <input type="checkbox" name="search_machine[]" value="google" <?php if (isset($kp['search_machine']) && in_array('google', json_decode($kp['search_machine']))) echo 'checked' ?>>
                        </label>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="region_promotion">Регион продвижения</label>
                    <div class="controls">
                        <?php if ($regions_promo): ?>
                            <select name="region_promotion" id="region_promotion" data-placeholder="Выберите регион..." class="chzn-select" required>
                                <?php foreach ($regions_promo as $region_promo): ?>
                                    <option value="<?php echo $region_promo['id']; ?>" <?php if (isset($kp['region_promotion']) && $region_promo['id'] == $kp['region_promotion']) echo 'selected'; ?>><?php echo $region_promo['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="control-group">
                    <label for="rate" class="control-label">Тариф</label>
                    <div class="controls">
                        <?php if ($rates): ?>
                            <select name="rate" id="rate" required>
                                <?php foreach ($rates as $rate): ?>
                                    <option value="<?php echo $rate['value']; ?>" <?php if (isset($kp['rate']) && $kp['rate'] == $rate['value']) echo 'selected'; ?>><?php echo $rate['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="control-group">
                    <label for="manager" class="control-label">Для кого ?</label>
                    <div class="controls">
                        <?php if ($managers): ?>
                            <select name="manager" id="manager">
                                <?php foreach ($managers as  $manager): ?>
                                    <option value="<?php echo $manager['id']; ?> " <?php if (isset($kp['userid']) && $kp['userid'] == $manager['id']) echo 'selected'; ?>><?php echo $manager['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="control-group hide">
                    <label for="subscription" class="control-label">Абонентская плата</label>
                    <div class="controls">
                        <input type="number" id="subscription" name="subscription" value="<?php if (isset($optional_data['subscription'])) echo $optional_data['subscription']; ?>">
                    </div>
                </div>
                <div class="control-group">
                    <div class="hide" id="hidden-content-traffic">
                        <div class="control-group">
                            <label for="traffic_count_month" class="control-label">Количество месяцов</label>
                            <div class="controls">
                                <input type="number" id="traffic_count_month" name="traffic_count_month" value="<?php if (isset($optional_data['traffic_count_month'])) echo $optional_data['traffic_count_month']; ?>">
                            </div>
                        </div>
                        <fieldset class="">
                            <h5>Первые N месяцов:</h5>
                            <div class="control-group">
                                <label for="first_month_subscription" class="control-label">Ежемесячная абонентская плата</label>
                                <div class="controls">
                                    <input type="number" name="traffic_first_month[subscription]" id="first_month_subscription" class="input-small" value="<?php if (isset($optional_data['traffic_first_month']['subscription'])) echo $optional_data['traffic_first_month']['subscription']; ?>">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="first_month_premium" class="control-label">Премия за переходы на сайт</label>
                                <div class="controls">
                                    <input type="checkbox" id="first_month_premium" name="traffic_first_month[premium]" value="1" <?php if (isset($optional_data['traffic_first_month']['premium'])) echo 'checked'; ?>>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="">
                            <h5>После N месяцов:</h5>
                            <div class="control-group">
                                <label for="traffic_after_month_subscription" class="control-label">Ежемесячная абонентская плата</label>
                                <div class="controls">
                                    <input type="number" name="traffic_after_month[subscription]" id="traffic_after_month_subscription" class="input-small" value="<?php if (isset($optional_data['traffic_after_month']['subscription'])) echo $optional_data['traffic_after_month']['subscription']; ?>">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="traffic_after_month_premium" class="control-label">Премия за переходы на сайт</label>
                                <div class="controls">
                                    <input type="checkbox" name="traffic_after_month[premium]" id="traffic_after_month_premium" class="input-small" value="1" <?php if (isset($optional_data['traffic_after_month']['premium'])) echo 'checked'; ?>>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="traffic_after_month_price_one_click" class="control-label">Цена одного перехода</label>
                                <div class="controls">
                                    <input type="number" name="traffic_after_month[price_one_click]" id="traffic_after_month_price_one_click" class="input-small" value="<?php if (isset($optional_data['traffic_after_month']['price_one_click'])) echo $optional_data['traffic_after_month']['price_one_click']; ?>">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="traffic_after_month_max_limit" class="control-label">Максимальный лимит ежемесячного платежа</label>
                                <div class="controls">
                                    <input type="number" name="traffic_after_month[max_limit]" id="traffic_after_month_max_limit" class="input-small" value="<?php if (isset($optional_data['traffic_after_month']['max_limit'])) echo $optional_data['traffic_after_month']['max_limit']; ?>">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="traffic_after_month_timeline_limit" class="control-label">Срок действия лимита платежа</label>
                                <div class="controls">
                                    <input type="number" class="input-small" id="traffic_after_month_timeline_limit" name="traffic_after_month[timeline_limit]" value="<?php if (isset($optional_data['traffic_after_month']['timeline_limit'])) echo $optional_data['traffic_after_month']['timeline_limit']; ?>">
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="control-group">
                    <div class="hide" id="hidden-content-top10">
                        <div class="control-group">
                            <label for="top10_count_month" class="control-label">Количество месяцов</label>
                            <div class="controls">
                                <input type="number" id="top10_count_month" name="top10_count_month" value="<?php if (isset($optional_data['top10_count_month'])) echo $optional_data['top10_count_month']; ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="top10_coefficient" class="control-label">Коэффициент</label>
                            <div class="controls">
                                <input type="number" id="top10_coefficient" name="top10_coefficient" value="<?php if (isset($optional_data['top10_coefficient'])) echo $optional_data['top10_coefficient']; else echo 3; ?>">
                            </div>
                        </div>
                        <fieldset class="">
                            <h5>Первые N месяцов:</h5>
                            <div class="control-group">
                                <label for="top10_first_month_subscription" class="control-label">Ежемесячная абонентская плата</label>
                                <div class="controls">
                                    <input type="number" name="top10_first_month[subscription]" id="top10_first_month_subscription" class="input-small" value="<?php if (isset($optional_data['top10_first_month']['subscription'])) echo $optional_data['top10_first_month']['subscription']; ?>">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="top10_first_month_premium" class="control-label">Премия за вывод сайта в ТОП-10</label>
                                <div class="controls">
                                    <input type="checkbox" id="top10_first_month_premium" name="top10_first_month[premium]" value="1" <?php if (isset($optional_data['top10_first_month']['premium'])) echo 'checked'; ?>>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="">
                            <h5>После N месяцов:</h5>
                            <div class="control-group">
                                <label for="top10_after_month_subscription" class="control-label">Ежемесячная абонентская плата</label>
                                <div class="controls">
                                    <input type="number" name="top10_after_month[subscription]" id="top10_after_month_subscription" class="input-small" value="<?php if (isset($optional_data['top10_after_month']['subscription'])) echo $optional_data['top10_after_month']['subscription']; ?>">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="top10_after_month_premium" class="control-label">Премия за вывод сайта в ТОП-10</label>
                                <div class="controls">
                                    <input type="checkbox" name="top10_after_month[premium]" id="top10_after_month_premium" class="input-small" value="1" <?php if (isset($optional_data['top10_after_month']['premium'])) echo 'checked'; ?>>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="top10_after_month_max_limit" class="control-label">Максимальный лимит ежемесячного платежа</label>
                                <div class="controls">
                                    <input type="number" name="top10_after_month[max_limit]" id="top10_after_month_max_limit" class="input-small" value="<?php if (isset($optional_data['top10_after_month']['max_limit'])) echo $optional_data['top10_after_month']['max_limit']; ?>">
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="top10_after_month_timeline_limit" class="control-label">Срок действия лимита платежа</label>
                                <div class="controls">
                                    <input type="number" class="input-small" id="top10_after_month_timeline_limit" name="top10_after_month[timeline_limit]" value="<?php if (isset($optional_data['top10_after_month']['timeline_limit'])) echo $optional_data['top10_after_month']['timeline_limit']; ?>">
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <input type="hidden" name="data_export" id="data_export" value='<?php if (isset($kp['data_export'])) echo $kp['data_export']; ?>'>
                <div class="control-group">
                    <label for="semantic_kernel" class="control-label">Семантическое ядро</label>
                    <div class="controls">
                        <div id="semantic_kernel_file">Загрузить</div>
                    </div>
                </div>
            </div>
            <div class="span5">
                <div class="control-group">
                    <label for="kp_description" class="control-label" style="display: block; text-align: left;">Description</label>
                    <div class="">
                        <textarea name="kp_description" id="kp_description" value="'asdasd" style="width: 100%; height: 200px;"><?php if (isset($kp['description'])) echo $kp['description']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="control-group" style="text-align: center;">
            <input type="submit" class="btn btn-primary" value="Сохранить" id="form-kp-submit">
        </div>
    </form>
</div>
<table class="table table-striped table-bordered table-condensed table-invoices table-hover" id="table-export-data"></table>
<style>
    .form-horizontal .control-label {
        width: 250px;
    }
    .form-horizontal .controls {
        margin-left: 280px;
    }
</style>
<script>
    $(function() {
        // Helper functions

        function notify(style, text) {
            $.notify({
                text: text,
            }, {
                style: 'metro',
                className: style,
                autoHide: true,
                clickToHide: true
            });
        }

        function get_html_table(data) {
            var html = '';

            for (var x = 0; x < data.length; x++) {
                html    += '<tr>';
                for (var y = 0; y < data[x].length; y++) {
                    html    += '<td>'+data[x][y]+'</td>';
                }
                html    += '</tr>';
            }

            return html;
        }

        // Generate table if edit page

        if ($('#data_export').val()) {
            var html = JSON.parse($('#data_export').val());
            $('#table-export-data').append(get_html_table(html));
        }

        // Rate value

        var rate_value = $('#rate').val();

        if (rate_value == 'fixed') {
            $('#subscription').parents('.control-group').slideDown();
        } else if (rate_value == 'top_10') {
            $('#hidden-content-top10').slideDown();
        } else if (rate_value == 'traffic') {
            $('#hidden-content-traffic').slideDown();
        } else {
            $('#subscription').parents('.control-group').slideDown();
        }

        $('#rate').change(function() {
            var value = $(this).find('option:selected').attr('value');

            $('.hide').hide();

            if (value == 'fixed') {
                $('#subscription').parents('.control-group').slideDown();
            }
            if (value == 'top_10') {
                $('#hidden-content-top10').slideDown();
            }
            if (value == 'traffic') {
                $('#hidden-content-traffic').slideDown();
            }
        });

        // Semantic kernel

        $("#semantic_kernel_file").uploadFile({
            url: '/kp/report',
            fileName: 'semantic_kernel',
            maxFileCount: 10,
            multiple: false,
            acceptFiles: '*.csv',
            onSuccess: function(files, data) {
                data = JSON.parse(data);
                var html = get_html_table(data);

                $('#data_export').val(JSON.stringify(data));
                $('#table-export-data').append(html);

                $('#form-kp-submit').prop('disabled', 0);
            },
            onSelect: function() {
                $('#form-kp-submit').prop('disabled', 'disabled');
            },

        });

        // Hanler ajax form

        $('#form-kp').submit(function(){
            var form   = $(this);
            var data   = form.serialize();
            var action = form.attr('action');
            var file   = $('#semantic_kernel_file').data('filename') != null ? $('#semantic_kernel_file').data('filename') : '';

            $.ajax({
                url: action,
                type: 'POST',
                data: data+'&semantic_kernel='+file,
                success: function(data) {
                    data = JSON.parse(data);

                    if (data[0] == 'error') {
                        notify('error', data[1]);
                    }

                    if (data[0] == 'success') {
                        window.location.href = "/kp";
                    }

                    console.log(data);
                },
                error: function(data) {
                    console.log('error: ', data);
                }
            });

            return false;
        });
    });
</script>