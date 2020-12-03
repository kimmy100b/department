<?php
// 분류 관리
include $_SERVER['DOCUMENT_ROOT']."/application/default.php";
include __BASE_PATH."/function/util_func.php";

// REFERER CHECK
//CheckRequest( "R" );
Check_Page_Use_Admin(__USER_ADMIN_SUPER);

include_once __MODULE_PATH."/department/Department.php";
$obj = new Department();

$skin = "<tr class=\"[MCODE1] [MCODE2]\" style=\"[HIDDEN]\">
					<td class=\"tac\">[CHECK]</td>
					<td class=\"tac\">[ORDERPOP]</td>
					<td>[FOLD] [ORDER] [NAME]</td>
					<td class=\"tac\">[BUTTON]</td>
				  </tr>";
				  
$listHtml = $obj->makeList( $skin );

// $optSkin = "<option>[YEAR]</option>";
$optSkin = "[OPTION]";
$optHtml = $obj->makeOption($optSkin);

// 메뉴 그룹 지정
$_dp_grp1 = 2;
$_dp_grp2 = 1;
include $admin_page_path."/include/header.boot.html";

$dpYear = $_GET['optYear'];

?>
<style type="text/css">
table.tbl_type1 > tr > td { height:10px; padding:1 1 1 1; }
table.tbl_type1 { line-height:90%; }
</style>
<script type="text/javascript">
var checked = false;
function checkAll()
{
	checked = !checked;
	$("input[name='check[]']").prop( "checked", checked );
}

//체크박스 하나 체크 해지 시 all 체크 박스 해지 
// function onCheckFunc(){
// 	var allObj = $("input[name='check[]']").length;
// 	var chkObj = $("input[name='check[]']:checked").length;
	
// 	if(allObj == chkObj){
// 		$("input[name='inputchk']").prop( "checked", checked );
// 	}else{
// 		$("input[name='inputchk']").prop( "checked", checked );
// 	}
// }

// $("input[name='check[]']").click(function(){

// 	console.log("AAA");
// 	var allObj = $("input[name='check[]']").length;
// 	var chkObj = $("input[name='check[]']:checked").length;
	
// 	if(allObj == chkObj){
// 		$("input[name='inputchk']").prop( "checked", checked );
// 	}else{
// 		$("input[name='inputchk']").prop( "checked", checked );
// 	}
// })

//부서 및 회원 추가
function add( sid, level, dpGParent )
{
	document.writeform.dpSid.value = sid;
	document.writeform.mode.value = "ADD";
	document.writeform.dpLevel.value = level;
	document.writeform.dpGParent.value = dpGParent;

	
	document.writeform.action = "departmentWrite.php";
	document.writeform.submit();
}


function modify( idx ) 
{
	if ( trim( idx) != "" )
	{
		document.writeform.dpSid.value = idx;
		document.writeform.mode.value = "MOD";
		document.writeform.action = "departmentWrite.php";
		document.writeform.submit();
	}
}

function del( idx, depth )
{

	if( trim(depth)=="1"){
		if ( confirm( "부서를 삭제하시겠습니까?\r\n하위 회원도 모두 삭제됩니다!" ) )
		{
			document.writeform.dpSid.value = idx;
			document.writeform.mode.value = "DEL";
			document.writeform.dpLevel.value = depth;
			document.writeform.action = "departmentProcess.php";
			document.writeform.submit();
		}
	}else{
		if ( confirm( "회원을 삭제하시겠습니까?\r\n삭제 시 회원탈퇴 처리됩니다! 주의해주세요." ) )
		{
			document.writeform.dpSid.value = idx;
			document.writeform.mode.value = "DEL";
			document.writeform.dpLevel.value = depth;
			document.writeform.action = "departmentProcess.php";
			document.writeform.submit();
		}
	}
}

function chkDelete( ){
	document.mainform.mode.value = "CHK_DEL";

	var selectedCheck = new Array();
    $('.inputchk:checked').each(function() {
        var sid = $(this).val();
        selectedCheck.push(sid);
    });

    if (selectedCheck.length < 1) {
    alert("최소 1개 이상의 항목을 선택하셔야합니다.");
        return false;
	}

	if ( confirm( "선택한 부서 및 회원을 삭제하시겠습니까?\r\n부서 삭제 시 하위 회원도 모두 삭제됩니다!\r\n회원 삭제 시 회원탈퇴 처리됩니다! 주의해주세요." ) )
	{
		document.mainform.action = "departmentProcess.php";
		document.mainform.submit();
	}
}

function layerPop( parentKey , optYear ) 
{
    window.open( "departmentOrder.php?pKey="+parentKey+"&&optYear="+optYear, "departmentOrder", "width=620,height=520,scrollbars=1,resizable=1" );
}

// 연도별 정보
function chgYear(obj)
{
	document.yearform.submit();
}

// 새 연도 추가
function addYear(){
	document.writeform.mode.value = "ADD_YEAR";
	document.writeform.action = "departmentProcess.php";

	if(confirm("새 연도에 해당 조직도를 추가하시겠습니까?")){
		document.writeform.submit();
	}else{
		return false;
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
						<p>순서변경 : 항목을 클릭하면 해당 부서 및 회원의 표시 순서를 변경할 수 있습니다.</p>
						<p><strong class="col-pink">하위 회원 추가</strong> 버튼을 클릭하면 해당 부서의 하위 회원을 등록할 수 있습니다.</p>
						<p><strong class="col-pink">해당 부서/회원 보기</strong> 버튼을 클릭하면 홈페이지의 해당 부서 및 회원 페이지를 보실수 있습니다.</p>
					</div>
					<!-- 도움말 영역 끝 -->	


          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">조직도</h3>


            </div>
            <!-- /.card-header -->
            <div class="card-body">
				<div class="card-tools">
					<div class="input-group input-group-sm" >
							<!-- <form name="deviceform" action="menuList.php" method="get"> -->
							<form name="yearform" action="departmentList.php" method="get">
								<p class="tal mgr5 ">
									<strong class="text-primary">연도 : </strong>
									<select name="optYear" onchange="chgYear(this)">
										<!-- <option class='optYear' value="<?php echo $dpYear;?>" selected><?php echo $dpYear;?></option> -->
										<?php echo $optHtml;?>
									</select>
								</p>
							</form>
							<span class="buttons tar" >
								<a href="#this" class="btn btn-sm btn-warning" onClick="addYear()">등록</a>
							</span>
					  </div>
					</div>


				<div class="over_table ">
							<table class="table table-bordered table-hover">

								<colgroup>
									<col width="5%">
									<col width="8%">
									<col width="*">
									<col width="25%">
								</colgroup>
							<form name="mainform" action="" method="post">
								<thead>
									<tr class="tac">
										<th><input type="checkbox" name="checkall" onClick="checkAll()" /></th>
										<th>순서변경</th>
										<th>조직도</th>
										<th>기능</th>
									</tr>
								</thead>
								<tbody>
									<input type="hidden" name="mode" value="" />
									<input type="hidden" name="dpYear" value="<?php echo $dpYear ?>" />
									<?php echo $listHtml; ?>
								</tbody>
							</form>
							</table>
						</div>
						<div class="mgt20">
							<a href="#this" class="btn btn-secondary btn-sm" onClick="chkDelete()">삭제</a>
						</div>              
						</div>
            <!-- /.card-body -->

						<div class="card-footer tac">
							<a href="#this" onclick="add('', 1, '')" class="btn btn-primary">신규 등록</a>
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





<form name="writeform" action="" method="POST">
	<input type="hidden" name="dpSid" value="" />
	<input type="hidden" name="dpType" value="<?php echo $dpType?>" />
	<input type="hidden" name="mode" value="" />
	<input type="hidden" name="dpLevel" value="" />
	<input type="hidden" name="dpGParent" value="" />
	<input type="hidden" name="dpYear" value="<?php echo $dpYear ?>" />
	<!-- <input type="hidden" name="deviceType" value="<?php echo $deviceType ?>" /> -->
</form>

<!--footer -->
<?php include($admin_page_path."/include/footer.boot.html");?>
<!--//footer -->