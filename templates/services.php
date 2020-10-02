<?php

assert('is_array($this->data["serviceList"])');

$this->data['header'] = $this->t('{rciaminfo:services:services_header}');
$serviceList = $this->data['serviceList'];

$this->includeAtTemplateBase('includes/header.php');
?>

<script src="res/js/jquery-3.2.1.min.js"></script>
<script src="res/js/jquery.dataTables-1.10.20.min.js"></script>
<script src="res/js/jquery-1.12.0.ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="res/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="res/css/rciam-info.css">

<h2><?php echo $this->t('{rciaminfo:services:services_header}') ?></h2>

<table id="rciam_info" width="100%">
  <thead>
    <tr>
      <th><?php echo $this->t('{rciaminfo:services:service_header_name}') ?></th>
      <th><?php echo $this->t('{rciaminfo:services:service_header_description}') ?></th>
      <th><?php echo $this->t('{rciaminfo:services:service_header_privacy}') ?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach($serviceList as $service): ?>
    <tr>
      <td><?php echo (!empty($service['name'])) ? trim($service['name']) : $this->t('{rciaminfo:services:service_empty_name}'); ?>
      <td><?php echo (!empty($service['description'])) ? trim($service['description']) : $this->t('{rciaminfo:services:service_empty_description}'); ?>
      <td><?php echo (!empty($service['privacyStatementURL'])) ? '<a href="' . trim($service['privacyStatementURL']) . '" target="_blank">' . trim($service['privacyStatementURL']) . '</a>' : $this->t('{rciaminfo:services:service_empty_privacy}'); ?>
    </tr>
<?php endforeach;?>
  </tbody>
</table>
</td>

<?php $this->includeAtTemplateBase('includes/footer.php');?>
<script type="text/javascript">
$(document).ready(function() {
    $('#rciam_info').DataTable({
        "columnDefs": [
            {
                "orderable": false,
                "targets": [1, 2]
            },
            { "width": "33%", "targets": [0,2] },
            { "width": "34%", "targets": 1 },
        ],
        "bAutoWidth": false,
        "bLengthChange": false,
        "order": [[ 0, "asc" ]]
    } );

    // Get the value from configuration
    $('#rciam_info').DataTable().page.len(<?php echo $this->data['table_len']?>).draw();
} );
</script>
