<?php

include $_SERVER['DOCUMENT_ROOT']."/application/default.php";

// REFERER CHECK
//CheckRequest( "R" );
Check_Page_Use_Admin(__USER_ADMIN_SUPER);

include_once __MODULE_PATH."/department/Department.php";
$obj = new Department();

$skin = "<tr id='TR_[COUNT]' onMouseOver=\"this.style.backgroundColor='#E5EFE7'\" onMouseOut=\"this.style.backgroundColor=''\";>
			<td style='text-align:center'>[NUM]</td>
			<td style='text-align:left'>[ORDER]</td>
			<td style='text-align:left'>[NAME]</td>
			<td style='text-align:left' hidden>[YEAR]</td>
		</tr>";

$listHtml	= $obj->makeOrderList($_GET['pKey'], $skin, $_GET['optYear']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ko" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>관리자 메뉴</title>
	<link href="http://fonts.googleapis.com/earlyaccess/nanumgothic.css" rel="stylesheet" type="text/css" />
	<link href="http://fonts.googleapis.com/earlyaccess/notosanskr.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="/adm_menu/css/admin_all.css" />
	<script type="text/javascript" src="<?=__SYSTEM_URL?>/js/jquery-1.10.0.min.js"></script>
	<script type="text/javascript" src="<?=__SYSTEM_URL?>/js/util.js"></script>
	<script type="text/javascript" src="<?=__SYSTEM_URL?>/js/department/changeOrder.js"></script>
	<script type="text/javascript">
	function changeOrder( param )
	{
		args = callAjax( "/adm_menu/department/changeOrder.php", "order="+param+"&year="+<?php echo $_GET['optYear']?>, "POST" );
	}

	function closewin()
	{
		window.opener.document.location.reload();
		window.close();
	}
	</script>
</head>
<body>
<!-- str : 안내 메시지 -->
<h2>안내 메시지</h2>
<div class="box">
	변경하실 분류 항목의 위/아래 화살표를 클릭하시면 순서가 변경됩니다.
	<!-- 강조는 다음과 같이 <strong class="col-pink">색상 표현</strong> 됩니다. -->
</div>

<div id="content" class="order">
	<p class="txt-bbs">* 상품 분류 순서 관리입니다.</p>
	<div class="bbs-list">
		<table class="tstyle_02" summary="상품 분류 순서">
			<caption>상품 분류 순서</caption>
			<colgroup>
				<col width="15%" />
				<col width="15%" />
				<col width="*" />
			</colgroup>
			<thead>
				<tr>
					<th scope="col">순서</th>
					<th scope="col">코드</th>
					<th scope="col">조직도</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $listHtml; ?>
			</tbody>
		</table>
	</div>

	<div class="buttons tac mgt20">
		<a href="#this" class="btn-a btn-a-a" onClick="closewin()">닫기</a>
	</div>



<!-- end : 조회 -->
</div>


</body>
</html>
