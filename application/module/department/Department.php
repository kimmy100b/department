<?php
/**
 * 사이트 분류 관리
 *
 * 차수별 코드 및 이름을 모두 저장 관리.
 * 등록 및 업데이트시 불필요 필드등으로 인해 부하가 생기나.. select 및 관리가 용이함..
 * morder1~4 : 001 001 001 001
 * mname1~4 : 가 나 다 라
 *
 * @180612 PC/Mobile 분류 정보 추가
 */
if ( ! defined('__BASE_PATH')) exit('No direct script access allowed');

/*
 * 메뉴 관리
 */

include_once __MODULE_PATH."/core/FileManagerSimple.php";

class Department extends FileManagerSimple 
{
	// 각 레벨별 코드 길이
	var $category_length = 3;
	// 각 차수별 최대수
	var $category_max_no = 999;
	// 최대 메뉴 레벨. 디폴트 2
	var $max_dp_lvl = 3;

	var $db;
	//var $img_fields = array( "dpImg1", "dpImg2", "dpImg3", "dpImg4", "dpImg5", "dpImg6" );

	var $positions = array();


	function Department() 
	{
		$this->TABLE	= "department";
		$this->db = $this->module( "core/DB" );

		$this->positions = array(
			"1"=>"대표이사",
			"2"=>"부장",
			"3"=>"과장",
			"4"=>"주임",
			"5"=>"센터장",
			"6"=>"팀장",
			"7"=>"팀원",
);

		// 최대 메뉴 레벨 설정
		if ( intval( __MAX_DP_LVL ) > 0 ) $this->max_dp_lvl = __MAX_DP_LVL;

		//-----------------------------------------------------------------//
		// 파일 설정
		$this->data_category				= "category";
		$this->setting['file_path']			= "common_file";	// 폴더1
		$this->setting['data_category']		= $this->data_category;	// 폴더 2
		//$this->setting['table']				= "department_file"; // 파일
		$this->setting['file_upload_size']	= 10;
		$this->setting['file_upload_count'] = 4;
		$this->setting['make_thumb']		= "true";
		$this->setting['thumb_width']		= 250;
		$this->setting['thumb_height']		= 200;
		$this->setting['m_image_width']	= 200;
		$this->setting['m_image_height']	= 150;

		// 원본을 썸네일 사이즈로 변경처리. 썸네일 크기 및 사용여부 true로 설정 필요
		$this->setting['orginal_resize']	= false;

		$this->setTableName( $this->setting['table'] );
		$this->setFile_Path( $this->setting['file_path'] );

		
		//-----------------------------------------------------------------//

//		include_once __BASE_PATH."/util/upload/nUpload.class.php";
//		$this->uploader = new nUpload( $this->db, $this->file_dataroom, $this->file_dir, $this->view_dir, $this->max_file, 0, $this->max_width, 0, 0 );
	}


	// function searchCon() 
	// {
	// 	$query = "";

	// 	return $query;
	// }

	function maxYear(){
		$maxQuery	=	"select max(dpYear) as maxYear
							from ". $this->TABLE ."
							where delState = 'N' order by dpYear ";	
		$maxRow = $this->db->fetch($maxQuery);

		return $maxRow['maxYear'];
	}


	//옵션 
	function makeOption($skin, $return = "HTML")
	{
		$count = 0;
		if ( $return == "ARRAY" ) 
			$html_result = array();
		else
			$html_result = "";

		$maxYear = $this->maxYear();
		
		$query	=	"select distinct(dpYear) as year
						 from ". $this->TABLE ."
						 where delState = 'N' order by dpYear ";	

		if ( $result = $this->db->query( $query ) )
		{
			while( $row = $this->db->fetch($result) )
			{
				
				if ( $return == "ARRAY" ) 
				{
					$html_result[] = $row;
				}
				else 
				{
					$tmpRow = $skin;

					if($_GET['optYear']==""&&$row['year']==$maxYear){
						$tmpRow = str_replace( "[OPTION]", "<option class='optYear' name=\"year[]\" value=\"".$row['year']."\" selected>".$row['year']."</option>",	$tmpRow );
					}else if($_GET['optYear']==$row['year']){
						$tmpRow = str_replace( "[OPTION]", "<option class='optYear' name=\"year[]\" value=\"".$row['year']."\" selected>".$row['year']."</option>",	$tmpRow );
					}else{
						$tmpRow = str_replace( "[OPTION]", "<option class='optYear' name=\"year[]\" value=\"".$row['year']."\">".$row['year']."</option>",	$tmpRow );
					}
					
					$html_result .= $tmpRow;
				}
				$count++;
			}
		}
		return $html_result;
	}


	// string 반환
	function makeList( $skin, $return = "HTML" )
	{
		$count = 0;
		if ( $return == "ARRAY" ) 
			$html_result = array();
		else
			$html_result = "";

		if($_GET['optYear']==""){
			$optYear = $this->maxYear();
		}else{
			$optYear = $_GET['optYear'];
		}

		$query	=	"select * 
						 from ". $this->TABLE ."
						 where delState = 'N' and dpLevel <= '".$this->max_dp_lvl."' and dpYear = '".$optYear."' 
						 order by dpCode ";

		if ( $result = $this->db->query( $query ) )
		{
			while( $row = $this->db->fetch($result) )
			{
				
				if ( $return == "ARRAY" ) 
				{
					$html_result[] = $row;
				}
				else 
				{
					$tmpRow = $skin;
					
					$tmpRow = str_replace( "[ORDERPOP]",	
											"<input type=\"hidden\" name=\"count[]\" value=\"$count\" />
											 <input type=\"hidden\" name=\"dpSid[]\" value=\"".$row['dpSid']."\" />
											 <input type=\"radio\" class=\"dp_popup\" onClick=\"layerPop('".$row['dpParent']."', '".$row['dpYear']."');\" name=\"order[]\" value=\"".$row['dpSid']."\" />",		
										$tmpRow );
					
					$tmpRow = str_replace( "[CHECK]", "<input type=\"checkbox\" class='inputchk' name=\"check[]\" value=\"".$row['dpSid']."\" />",	$tmpRow );
					//$tmpRow = str_replace( "[CHECK]", "<input type=\"checkbox\" class='inputchk' name=\"check_".$row['dpYear']."[]\" value=\"".$row['dpSid']."\" />",	$tmpRow );
					// $tmpRow = str_replace( "[CODE]",	$row['dpSid'],	$tmpRow );
					if($row['useContent']=="Y"){
						$tmpRow = str_replace( "[NAME]",	"<a href=\"#this\" onClick=\"modify('".$row['dpSid']."')\">".$row['userName']."</a>",	$tmpRow );				
					}else{
						$tmpRow = str_replace( "[NAME]",	"<a href=\"#this\" onClick=\"modify('".$row['dpSid']."')\">".$row['dpName']."</a>",	$tmpRow );				
					}

					// 레벨 표시 . $row['dpSid']
					$tmpRow = str_replace( 
										"[ORDER]", 
										str_repeat( "&nbsp;&nbsp;", $row['dpLevel'] ). "<img src=\"/pages/images/level_0{$row['dpLevel']}.gif\" border=\"0\" alt=\"".$row['dpLevel']."차\" /> ", $tmpRow );
					// 미리보기
					// $tmpRow = str_replace( "[VIEW]", 
					// 				"<a href=\"#this\" class=\"btn btn-secondary btn-sm\" onClick=\"preview('".$row['dpSid']."')\" style=\"cursor:pointer;\">미리 보기</a> ", $tmpRow );
					
					$button = "";

					// 기능 버튼( 최대 카테고리 이하 레벨일 경우에만 하위 분류 추가 )
					if ( $row['dpLevel'] < $this->max_dp_lvl ) 
						$button .= "<a href=\"#this\" class=\"btn btn-sm btn-success\" onClick=\"add('".$row['dpSid']."',".intval($row['dpLevel']+1).",'".$row['dpCode']."')\">하위 추가</a>&nbsp;&nbsp;";
					$button .= "<a href=\"#this\" class=\"btn btn-sm btn-warning\" onClick=\"modify('".$row['dpSid']."')\">수정</a>";
					// 삭제 불가 페이지 여부
					if ( $row['essential'] != 'Y' ) 
						$button .= "&nbsp;&nbsp;<a href=\"#this\" class=\"btn btn-danger btn-sm\" onClick=\"del('".$row['dpSid']."','".$row['dpLevel']."')\">삭제</a>";
					
					// 마루아이디만 컨텐츠 관리가능하게 변경
					$tmpRow = str_replace( "[BUTTON]", $button, $tmpRow );

					// 3차분류
					if ( $row['dpLevel'] == 3 )
					{
						$tmpRow = str_replace( "[FOLD]", "&nbsp;&nbsp;", $tmpRow );
						$tmpRow = str_replace( "[HIDDEN]", "", $tmpRow );

						$tmpRow = str_replace( "[MCODE1]", substr($row['dpCode'],0,$this->category_length), $tmpRow );
						$tmpRow = str_replace( "[MCODE2]", substr($row['dpCode'],0,$this->category_length*2), $tmpRow );
					}
					else if ( $row['dpLevel'] == 2 ) 
					{
						$tmpRow = str_replace( "[FOLD]", "<span class=\"fold_dp lv3\" rel=\"".$row['dpCode']."\">-</span>", $tmpRow );
						$tmpRow = str_replace( "[HIDDEN]", "", $tmpRow );

						$tmpRow = str_replace( "[MCODE1]", substr($row['dpCode'],0,$this->category_length), $tmpRow );
						$tmpRow = str_replace( "[MCODE2]", "", $tmpRow );
					} 
					else if ( $row['dpLevel'] == 1 ) 
					{
						//$tmpRow = str_replace( "[FOLD]", "", $tmpRow );
						$tmpRow = str_replace( "[FOLD]", "<span class=\"fold_dp lv2\" rel=\"".$row['dpCode']."\">-</span>", $tmpRow );
						$tmpRow = str_replace( "[HIDDEN]", "", $tmpRow );

						$tmpRow = str_replace( "[MCODE1]", "", $tmpRow );
						$tmpRow = str_replace( "[MCODE2]", "", $tmpRow );
					}

					$html_result .= $tmpRow;
				}
				$count++;
			}
		}
		return $html_result;
	}

	// 메뉴 차수별 순서 관리
	function makeOrderList( $pKey, $skin, $optYear )
	{
		$pKey = clean( $pKey );
		$optYear = clean( $optYear );

		$html_result	= "";
		$count			= 0;

		$query	=	"select * 
						 from ". $this->TABLE ."
						 where delState = 'N' and dpParent = '".$pKey."'  and dpYear = '".$optYear."'
						 order by dpCode ";

		if ( $result = $this->db->query( $query ) )
		{
			while( $row = $this->db->fetch($result) )
			{
				$row = @array_map( "cleanRev", $row );
				$tmpRow = $skin;
				
				$tmpRow = str_replace( "[NUM]", 
				"<input type=\"hidden\" name=\"dpSid[]\" value=\"".$row['dpSid']."\" />
				<input type=\"image\" src=\"".__PAGE_URL."/images/bt_arrow_up.gif\" border=\"0\" class=\"arrowup\" style=\"cursor:pointer\" />
				<input type=\"hidden\" name=\"dpCode[]\" value=\"".$row['dpCode']."\" />
				<input type=\"image\" src=\"".__PAGE_URL."/images/bt_arrow_down.gif\" border=\"0\" class=\"arrowdown\" style=\"cursor:pointer\" />",
										$tmpRow );

				if($row['userSid']==0){
					$tmpRow = str_replace( "[NAME]", $row['dpName'], $tmpRow );
				}else{
					$tmpRow = str_replace( "[NAME]", $row['userName'], $tmpRow );
				}

				// 레벨 표시 . $row['dpSid']
				$tmpRow = str_replace( 
									"[ORDER]", 
									str_repeat( "&nbsp;&nbsp;", $row['dpLevel'] ). "<img src=\"/pages/images/level_0{$row['dpLevel']}.gif\" border=\"0\" alt=\"".$row['dpLevel']."차\" /> ", $tmpRow );

				$tmpRow = str_replace( "[COUNT]", $count, $tmpRow );
				//연도표시
				$tmpRow = str_replace( "[YEAR]", $optYear, $tmpRow );

				$html_result .= $tmpRow;
				$count++;
			}
		}

		if ( $html_result == "" )
		{
			$skin = str_replace( "[CHECK]", "&nbsp;", $skin );
			$skin = str_replace( "[ORDER]", "&nbsp;", $skin );
			$skin = str_replace( "[VIEW]", "&nbsp;", $skin );
			$skin = str_replace( "[NAME]", "등록된 메뉴가 없습니다", $skin );
			$skin = str_replace( "[BUTTON]", "&nbsp;", $skin );
			$html_result = $skin;
		}

		return $html_result;
	}

	/**
	 * top 메뉴용
	 * 전체 카테고리 배열(사용자 페이지) --> 바로 하위 메뉴만 조회
	 * @param string $pKey 메뉴 상위키
	 * @param $prd_count string 해당분류 상품 숫자 정보 출력 여부
	 */
	function getCategoryArray( $pKey = "", $prd_count = "" )
	{
		$result = array();

		// 상위 코드 정보
		$pKey		= clean( $pKey );
		$pLevel	= 1;	// 기본값 1
		// 상위 코드 존재시.. 해당 상위코드의 하위 카테고리만 검색
		if ( $pKey != "" ) 
		{
			$query = "select dpCode, dpLevel from ". $this->TABLE ." where dpSid = '".$pKey."' ";
			$row = $this->db->fetch( $query );
			$pCode = $row['dpCode'];
			$pLevel = $row['dpLevel'];
			// 상위 코드 번호 검색 and 하위분류 코드 길이 검색
			$query_parent = " and substring(dpCode,1,".strlen($pCode).") = '".$pCode."' and dpLevel = '".intval($pLevel+1)."' ";
		}

		//$query	=	"select dpSid, dpCode, dpName, dpLevel, useTopMenu, contentType, content
		//					{$prdQuery}
		//				 from ". $this->TABLE ." a
		//				 where delState = 'N' and dpType not in ( 'MAIN' )
		//					{$query_parent}
		//				order by dpCode ";
		$query	=	"select dpSid, dpCode, dpName, dpLevel
							{$prdQuery}
						 from ". $this->TABLE ."
						 where delState = 'N' and dpType not in ( 'MAIN' )
							{$query_parent}
						order by dpCode ";
//echo $query;
		if ( $rs = $this->db->query( $query ) )
		{
			while( $row = $this->db->fetch($rs) )
			{
				$row = @array_map( "cleanRev", $row );

				// 1차 카테고리 선택시 : 001, 2차 카테고리 선택시 : 001002
				$parentCd = substr( $row['dpCode'], 0, $this->category_length * $pLevel );
				$result[ $parentCd ][] = $row;
			}
		}
		return $result;
	}

	/**
	 * left 메뉴용
	 * 2차-3차 메뉴만 조회
	 * @param string $pKey 메뉴 코드( 001001002 )
	 */
	function getLeftCategory( $pKey = "" )
	{
		$result = array();

		// 상위 코드 정보
		$pKey		= clean( $pKey );
		$pLevel	= 1;	// 기본값 1
		// 상위 코드 존재시.. 해당 상위코드의 하위 카테고리만 검색
		if ( $pKey != "" ) 
		{
			$query = "select dpCode, dpLevel from ". $this->TABLE ." where dpCode = '".substr($pKey,0,3)."' ";
			$row = $this->db->fetch( $query );
			$pCode = $row['dpCode'];
			$pLevel = $row['dpLevel'];
			// 상위 코드 번호 검색 and 하위분류 코드 길이 검색
			$query_parent = " and substring(dpCode,1,".strlen($pCode).") = '".$pCode."' and dpLevel > '".$pLevel."' ";
		}
		$query	=	"select dpSid, dpCode, dpName, dpLevel, useTopMenu, contentType, content
						 from ". $this->TABLE ." a
						 where delState = 'N' and dpType not in ( 'MAIN' )
							{$query_parent}
						order by dpCode ";
//echomx( $query );
		if ( $rs = $this->db->query( $query ) )
		{
			while( $row = $this->db->fetch($rs) )
			{
				$row = @array_map( "cleanRev", $row );

				// 1차 카테고리 선택시 : 001, 2차 카테고리 선택시 : 001002
				$parentCd = substr( $row['dpCode'], 0, $this->category_length * 2 );
				$result[ $parentCd ][] = $row;
			}
		}
		return $result;
	}

	/** 
	 * 카테고리 정보 등록/수정시 분류명 카테고리 명칭 별도 저장
	 * @param string $newDpCd 메뉴 코드
	 * @return string $dpNameSql 쿼리문
	 */
	function getDpNm($newDpCd)
	{
		// 메뉴 계층 명칭
		$dpNameSql = "";
		$tmpNameArr = array();

		// [17.01.05] 자신 바로 상위까지의 정보만 조회
		for ( $i = 0; $i < strlen( $newDpCd ) / $this->category_length - 1; $i++ )
		{
			$cquery = "select dpName from {$this->TABLE} 
							where delState = 'N' and dpCode like '".substr( $newDpCd, 0, intval($i+1) * $this->category_length )."%' 
							order by dpCode asc limit 1 ";
//errorLog( $cquery );
			$cRow = $this->db->fetch( $cquery );
			if ( $cRow['dpName'] != "" ) {
				$tmpNameArr[] = $cRow['dpName'];
				//echo $cRow['dpName'];
			}
		}

		$dpNameSql = join( " > ", $tmpNameArr );

		return $dpNameSql;
	}

	//아이디 중복체크(삭제상태도 포함)
	function checkId( $userId ){
		$query = "select userId from user where userId = '".clean( $userId )."' ";

		$rs = $this->db->query( $query );
		if ( $this->db->num_rows( $rs ) > 0 ) 
			return false;
		else
			return true;
	}

	//권한 중복체크
	function checkPermit($dpSid, $mode, $dpLevel){
		$dpSid = $_POST['dpSid'];
		$mode = $_POST['mode'];
		$dpLevel = $_POST['dpLevel'];

		if($mode == "ADD"){
			//$sidQuery = "select userSid from {$this->TABLE} where dpParent = '".$dpSid."' and dpLevel = '".$dpLevel."'";
			$sidQuery = "select userSid from {$this->TABLE} where dpParent = '".$dpSid."' and dpLevel = '".$dpLevel."' and delState = 'N'";
			if ( $sidRs = $this->db->query( $sidQuery ) )
			{
				while( $sidRow = $this->db->fetch($sidRs) )
				{
					$query = "select userSid, permit from user where userSid = '".$sidRow['userSid']."' ";
					$rs = $this->db->fetch( $query );
					if($rs['permit']=='Y'){
						return $rs['userSid'];
					}
				}
			} 				
		}else if($mode == "MOD"){
			//$pQuery = "select dpParent from {$this->TABLE} where dpSid = '".$dpSid."' and dpLevel = '".$dpLevel."'";
			$pQuery = "select dpParent from {$this->TABLE} where dpSid = '".$dpSid."' and dpLevel = '".$dpLevel."' and delState = 'N'";
			$prs = $this->db->fetch( $pQuery );
			
			//$sidQuery = "select userSid from {$this->TABLE} where dpParent = '".$prs['dpParent']."'";
			$sidQuery = "select userSid from {$this->TABLE} where dpParent = '".$prs['dpParent']."' and delState = 'N'";
			
			if ( $sidRs = $this->db->query( $sidQuery ) )
			{
				while( $sidRow = $this->db->fetch($sidRs) )
				{
					if($sidRow['userSid'] != '0'){
						$query = "select permit, userSid from user where userSid = '".$sidRow['userSid']."'";
						$rs = $this->db->fetch( $query );
						if($rs['permit']=='Y'&&$rs['userSid']!=$dpSid){
							return $rs['userSid'];
						}
					}
				}
			} 
		}

		return false;
	}

	/**
	 * 부서 및 회원 등록
	 * 
	 */
	function addDepartment() 
	{
		$availUser = clean( @join( "|", $_POST['availUser'] ) );

		$_POST = clean( $_POST, "HTML" );
		
		//dpYear이 없는 경우 
		if($_POST['dpYear']==""){
			$dpYear = $this->maxYear();;
		}else{
			$dpYear = $_POST['dpYear'];
		}


		//dpYear기록이 없을 경우
		if($dpYear == ""){
			$nowQuery = "select year(now()) as year";
			$nowRow = $this->db->fetch( $nowQuery );
			$dpYear = $nowRow['year'];
		}

		// 선택 레벨
		$level = (int)$_POST['dpLevel'];
		if ( $level <= 0 ) $level = 1;
		// 상위 코드
		$parent = $_POST['dpGParent'];
		// 상위코드 존재시 해당 상위코드에 속하는 요소 검색
		if ( $parent != "" ) $parentSql = " and substring( dpCode, 1, ". ( $level - 1 ) * $this->category_length . " ) = '".$parent."' ";

		// 해당 차수의 최대 수
		$max_query  = "select max( morder{$level} ) as max
							 from {$this->TABLE} 
							 where delState = 'N' and dpYear = '".$dpYear."' ". $parentSql;
		$maxRow = $this->db->fetch( $max_query );

		// 999, 9999 초과시
		if ( intval( $maxRow['max'] ) == $this->category_max_no ) errorMsg( "더이상 카테고리를 생성할 수 없습니다!" );
		$maxNo = intval( $maxRow['max'] ) + 1;
		// 0 채우기
		$maxNoStr = str_repeat( '0', $this->category_length - strlen($maxNo) ) . $maxNo;
		// 신규 메뉴 코드 
		$newDpCd = $parent . $maxNoStr;

		// 메뉴 계층 코드
		$dpOrderSql = "";
		for ( $i = 0; $i < strlen( $newDpCd ) / $this->category_length; $i++ )
			$dpOrderSql .= "morder".intval($i+1)." = '".substr( $newDpCd, $i * $this->category_length, $this->category_length )."', ";

		// 사용자 비밀번호
		$userPasswd = $_POST['userPw'];


		if($_POST['useContent']=="Y"){
			//아이디 중복검사
			if ( !$this->checkId($_POST['userNum']) )	errorMsg( "이미 등록되어 있는 아이디 입니다!", "BACK");
			$permitSid = $_POST['permitCheck'];

			if($permitSid != "0" ){
				$updatePermit = "update user set permit = 'N' where userSid = '".$permitSid."'";
				$this->db->query( $updatePermit );
			}

			$userQuery = "insert into user set
											userId 		     = '".$_POST['userNum']."',
											userNum 		 = '".$_POST['userNum']."',
											userPosition	 = '".$_POST['userPosition']."',
											permit			 = '".$_POST['permit']."',  
											userName	     = '".$_POST['userName']."', 
											userPassword 	 = ".Encode($userPasswd).",
											userTel 		 = '".$_POST['userTel']."', 
											userPhone		 = '".$_POST['userPhone']."', 
											userEmail		 = '".$_POST['userEmail']."',
											userLevel        = '11',
											loginState       = 'OK',
											regDate          = now()";

			//userSid를 뽑아서 userSid를 department에 넣어주기
			if($this->db->query($userQuery)){
				$sidQuery = "SELECT MAX(userSid) AS userSid FROM user";
				$sidRow = $this->db->fetch( $sidQuery );
				$userSid = $sidRow['userSid'];
			}
		}

		

		$dpQuery = "insert into {$this->TABLE} set
											dpYear 			= '".$dpYear."',
											userSid 		= '".$userSid ."',
											dpCode			= '".$newDpCd."', 
											dpName			= '".$_POST['dpName']."', 
											userName	    = '".$_POST['userName']."',  
											dpParent 		= '".$_POST['dpParent']."', 
											dpLevel			= '".$_POST['dpLevel']."', 
											useContent		= '".$_POST['useContent']."',
											{$dpOrderSql}
											regDate         = now()";

		if ( $this->db->query($dpQuery) ) 
		{
			$sid = $this->db->insert_id();
			// 에디터 파일 이미지 처리
			//$this->updateFileInfo($sid);
		}

	}

	//새 연도 추가
	function addYear() 
	{
		$availUser = clean( @join( "|", $_POST['availUser'] ) );

		$_POST = clean( $_POST, "HTML" );

		//새 연도
		$yearQuery  = "select MAX(dpYear)+1 as newYear 
							 from {$this->TABLE} 
							 where delState = 'N' ";
		$yearRow = $this->db->fetch( $yearQuery );
		$newYear = $yearRow['newYear'];

		//연도 지정
		if($_POST['dpYear']==""){
			$dpYear = $this->maxYear();
		}else{
			$dpYear = $_POST['dpYear'];
		}

		if($dpYear == ""){
			errorMsg( "조직도가 없어 등록할 수 없습니다!", "BACK" );
		}

		$query = "select *
						from {$this->TABLE} 
						where delState = 'N' and dpYear = '".$dpYear."'
						order by dpCode";

		if ( $rs = $this->db->query( $query ) )
		{
			while( $row = $this->db->fetch($rs) )
			{
				if($row['dpLevel']=="2"){
					$dpParent = $sid_1 ;
				}else if($row['dpLevel']=="3"){
					$dpParent = $sid_2 ;
				}else{
					$dpParent = $row['dpParent'];
				}
				
				$dpQuery = "insert into {$this->TABLE} set
											dpYear          = '".$newYear."',
											userSid 		= '".$row['userSid'] ."',
											dpCode			= '".$row['dpCode']."', 
											dpName			= '".$row['dpName']."', 
											userName	    = '".$row['userName']."',  
											dpParent 		= '".$dpParent."', 
											dpLevel			= '".$row['dpLevel']."', 
											useContent		= '".$row['useContent']."',
											morder1  		= '".$row['morder1']."',
											morder2  		= '".$row['morder2']."',
											morder3  		= '".$row['morder3']."',
											morder4  		= '".$row['morder4']."',
											regDate         = now()";

				if($this->db->query($dpQuery)){
					if($row['dpLevel']=="1"){
						$sid_1 = $this->db->insert_id();
					}else if($row['dpLevel']=="2"){
						$sid_2 = $this->db->insert_id();
					}
				}
			}
		}
	}

	// 메뉴 정보 수정
	function updateDepartment( $dpSid )
	{
		$dpSid = clean( $dpSid );
		if ( trim( $dpSid ) == "" ) errorMsg( "필수정보가 없습니다!", "BACK" );

		$_POST = clean( $_POST, "HTML" );

		// 메뉴 계층 명칭
		$query = "select dpCode, dpName, userSid from {$this->TABLE} where dpSid = '".$dpSid."'";
		$row = $this->db->fetch( $query );
		$dpName = $this->getDpNm($row['dpCode']);
		$oldMenuName = $row['dpName'];
		$userSid = $row['userSid'];
		if ( $dpName != "" ) $dpName .= " > ";
		$dpName .= $_POST['dpName'];
		$userPasswd = $_POST['userPw'];
		
		if($_POST['useContent']=="Y"){
			$permitSid = $_POST['permitCheck'];
			if($permitSid != "0" ){
				$updatePermit = "update user set permit = 'N' where userSid = '".$permitSid."'";
				$this->db->query($updatePermit);
			}
			
			if($userPasswd==""){
				$userQuery = "UPDATE user SET 
										userId 		     = '".$_POST['userNum']."',
										userNum 		 = '".$_POST['userNum']."',
										userPosition	 = '".$_POST['userPosition']."', 
										userName	     = '".$_POST['userName']."', 
										userPassword 	 = ".Encode($userPasswd).",
										userTel 		 = '".$_POST['userTel']."', 
										userPhone		 = '".$_POST['userPhone']."', 
										userEmail		 = '".$_POST['userEmail']."',
										permit		 = '".$_POST['permit']."',
										userLevel        = '11',
										loginState       = 'OK',
										modDate          = now()
									WHERE userSid = '".$userSid."'";
			}else{
				$userQuery = "UPDATE user SET 
										userId 		     = '".$_POST['userNum']."',
										userNum 		 = '".$_POST['userNum']."',
										userPosition	 = '".$_POST['userPosition']."', 
										userName	     = '".$_POST['userName']."', 
										userTel 		 = '".$_POST['userTel']."', 
										userPhone		 = '".$_POST['userPhone']."', 
										userEmail		 = '".$_POST['userEmail']."',
										permit		 = '".$_POST['permit']."',
										userLevel        = '11',
										loginState       = 'OK',
										modDate          = now()
									WHERE userSid = '".$userSid."'";
			}


			$this->db->query($userQuery);
		

			$dpQuery = "UPDATE {$this->TABLE} SET 
								dpName			= '".$_POST['dpName']."',  
								userName	    = '".$_POST['userName']."',  
								useContent		= '".$_POST['useContent']."', 
								modDate          = now()
							WHERE dpSid = '".$dpSid."'";
		}else{
			$dpQuery = "UPDATE {$this->TABLE} SET 
								dpName			= '".$_POST['dpName']."',  
								useContent		= '".$_POST['useContent']."', 
								modDate          = now()
							WHERE dpSid = '".$dpSid."'";
		}


		if ( $this->db->query($dpQuery) )
		{
			//메뉴명 변경시 하위메뉴 명칭도 변경 처리
			if ( $oldMenuName != $_POST['dpName'] && $this->max_dp_lvl )
			{
				//TODO : dpYear post로 넘어오는 지 체크
				$categories = $this->selectAllSub( $dpSid , $_POST['dpYear'] );
				if ( count( $categories ) == 0 ) return;
				
				$dpSidArr = array();
				foreach( $categories as $val ) 
				{
					if ( $dpSid != $val['dpSid'] ) $dpSidArr[] = $val['dpSid'];
				}
				//if ( count( $dpSidArr ) > 0 ) 
				//{
				//	$upQuery = "update {$this->TABLE} set mname = concat( '".$menuName."', '>', menuName ) where dpSid in (".join( ",", $dpSidArr ).")";
				//	$this->db->query( $upQuery );
				//}
			}
		}
	}

	/**
	 * 자신 포함 전체 하위 메뉴를 일차원 배열로 
	 */
	function selectAllSub( $dpSid , $dpYear)
	{
		$result = array();

		//if($_POST['dpYear']==""){
		//	$dpYear = $this->maxYear();
		//}else{
		//	$dpYear = $_POST['dpYear'];
		//}

		// 상위 코드 정보
		$dpSid		= clean( $dpSid );
		$pLevel	= 1;	// 기본값 1
		// 상위 코드 존재시.. 해당 상위코드의 하위 카테고리만 검색
		if ( $dpSid != "" ) 
		{
			$query = "select dpCode, dpLevel from ". $this->TABLE ." where dpSid = '".$dpSid."' and dpYear = '".$dpYear."'";			
			$row = $this->db->fetch( $query );
			$pCode = $row['dpCode'];
			$pLevel = $row['dpLevel'];
			// 상위 코드 번호 검색 and 하위분류 코드 길이 검색
			$query_parent = " and substring(dpCode,1,".strlen($pCode).") = '".$pCode."' ";
		}

		$query	=	"select dpSid, dpCode, dpName, dpLevel
						 from ". $this->TABLE ." a
						 where delState = 'N' and dpYear = '".$dpYear."'
							{$query_parent}
						order by dpCode ";
//echomx( $query );
		if ( $rs = $this->db->query( $query ) )
		{
			while( $row = $this->db->fetch($rs) )
			{
				$row = @array_map( "cleanRev", $row );
				$result[] = $row;
			}
		}
		return $result;
	}

	//삭제 기능(한 열마다)
	function deleteDepartment( $dpSid, $depth ) 
	{
		$dpSid = clean( $dpSid );

		if($_POST['dpYear']==""){
			$dpYear = $this->maxYear();
		}else{
			$dpYear = $_POST['dpYear'];
		}

	
		if ( trim( $dpSid ) == "" ) errorMsg( "필수정보가 없습니다!", "BACK" );
		
		$categories = $this->selectAllSub( $dpSid , $dpYear );
	
		if ( count( $categories ) == 0 ) return;

		foreach( $categories as $val )
		{
			if($depth != "1"){
				$query = "select userSid from {$this->TABLE} where dpSid = '".$val['dpSid']."'";
				$row = $this->db->fetch( $query );

				$deleteQuery = "update user set loginState = 'BAN', delState = 'Y', delDate = now()
								where userSid = '".$row['userSid']."' ";
				
				$this->db->query( $deleteQuery );
			}
			$deleteQuery = "update {$this->TABLE} set delState = 'Y', delDate = now()
								where dpSid = '".$val['dpSid']."' and dpYear = '".$dpYear."'";

			//where dpSid = '".$val['dpSid']."' and dpYear = '".$dpYear."'";
			
			$this->db->query( $deleteQuery );
		}

		return true;
	}

	//부서관리 선택 삭제
	function chkDelete(){
		
		for($i=0;$i<count($_POST['check']);$i++){
			$mester_sid = $_POST['check'][$i];
			$state_key = "state_".$mester_sid;
			$state = $_POST[$state_key];

			$query = "select dpLevel from {$this->TABLE} where dpSid = '".$mester_sid."'";

			$row = $this->db->fetch($query);

			$this->deleteDepartment($mester_sid, $row['dpLevel']);
		}
		return true;
	}

	// 일괄 레이아웃 변경
	// function changeLayout()
	// {
	// 	if ( trim( $_POST['laysid'] ) == "" || !is_numeric( $_POST['laysid'] ) )
	// 		errorMsg( "[오류] 변경하고자 하는 항목을 선택해주세요!" );

	// 	$sids = implode( ",", $_POST['check'] );
	// 	$query = "UPDATE {$this->TABLE} SET lay_sid = '".(int)$_POST['laysid']."' WHERE dpSid in (".clean($sids).")";
	// 	return $this->db->query( $query );
	// 	//echo $query;
	// 	//exit;
	// }

	// 정보 
	function getInfo( $dpSid ) 
	{
		$query = "select useContent, dpLevel from {$this->TABLE} where dpSid = '".clean( $dpSid )."' and delState = 'N'";
		$row = $this->db->fetch( $query );
		
		if($row['useContent']=='Y'&&$row['dpLevel']=='3'){
			$query = "select a.*, b.*, c.dpName from {$this->TABLE} as a, user as b, {$this->TABLE} as c						
			where a.dpSid = '".clean( $dpSid )."' and a.userSid = b.userSid and a.delState = 'N' and a.dpParent = c.dpSid";
		}
		else if($row['useContent']=='Y'&&$row['dpLevel']=='2'){
			$query = "select a.*, b.* from {$this->TABLE} as a, user as b					
			where a.dpSid = '".clean( $dpSid )."' and a.userSid = b.userSid and a.delState = 'N' ";
		}
		else{
			$query = "select dpName, useContent from {$this->TABLE} where dpSid = '".clean( $dpSid )."' and delState = 'N'";
		}
		
		// 관리자일 경우에만 표시안함 메뉴 정보 조회 가능
		// if ( !is_admin() ) $query .= " and isUse ='Y'";

		$row = $this->db->fetch( $query );
		$row = @array_map('cleanRev', $row);
		return $row;
	}

	// 정보 - 메뉴코드로 조회
	function getInfoByCode( $dpCode ) 
	{
		$query = "select * from {$this->TABLE}  						
					 where dpCode = '".clean( $dpCode )."' and delState = 'N' ";
		$row = $this->db->fetch( $query );
		$row = @array_map('cleanRev', $row);
		return $row;
	}

	/**
	@ 순서 정보 변경
	@ params : 현재 orderdpType, 변경할 order (001001,001002)
	**/
	function changeOrder( $params )
	{
		$year = $_POST['year'];

		if ( trim( $params ) != "" )
		{
			$paramArr = explode( ",", $params );

			// 두개의 order 정보를 서로 변경처리
			$target1 = $paramArr[0];
			$target2 = $paramArr[1];
			// 대상 레벨
			$level = strlen( $target1 ) / $this->category_length;
			// 각 레벨별 분해
			$target1Arr = array('');	// 0번째 index에 공백 추가
			$target2Arr = array('');
			for ( $i = 0; $i < $level; $i++ )
			{
				$target1Arr[] = substr( $target1, $i * $this->category_length, $this->category_length );
				$target2Arr[] = substr( $target2, $i * $this->category_length, $this->category_length );
			}

			// 선택 카테고리 및 하위 카테고리 포함 검색
			$query = "select dpSid, morder1, morder2, morder3, morder4 
							from {$this->TABLE} 
							where delState = 'N' and dpYear = '".$year."' and substring( dpCode, 1, ". $level * $this->category_length . " ) in ( '".$target1."', '".$target2."' ) ";
//errorLog( $query );

			if ( $rs = $this->db->query( $query ) )
			{
				while ( $row = $this->db->fetch( $rs ) )
				{
					// 해당 카테고리 레벨 코드 변경
					if ( $row['morder'.$level] == $target1Arr[$level] )
						$uQuery = "update {$this->TABLE} set morder{$level} = '".$target2Arr[$level]."' where dpSid = '".$row['dpSid']."' ";
					else if ( $row['morder'.$level] == $target2Arr[$level] )
						$uQuery = "update {$this->TABLE} set morder{$level} = '".$target1Arr[$level]."' where dpSid = '".$row['dpSid']."' ";					
					
				
					$this->db->query( $uQuery );
//errorLog($uQuery );
					// 전체 코드 변경
					$uQuery = "update {$this->TABLE} set dpCode = concat( morder1, ifnull(morder2,''), ifnull(morder3,''), ifnull(morder4,'') ) 
									where dpSid = '".$row['dpSid']."' ";
					$this->db->query($uQuery);
//errorLog( $uQuery );
				}
			}
		}
	}	


	/**
	 * 게시판 목록 (컨텐트에서 선택용)
	*/
	function _list_bbs( $skin )
	{
		$result	= "";
		$temp	= "";

		$this->config( "board_config" );

		// main query
		$query	= "
						select a.board_sid, board_id, board_type, board_lang, board_title,  board_dp_title, register_date, b.category_name, b.category_sid  
						from board_config a
						left join board_category b
						on ( a.board_sid = b.board_sid )
						where a.delete_state = 'N' 
						order by a.board_sid, b.category_order ";
						// user_sid =	'".$this->getUserSid()."' 
		// db query
		if ( $rs = $this->db->query( $query ) )
		{
			while ( $row = $this->db->fetch( $rs ) )
			{
				$temp = $skin;
				
				$link = "<a href=\"#this\" onClick=\"selBBS('/pages/board/list.php?board_sid=".$row['board_sid']."&category_code1=".$row['category_sid']."')\" class=\"btn-a btn-a-b\">";

				$temp = str_replace( "[NUM]",			$row['board_sid'],										$temp );
				$temp = str_replace( "[TYPE]",		$this->config['board'][$row['board_type']],$temp );
				$temp = str_replace( "[LANG]",		$row['board_lang'],										$temp );
				$temp = str_replace( "[TITLE]",		$row['board_dp_title'] ." > <strong><font color=\"#000099\">" . $row['board_title']."</font></strong>",										$temp );
				$temp = str_replace( "[CATEGORY]",$row['category_name'],	$temp );
				$temp = str_replace( "[DATE]",		substr($row['register_date'],0,10),				$temp );
				$temp = str_replace( "[BUTTON]",	$link."선택</a>",				$temp );

				$result .= $temp;
			}
		}

		return $result;
	}

	/** 
	 * 특수 페이지 조회
	 * @param $dpType string 'MAIN' - 메인,'LOGIN' - 로그인,'SITEMAP' - 사이트맵
	 */
	public function getPage( $dpType )
	{
		$query = "select dpSid from {$this->TABLE} 
					 where dpType = '".clean($dpType,"HTML")."' and delState = 'N'";
		$row = $this->db->fetch( $query );
		return $row['dpSid'];
	}

	/** 
	 * 메인 페이지 조회
	 */
	public function getMainPage()
	{
		$query = "select dpSid, dpCode from {$this->TABLE} 
					 where dpType = 'MAIN' and delState = 'N'";
		$row = $this->db->fetch( $query );
		return $row;
	}

	/**
	 * 단순 링크 메뉴 일 경우.. 컨텐츠가 있는 최하위 메뉴 조회
	 * @param $dp array 메뉴정보
	 */
	function getLastMenuInfo( $dp )
	{
		if ( !is_array( $dp ) ) return "";
		
		$mCode = $dp['dpCode'];
		$mLvl = $dp['dpLevel'];

		$query = "select * from {$this->TABLE}  						
					 where useContent = 'Y' and delState = 'N' and substring( dpCode, 1, ".$this->category_length * $mLvl." ) = '".$mCode."' ";
		$row = $this->db->fetch( $query );
		$row = @array_map('cleanRev', $row);
		return $row;
	}

	/**
	 *	제품 뷰 바로 가기
	 *  @todo 180612 product 테이블의 dpCode는 제품의 분류코드인데.. 메뉴의 분류코드로 조회하고 있음.. 
	 *    이 부분 명백한 오류인듯. 사용처 확인 후 필히 수정하기
	 */
	function getProductSid( $dp_sid ){
		$query = "select sid from product where dpCode= right(( select dpCode from site_category where dpSid = '".$dp_sid."' ),3) and del_st = 'N' ";
		if ( $rs = $this->db->query( $query ) ){
			$row = $this->db->fetch( $query );
		}else{
			$row = "";
		}
		
		return $row;
	}

	
	//3뎁스
	function getdepth_dp($dpCode){
		$result = array();
		
		$p_query = "select * from site_category where delState = 'N' and dpType not in ( 'MAIN' ) and dpSid = '".$dpCode."' and dpLevel = '3'";
		if ( $rs = $this->db->query( $p_query ) ){
			$row = $this->db->fetch( $p_query );
			if($row != ""){
				$pcode = $row['dpParent'];
				$dpCode = $row['dpParent'];
			}
		}

		if($dpCode == "103"){
			$dpCode = "104";
		}

		$query = "select * from site_category where delState = 'N' and dpType not in ( 'MAIN' ) and dpParent = '".$dpCode."'  and dpLevel = '3'";
		if ( $rs = $this->db->query( $query ) )
		{
			while ( $row = $this->db->fetch( $rs ) )
			{
				$result[] = $row;
			}
		}		
		
		return array($result, $pcode);
	}
}
?>