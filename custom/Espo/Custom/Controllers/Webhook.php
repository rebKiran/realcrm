<?php

namespace Espo\Custom\Controllers;

use \Espo\Core\Utils\Util;

use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;
use \Espo\Core\Exceptions\NotFound;
use \PDO;
use Espo\ORM\Entity;
use \Espo\Core\Utils\Config;
use \Espo\Core\ORM\EntityManager;

class Webhook extends \Espo\Core\Templates\Controllers\Base
{
	public function actionGenerate($params, $data, $request, Entity $entity) {
		
		$pdo = $this->getEntityManager()->getPDO();
		
		$query = " select COLUMN_NAME 
			from information_schema.columns 
			where table_name = 'lead' 
			";
			
		$sth = $pdo->prepare($query);
		$sth->execute();

		$rowList = $sth->fetchAll(PDO::FETCH_ASSOC);
		$coulmns = array();
		foreach ($rowList as $row) {
			$coulmns[] = strtolower(str_replace('_', '',  preg_replace('/\s+/', ' ',trim( $row['COLUMN_NAME'] )) ) ); //$row['COLUMN_NAME'];
		}
		
		$parameters = array();
		foreach ($request->get() as $key => $values) {
			$parameters[strtolower(str_replace('_', '',  preg_replace('/\s+/', ' ',trim( $key )) ) )] = $values; //$row['COLUMN_NAME']; 
		}
		
		$paramflag = false;
		
		if( (isset($parameters['firstname']) && !empty($parameters['firstname'])) && (isset($parameters['emailaddress']) && !empty($parameters['emailaddress'])) ) {  echo 'inside';
			$paramflag = true;
		}
		
		/*foreach ($parameters as $strKey => $strInputData) {	
			if($strKey == 'firstname' && !empty( $strInputData )) {
				$paramflag = true;
			} 
			
			if($strKey == 'emailaddress' && !empty( $strInputData )) {
				$paramflag = true;
			} 
		}*/
	
	    if( false == $paramflag) {
		     return array( 'result' => 'false' , 'msg' => 'First name and email address is required.');
	    }   
			
		
	  	$queryName = " select count(l.id) as record 
			from lead l
			join entity_email_address ema ON ( ema.entity_id = l.id AND l.deleted='0')
			join email_address ea ON ( ema.email_address_id = ea.id)
			join entity_phone_number epn ON ( epn.entity_id = l.id )
			join phone_number pn ON ( epn.phone_number_id = pn.id )
			where ( l.first_name = '".$parameters['firstname']."' and l.last_name = '".$parameters['lastname']."' )
			and ( ea.name = '".$parameters['emailaddress']."')	and ( pn.name = '".$parameters['phonenumber']."')
			and ema.entity_type = 'Lead' and ema.primary = '1'
			and epn.entity_type = 'Lead' and epn.primary = '1'
			 and pn.deleted='0' and ea.deleted=0
			";
		
        $sthname = $pdo->prepare($queryName);
        $sthname->execute();

        $rowNames = $sthname->fetchAll(PDO::FETCH_ASSOC);
	
		if( $rowNames[0]['record'] > 0) {
			return array(  'result' => 'false' , 'msg' => 'Duplicate Record found.' );
		} else {	
			
			$id = uniqid();
			$data = array( 'id' => $id );
			$email = '';
			$phone = '';
			
			foreach ($parameters as $strKey => $strInputData) {	
				if( in_array($strKey,$coulmns) && !empty($strInputData)) {
					if($strKey == 'firstname') {
						$data['first_name'] = $strInputData;
					}
					if($strKey == 'lastname') {
						$data['last_name'] = $strInputData;
					}
					if($strKey == 'source') {
						$data['source'] = $strInputData;
					}
					if($strKey == 'status') {
						$data['status'] = $strInputData;
					}
					if($strKey == 'campaignid') {
						$data['campaign_id'] = $strInputData;
					}
				}	
				if($strKey == 'emailaddress') { 
					$email = $strInputData;
				}
				if($strKey == 'phonenumber') { 
					$phone = $strInputData;
				}
			}	
			
			$data['created_at'] = date('Y-m-d H:i:s');
			$fields = implode( ',', array_keys($data) );
			$fieldValues = '"'.implode( '","', $data  ).'"'; //implode( ',', $data );
			
				$sql = "INSERT INTO `lead`
						(".$fields .")
						VALUES
						(". $fieldValues. ")
					"; 
			$flag = false;
			//print_r($pdo->prepare($sql)->execute()); die;
			if( $pdo->prepare($sql)->execute()) {
				
				$flag = true;
				
				$intEmailId = uniqid();
				
				$sqlEmail = "INSERT INTO `email_address`
					(id, name, lower)
					VALUES
					('".$intEmailId."', '".$email."', '".$email."')
				"; 
				
				if( $pdo->prepare($sqlEmail)->execute() ) {
					
					$flag = true;
					
					$sqlEntityEmail = "INSERT INTO `entity_email_address`
						(entity_id, email_address_id, entity_type, `primary`)
						VALUES
						('".$id."', '".$intEmailId."', 'Lead', '1')
					";
					if( $pdo->prepare($sqlEntityEmail)->execute() ) {
						$flag = true;
					} else {
						$flag = false;
					}	
				} else {
					$flag = false;
				}	
				
				$intPhoneId = uniqid();
				
				$sqlPhone = "INSERT INTO `phone_number`
					(id, name)
					VALUES
					('".$intPhoneId."', '".$phone."')
				"; 
				
				if( $pdo->prepare($sqlPhone)->execute() ) {
					
					$flag = true;
					
					$sqlEntityPhone = "INSERT INTO `entity_phone_number`
						(entity_id, phone_number_id, entity_type, `primary`)
						VALUES
						('".$id."', '".$intPhoneId."', 'Lead', '1')
					";
					if( $pdo->prepare($sqlEntityPhone)->execute() ) {
						$flag = true;
					} else {
						$flag = false;
					}	
				} else {
					$flag = false;
				}	
				
			} else {
				$flag = false;
				
			}

			if( $flag == true) {
				return array( 'result' => 'true' );
			} else {
				return array( 'result' => 'false' );
			}
		} 		
	}	
	
	public function actionValidate($params, $data, $request) {
		
		$pdo = $this->getEntityManager()->getPDO();
		
		$hook = trim($data['hook']);
		if( isset( $data['hookId'] ) && !empty( $data['hookId'] ) ) {
			$hookId = trim($data['hookId']);
		}
		
		$query = " select count(id) as total 
			from webhook 
			where name = '".$hook."' and deleted = '0'
			";
		
		if( isset( $data['hookId'] ) && !empty( $data['hookId'] ) ) {
			$query .= " AND id != '" . $hookId . "'";
		}
		
        $sth = $pdo->prepare($query);
        $sth->execute();

        $rowList = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		if( $rowList[0]['total'] > 0) {
			return array( 'result' => 'true' );
		} else {
			return array( 'result' => 'false' );
		}		
	}	
}
