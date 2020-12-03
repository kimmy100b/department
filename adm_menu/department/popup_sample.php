<?php
// 팝업 샘플 선택
include $_SERVER['DOCUMENT_ROOT']."/application/default.php";
Check_Page_Use_Admin(__USER_ADMIN_SUPER);
include __BASE_PATH."/function/util_func.php";

require __MODULE_PATH."/util/FileExplorer.php";
$explorer	= new FileExplorer();
// Root 폴더 설정
//$explorer->basePath = __WEBHARD_URL;

// 검색 폴더( 상위폴더 이동시 /.. 삭제 )
$sUrl = clean( $_POST['sUrl'] );

$fileResult = $explorer->fileList( $sUrl );

$sUrl = str_replace( "/..", "", $sUrl );
$curUrl = str_replace( "//", "/", str_replace( $_SERVER['DOCUMENT_ROOT'], "", $explorer->basePath.$sUrl ) );
// 끝의 / 제거
$curUrl = preg_replace( "/[\/]$/", "", $curUrl );
//print_r( $fileResult );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ko" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>샘플 선택</title>
<link href="http://fonts.googleapis.com/earlyaccess/nanumgothic.css" rel="stylesheet" type="text/css" />
<link href="http://fonts.googleapis.com/earlyaccess/notosanskr.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="/adm_menu/css/btn.css" />
<link rel="stylesheet" type="text/css" href="/adm_menu/css/template.css" />
<link rel="stylesheet" type="text/css" href="/adm_menu/css/member.css" />

<script type="text/javascript" charset="utf-8" src="/application/js/jquery-1.10.0.min.js"></script>
<script type="text/javascript" src="/application/js/util.js"></script>

<script type="text/javascript">
function showFileList( surl )
{
	if ( $.trim( surl ) == "" ) return false;

	if ( surl == "/.." )
	{
		// @todo 상위 폴더 이동 선택시.. 상위폴더로 이동 처리
	}

	var frm = document.mainform;
	frm.sUrl.value = surl;
	frm.submit();
}

function selectSample( url )
{
	if ( $.trim( url ) != "" )
	{
		if ( confirm( "해당 샘플을 선택하시겠습니까?" ) )
		{
			$("input[name='content_INC']", opener.document).val( url );
			window.close();
		}
	}
}

function preview( content )
{
	if ( $.trim( content ) == "" ) return;

	var preview = window.open( "", "preview", "resizable=1,scrollbars=1" );
	var frm = document.previewform;
	frm.url.value = content;
	frm.target = "preview";
	frm.submit();
}
</script>
</head>
<body>
			<h4 class="reader"> 안내 메시지</h4>
			<div class="info-msg box-gray-gray mgt0">
				<ul class="bul-dot-gray">
					<li><strong class="col-blue">사용할 샘플을 선택하세요.</strong></li>
				</ul>
			</div>
			<br />

						<!-- content_all -->
						<div id="content_all">
							<!-- head_title -->
							<div class="head_title">
								<h2>샘플 목록</h2>								
							</div>
							<!-- head_title end-->
							<p><?php echo "현재위치 : " . $curUrl ."/"; ?></p>
							<!-- content_body -->
							<div id="content_body">
								<!-- cbody_table -->
								<div class="cbody_table">
									<table class="tstyle_02">
									<caption>샘플 목록</caption>
									<colgroup>
										<col width="*" />
										<col width="8%" />
										<col width="30%" />
										<col width="30%" />
									</colgroup>
									<tbody>
										<tr>
											<th scope="col">파일명</th>
											<th scope="col">크기</th>
											<th scope="col">수정일</th>
											<th scope="col">기능</th>
										</tr>
<?php 
// 디렉토리 출력 
if ( count($fileResult['DIR']) > 0 )
{
    foreach( $fileResult['DIR'] as $val )
    {
		// 상위 폴더 이동 처리
		if ( str_replace( $explorer->curDir, "", $val ) == "/.." ) 
		{
			// 현재 폴더가 맨 상위 폴더일 경우
			if ( $curUrl == $explorer->rootDir ) 
			{
				$linkDir = "";
				continue;
			}
			else
			{
				// 기본 루트 폴더명을 제외한 실제 폴더명
				$tmpDir = str_replace( "/..", "", str_replace( $explorer->basePath, "", $val ) );
				$dirArr = explode( "/", $tmpDir );
				unset( $dirArr[count($dirArr)-1] );
				$linkDir = join( "/", $dirArr );

				// ""일 경우 맨 상위 폴더로 이동
				if ( $linkDir == "" ) $linkDir = "/";
			}
		}
		else
			$linkDir = str_replace( $explorer->basePath, "", $val );
?>
    <tr>
        <td style="text-align:left;"> <a href="javascript:void(0)" onclick="showFileList( '<?php echo $linkDir ?>' )"><img src="/application/js/tree/img/folder.gif" alt="폴더이미지" />
           <?php echo str_replace('/', '', str_replace( $explorer->basePath.$sUrl, "", $val ) ) ?></a></span>
        </td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
<?php 
    }
}
// 파일 출력
if ( count($fileResult['FILE']) > 0 )
{
    foreach( $fileResult['FILE'] as $val )
    {
        if ( is_file( $val ) )
        {
            // bytes
            $tmpfilesize = floatval( filesize( $val ) );
            // 단위 변환
            $tmpfilesize = FileSizeConvert( $tmpfilesize, 0 );
			// 선택 경로명
			$selUrl = $explorer->rootDir . str_replace( "//", "/", str_replace( $explorer->basePath, "", $val ) );
?>
    <tr>
        <td><a href="#this" onclick="selectSample( '<?php echo $selUrl ?>' )"><?php echo str_replace('/', '', str_replace( $explorer->basePath.$sUrl, "", $val ) ) ?></a></td>
        <td class="tar"><?php echo  $tmpfilesize ?></td>
        <td class="tac"><?php echo date("Y-m-d H:i:s.", filemtime($val)) ?></td>
        <td class="tac">
			<a href="#this" onclick="preview( '<?php echo str_replace( $explorer->basePath, "", $val ) ?>' )" class="btn-a btn-a-d">미리보기</a> 
			<a href="#this" onclick="selectSample( '<?php echo $selUrl ?>' )" class="btn-a btn-a-a">선택</a></td>
    </tr>
<?php 
        }
    }
}
?>
									</tbody>
								</table>
							</div>

							<div class="buttons mgt20 tac">
								<a href="javascript:window.close()" class="btn-b btn-b-a">창닫기</a>
							</div>

							</div>
							<!-- content_body end-->
						</div>
						<!-- content_all end-->


<form name="mainform" action="" method="post">
<input type="hidden" name="sUrl" value="<?php echo $sUrl ?>" />
<input type="hidden" name="curUrl" value="<?php echo $curUrl ?>" />
</form>

<form name="previewform" action="/pages/tool/preview_sample.php" method="post">
<input type="hidden" name="url" value="" />
</form>

</body>
</html>