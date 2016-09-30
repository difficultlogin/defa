<link rel="stylesheet" href="/js/chosen/chosen.css"/>
<script src="/js/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript" src="/js/jquery.tablenavigator.js"></script>
<script type="text/javascript" src="/js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="/js/jquery.tablesorter.widgets.js"></script>
<script src="/js/chosen/chosen.jquery.js" type="text/javascript"></script>
<div class="well">
    <a href="/kp/add" class="btn btn-success">
        <i class="icon-plus-sign icon-white"></i>
        Создать КП
    </a>
    <form action="/kp" method="POST" id="filter_manager_form">
        <select name="manager" id="manager_filter" data-placeholder="Выберите менеджера...">
            <option></option>
            <option value="all" <?php if ($manager_filter == 'all') echo 'selected'; ?>>Полный список</option>
            <?php foreach ($managers as $manager): ?>
                <option value="<?php echo $manager['id']; ?>" <?php if ($manager_filter == $manager['id']) echo 'selected'; ?>><?php echo $manager['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>
<table id="kp-table" class="table table-striped table-bordered table-condensed table-hover">
    <thead>
    <tr>
        <th>Дата создания</th>
        <th>Продавец</th>
        <th>Проект</th>
        <th>Договор</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items_kp as $item): ?>
        <tr>
            <td><?php echo date('d.m.Y H:m:s', $item['date_create']); ?></td>
            <td><?php echo $item['name']; ?></td>
            <td><?php echo $item['site_name']; ?></td>
            <td><?php echo $rates[$item['rate']]['name']; ?></td>
            <td style="width: 183px">
                <a href="/kp/edit/<?php echo $item['id']; ?>" class="btn btn-default"><i class="icon-pencil"></i></a>
                <a href="/kp/" class="btn btn-default" disabled="disabled"><i class="icon-file"></i></a>
                <a href="/kp/download/<?php echo $item['id']; ?>" class="btn btn-default"><i class="icon-download"></i></a>
                &nbsp;
                <a href="/kp/remove/<?php echo $item['id']; ?>" data-id="<?php echo $item['id']; ?>" class="btn btn-danger kp-remove"><i class="icon-trash"></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<style>
    #filter_manager_form {
        float: right;
    }
</style>
<script>
    $(function(){
        $("#manager_filter").chosen();

        $('#kp-table').tablesorter({
            widgets: ["resizable"],
            useCache: false
        });

        $("#kp-table thead th").bind("click",function() {
            $('#kp-table').tablenavigator_clean();
        });
    });

    $('.kp-remove').click(function() {
        var btn = $(this);
        var id  = btn.attr('data-id');
        var url = btn.attr('href');

        $.ajax({
            url: url,
            type: 'POST',
            success: function(data) {
                data = JSON.parse(data);

                if (data[0] == 'success') {
                    btn.parents('tr').remove();
                }

                console.log('success: ', data);
            },
            error: function(data) {
                data = JSON.parse(data);
                console.log('error: ', data);
            }
        });

        return false;
    });

    $('#manager_filter').change(function() {
        $(this).parents('form').submit();
    });
</script>