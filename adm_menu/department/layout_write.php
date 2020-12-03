<?php
include $_SERVER['DOCUMENT_ROOT']."/application/default.php";
include __BASE_PATH."/function/util_func.php";

// REFERER CHECK
CheckRequest( "R" );
Check_Page_Use_Admin(__USER_ADMIN_SUPER);

$menuName = "컨텐츠";

// 디바이스별 분류정보
$deviceType = ( $_POST['deviceType'] != "" ) ? clean( $_POST['deviceType'] ) : "PC";

include_once __MODULE_PATH."/menu/Menu.php";
$object	= new Layout($deviceType);

// 본문 치환문자열
$replace_content = $object->replace_content;

$mode = ( $_GET['mode'] != "" ) ? clean($_GET['mode']) : "ADD";
$sid = clean( $_GET['sid'] );

if ( $mode == "MOD" ) 
{
	$info	= $object->getInfo( $sid );
	$header_content = $info["header_content"];
	$footer_content = $info["footer_content"];
	
	// 헤더
	$header_content_DB = "";
	$header_content_INC = "";
	// 컨텐트 변수에 값 할당
	${'header_content_'.$info['header_type']} = $header_content;
	// 푸터
	$footer_content_DB = "";
	$footer_content_INC = "";
	// 컨텐트 변수에 값 할당
	${'footer_content_'.$info['footer_type']} = $footer_content;
}
else
	$content = "<!-- 헤더 HTML 입력 영역 시작 -->\n\n<!-- 여기에 헤더 HTML 입력 영역 끝 -->\n\n<!-- 본문 삽입 부분 시작 -->\r".$replace_content."\r<!-- 본문 삽입 부분 끝 -->\n\n<!-- 푸터 HTML 입력 영역 시작 -->\n\n<!-- 헤더 HTML 입력 영역 끝 -->";

// 메뉴 그룹 지정
$_menu_grp1 = 2;
$_menu_grp2 = 1;
include $admin_page_path."/include/header.boot.html";
?>
<style type="text/css">
.img_title{ width:150px;color:red; }
.disabled_color{background-color:#eee !important; }
</style>
<script type="text/javascript">
$(document).ready(function(){
	$("input[name='header_type']").click( function(){		
		$(".header_content_type").attr("disabled", true);
		$(".header_content_type").addClass("disabled_color");

		$(".header_content_"+$(this).val()).attr("disabled", false).removeClass("disabled_color");
	});
	$("input[name='footer_type']").click( function(){		
		$(".footer_content_type").attr("disabled", true);
		$(".footer_content_type").addClass("disabled_color");

		$(".footer_content_"+$(this).val()).attr("disabled", false).removeClass("disabled_color");
	});
	
	// 컨텐츠 타입 disabled 세팅
	$(".header_content_type").each( function() {
		if ( !$(this).hasClass( "header_content_<?php echo $info['header_type']?>" ) )
		{
			$(this).addClass("disabled_color");
			$(this).attr("disabled", true);
		}
	});
	$(".footer_content_type").each( function() {
		if ( !$(this).hasClass( "footer_content_<?php echo $info['footer_type']?>" ) )
		{
			$(this).addClass("disabled_color");
			$(this).attr("disabled", true);
		}
	});
});

function checkForm()
{
	var frm = document.mainform;
	if ( trim( frm.title.value) == "" )
	{
		alert( "제목을 입력하세요!" );
		frm.title.focus();
		return false;
	}
	/*
	else if ( trim( frm.content.value) == "" )
	{
		alert( "내용을 입력하세요!" );
		frm.content.focus();
		return false;
	}
	else if ( !/<?php echo $replace_content ?>/.test( frm.content.value ) )
	{
		alert( "내용에 {{_CONTENT_}} 항목이 포함되어야 합니다!" );
		return false;
	}
	*/

	frm.action = "layoutProcess.php";
	frm.submit();
}

function delForm()
{
	var frm = document.mainform;

	if ( confirm("삭제하시겠습니까?") )
	{
		frm.action = "layoutProcess.php";
		frm.mode.value = "DEL";
		frm.submit();
	}
}
</script>

		<!-- Main content -->
    <section class="content">
      <div class="container-fluid">   
		  <div class="row">   
			<div class="col-12">

					<!-- 도움말 영역 -->
					<div class="callout callout-info">
						<h5><i class="fa fa-info"></i> 도움말:</h5>
						<p><strong class="col-pink">내용 입력란에 레이아웃 소스를 입력하세요.</strong></p>
						<p><strong class="col-pink">{{_META_}}</strong> : 메타정보(타이틀,저자등)가 치환되는 영역</p>
						<p><strong class="col-pink"><strong class="col-pink">{{_TITLE_}}</strong> : 타이틀이 치환되는 영역</p>
					</div>
					<!-- 도움말 영역 끝 -->

					
			  <div class="card card-primary">
				<div class="card-header">
				  <h3 class="card-title">레이아웃 정보</h3>
				</div>


<form name="mainform" action="" method="post" onsubmit="return checkForm();">
<input type="hidden" name="mode" value="<? echo $mode ?>" />
<input type="hidden" name="sid" value="<?php echo $info['lay_sid'] ?>" />
<input type="hidden" name="deviceType" value="<?php echo $deviceType; ?>" />

				<!-- /.card-header -->
				<div class="card-body">
							<table class="table table-bordered">
								<colgroup>
									<col width="100px">
									<col width="*">
								</colgroup>
								<tbody>
									 <tr>
										<th>디바이스</th>
										<td><?php echo $deviceType; ?></td>
									 </tr>
									 <tr>
										<th><span class="col-pink">*</span>제목</th>
										<td class="input_lg"><input type="text" name="title" value='<?php echo $info["title"] ?>'  maxlength="30" class="inputbox" /></td>
									 </tr>										   
									<tr class="contents_tr">
										<th>header</th>
										<td class="input_md">
											<p><input type="radio" name="header_type" value="INC" <?php echo ( $info['header_type'] == "INC" ) ? "checked" : "" ?> id="ct_INC" /> <label for="ct_INC">인클루드</label> &nbsp;
											<span class="smbr"><input type="text" name="header_content_INC" value="<?php echo $header_content_INC?>" class="header_content_type header_content_INC"  /></span></p>

											<p style="margin-top:5px;"><input type="radio" name="header_type" value="DB" <?php echo ( $info['header_type'] == "DB" ) ? "checked" : "" ?> id="ct_DB" /> <label for="ct_DB">직접입력</label>
											<textarea name="header_content_DB" style="width:100%;height:300px" class="header_content_type header_content_DB"><?php echo $header_content_DB?></textarea>
											</p>
										</td>
									</tr>
									<tr class="contents_tr">
										<th>footer</th>
										<td class="input_md">
											<p><input type="radio" name="footer_type" value="INC" <?php echo ( $info['footer_type'] == "INC" ) ? "checked" : "" ?> id="ft_INC" /> <label for="ft_INC">인클루드</label> &nbsp;
											<span class="smbr"><input type="text" name="footer_content_INC" value="<?php echo $footer_content_INC?>" class="footer_content_type footer_content_INC"  /></span></p>

											<p style="margin-top:5px;"><input type="radio" name="footer_type" value="DB" <?php echo ( $info['footer_type'] == "DB" ) ? "checked" : "" ?> id="ft_DB" /> <label for="ft_DB">직접입력</label>
											<textarea name="footer_content_DB" style="width:100%;height:300px" class="footer_content_type footer_content_DB"><?php echo $footer_content_DB?></textarea>
											</p>
										</td>
									</tr>
								</table>
</form>
              
						</div>
            <!-- /.card-body -->

						<div class="card-footer tac">
			<? if ( $mode == "ADD" ) { ?>
							<a href="#this" onclick="checkForm();" class="btn btn-primary">등록</a> 
			<? } else if ( $mode == "MOD" ) { ?>
							<a href="#this" onclick="checkForm();" class="btn btn-primary">수정</a> 
							<a href="#this" onclick="delForm();" class="btn btn-danger">삭제</a> 
			<? } ?>
							<a href="layout_list.php<?php echo $object->queryStrings( $object->query_str, "sid") ?>" class="btn btn-success">목록</a>
						</div>

				</div>
				<!-- /.card -->
			</div>
			<!-- /.col -->
		  </div>
		  <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->


<!--footer -->
<?php include($admin_page_path."/include/footer.boot.html");?>
<!--//footer -->