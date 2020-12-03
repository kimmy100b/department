<?php
$__CASTLE_NOT = "true";
include $_SERVER['DOCUMENT_ROOT']."/application/default.php";

// REFERER CHECK
CheckRequest( "A" );
// 권한 체크
Check_Page_Use_Admin( __USER_ADMIN_SUPER );

// 디바이스별 분류정보
$deviceType = ( $_POST['deviceType'] != "" ) ? clean( $_POST['deviceType'] ) : "PC";

include_once __BASE_PATH."/function/util_func.php";
include_once __MODULE_PATH."/menu/Menu.php";
$object = new Layout($deviceType);

if ( $_POST["mode"] == "ADD" ) {
	$idx = $object->_add();
	$msg = "등록했습니다";
} else if ( $_POST["mode"] == "MOD" ) {
	$object->_update( $_POST["sid"] );
	$msg = "수정했습니다";
} else if ( $_POST["mode"] == "DEL" ) {
	$object->_delete( $_POST["sid"] );	
	$msg = "삭제했습니다";
}

// 이전페이지 옵션에 따라 이동 페이지 변경
//movepage( "member_list_all.php?$QUERY_STRING" );
?>
<form name="mainform" action="layout_list.php" method="get">
<input type="hidden" name="page_num" value="<?php echo $_POST['page_num'] ?>" />
<input type="hidden" name="deviceType" value="<?php echo $_POST['deviceType'] ?>" />
</form>
<script type="text/javascript">
	alert( "<?php echo $msg?>" );
	document.mainform.submit();
</script>
