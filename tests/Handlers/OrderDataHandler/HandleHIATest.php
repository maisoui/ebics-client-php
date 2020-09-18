<?php

declare(strict_types=1);

namespace AndrewSvirin\Ebics\Tests\Handlers\OrderDataHandler;

use AndrewSvirin\Ebics\Exceptions\EbicsException;
use AndrewSvirin\Ebics\Handlers\OrderDataHandler;
use AndrewSvirin\Ebics\Models\Bank;
use AndrewSvirin\Ebics\Models\Certificate;
use AndrewSvirin\Ebics\Models\CertificateX509;
use AndrewSvirin\Ebics\Models\KeyRing;
use AndrewSvirin\Ebics\Models\OrderData;
use AndrewSvirin\Ebics\Models\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class HandleHIATest extends TestCase
{
    public function testNotCertified(): void
    {
        $sUT = new OrderDataHandler();

        $orderData    = new OrderData();
        $bank         = self::createMock(Bank::class);
        $user         = self::createMock(User::class);
        $keyRing      = self::createMock(KeyRing::class);
        $certificateE = self::createMock(Certificate::class);
        $certificateX = self::createMock(Certificate::class);
        $datetime     = new DateTime('2010-10-10 10:10:10');

        $certificateE->expects(self::once())->method('getPublicKey')->willReturn('-----BEGIN RSA PUBLIC KEY-----
MIIBCgKCAQEA+xGZ/wcz9ugFpP07Nspo6U17l0YhFiFpxxU4pTk3Lifz9R3zsIsu
ERwta7+fWIfxOo208ett/jhskiVodSEt3QBGh4XBipyWopKwZ93HHaDVZAALi/2A
+xTBtWdEo7XGUujKDvC2/aZKukfjpOiUI8AhLAfjmlcD/UZ1QPh0mHsglRNCmpCw
mwSXA9VNmhz+PiB+Dml4WWnKW/VHo2ujTXxq7+efMU4H2fny3Se3KYOsFPFGZ1TN
QSYlFuShWrHPtiLmUdPoP6CV2mML1tk+l7DIIqXrQhLUKDACeM5roMx0kLhUWB8P
+0uj1CNlNN4JRZlC7xFfqiMbFRU9Z4N6YwIDAQAB
-----END RSA PUBLIC KEY-----');

        $certificateX->expects(self::once())->method('getPublicKey')->willReturn('-----BEGIN RSA PUBLIC KEY-----
MIIBCgKCAQEA+xGZ/wcz9ugFpP07Nspo6U17l0YhFiFpxxU4pTk3Lifz9R3zsIsu
ERwta7+fWIfxOo208ett/jhskiVodSEt3QBGh4XBipyWopKwZ93HHaDVZAALi/2A
+xTBtWdEo7XGUujKDvC2/aZKukfjpOiUI8AhLAfjmlcD/UZ1QPh0mHsglRNCmpCw
mwSXA9VNmhz+PiB+Dml4WWnKW/VHo2ujTXxq7+efMU4H2fny3Se3KYOsFPFGZ1TN
QSYlFuShWrHPtiLmUdPoP6CV2mML1tk+l7DIIqXrQhLUKDACeM5roMx0kLhUWB8P
+0uj1CNlNN4JRZlC7xFfqiMbFRU9Z4N6YwIDAQAB
-----END RSA PUBLIC KEY-----');

        $orderData = $sUT->handleHIA($bank, $user, $keyRing, $orderData, $certificateE, $certificateX, $datetime);

        self::assertXmlStringEqualsXmlString('<?xml version="1.0"?>
<HIARequestOrderData xmlns="urn:org:ebics:H004" xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
  <AuthenticationPubKeyInfo>
    <PubKeyValue>
      <ds:RSAKeyValue>
        <ds:Modulus>+xGZ/wcz9ugFpP07Nspo6U17l0YhFiFpxxU4pTk3Lifz9R3zsIsuERwta7+fWIfxOo208ett/jhskiVodSEt3QBGh4XBipyWopKwZ93HHaDVZAALi/2A+xTBtWdEo7XGUujKDvC2/aZKukfjpOiUI8AhLAfjmlcD/UZ1QPh0mHsglRNCmpCwmwSXA9VNmhz+PiB+Dml4WWnKW/VHo2ujTXxq7+efMU4H2fny3Se3KYOsFPFGZ1TNQSYlFuShWrHPtiLmUdPoP6CV2mML1tk+l7DIIqXrQhLUKDACeM5roMx0kLhUWB8P+0uj1CNlNN4JRZlC7xFfqiMbFRU9Z4N6Yw==</ds:Modulus>
        <ds:Exponent>AQAB</ds:Exponent>
      </ds:RSAKeyValue>
      <TimeStamp>2010-10-10T10:10:10Z</TimeStamp>
    </PubKeyValue>
    <AuthenticationVersion/>
  </AuthenticationPubKeyInfo>
  <EncryptionPubKeyInfo>
    <PubKeyValue>
      <ds:RSAKeyValue>
        <ds:Modulus>+xGZ/wcz9ugFpP07Nspo6U17l0YhFiFpxxU4pTk3Lifz9R3zsIsuERwta7+fWIfxOo208ett/jhskiVodSEt3QBGh4XBipyWopKwZ93HHaDVZAALi/2A+xTBtWdEo7XGUujKDvC2/aZKukfjpOiUI8AhLAfjmlcD/UZ1QPh0mHsglRNCmpCwmwSXA9VNmhz+PiB+Dml4WWnKW/VHo2ujTXxq7+efMU4H2fny3Se3KYOsFPFGZ1TNQSYlFuShWrHPtiLmUdPoP6CV2mML1tk+l7DIIqXrQhLUKDACeM5roMx0kLhUWB8P+0uj1CNlNN4JRZlC7xFfqiMbFRU9Z4N6Yw==</ds:Modulus>
        <ds:Exponent>AQAB</ds:Exponent>
      </ds:RSAKeyValue>
      <TimeStamp>2010-10-10T10:10:10Z</TimeStamp>
    </PubKeyValue>
    <EncryptionVersion/>
  </EncryptionPubKeyInfo>
  <PartnerID/>
  <UserID/>
</HIARequestOrderData>
', $orderData->saveXML());
    }

    public function testCertified(): void
    {
        $sUT = new OrderDataHandler();

        $orderData    = new OrderData();
        $bank         = self::createMock(Bank::class);
        $user         = self::createMock(User::class);
        $keyRing      = self::createMock(KeyRing::class);
        $certificateE = self::createMock(Certificate::class);
        $certificateX = self::createMock(Certificate::class);
        $datetime     = new DateTime('2010-10-10 10:10:10');
        $x509         = self::createMock(CertificateX509::class);

        $x509->expects(self::exactly(2))->method('getSerialNumber')->willReturn('test');
        $x509->expects(self::exactly(2))->method('getInsurerName')->willReturn('test2');
        $bank->expects(self::exactly(2))->method('isCertified')->willReturn(true);
        $certificateE->expects(self::once())->method('toX509')->willReturn($x509);
        $certificateX->expects(self::once())->method('toX509')->willReturn($x509);

        $certificateE->expects(self::once())->method('getPublicKey')->willReturn('-----BEGIN RSA PUBLIC KEY-----
MIIBCgKCAQEA+xGZ/wcz9ugFpP07Nspo6U17l0YhFiFpxxU4pTk3Lifz9R3zsIsu
ERwta7+fWIfxOo208ett/jhskiVodSEt3QBGh4XBipyWopKwZ93HHaDVZAALi/2A
+xTBtWdEo7XGUujKDvC2/aZKukfjpOiUI8AhLAfjmlcD/UZ1QPh0mHsglRNCmpCw
mwSXA9VNmhz+PiB+Dml4WWnKW/VHo2ujTXxq7+efMU4H2fny3Se3KYOsFPFGZ1TN
QSYlFuShWrHPtiLmUdPoP6CV2mML1tk+l7DIIqXrQhLUKDACeM5roMx0kLhUWB8P
+0uj1CNlNN4JRZlC7xFfqiMbFRU9Z4N6YwIDAQAB
-----END RSA PUBLIC KEY-----');

        $certificateX->expects(self::once())->method('getPublicKey')->willReturn('-----BEGIN RSA PUBLIC KEY-----
MIIBCgKCAQEA+xGZ/wcz9ugFpP07Nspo6U17l0YhFiFpxxU4pTk3Lifz9R3zsIsu
ERwta7+fWIfxOo208ett/jhskiVodSEt3QBGh4XBipyWopKwZ93HHaDVZAALi/2A
+xTBtWdEo7XGUujKDvC2/aZKukfjpOiUI8AhLAfjmlcD/UZ1QPh0mHsglRNCmpCw
mwSXA9VNmhz+PiB+Dml4WWnKW/VHo2ujTXxq7+efMU4H2fny3Se3KYOsFPFGZ1TN
QSYlFuShWrHPtiLmUdPoP6CV2mML1tk+l7DIIqXrQhLUKDACeM5roMx0kLhUWB8P
+0uj1CNlNN4JRZlC7xFfqiMbFRU9Z4N6YwIDAQAB
-----END RSA PUBLIC KEY-----');

        $orderData = $sUT->handleHIA($bank, $user, $keyRing, $orderData, $certificateE, $certificateX, $datetime);

        self::assertXmlStringEqualsXmlString('<?xml version="1.0"?>
<HIARequestOrderData xmlns="urn:org:ebics:H004" xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
  <AuthenticationPubKeyInfo>
    <ds:X509Data>
      <ds:X509IssuerSerial>
        <ds:X509IssuerName>test2</ds:X509IssuerName>
        <ds:X509SerialNumber>test</ds:X509SerialNumber>
      </ds:X509IssuerSerial>
      <ds:X509Certificate/>
    </ds:X509Data>
    <PubKeyValue>
      <ds:RSAKeyValue>
        <ds:Modulus>+xGZ/wcz9ugFpP07Nspo6U17l0YhFiFpxxU4pTk3Lifz9R3zsIsuERwta7+fWIfxOo208ett/jhskiVodSEt3QBGh4XBipyWopKwZ93HHaDVZAALi/2A+xTBtWdEo7XGUujKDvC2/aZKukfjpOiUI8AhLAfjmlcD/UZ1QPh0mHsglRNCmpCwmwSXA9VNmhz+PiB+Dml4WWnKW/VHo2ujTXxq7+efMU4H2fny3Se3KYOsFPFGZ1TNQSYlFuShWrHPtiLmUdPoP6CV2mML1tk+l7DIIqXrQhLUKDACeM5roMx0kLhUWB8P+0uj1CNlNN4JRZlC7xFfqiMbFRU9Z4N6Yw==</ds:Modulus>
        <ds:Exponent>AQAB</ds:Exponent>
      </ds:RSAKeyValue>
      <TimeStamp>2010-10-10T10:10:10Z</TimeStamp>
    </PubKeyValue>
    <AuthenticationVersion/>
  </AuthenticationPubKeyInfo>
  <EncryptionPubKeyInfo>
    <ds:X509Data>
      <ds:X509IssuerSerial>
        <ds:X509IssuerName>test2</ds:X509IssuerName>
        <ds:X509SerialNumber>test</ds:X509SerialNumber>
      </ds:X509IssuerSerial>
      <ds:X509Certificate/>
    </ds:X509Data>
    <PubKeyValue>
      <ds:RSAKeyValue>
        <ds:Modulus>+xGZ/wcz9ugFpP07Nspo6U17l0YhFiFpxxU4pTk3Lifz9R3zsIsuERwta7+fWIfxOo208ett/jhskiVodSEt3QBGh4XBipyWopKwZ93HHaDVZAALi/2A+xTBtWdEo7XGUujKDvC2/aZKukfjpOiUI8AhLAfjmlcD/UZ1QPh0mHsglRNCmpCwmwSXA9VNmhz+PiB+Dml4WWnKW/VHo2ujTXxq7+efMU4H2fny3Se3KYOsFPFGZ1TNQSYlFuShWrHPtiLmUdPoP6CV2mML1tk+l7DIIqXrQhLUKDACeM5roMx0kLhUWB8P+0uj1CNlNN4JRZlC7xFfqiMbFRU9Z4N6Yw==</ds:Modulus>
        <ds:Exponent>AQAB</ds:Exponent>
      </ds:RSAKeyValue>
      <TimeStamp>2010-10-10T10:10:10Z</TimeStamp>
    </PubKeyValue>
    <EncryptionVersion/>
  </EncryptionPubKeyInfo>
  <PartnerID/>
  <UserID/>
</HIARequestOrderData>

', $orderData->saveXML());
    }

    public function testCertifiedButEmptyX509(): void
    {
        $sUT = new OrderDataHandler();

        $orderData    = new OrderData();
        $bank         = self::createMock(Bank::class);
        $user         = self::createMock(User::class);
        $keyRing      = self::createMock(KeyRing::class);
        $certificateE = self::createMock(Certificate::class);
        $certificateX = self::createMock(Certificate::class);
        $datetime     = new DateTime('2010-10-10 10:10:10');
        $x509         = self::createMock(CertificateX509::class);

        $certificateE->expects(self::never())->method('toX509')->willReturn(null);
        $certificateX->expects(self::once())->method('toX509')->willReturn(null);
        $bank->expects(self::once())->method('isCertified')->willReturn(true);

        self::expectException(EbicsException::class);
        self::expectExceptionMessage('Certificate X509 is empty.');

        $sUT->handleHIA($bank, $user, $keyRing, $orderData, $certificateE, $certificateX, $datetime);
    }
}
