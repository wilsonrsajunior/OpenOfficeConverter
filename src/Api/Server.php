<?
    include_once(__DIR__ . "/../autoload.php");

    // @TODO change all input gets for post.

    $server_obj = new Server\Server;

    $conv_from   = filter_input(INPUT_POST, 'from');
    $conv_to     = filter_input(INPUT_POST, 'to');
    $action      = filter_input(INPUT_POST, 'action');
    $resource_id = filter_input(INPUT_POST, 'resourceId');

    $server_obj->SetSourceFormat($conv_from);
    $server_obj->SetTargetFormat($conv_to);

    // @TODO Check Auth

    switch($action)
	{
        case 'up':
            $server_obj->Up();
            break;
        case 'upload':
            $server_obj->Upload();
            break;
        case 'convert':
            $server_obj->Convert($resource_id);
            break;
        case 'get':
            $pdf_content = $server_obj->Get($resource_id);
            if($pdf_content) {
                echo $pdf_content;
                die;
            }
            break;
        case 'remove':
            // $server_obj->remove($resource_id);
            break;
        default:
            break;
	}

    die();
?>
