<?
include $_SERVER['DOCUMENT_ROOT']."/application/default.php";

// REFERER CHECK
CheckRequest( "A" );
// 권한 체크
Check_Page_Use_Admin();

include_once __BASE_PATH."/function/util_func.php";

include __MODULE_PATH."/department/Department.php";

$object	= new Department();

$permit = $object->checkPermit( $_POST['dpSid'], $_POST['mode'], $_POST['dpLevel'] );
echo json_encode($permit);
?>
