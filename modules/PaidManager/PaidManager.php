<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);

LIB("dp");
LIB("ds");
LIB("rp");
class PaidManager extends DBManager{
    public function info()
    {
        return "支付回调管理器"; // TODO: Change the autogenerated stub
    }

	public function PaidManager(){
		
	}
	
	public function OrderFinished($oid,$traid,$bill,$state){
        $res = $this->UpdateDataToTableByQuery($this->TName('tOrder'),
            [
                'traid'=>$traid,
                'bill'=>$bill
            ],
            self::FieldIsValue('oid',$oid)
        );
		
		return;
		if($state!= "SUCCESS"){
			return RESPONDINSTANCE("58");
		}
		$tryOrderData = DBResultToArray($this->SelectDataByQuery(
				$this->TName("tROrder"),
					self::C_And(
						self::FieldIsValue("rid",$oid),
						self::FieldIsValue("bill",$bill)
					)
				)
			,true);
		if(!empty($tryOrderData)){
			$tryOrderData = $tryOrderData[0];
			if($tryOrderData['state'] == "PAYMENT"){
				$pid = $tryOrderData['pid'];
				$ubill = DreamPoolManager::Pool($pid)["ubill"];
				//更新红包订单状态
				return RESPONDINSTANCE('0');
			}else{
				return RESPONDINSTANCE('75');
			}
		}
		
		$tryOrderData = DBResultToArray($this->SelectDataByQuery(
				$this->TName("tOrder"),
					self::C_And(
						self::FieldIsValue("oid",$oid),
						self::FieldIsValue("bill",$bill)
					)
				)
			,true);
		
		if(!empty($tryOrderData)){
			$tryOrderData = $tryOrderData[0];
			if($tryOrderData['state'] == "SUBMIT"){
				$pid = $tryOrderData['pid'];
				$ubill = DreamPoolManager::Pool($pid)["ubill"];
				$pcount = $bill/$ubill;
				//更新梦想订单状态
				return RESPONDINSTANCE('0');
			}else{
				return RESPONDINSTANCE('75');
			}
		}
		
		return RESPONDINSTANCE('19');
	}
	
	
}
?>