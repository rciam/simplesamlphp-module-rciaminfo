<?php
/*
 * Shows the following information about connected SAML and OIDC services:
 *  - Display Name
 *  - Description
 *  - Privacy Statement URL
 *
 * Author: Nicolas Liampotis <nliam@grnet.gr>
 */

// Get global config
$config = \SimpleSAML\Configuration::getInstance();
// Get module config
$rciamInfoConfig = \SimpleSAML\Configuration::getConfig('module_rciaminfo.php');
$serviceStoreConfig = $rciamInfoConfig->getArray('store', null);
$serviceIdExcludeList = $rciamInfoConfig->getArray('serviceIdExcludeList', []);
$metadataSrcExcludeList = $rciamInfoConfig->getArray('metadataSrcExcludeList', []);
$tableConfig = $rciamInfoConfig->getArray('infoConfig', []);

$serviceStore = new \SimpleSAML\Module\rciaminfo\Service\Store\Database($serviceStoreConfig);

\SimpleSAML\Logger::debug('[rciaminfo:services] Initialising');

// Init template
$template = new \SimpleSAML\XHTML\Template($config, 'rciaminfo:services.php');

$serviceList = [];

// Get SAML SP information
// Get metadata storage handler
$metadataHandler = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();
$spMetadataList = $metadataHandler->getList('saml20-sp-remote');
foreach ($spMetadataList as $spMetadata) {
    if (empty($spMetadata['entityid'])) {
        continue;
    }
    $serviceId = $spMetadata['entityid'];

    // Check if service needs to be excluded based on ID
    if (!empty($serviceIdExcludeList) && in_array($serviceId, $serviceIdExcludeList)) {
        \SimpleSAML\Logger::info('[rciaminfo:services] Excluding SAML SP with entityID ' . $serviceId);
        continue; 
    }
    // Check if service needs to be excluded based on metadata source
    if (!empty($metadataSrcExcludeList) && !empty($spMetadata['metarefresh:src']) && in_array($spMetadata['metarefresh:src'], $metadataSrcExcludeList)) {
        \SimpleSAML\Logger::info('[rciaminfo:services] Excluding SAML SP with entityID ' . $serviceId);
        continue; 
    }

    // Get service name
    if (!empty($spMetadata['UIInfo']['DisplayName']['en'])) {
        $serviceName = $spMetadata['UIInfo']['DisplayName']['en'];
    } else if (!empty($spMetadata['UIInfo']['DisplayName']) && is_string($spMetadata['UIInfo']['DisplayName'])) {
        $serviceName = $spMetadata['UIInfo']['DisplayName'];
    } else if (!empty($spMetadata['name']['en'])) {
        $serviceName = $spMetadata['name']['en'];
    } else if (!empty($spMetadata['name']) && is_string($spMetadata['name'])) {
        $serviceName = $spMetadata['name'];
    } else {
        $serviceName = $serviceId;
    }

    // Get service description
    if (!empty($spMetadata['UIInfo']['Description']['en'])) {
        $serviceDescription = $spMetadata['UIInfo']['Description']['en'];
    } else if (!empty($spMetadata['UIInfo']['Description']) && is_string($spMetadata['UIInfo']['Description'])) {
        $serviceDescription = $spMetadata['UIInfo']['Description'];
    } else if (!empty($spMetadata['description']['en'])) {
        $serviceDescription = $spMetadata['description']['en'];
    } else if (!empty($spMetadata['description']) && is_string($spMetadata['description'])) {
        $serviceDescription = $spMetadata['description'];
    } else {
        $serviceDescription = null;
    }

    // Get service privacy statement
    if (!empty($spMetadata['UIInfo']['PrivacyStatementURL']['en'])) {
        $servicePrivacyStatementUrl = $spMetadata['UIInfo']['PrivacyStatementURL']['en'];
    } else if (!empty($spMetadata['UIInfo']['PrivacyStatementURL']) && is_string($spMetadata['UIInfo']['PrivacyStatementURL'])) {
        $servicePrivacyStatementUrl = $spMetadata['UIInfo']['PrivacyStatementURL'];
    } else {
        $servicePrivacyStatementUrl = null;
    }

    $serviceList["$serviceId"] = [
        'name'                => $serviceName,
        'description'         => $serviceDescription,
        'privacyStatementURL' => $servicePrivacyStatementUrl,
    ];
}

// Get OIDC client information
$clientList = $serviceStore->getServices();
foreach ($clientList as $client) {
    if (empty($client['client_id'])) {
        continue;
    }

    // Check if service needs to be excluded based on ID
    if (!empty($serviceIdExcludeList) && in_array($client['client_id'], $serviceIdExcludeList)) {
        \SimpleSAML\Logger::info('[rciaminfo:services] Excluding OIDC client with ID ' . $client['client_id']);
        continue; 
    }

    $serviceList[$client['client_id']] = [
        'name'                => (!empty($client['client_name'])) ? $client['client_name'] : $client['client_id'],
        'description'         => $client['client_description'],
        'privacyStatementURL' => $client['policy_uri'],
    ];
    \SimpleSAML\Logger::debug('[rciaminfo:services] Including OIDC client with ID ' . $client['client_id']);
}

function compareByName($a, $b) {
  return strcmp($a["name"], $b["name"]);
}
usort($serviceList, 'compareByName');

$template->data['serviceList'] = $serviceList;
if(empty($tableConfig['table'])) {
  $tableConfig['table']['length'] = 10;
}
$template->data['table_len'] = $tableConfig['table']['length'];

$template->show();
