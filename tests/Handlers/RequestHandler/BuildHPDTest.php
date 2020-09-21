<?php

declare(strict_types=1);

namespace AndrewSvirin\Ebics\Tests\Handlers\RequestHandler;

use AndrewSvirin\Ebics\Handlers\AuthSignatureHandler;
use AndrewSvirin\Ebics\Handlers\HeaderHandler;
use AndrewSvirin\Ebics\Handlers\RequestHandler;
use AndrewSvirin\Ebics\Models\Bank;
use AndrewSvirin\Ebics\Models\Certificate;
use AndrewSvirin\Ebics\Models\KeyRing;
use AndrewSvirin\Ebics\Models\User;
use AndrewSvirin\Ebics\Services\CryptService;
use DateTime;
use PHPUnit\Framework\TestCase;

class BuildHPDTest extends TestCase
{
    public function testOk(): void
    {
        $bank    = self::createMock(Bank::class);
        $user    = self::createMock(User::class);
        $keyring = self::createMock(KeyRing::class);

        $rsaPrivateKey = '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDWidncNpkqmHnFZbicgeZfmRht/+TzVO9RtZQ7NDHPWvWYih3L
BMsBKfX9rSKeso+c+feDLge5+Tp9vKt3Ip1vnaBr48jfAvkmzQyGk6OAMk2HTXY7
rOZls3Cv5jhuR95h+pO6AVCloN6wq4+Y5PnyyX7ZA3jkP/yhA0WITVryywIDAQAB
AoGBAMHvTUR3CpBp0zIxGOhJuOHUODQ/rUyWC9y2IvA954UFOZwRxoreo1BDCT6v
AuuoiJAjmq43rv5boJdHNUz1upAijaA7Ffz+kgnyxcpEydNN5/LeAGRbQoR0OOCy
0X8kTuigCDSX5Jz40D8IPTyQt4ClaVads7z405Cc8K21LzyxAkEA7pYGe3vC3asM
++wo6ZoG6op9csIq7DQLdFbSrc5pK80X3lc9tFwCgvLrpjGTqrT7DxVqSTaw69Zs
iNmmeK41SQJBAOYyf2aN9AwJ+aETcx3L/dRb9AlvlghVdy6pbEQRSfW+Ix/0BnPr
OelUyb3uvfnNJNrMmFCCToPiD/QPwG2363MCQCQ4GEHUtu9p0S3JWyijXltqrMKo
IVX8TKTbrV3/UM36c54oRlDECwmQESAItK8cjGRsztbQe6lBwvY/fFsjrykCQGrI
aALpvJjNE8hdnlvnIeMdmLG72owZIUU7AGb+4iElx2NuLoQdTOrpEwCRO/0h5YO9
vrYyDDvvZNkhaXE8DPMCQCelc+4FrWDAmVwjmBSIRK8wmlNYuzEDp6Y5XjO1gQcG
CdS9998mcx0ebl+HQ0y4oUSxPnXkuuoRb60MTqcAOmg=
-----END RSA PRIVATE KEY-----';

        $rsaPublicKey = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDWidncNpkqmHnFZbicgeZfmRht
/+TzVO9RtZQ7NDHPWvWYih3LBMsBKfX9rSKeso+c+feDLge5+Tp9vKt3Ip1vnaBr
48jfAvkmzQyGk6OAMk2HTXY7rOZls3Cv5jhuR95h+pO6AVCloN6wq4+Y5PnyyX7Z
A3jkP/yhA0WITVryywIDAQAB
-----END PUBLIC KEY-----';

        $certificatX = self::createMock(Certificate::class);

        $certificatX->expects(self::any())->method('getPublicKey')->willReturn($rsaPublicKey);
        $keyring->expects(self::once())->method('getBankCertificateX')->willReturn($certificatX);
        $keyring->expects(self::once())->method('getBankCertificateE')->willReturn($certificatX);
        $bank->expects(self::once())->method('getHostId')->willReturn('myHostId');

        $cryptService = self::createMock(CryptService::class);
        $cryptService->expects(self::once())->method('generateNonce')->willReturn('myNonce');
        $cryptService->expects(self::exactly(2))->method('calculateHash')->willReturn('myNonce');
        $cryptService->expects(self::once())->method('cryptSignatureValue')->willReturn('myNonce');
        $cryptService->expects(self::exactly(2))->method('calculateDigest')->willReturn('myDigest');

        $headerHandler        = new HeaderHandler($cryptService);
        $authSignatureHandler = new AuthSignatureHandler($cryptService);

        $sUT = new RequestHandler(null, $headerHandler, null, null, $authSignatureHandler);

        $date     = new DateTime();
        $expected = '<?xml version="1.0" encoding="UTF-8"?>
<ebicsRequest xmlns="urn:org:ebics:H004" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Version="H004" Revision="1">
   <header authenticate="true">
      <static>
         <HostID>myHostId</HostID>
         <Nonce>myNonce</Nonce>
         <Timestamp>' . $date->format('Y-m-d\TH:i:s\Z') . '</Timestamp>
         <PartnerID />
         <UserID />
         <Product Language="de">Ebics client PHP</Product>
         <OrderDetails>
            <OrderType>HPD</OrderType>
            <OrderAttribute>DZHNN</OrderAttribute>
            <StandardOrderParams />
         </OrderDetails>
         <BankPubKeyDigests>
            <Authentication Version="" Algorithm="http://www.w3.org/2001/04/xmlenc#sha256">bXlEaWdlc3Q=</Authentication>
            <Encryption Version="" Algorithm="http://www.w3.org/2001/04/xmlenc#sha256">bXlEaWdlc3Q=</Encryption>
         </BankPubKeyDigests>
         <SecurityMedium>0000</SecurityMedium>
      </static>
      <mutable>
         <TransactionPhase>Initialisation</TransactionPhase>
      </mutable>
   </header>
   <AuthSignature>
      <ds:SignedInfo>
         <ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" />
         <ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256" />
         <ds:Reference URI="#xpointer(//*[@authenticate=\'true\'])">
            <ds:Transforms>
               <ds:Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" />
            </ds:Transforms>
            <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256" />
            <ds:DigestValue>bXlOb25jZQ==</ds:DigestValue>
         </ds:Reference>
      </ds:SignedInfo>
      <ds:SignatureValue>bXlOb25jZQ==</ds:SignatureValue>
   </AuthSignature>
   <body />
</ebicsRequest>';

        self::assertXmlStringEqualsXmlString($expected, $sUT->buildHPD($bank, $user, $keyring, $date)->getContent());
    }
}
