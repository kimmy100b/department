<?php
/** 
 * 컨텐츠 정보 등록/수정
 * @180612 PC / MOBILE 구분 등록 처리
 */
include $_SERVER['DOCUMENT_ROOT']."/application/default.php";
include __BASE_PATH."/function/util_func.php";

// REFERER CHECK
//CheckRequest( "R" );
Check_Page_Use_Admin(__USER_ADMIN_SUPER);

//$dpName = "컨텐츠";

include_once __MODULE_PATH."/department/Department.php";
$obj = new Department();

$mode = ( $_POST[mode] != "" ) ? clean($_POST[mode]) : "ADD";
$dpSid = clean( $_POST['dpSid'] );

if($dpLevel == 1){
	$dpInfo["useContent"] = "N";
}


if ( $mode == "MOD" ) 
{
	$dpInfo	= $obj->getInfo( $dpSid );
	$dpLevel	= $dpInfo['dpLevel'];

	$dpParent = $dpInfo['dpParent'];
	$dpCode = $dpInfo['dpCode'];
	$dpName = $dpInfo["dpName"];
	

	// 컨텐트
	$content = stripslashes( $dpInfo['content'] );
	
	$content_DB = "";
	$content_INC = "";
	$content_BBS = "";
	$content_PRD = "";
	// 컨텐트 변수에 값 할당
	// ${'content_'.$dpInfo['contentType']} = $content;

	//----------------------------------------------------------//
	// 에디터용 첨부 이미지 정보만
	$fileResult = $obj->getFiles($dpSid, "IMAGE");

	$attImages	= $fileResult->attach_image;
	$attFiles	= $fileResult->attach_file;
	$attSize	= $fileResult->attach_fileSize;
	// 첨부된 파일 수
	$previous_files_count = count( $attImages ) + count( $attFiles );
	// 첨부된 파일 용량( KB )
	$up_files_size = $attSize;

	$data_sid = $dpSid;
	// 에디터용 첨부 이미지/파일 정보 끝
	//----------------------------------------------------------//
}
else
{
	$dpParent =  $dpSid;
	$dpLevel	= intval( $_POST['dpLevel'] );

	// 하위 메뉴 추가시 상위 메뉴의 deviceType 값 상속 받게 처리(POST값 무시!)
	if ( $dpParent != "" ) 
	{
		
		$dpParentInfo	= $obj->getInfo( $dpSid );
		$dpName = $dpParentInfo['dpName'];
		
	}
}

if ( $dpLevel <= 0 ) $dpLevel = 1;

$uploader = $obj->getUploader();


//----------------------------------------------------------//
// 에디터 파일 세팅
$setting = (object)$obj->setting;
// 이미지 구분
$edt_data_category = $setting->data_category;
// 파일 폴더
$edt_file_path = $setting->file_path;
// 테이블
$edt_table = $setting->table;
//----------------------------------------------------------//

// 메뉴 그룹 지정
$_dp_grp1 = 2;
$_dp_grp2 = 1;

include $admin_page_path."/include/header.boot.html";

?>
<style type="text/css">
.img_title{ width:150px;color:red; }
.disabled_color{background-color:#eee !important; }
</style>
<script type="text/javascript">
$(document).ready(function(){	
	// 컨텐츠 사용여부 처음 상태에 따른 disable
	if($("input[name='useContent']:checked").val() == "N")
		{			
			// 입력항목 reset
			$(':input','.contents_tr')
			  .not(':button, :submit, :reset, :hidden, :radio')
			  .val('')
			  .removeAttr('checked')
			  .removeAttr('selected');

			$(".contents_tr").hide();
			//$("input[name='dpName']").prop('readonly', false);
		}
		else{
			$(".contents_tr").show();
			//$("input[name='dpName']").prop('readonly', true);
		}
	// 컨텐츠 사용여부에 따른 disable
	$("input[name='useContent']").click( function(){
		if ( $(this).val() == "N" )
		{			
			// 입력항목 reset
			$(':input','.contents_tr')
			  .not(':button, :submit, :reset, :hidden')
			  .val('')
			  .removeAttr('checked')
			  .removeAttr('selected');

			$(".contents_tr").hide();
			//$("input[name='dpName']").prop('readonly', false);
		}
		else{
			$(".contents_tr").show();
			//$("input[name='dpName']").prop('readonly', true);
		}
	});
	// 컨텐츠 사용안함일 경우 컨텐츠 등록 항목 숨김
	if ( "<?php echo $dpInfo['useContent']?>" == "N" ) 
	{
		$(".contents_tr").hide();
		//$("input[name='dpName']").prop('readonly', false);
	};
});

function CheckId() 
{
	var id = $("#userNum").val();
	var url = "../member/idCheck.php";
	var params = "userId="+id+"&mode=ID&user=ADMIN";

	$.ajax({
		type:"POST",
		url:url,
		data:params,
		success:function(args){
			if ( args == true )
			{
				$("#dupCheck").val( 1 );
				$("#idResult").html("사용가능한 아이디 입니다.");
			}
			else
			{
				$("#dupCheck").val( 0 );
				$("#idResult").html("이미 사용중인 아이디 입니다.");
			}
		},
		
		error:function(e){
			alert( "에러로 조회하지 못했습니다!" );
		}
	});
}

function CheckPermit() 
{
	var frm = document.mainform;
	var permit = $("input:radio[name='permit']:checked").val();
	var url = "./permitCheck.php";

	var params = {"permit" : permit,
				  "mode" : "<?php echo $mode ?>",
				  "dpLevel" : "<?php echo $dpLevel ?>",
				  "dpSid" : "<?php echo $dpSid ?>"}


	if(permit=="Y"){
		$.ajax({
			type:"POST",
			url:url,
			dataType:'json',
			data:params,
			success:function(args){
				if ( args != "" )
				{	
					$("#permitCheck").val( args );
					$("#permitResult").html("해당 부서에 결재권한자가 있습니다.(있음을 클릭 시 결재권한자가 변경됩니다.)");
				}
				else
				{
					$("#permitCheck").val( 0 );
					$("#permitResult").html("");
				}
			},
			
			error:function(e){
				alert( "에러로 조회하지 못했습니다!" );
			}
		});
	}else{
		console.log(permit);
		$("#permitCheck").val( 0 );
		$("#permitResult").html("");
	}
}


function checkForm()
{
	var frm = document.mainform;
	if ( trim( frm.dpName.value) == "" )
	{
		alert( "부서명을 입력하세요!" );
		frm.dpName.focus();
		return false;
	}
	if(frm.useContent.value == 'Y'){
		if (!$("input:radio[name='userPosition']").is(":checked"))
		{
			alert( "직급을 선택하세요!" );
			return false;
		}else if ( !$("input:radio[name='permit']").is(":checked"))
		{
			alert( "결재권한을 선택하세요!" );
			return false;
		}else if ( trim( frm.userName.value) == "" )
		{
			alert( "성명을 입력하세요!" );
			frm.userName.focus();
			return false;
		}else if ( trim( frm.userNum.value) == "" )
		{
			alert( "사원번호을 입력하세요!" );
			frm.userNum.focus();
			return false;
		}else if ( trim( frm.userPhone.value) == "" )
		{
			alert( "휴대폰번호를 입력하세요!" );
			frm.userPhone.focus();
			return false;
		}

		if ( trim( frm.userPw.value) == "" && trim(frm.mode.value) == "ADD")
		{
			alert( "비밀번호를 입력하세요!" );
			frm.userPw.focus();
			return false;
		}
	}

	if(frm.permitCheck.value != "0"){
		if(confirm("해당 부서에 결재권한자가 있습니다.\r\n(있음을 클릭 시 결재권한자가 변경됩니다.)")){
			frm.action = "departmentProcess.php";
			frm.submit();
		}else{
			return false;
		}
	}
	if(frm.dupCheck.value == "0"){
		alert( "이미 등록되어 있는 아이디 입니다!");
		frm.userNum.focus();
		return false;
	}
	frm.action = "departmentProcess.php";

	if ( $("input[name='contentType']:checked").val() == "DB" )
		saveContent();
	else
		frm.submit();
}

</script>

		<!-- Main content -->
    <section class="content">
      <div class="container-fluid"> 
		<div class="row">        
			<div class="col-12">
					
			  <div class="card card-primary">
				<div class="card-header">
				  <h3 class="card-title">분류정보</h3>
				</div>

<form name="mainform" action="" method="post" onsubmit="return checkForm();" enctype="multipart/form-data">
<input type="hidden" name="mode" value="<? echo $mode ?>" />
<input type="hidden" name="dpParent" value="<?php echo $dpParent ?>" />
<input type="hidden" name="dpGParent" value="<?php echo $_POST['dpGParent'] ?>" />
<input type="hidden" name="dpCode" value="<?php echo $dpCode ?>" />
<input type="hidden" name="dpLevel" value="<?php echo $dpLevel ?>" />
<input type="hidden" name="dpYear" value="<?php echo $_POST['dpYear'] ?>" />
<input type="hidden" name="dupCheck" id="dupCheck" value="1" />
<input type="hidden" name="permitCheck" id="permitCheck" value="0" />

				<!-- /.card-header -->
				<div class="card-body">
				
					<div class="over_table ">
							<table class="table table-bordered ">
									<colgroup>
										<col width="120px">
										<col width="*">
									</colgroup>
									<tbody>																					 
										<tr>
											<th><span class="col-pink">*</span>부서명</th>
											<td class="input_md">
											<input type="text" name="dpName" id="dpName" class="dpName" value='<?php echo $dpName ?>' maxlength="60" class="inputbox" <?php if($dpLevel==3){?>readOnly<?php }?> /> <span class="col-pink smbr">* 입력하세요</span></td>
										</tr>
										<tr>
											<th>코드/차수</th>
											<td><input type=text name="dpSid" size=20 maxlength=20 value="<? echo $dpSid ?>" readOnly /> / <input type="text" name="dpLevel" value='<?php echo $dpLevel ?>' size="3" maxlength="3" class="inputbox" readonly="true" /> 
											<span class="smbr">* 자동 설정</span>
											</td>
										</tr>
										<tr>
											<th>직원 등록여부<br /></th>
											<td>
											<span class="smbr"><input type="radio" name="useContent" value='Y' onClick="setReadOnly()" id="useContent2" class="inputbox" <? if ( $dpInfo["useContent"] == "Y" || ($dpInfo["useContent"]==""&&$dpLevel=='3')) echo "checked"; ?>> <label for="useContent2"><strong>사용함</strong> :</span><span class="fb blue smbr">아래에서 직원 정보를 구성</span></label><br />
											<span class="smbr"><input type="radio" name="useContent" value='N' onClick="setReadOnly()" id="useContent1" class="inputbox" <? if ( $dpInfo["useContent"] == "N" || ($dpInfo["useContent"]==""&&$dpLevel=='2')) echo "checked"; ?>> <label for="useContent1"><strong>사용안함</strong> : </span><span class="fb blue smbr">부서 정보를 구성</span></label>
											</td>
										</tr>
										<tr class="contents_tr">
											<th><span class="col-pink">*직급</span></th>
											<td>
											<?php 
											//직급 값 설정
											if($dpInfo["userPosition"]==""){
												$chkVal = 7;
											}else{
												$chkVal = $dpInfo["userPosition"];
											}
											echo MakeRadio( "userPosition", $obj->positions, $chkVal, 3 ); ?>							
											</td>
										</tr>			
										<tr class="contents_tr">
											<th><span class="col-pink">*결재권한</span></th>
											<td>
											<span class="input_md"><input type="radio" name="permit" value='Y' id="permit2" class="inputbox" onClick="CheckPermit()" <? if ( $dpInfo["permit"] == "Y")  echo "checked";  ?>> <label for="permit2"><strong>있음</strong></span></label>
											<span class="input_md"><input type="radio" name="permit" value='N' id="permit1" class="inputbox" onClick="CheckPermit()" <? if ( $dpInfo["permit"] == "N" || $dpInfo["permit"] == "") echo "checked"; ?>> <label for="permit1"><strong>없음</strong> </span></label>
											<div id="permitResult"></div>
											</td>
										</tr>
										<tr class="contents_tr">
											<th><span class="col-pink">*</span>성명</th>
											<td class="input_md"><input type="text" name="userName" value='<?php echo $dpInfo["userName"] ?>' maxlength="60" class="inputbox" /> <span class="col-pink smbr">* 입력하세요</span></td>
										</tr>
										<tr class="contents_tr">
											<th><span class="col-pink">*</span>사원번호</th>
											<td class="input_md">
											<span class="smbr">사원번호는 로그인 시 아이디로 사용됩니다.<br></span>
											<!-- <input type="number" name="userNum" id="userNum" value='<?php echo $dpInfo["userNum"] ?>' maxlength="20" class="inputbox" onChange="CheckId()"/> <span class="col-pink smbr">* 숫자만 입력하세요.</span><span class="col-pink smbr"><div id="idResult"></div></span> -->
											<input type="number" name="userNum" id="userNum" value='<?php echo $dpInfo["userNum"] ?>' maxlength="20" class="inputbox" onChange="CheckId()"/> <span class="col-pink smbr">* 숫자만 입력하세요.</span><span class="col-pink smbr"><div id="idResult"></div></span>
											</td>
										</tr>
										<tr class="contents_tr">
											<th><span class="col-pink">*</span>비밀번호</th>
											<td class="input_md"><input type="password" name="userPw" value='<?php echo $dpInfo["userPw"] ?>' maxlength="60" class="inputbox" /> <span class="col-pink smbr">* 입력하세요(수정 시에는 입력하지 않아도 됩니다.)</span></td>
										</tr>
										<tr class="contents_tr">
											<th>전화번호</th>
											<td class="input_md"><input type="text" name="userTel" value='<?php echo $dpInfo["userTel"] ?>' maxlength="60" class="inputbox" /> <span class="col-pink smbr">* 입력하세요</span></td>
										</tr>
										<tr class="contents_tr">
											<th><span class="col-pink">*</span>휴대폰번호</th>
											<td class="input_md"><input type="text" name="userPhone" value='<?php echo $dpInfo["userPhone"] ?>' maxlength="60" class="inputbox" /> <span class="col-pink smbr">* 입력하세요</span></td>
										</tr>
										<tr class="contents_tr">
											<th>email</th>
											<td class="input_md"><input type="text" name="userEmail" value='<?php echo $dpInfo["userEmail"] ?>' maxlength="60" class="inputbox" /> <span class="col-pink smbr">* 입력하세요</span></td>
										</tr>
										<tr class="contents_tr">
											<th>회원가입일</th>
											<td class="input_md"><?php echo $dpInfo["regDate"]?></td>
										</tr>
									</table>
</form>
              
						</div>
				</div>
            <!-- /.card-body -->

							<div class="card-footer tac">
								<!-- <a href="javascript:previewContent()" class="btn btn-danger">미리보기</a> -->
				<? if ( $mode == "ADD" ) { ?>
								<a href="#this" onclick="checkForm();" class="btn btn-primary">등록</a>
				<? } else if ( $mode == "MOD" ) { ?>
								<a href="#this" onclick="checkForm();" class="btn btn-primary">수정</a>
				<? } ?>
								<a href="departmentList.php?" class="btn btn-success">목록</a>
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