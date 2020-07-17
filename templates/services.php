<?php

assert('is_array($this->data["serviceList"])');

$this->data['header'] = $this->t('{rciaminfo:services:services_header}');
$serviceList = $this->data['serviceList'];

$this->includeAtTemplateBase('includes/header.php');
?>

<h2><?php echo $this->t('{rciaminfo:services:services_header}') ?></h2>	
		
<table>
  <tr>
    <th width="25%"><?php echo $this->t('{rciaminfo:services:service_header_name}') ?></th>
    <th width="50%"><?php echo $this->t('{rciaminfo:services:service_header_description}') ?></th>
    <th width="25%"><?php echo $this->t('{rciaminfo:services:service_header_privacy}') ?></th>
  </tr>
<?php foreach($serviceList as $service): ?>
  <tr>
    <td><?php echo (!empty($service['name'])) ? $service['name'] : $this->t('{rciaminfo:services:service_empty_name}'); ?>
    <td><?php echo (!empty($service['description'])) ? $service['description'] : $this->t('{rciaminfo:services:service_empty_description}'); ?>
    <td><?php echo (!empty($service['privacyStatementURL'])) ? '<a href="' . $service['privacyStatementURL'] . '" target="_blank">' . $service['privacyStatementURL'] . '</a>' : $this->t('{rciaminfo:services:service_empty_privacy}'); ?>
  </tr>
<?php endforeach;?>
</table> 
</td>
	
<?php $this->includeAtTemplateBase('includes/footer.php');
