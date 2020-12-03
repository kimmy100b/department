<?php
// 상품 분류 관리
include $_SERVER['DOCUMENT_ROOT']."/application/default.php";

// REFERER CHECK
//CheckRequest( "R" );
Check_Page_Use_Admin(__USER_ADMIN_SUPER);

// 디바이스별 분류정보
$deviceType = ( $_GET['deviceType'] != "" ) ? clean( $_GET['deviceType'] ) : "PC";

include_once __MODULE_PATH."/menu/Menu.php";
$layout = new Layout($deviceType);

$skin = "<tr>
			  <td class=\"tac\">[NUM]</td>
			  <td>[LINK][TITLE]</a></td>
			  <td class=\"tac\">[DATE]</td>
		   </tr>";

$result	= $layout->makeList( $skin );
$html = $result[0];
$page = $result[1];

// 메뉴 그룹 지정
$_menu_grp1 = 2;
$_menu_grp2 = 1;
include $admin_page_path."/include/header.boot.html";
?>
<script type="text/javascript">
function add()
{
	document.writeform.mode.value = "ADD";
	document.writeform.deviceType.value = document.deviceform.deviceType.value;
	document.writeform.action = "layout_write.php";
	document.writeform.submit();
}

function preview( mcd )
{
	window.open( "/index.php?menu_code="+mcd, "", "" );
}

// 디바이스별 메뉴 정보
function chgDevice(obj)
{
	document.deviceform.submit();
}
</script>

	<!-- Main content -->
    <section class="content">
      <div class="container-fluid">   
		<div class="row">   
			<div class="col-12">
			  <div class="card card-primary">
				<div class="card-header">
				  <h3 class="card-title">레이아웃 정보</h3>
				 </div>

				<!-- /.card-header -->
				<div class="card-body">
					<div class="card-tools">
						  <div class="input-group input-group-sm" style="width: 250px;">
<form name="deviceform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
							<p class="tal mgr5 ">
								<strong class="text-primary">디바이스 : 	</strong>
								<select name="deviceType" onchange="chgDevice(this)">
									<option value="PC" <?php if ( $deviceType == "" || $deviceType == "PC" ) echo "selected" ?>>PC</option>
									<option value="MOBILE" <?php if ( $deviceType == "MOBILE" ) echo "selected" ?>>MOBILE</option>
								</select>
							</p>
</form>
							<span class="buttons tar">
								<a href="#this" class="btn btn-sm btn-warning" onClick="add()">신규 등록</a>
							</span>
						</div>
                  </div>
								<table class="table table-bordered table-hover">
									<colgroup>
										<col width="8%">
										<col width="*">
										<col width="25%">
									</colgroup>
									<tbody>
										<tr class="tac">
											<th>번호</th>
											<th>제목</th>
											<th>등록일</th>
										</tr>
									<tbody>
										<?php echo $html; ?>
									</tbody>
								</table>



								<div class="btn-paging">
									<?php echo $page; ?>
								</div> 
              
						</div>
            <!-- /.card-body -->

						<div class="card-footer tac">
							<a href="#this" onclick="add();" class="btn btn-primary">신규 등록</a>
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
<input type="hidden" name="mode" value="" />
<input type="hidden" name="deviceType" value="" />
</form>

<!--footer -->
<?php include($admin_page_path."/include/footer.boot.html");?>
<!--//footer -->