<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
*/


require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/CurrencyAmountType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/DataUriScheme.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/MerchantInformationType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/MerchantTransactionReferenceType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/NumericTokenType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/PositiveDecimal.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token100Type.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token250Type.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token3000Type.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token3Type.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/Token50Type.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/UuidType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/AccountLockedError.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/ActiveOrderError.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/ActivePairingError.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/AuthorizationError.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/BaseFault.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/BaseFaultElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/BusinessError.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/CashregisterAccessError.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/CertificateRenewalNotAllowed.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/CertificateRenewalRefused.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/DurationTooLong.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/ErrorCode.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/ExpressCheckoutCredentialsInvalid.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/ExpressConnectionCanceled.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/FundsError.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/InvalidAmount.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/InvalidCashRegister.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/InvalidCurrency.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/InvalidCustomerRelationKey.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/InvalidMerchant.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/InvalidMerchantTransactionReference.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/InvalidOfflineAuthorization.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/InvalidOrder.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/InvalidParameter.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/InvalidToken.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/InvalidVoucher.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/InvalidVoucherCategory.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/PairingError.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/ReversalError.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/StatusTransitionError.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/SystemError.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/TimeoutError.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Fault/Types/V2/UnspecifiedPairingError.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Header/Types/V2/RequestHeaderElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Header/Types/V2/RequestHeaderType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Header/Types/V2/ResponseHeaderElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Header/Types/V2/ResponseHeaderType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/BeaconSecurityType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CancelCheckInRequestElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CancelCheckInRequestType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CancelCheckInResponseElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CancelCheckInResponseType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CancelCheckinReason.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CancelOrderRequestElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CancelOrderRequestType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CancelOrderResponseElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CancelOrderResponseType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CashRegisterType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CheckInNotificationType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CheckSystemStatusRequestElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CheckSystemStatusRequestType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CheckSystemStatusResponseElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CheckSystemStatusResponseType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CodeValueType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/ConfirmOrderRequestElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/ConfirmOrderRequestType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/ConfirmOrderResponseElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/ConfirmOrderResponseType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CouponListType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CouponRejectionReason.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CouponType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/CustomerInformationType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/EnrollCashRegisterRequestElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/EnrollCashRegisterRequestType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/EnrollCashRegisterResponseElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/EnrollCashRegisterResponseType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/ExpressMerchantAuthorizationType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/FindOrderRequestElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/FindOrderRequestType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/FindOrderResponseElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/FindOrderResponseType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/GetCertificateValidityRequestElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/GetCertificateValidityRequestType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/GetCertificateValidityResponseElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/GetCertificateValidityResponseType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/KeyValueType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/LoyaltyType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/MonitorCheckInRequestElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/MonitorCheckInRequestType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/MonitorCheckInResponseElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/MonitorCheckInResponseType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/MonitorOrderRequestElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/MonitorOrderRequestType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/MonitorOrderResponseElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/MonitorOrderResponseType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OperationResultType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderKindType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderLinkType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderRequestType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderStatusType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/OrderType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/PairingStatusType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/PaymentAmountType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/PostingType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RejectedCouponType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RejectionReasonType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RenewCertificateRequestElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RenewCertificateRequestType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RenewCertificateResponseElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RenewCertificateResponseType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RequestCheckInRequestElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RequestCheckInRequestType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RequestCheckInResponseElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RequestCheckInResponseType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/RequestCustomerAliasType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/StartOrderRequestElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/StartOrderRequestType.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/StartOrderResponseElement.php');
require_once('Customweb/Twint/Stubs/Ch/Twint/Service/Merchant/Types/V2/StartOrderResponseType.php');
require_once('Customweb/Twint/Stubs/Org/W3/XMLSchema/AnyType.php');
require_once('Customweb/Twint/Stubs/Org/W3/XMLSchema/Boolean.php');
require_once('Customweb/Twint/Stubs/Org/W3/XMLSchema/Date.php');
require_once('Customweb/Twint/Stubs/Org/W3/XMLSchema/DateTime.php');
require_once('Customweb/Twint/Stubs/Org/W3/XMLSchema/Float.php');
require_once('Customweb/Twint/Stubs/Org/W3/XMLSchema/Integer.php');
require_once('Customweb/Twint/Stubs/Org/W3/XMLSchema/String.php');
require_once 'Customweb/Soap/AbstractService.php';
/**
 */

class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_V2_TWINTMerchantService extends Customweb_Soap_AbstractService {

	/**
	 * @var Customweb_Soap_IClient
	 */
	private $soapClient;
		
	/**
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInRequestElement $requestCheckInRequestElement
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInResponseElement
	 */ 
	public function requestCheckIn(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInRequestElement $requestCheckInRequestElement){
		$data = func_get_args();
		if (count($data) > 0) {;
			$data = current($data);
		} else {;
			 throw new InvalidArgumentException();
		};
		$call = $this->createSoapCall("RequestCheckIn", $data, "Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RequestCheckInResponseElement", "RequestCheckIn");
		$call->setStyle(Customweb_Soap_ICall::STYLE_DOCUMENT);
		$call->setInputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setOutputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setSoapVersion(Customweb_Soap_ICall::SOAP_VERSION_11);
		$call->setLocationUrl($this->resolveLocation("http://service.twint.ch/merchant/v2"));
		$result = $this->getClient()->invokeOperation($call);
		return $result;
		
	}
		
	/**
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorCheckInRequestElement $monitorCheckInRequestElement
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorCheckInResponseElement
	 */ 
	public function monitorCheckIn(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorCheckInRequestElement $monitorCheckInRequestElement){
		$data = func_get_args();
		if (count($data) > 0) {;
			$data = current($data);
		} else {;
			 throw new InvalidArgumentException();
		};
		$call = $this->createSoapCall("MonitorCheckIn", $data, "Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorCheckInResponseElement", "MonitorCheckIn");
		$call->setStyle(Customweb_Soap_ICall::STYLE_DOCUMENT);
		$call->setInputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setOutputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setSoapVersion(Customweb_Soap_ICall::SOAP_VERSION_11);
		$call->setLocationUrl($this->resolveLocation("http://service.twint.ch/merchant/v2"));
		$result = $this->getClient()->invokeOperation($call);
		return $result;
		
	}
		
	/**
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckInRequestElement $cancelCheckInRequestElement
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckInResponseElement
	 */ 
	public function cancelCheckIn(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckInRequestElement $cancelCheckInRequestElement){
		$data = func_get_args();
		if (count($data) > 0) {;
			$data = current($data);
		} else {;
			 throw new InvalidArgumentException();
		};
		$call = $this->createSoapCall("CancelCheckIn", $data, "Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelCheckInResponseElement", "CancelCheckIn");
		$call->setStyle(Customweb_Soap_ICall::STYLE_DOCUMENT);
		$call->setInputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setOutputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setSoapVersion(Customweb_Soap_ICall::SOAP_VERSION_11);
		$call->setLocationUrl($this->resolveLocation("http://service.twint.ch/merchant/v2"));
		$result = $this->getClient()->invokeOperation($call);
		return $result;
		
	}
		
	/**
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestElement $startOrderRequestElement
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseElement
	 */ 
	public function startOrder(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderRequestElement $startOrderRequestElement){
		$data = func_get_args();
		if (count($data) > 0) {;
			$data = current($data);
		} else {;
			 throw new InvalidArgumentException();
		};
		$call = $this->createSoapCall("StartOrder", $data, "Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_StartOrderResponseElement", "StartOrder");
		$call->setStyle(Customweb_Soap_ICall::STYLE_DOCUMENT);
		$call->setInputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setOutputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setSoapVersion(Customweb_Soap_ICall::SOAP_VERSION_11);
		$call->setLocationUrl($this->resolveLocation("http://service.twint.ch/merchant/v2"));
		$result = $this->getClient()->invokeOperation($call);
		return $result;
		
	}
		
	/**
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderRequestElement $monitorOrderRequestElement
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderResponseElement
	 */ 
	public function monitorOrder(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderRequestElement $monitorOrderRequestElement){
		$data = func_get_args();
		if (count($data) > 0) {;
			$data = current($data);
		} else {;
			 throw new InvalidArgumentException();
		};
		$call = $this->createSoapCall("MonitorOrder", $data, "Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_MonitorOrderResponseElement", "MonitorOrder");
		$call->setStyle(Customweb_Soap_ICall::STYLE_DOCUMENT);
		$call->setInputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setOutputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setSoapVersion(Customweb_Soap_ICall::SOAP_VERSION_11);
		$call->setLocationUrl($this->resolveLocation("http://service.twint.ch/merchant/v2"));
		$result = $this->getClient()->invokeOperation($call);
		return $result;
		
	}
		
	/**
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ConfirmOrderRequestElement $confirmOrderRequestElement
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ConfirmOrderResponseElement
	 */ 
	public function confirmOrder(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ConfirmOrderRequestElement $confirmOrderRequestElement){
		$data = func_get_args();
		if (count($data) > 0) {;
			$data = current($data);
		} else {;
			 throw new InvalidArgumentException();
		};
		$call = $this->createSoapCall("ConfirmOrder", $data, "Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_ConfirmOrderResponseElement", "ConfirmOrder");
		$call->setStyle(Customweb_Soap_ICall::STYLE_DOCUMENT);
		$call->setInputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setOutputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setSoapVersion(Customweb_Soap_ICall::SOAP_VERSION_11);
		$call->setLocationUrl($this->resolveLocation("http://service.twint.ch/merchant/v2"));
		$result = $this->getClient()->invokeOperation($call);
		return $result;
		
	}
		
	/**
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelOrderRequestElement $cancelOrderRequestElement
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelOrderResponseElement
	 */ 
	public function cancelOrder(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelOrderRequestElement $cancelOrderRequestElement){
		$data = func_get_args();
		if (count($data) > 0) {;
			$data = current($data);
		} else {;
			 throw new InvalidArgumentException();
		};
		$call = $this->createSoapCall("CancelOrder", $data, "Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CancelOrderResponseElement", "CancelOrder");
		$call->setStyle(Customweb_Soap_ICall::STYLE_DOCUMENT);
		$call->setInputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setOutputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setSoapVersion(Customweb_Soap_ICall::SOAP_VERSION_11);
		$call->setLocationUrl($this->resolveLocation("http://service.twint.ch/merchant/v2"));
		$result = $this->getClient()->invokeOperation($call);
		return $result;
		
	}
		
	/**
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderRequestElement $findOrderRequestElement
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderResponseElement
	 */ 
	public function findOrder(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderRequestElement $findOrderRequestElement){
		$data = func_get_args();
		if (count($data) > 0) {;
			$data = current($data);
		} else {;
			 throw new InvalidArgumentException();
		};
		$call = $this->createSoapCall("FindOrder", $data, "Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_FindOrderResponseElement", "FindOrder");
		$call->setStyle(Customweb_Soap_ICall::STYLE_DOCUMENT);
		$call->setInputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setOutputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setSoapVersion(Customweb_Soap_ICall::SOAP_VERSION_11);
		$call->setLocationUrl($this->resolveLocation("http://service.twint.ch/merchant/v2"));
		$result = $this->getClient()->invokeOperation($call);
		return $result;
		
	}
		
	/**
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterRequestElement $enrollCashRegisterRequestElement
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterResponseElement
	 */ 
	public function enrollCashRegister(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterRequestElement $enrollCashRegisterRequestElement){
		$data = func_get_args();
		if (count($data) > 0) {;
			$data = current($data);
		} else {;
			 throw new InvalidArgumentException();
		};
		$call = $this->createSoapCall("EnrollCashRegister", $data, "Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_EnrollCashRegisterResponseElement", "EnrollCashRegister");
		$call->setStyle(Customweb_Soap_ICall::STYLE_DOCUMENT);
		$call->setInputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setOutputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setSoapVersion(Customweb_Soap_ICall::SOAP_VERSION_11);
		$call->setLocationUrl($this->resolveLocation("http://service.twint.ch/merchant/v2"));
		$result = $this->getClient()->invokeOperation($call);
		return $result;
		
	}
		
	/**
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckSystemStatusRequestElement $checkSystemStatusRequestElement
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckSystemStatusResponseElement
	 */ 
	public function checkSystemStatus(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckSystemStatusRequestElement $checkSystemStatusRequestElement){
		$data = func_get_args();
		if (count($data) > 0) {;
			$data = current($data);
		} else {;
			 throw new InvalidArgumentException();
		};
		$call = $this->createSoapCall("CheckSystemStatus", $data, "Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckSystemStatusResponseElement", "CheckSystemStatus");
		$call->setStyle(Customweb_Soap_ICall::STYLE_DOCUMENT);
		$call->setInputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setOutputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setSoapVersion(Customweb_Soap_ICall::SOAP_VERSION_11);
		$call->setLocationUrl($this->resolveLocation("http://service.twint.ch/merchant/v2"));
		$result = $this->getClient()->invokeOperation($call);
		return $result;
		
	}
		
	/**
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityRequestElement $getCertificateValidityRequestElement
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityResponseElement
	 */ 
	public function getCertificateValidity(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityRequestElement $getCertificateValidityRequestElement){
		$data = func_get_args();
		if (count($data) > 0) {;
			$data = current($data);
		} else {;
			 throw new InvalidArgumentException();
		};
		$call = $this->createSoapCall("GetCertificateValidity", $data, "Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_GetCertificateValidityResponseElement", "GetCertificateValidity");
		$call->setStyle(Customweb_Soap_ICall::STYLE_DOCUMENT);
		$call->setInputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setOutputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setSoapVersion(Customweb_Soap_ICall::SOAP_VERSION_11);
		$call->setLocationUrl($this->resolveLocation("http://service.twint.ch/merchant/v2"));
		$result = $this->getClient()->invokeOperation($call);
		return $result;
		
	}
		
	/**
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RenewCertificateRequestElement $renewCertificateRequestElement
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RenewCertificateResponseElement
	 */ 
	public function renewCertificate(Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RenewCertificateRequestElement $renewCertificateRequestElement){
		$data = func_get_args();
		if (count($data) > 0) {;
			$data = current($data);
		} else {;
			 throw new InvalidArgumentException();
		};
		$call = $this->createSoapCall("RenewCertificate", $data, "Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_RenewCertificateResponseElement", "RenewCertificate");
		$call->setStyle(Customweb_Soap_ICall::STYLE_DOCUMENT);
		$call->setInputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setOutputEncoding(Customweb_Soap_ICall::ENCODING_LITERAL);
		$call->setSoapVersion(Customweb_Soap_ICall::SOAP_VERSION_11);
		$call->setLocationUrl($this->resolveLocation("http://service.twint.ch/merchant/v2"));
		$result = $this->getClient()->invokeOperation($call);
		return $result;
		
	}
	
}