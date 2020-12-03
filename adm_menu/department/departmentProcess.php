<?php
include $_SERVER['DOCUMENT_ROOT']."/application/default.php";

// REFERER CHECK
CheckRequest( "A" );
// 권한 체크
Check_Page_Use_Admin( __USER_ADMIN_SUPER );

include_once __BASE_PATH."/function/util_func.php";
include_once __MODULE_PATH."/department/Department.php";
$obj = new Department();

if ( $_POST["mode"] == "ADD" ) {
	$idx = $obj->addDepartment();
	$dpYear = $_POST['dpYear'];
	$msg = "등록했습니다";
} else if ( $_POST["mode"] == "MOD" ) {
	$obj->updateDepartment( $_POST["dpSid"] );
	$dpYear = $_POST['dpYear'];
	$msg = "수정했습니다";
} else if ( $_POST["mode"] == "DEL" ) {
	$obj->deleteDepartment( $_POST["dpSid"], $_POST["dpLevel"] );
	//$dpYear = "";	
	$dpYear = $_POST['dpYear'];
	$msg = "삭제했습니다";
} else if ( $_POST["mode"] == "CHK_DEL" ) {
	//일괄삭제
	$dpYear = "";
	$obj->chkDelete();	
	$dpYear = "";	
	$msg = "삭제했습니다";
} else if ( $_POST["mode"] == "ADD_YEAR" ) {
	//연도별 추가
	$obj->addYear();	
	$dpYear = "";
	$msg = "등록했습니다";
}
?>
<form name="mainform" action="departmentList.php" method="get">
<input type="hidden" name="optYear" value="<?php echo $dpYear?>" />
</form>
<script type="text/javascript">
	alert( "<?php echo $msg?>" );
	document.mainform.submit();
</script>
