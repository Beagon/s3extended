<?php
$output = '';
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:
		$output = '<h2>Installing S3 Extended</h2><p>Thanks for installing S3 Extended! It should now be available as a media source type.</p><br />';
		break;
	case xPDOTransport::ACTION_UPGRADE:
	case xPDOTransport::ACTION_UNINSTALL:
		break;
}
return $output;
