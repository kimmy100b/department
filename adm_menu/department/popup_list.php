<?php
// 팝업 게시판 선택
include $_SERVER['DOCUMENT_ROOT']."/application/default.php";
Check_Page_Use_Admin(__USER_ADMIN_SUPER);
include __BASE_PATH."/function/util_func.php";

// skin 
$skin = "<tr>
					<td>[TYPE]</td>
					<td>[LANG]</td>
					<td style=\"text-align:left;\">[TITLE]</td>
					<td>[CATEGORY]</td>
					<td>[DATE]</td>
					<td>[BUTTON]</td>
				</tr>";

include __MODULE_PATH."/menu/Menu.php";
$menu	= new Menu();
$html			= $menu->_list_bbs( $skin );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ko" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>게시판 선택</title>
<link href="http://fonts.googleapis.com/earlyaccess/nanumgothic.css" rel="stylesheet" type="text/css" />
<link href="http://fonts.googleapis.com/earlyaccess/notosanskr.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="/adm_menu/css/btn.css" />
<link rel="stylesheet" type="text/css" href="/adm_menu/css/template.css" />
<link rel="stylesheet" type="text/css" href="/adm_menu/css/member.css" />

<script type="text/javascript" charset="utf-8" src="/application/js/jquery-1.10.0.min.js"></script>
<script type="text/javascript" src="/application/js/util.js"></script>

<script type="text/javascript">
function selBBS( board_sid )
{
	if ( $.trim( board_sid ) != "" )
	{
		if ( confirm( "해당 게시판을 선택하시겠습니까?" ) )
		{
			$("input[name='content_BBS']", opener.document).val( board_sid );
			window.close();
		}
	}
}
</script>
</head>
<body>
			<h4 class="reader"> 안내 메시지</h4>
			<div class="box-gray-gray mt0" style="100%;">
				<ul class="bul-dot-gray">
					<li><strong class="col-blue">사용할 게시판을 선택하세요.</strong></li>
				</ul>
			</div>
			<br />

						<!-- content_all -->
						<div id="content_all">
							<!-- head_title -->
							<div class="head_title">
								<h2>게시판 목록</h2>								
							</div>
							<!-- head_title end-->
							<!-- content_body -->
							<div id="content_body">
								<!-- cbody_table -->
								<div class="cbody_table">
									<table class="tbl_type1">
									<caption>게시판 목록</caption>
									<colgroup>
										<col width="15%" />
										<col width="12%" />
										<col width="*" />
										<col width="20%" />
										<col width="12%" />
										<col width="6%" />
									</colgroup>
									<tbody>
										<tr>
											<th scope="col">형태</th>
											<th scope="col">언어</th>
											<th scope="col">게시판명</th>
											<th scope="col">분류명</th>
											<th scope="col">등록일</th>
											<th scope="col">기능</th>
										</tr>
<?php 
if ( $html != "" ) echo $html; 
else {
?>
										<tr>
											<td colspan="5">등록된 게시판이 없습니다.</td>
										</tr>
<?php } ?>
									</tbody>
								</table>
							</div>
							<div class="btn-paging">
								<?php echo $page ?>
							</div>

							<div class="buttons mgt5 tac">
								<a href="javascript:window.close()" class="btn-b btn-b-a">창닫기</a>
							</div>

							</div>
							<!-- content_body end-->
						</div>
						<!-- content_all end-->

</body>
</html>