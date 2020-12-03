<?php
/**
 *
 * Last modified May 15 2015
 *
 */
if ( ! defined('__BASE_PATH')) exit('No direct script access allowed');

/*
 * 차량 코드 관리
 */

include_once __MODULE_PATH."/core/CoreObject.php";

class Code extends CoreObject {	
	var $main_query;
	var $page_query;
	var $foot_query;

	var $skin		= "";
	var $db;
	var $groupNm = "";

	function Code( $codeType, $groupNm = "CODE" ) 
	{
		// 차량 설정
		if ( $codeType == "CAR" ) 
			$this->TABLE	= "code_car";
		// 차량 옵션 설정
		else if ( $codeType == "CAR_OPT" ) 
			$this->TABLE	= "code_option";

		$this->db = $this->module( "core/DB" );
		$this->groupNm = $groupNm;
	}

	function searchCon() 
	{
		$query = "";

		return $query;
	}
	
	// 메뉴 차수별 순서 관리
	function makeOrderList( $pKey, $skin )
	{
		$pKey = clean( $pKey );
		$html_result	= "";
		$count			= 0;

		$query	=	"select * from ". $this->TABLE ."
						 where delState = 'N' and groupNm = '".$this->groupNm."' and parentCode = '".$pKey."' 
						 order by orderCnt asc";

		if ( $result = $this->db->query( $query ) )
		{
			while( $row = $this->db->fetch($result) )
			{
				$row = array_map( "stripslashes", $row );
				$tmpRow = $skin;
				
				$tmpRow = str_replace( "[NUM]", 
				"<input type=\"hidden\" name=\"code[]\" value=\"".$row['code']."\" />
				<input type=\"image\" src=\"".__PAGE_URL."/images/bt_arrow_up.gif\" border=\"0\" class=\"arrowup\" style=\"cursor:pointer\" />
				<input type=\"image\" src=\"".__PAGE_URL."/images/bt_arrow_down.gif\" border=\"0\" class=\"arrowdown\" style=\"cursor:pointer\" />
				<input type=\"text\" name=\"orderCnt[]\" style=\"padding: 1px 1px 1px 1px;border:none;text-align:center;width:15px\" value=\"".$row['orderCnt']."\" size=\"2\" readonly />",
										$tmpRow );

				// 레벨 표시
				$tmpRow = str_replace( "[ORDER]", str_repeat( "&nbsp;&nbsp;", $row['level'] ). $row['code'], $tmpRow );
				//"<img src=\"/pages/images/level_0{$row['level']}.gif\" border=\"0\" alt=\"".$row['level']."차\" /> ". 

				$tmpRow = str_replace( "[CODE]",	$row['code'], $tmpRow );
				
				$tmpRow = str_replace( "[COUNT]",	$count, $tmpRow );

				$button = " <span class=\"button_s\"><a href=\"#this\" onClick=\"modify('".$row['code']."','".$row['name']."','".$row['level']."')\">수정</a></span>";
				$button .= " <span class=\"button_s\"><a href=\"#this\" onClick=\"del('".$row['code']."','".$row['level']."')\">삭제</a></span>";

				$tmpRow = str_replace( "[NAME]",	$row['name'],	$tmpRow );
				/*if ( $row['isUse'] == 'Y' ) 
				{
					$tmpRow = str_replace( "[NAME]",	$row['name'],	$tmpRow );
					$button .= "<div class=\"mgt5\"><span class=\"button_s black\"><a href=\"#this\" onClick=\"chgUSE('".$row['code']."','N')\">사용안함</a></span></div>";
				}
				else
				{
					$tmpRow = str_replace( "[NAME]",	"<font color=\"#cccdd9\"><strike><사용X> ".$row['name']."</strike></font>",	$tmpRow );
					$button .= "<div class=\"mgt5\"><span class=\"button_s black\"><a href=\"#this\" onClick=\"chgUSE('".$row['code']."','Y')\">[사용함]</a></div>";
				}*/
				$tmpRow = str_replace( "[BUTTON]", $button, $tmpRow );

				//[15.08.13] 레벨 관련				
				$tmpRow = str_replace( "[LEVEL]", $row['level'], $tmpRow );

				$html_result .= $tmpRow;
				$count++;
			}
		}

		if ( $html_result == "" )
		{
			$skin = str_replace( "[CHECK]", "&nbsp;", $skin );
			$skin = str_replace( "[ORDER]", "&nbsp;", $skin );
			$skin = str_replace( "[VIEW]", "&nbsp;", $skin );
			$skin = str_replace( "[NAME]", "등록된 모델이 없습니다", $skin );
			$skin = str_replace( "[BUTTON]", "&nbsp;", $skin );
			$skin = str_replace( "[CODE]", "", $skin );
			$skin = str_replace( "[NUM]", "", $skin );
			$html_result = $skin;
		}

		return $html_result;
	}

	// 메뉴 차수별 순서
	// $modelType : 2차 카테고리일 경우 트럭 구분용.
	function userList( $pKey, $skin, $selectedKey = "", $menuType = "multi", $modelType = "" )
	{
		$pKey = clean( $pKey );
		$selectedKey = clean( $selectedKey, "HTML" );

		$html_result	= "";
		$count		= 0;

		// 트럭일 경우 해당 분류만 검색해야 함.
		$query_truck = "";
		if ( $modelType == "TRUCK" )
		{
			$query = "select group_concat( code ) as truckCD from code_option where name in ( '승합', '화물/트럭', '버스' ) ";
			$row = $this->db->fetch( $query );
			if ( trim( $row['truckCD'] ) != "" ) 
				$query_truck = " and car_type_cd in ( ".$row['truckCD']." ) ";
		}

		$query_parent = "";
		if ( $pKey != "" ) 
			$query_parent = " and parentCode = '".$pKey."'  ";
		
		// 국산, 수입 구분
		$query_group = "";
		if ( $this->groupNm != "ALL" )
			$query_group = " and groupNm = '".$this->groupNm."' ";

		$query = "select * from ". $this->TABLE ."
					 where delState = 'N' 
					{$query_parent}
					{$query_group}
					 {$query_truck}
					 order by groupNm asc, orderCnt asc";
//echo $query."<br />";
		if ( $result = $this->db->query( $query ) )
		{
			while( $row = $this->db->fetch($result) )
			{
				$row = array_map( "stripslashes", $row );
				$tmpRow = $skin;
				
				$tmpRow = str_replace( "[CODE]",	$row['code'], $tmpRow );
				$tmpRow = str_replace( "[NAME]",	$row['name'],	$tmpRow );
				$tmpRow = str_replace( "[LEVEL]", $row['level'], $tmpRow );
				
				// 선택 항목 체크
				$selectedOpt = false;
				if ( $selectedKey != "" ) 
				{
					// 다중 선택값 체크
					if ( is_array( $selectedKey ) ) 
					{
						if ( in_array( $row['code'], $selectedKey ) ) 
							$selectedOpt = true;
					} 
					else if ( $selectedKey == $row['code'] ) 
						$selectedOpt = true;
				}
				// 선택 checkbox 및 li 선택 처리
				if ( $selectedOpt )
				{
					if ( $menuType == "multi" ) 
						$tmpRow = str_replace( "[SELECTED]", "class=\"car_model_seleced\"", $tmpRow );
					else
						$tmpRow = str_replace( "[SELECTED]", "selected", $tmpRow );

					$tmpRow = str_replace( "[CHECK]", 
					"<input type=\"checkbox\" name=\"code".$row['level']."[]\" onclick=\"showSub('".$row['code']."','".$row['level']."','".$row['name']."')\" value=\"".$row['code']."\" id=\"code_".$row['code']."\" checked />", 
										$tmpRow );
				}
				else
				{
					$tmpRow = str_replace( "[SELECTED]", "", $tmpRow );
					$tmpRow = str_replace( "[CHECK]", 
					"<input type=\"checkbox\" name=\"code".$row['level']."[]\" onclick=\"showSub('".$row['code']."','".$row['level']."','".$row['name']."')\" value=\"".$row['code']."\" id=\"code_".$row['code']."\" />", 
										$tmpRow );
				}

				$html_result .= $tmpRow;
				$count++;
			}
		}

		if ( $html_result == "" && $_GET['menuType'] == "multi" )
		{
			$skin = str_replace( "[CHECK]", "&nbsp;", $skin );
			$skin = str_replace( "[NAME]", "등록된 모델이 없습니다", $skin );
			$skin = str_replace( "[CODE]", "", $skin );
			$skin = str_replace( "[SELECTED]", "", $skin );
			$html_result = $skin;
		}

		return $html_result;
	}

	/**
	 * 메뉴 등록
	 * 
	 */
	function addMenu( $pName = "" ) 
	{
		$_POST = clean( $_POST, "HTML" );

		// 1차 카테고리 등록시는 없으므로 0으로 입력
		// 2차 카테고리 등록시는 부모 카테고리 코드 입력
		$parentCode = intval( $_POST['parentCode'] );

		$level	= intval( $_POST['level'] );

		if ( trim( $pName ) != "" )
			$name = clean( $pName, "HTML" );
		else
			$name = $_POST['new_name'];
		if ( $name == "" || $level == 0 ) errorMsg( "필수정보가 누락되었습니다!" );

		/* 트랜잭션 시작 */
		$this->db->startTrans();

		// 신규 차수
		$query = "select ifnull(max(orderCnt),0) + 1 as newOrder 
					 from {$this->TABLE} 
					 where level = '".$level."' and groupNm = '".$this->groupNm."' ";
		if ( $level > 1 ) 
			$query .= " and parentCode = '".$parentCode."' ";
		$query .= " for update ";
		$row = $this->db->fetch( $query );
		$newOrder = $row['newOrder'];
		if ( $newOrder == 0 ) 
		{
			$this->db->rollback();
			return false;
		}

		$insertQuery = "insert into {$this->TABLE} set
									 name			= '".$name."', 
									 groupNm	= '".$this->groupNm."', 
									 parentCode = '".$parentCode."', 
									 level			= '".$level."', 
									 orderCnt		= '".$newOrder."', 
									 isUse			= 'Y' 
									 ";
		if ( $this->db->query($insertQuery) )
		{
			$this->db->commit();
			return true;
		}
		else
		{
			$this->db->rollback();
			return false;
		}
	}

	// 2차 이상의 분류 다중 등록
	function addMenuMulti()
	{
		$names = clean( $_POST['multi_input'], "HTML" );
		if ( $names == "" ) errorMsg( "분류정보가 누락되었습니다!" );
		$nameArr = explode( "<br />", nl2br( $names ) );

		$result = true;
		for ( $i = 0; $i < count( $nameArr ); $i++ )
		{
			if ( trim( $nameArr[$i] ) != "" ) 
				$result = $this->addMenu( $nameArr[$i] );
		}
		return $result;
	}

	// 메뉴 정보 수정
	function updateMenu( $code )
	{
		$code = clean( $code, "HTML" );
		if ( trim( $code ) == "" ) errorMsg( "필수정보가 없습니다!", "BACK" );


		$query = "UPDATE {$this->TABLE} SET 
							 name	= '".$_POST['new_name']."'
						WHERE code = '".$code."'";
//echo $query;
		return $this->db->query($query);
	}

	// 메뉴 삭제
	function deleteMenu( $code ) 
	{
		$code = clean( $code, "HTML" );
		if ( trim( $code ) == "" ) errorMsg( "필수정보가 없습니다!", "BACK" );
		
		// 카테고리 정보 삭제
		$deleteQuery = "update {$this->TABLE} set delState = 'Y' where code = '".$code."' or parentCode = '".$code."' ";
		return $this->db->query( $deleteQuery );
	}

	// 메뉴 사용 유무 변경
	function changeUse( $code, $isUse ) 
	{
		$code = clean( $code, "HTML" );
		$isUse = clean( $isUse, "HTML" );
		if ( $code == "" || $isUse == "" ) errorMsg( "필수정보가 없습니다!", "BACK" );
		
		// 카테고리 정보 삭제
		$deleteQuery = "update {$this->TABLE} set isUse = '".$isUse."'
							where code = '".$code."' or parentCode = '".$code."' ";
		return $this->db->query( $deleteQuery );
	}

	// 정보 
	function getInfo( $code ) 
	{
		$query = "select * from {$this->TABLE}  						
						 where code = '".clean( $code )."' and delState = 'N' ";

		$row = $this->db->fetch( $query );
		$row = @array_map('stripslashes', $row);
		return $row;
	}

	// 등록하는 메뉴 레벨의 최대 값으로 새로운 순서 값 리턴
	function getNewOrder( $parentCode, $level )
	{
		$query = "select orderCnt from {$this->TABLE} 
						  where groupNm = '".$this->groupNm."' and parentCode = '".clean($parentCode)."' and level = '".clean($level)."' 
						  order by orderCnt desc";
		$row = $this->db->fetch( $query );
		return (int)$row['orderCnt'] + 1;
	}

	/**
	@ 순서 정보 변경
	@ params : key-order,key-order (1-4,2-3)
	**/
	function changeOrder( $params )
	{
		if ( trim( $params ) != "" )
		{
			$paramArr = explode( ",", $params );

			for ( $i = 0; $i < sizeof( $paramArr ); $i++ ) 
			{
				$tmpArr = explode( "-", $paramArr[$i] );
				$query = "update {$this->TABLE} set orderCnt = '".intval( $tmpArr[1] )."' where code = '".intval( $tmpArr[0] )."' ";
				$this->db->query( $query );
			}
		}
	}

	// 옵션 생성
	// param :	$level - 1/2
	//				$parent - 부모 그룹 값
	function getOption( $parentCode, $sel = "" )
	{
		$result = "";

		$level = 2;
		$parentCode = clean( $parentCode, "HTML" );
		$sel = clean( $sel, "HTML" );
	
		if ( $parentCode == "" ) return "";

		$query = "select a.* from {$this->TABLE} a
					 where level = '".$level."' and parentCode = '".$parentCode."' and a.delState = 'N' and a.isUse = 'Y' ";
		$query .= " order by orderCnt asc ";
		if ( $rs = $this->db->query( $query ) )
		{
			while ($row = $this->db->fetch( $rs ) )
			{
				if ( $row['code'] == $sel )
					$result .= "<option value=\"".$row['code']."\" selected>".$row['name']."</option>";
				else
					$result .= "<option value=\"".$row['code']."\">".$row['name']."</option>";
			}
		}

		return $result;
	}

	// 차종 설정
	function setCarType( $code, $type_cd )
	{
		$code = clean( $code, "HTML" );
		$type_cd = clean( $type_cd, "HTML" );
		if ( $code == "" || $type_cd == "" ) errorMsg( "필수정보가 없습니다!", "BACK" );
		
		// 차종 설정
		//$query = "replace into car_type set type_cd = '".$type_cd."', car_cd = '".$code."' ";
		$query = "update code_car set car_type_cd = '".$type_cd."' where code = '".$code."' ";
		return $this->db->query( $query );
	}

	// 차종 코드 조회
	function getCarTypeCode( $carCd )
	{
		$carCd = clean( $carCd, "HTML" );
		if ( $carCd == "" || $carCd == "" ) errorMsg( "필수정보가 없습니다!", "BACK" );

		// 차종 설정
		//$query = "select type_cd from car_type where car_cd = '".$carCd."' ";
		$query = "select car_type_cd from code_car where code = '".$carCd."' ";
		$row = $this->db->fetch( $query );
		return $row['car_type_cd'];
	}

	// 차종(경차.중형,화물..) 옵션
	function carTypeOption( $selected )
	{
		$result = "";
		$selected = clean( $selected, "HTML" );
		
		$query = "select code, name from code_option 
					 where delState = 'N' and isUse = 'Y' and groupNm = 'CAR_TYPE'
					 order by orderCnt asc ";
		if ( $rs = $this->db->query( $query ) )
		{
			while ($row = $this->db->fetch( $rs ) )
			{
				if ( $selected != "" && $row['code'] == $selected )
					$result .= "<option value=\"".$row['code']."\" selected>".$row['name']."</option>";
				else
					$result .= "<option value=\"".$row['code']."\">".$row['name']."</option>";
			}
		}

		return $result;
	}

	// 회원 목록 옵션
	function memberOption( $userSid )
	{
		$result = "";
		$userSid = clean( $userSid, "HTML" );
	

		$query = "select userSid, userName, userId from user
					 where delState = 'N' 
					 order by userName asc ";
		if ( $rs = $this->db->query( $query ) )
		{
			while ($row = $this->db->fetch( $rs ) )
			{
				if ( $row['userSid'] == $userSid )
					$result .= "<option value=\"".$row['userSid'].__SEPERATOR_FIELD.$row['userId']."\" selected>".$row['userName']."</option>";
				else
					$result .= "<option value=\"".$row['userSid'].__SEPERATOR_FIELD.$row['userId']."\">".$row['userName']."</option>";
			}
		}

		return $result;
	}

}
?>
